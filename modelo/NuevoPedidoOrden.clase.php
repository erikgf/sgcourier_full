<?php 

require_once 'Modelo.clase.php';

class PedidoOrden extends Modelo{
    public $id_usuario_responsable;
    public $id_pedido;
    public $id_pedido_orden;
    public $id_cliente;
    public $codigo_numero_orden;
    public $id_tipo_usuario;
    public $tipo_visita;
    public $es_reenvio;
    public $es_receptor_destinatario;
    public $numero_documento_receptor;
    public $nombres_receptor;
    public $observaciones;
    public $motivaciones;
    public $imagenes;
    public $ultima_visita;
    public $estado;

    private $DIAS_MAXIMOS_APP = 30;

    private $ESTADO_ALMACEN_AGENCIA = 'A';
    private $ESTADO_ZONA_REPARTO = 'R';
    private $ESTADO_ZONA_REENVIO = 'V';
    private $ESTADO_ENTREGADO = 'E';
    private $ESTADO_MOTIVADO = 'M';

    public function __construct($BD = null){
        try {
            parent::__construct("nuevo_pedido_orden", $BD);   
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function listarPedidosPendientesXApp($pagina_actual, $items_per_load, $primera_carga){
        try{
            if (!$this->id_usuario_responsable){
                return [];
            }

            $ff = date("Y-m-d");
            $fi = date('Y-m-d', strtotime('-'.$this->DIAS_MAXIMOS_APP.' days'));
            $params = [$this->id_usuario_responsable, $fi, $ff, $this->id_cliente, $this->ESTADO_ZONA_REPARTO, $this->ESTADO_ZONA_REENVIO];

            if ($primera_carga == 1){
                $sql = "SELECT COUNT(po.id_pedido_orden) 
                            FROM nuevo_pedido_orden po
                            INNER JOIN nuevo_pedido_orden_estados poe ON poe.id_pedido_orden = po.id_pedido_orden AND poe.estado_mrcb AND poe.id_usuario_registro = po.id_usuario_asociado AND po.estado_actual = poe.id_estado_orden
                            LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                            WHERE po.estado_mrcb AND poe.id_usuario_registro = :0 
                                AND p.tipo_pedido = 1 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) 
                                AND p.id_cliente = :3
                                AND po.estado_actual IN (:4, :5)";

                $data["max_items"] = $this->BD->consultarValor($sql, $params);
            }

            $sql = "SELECT
                    p.id_cliente,
                    po.id_pedido_orden,
                    codigo_numero_orden,
                    codigo_guia,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion,
                    UPPER(referencia) as referencia,
                    numero_visitas,
                    DATE_FORMAT(poe.fecha_hora_registro, '%d/%m/%Y') as fecha_asignado,
                    po.estado_actual,
                    (numero_orden_asignado + 1) as numero_orden
                    FROM nuevo_pedido_orden po
                    INNER JOIN nuevo_pedido_orden_estados poe ON poe.id_pedido_orden = po.id_pedido_orden AND poe.estado_mrcb AND poe.id_usuario_registro = po.id_usuario_asociado AND po.estado_actual = poe.id_estado_orden
                    LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                    WHERE po.estado_mrcb 
                        AND poe.id_usuario_registro = :0 
                        AND p.tipo_pedido = 1 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) 
                        AND p.id_cliente = :3
                        AND po.estado_actual IN (:4, :5)
                    ORDER BY poe.fecha_hora_registro, po.numero_orden_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;

            $data["registros"] = $this->BD->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarPedidosCompletadosXApp($pagina_actual, $items_per_load, $primera_carga){
        try{
            if (!$this->id_usuario_responsable){
                return [];
            }

            $ff = date("Y-m-d");
            $fi = date('Y-m-d', strtotime('-'.$this->DIAS_MAXIMOS_APP.' days'));

            $params = [$this->id_usuario_responsable, $fi, $ff, $this->id_cliente, $this->ESTADO_ENTREGADO, $this->ESTADO_MOTIVADO];

            if ($primera_carga == 1){
                $sql = "SELECT COUNT(po.id_pedido_orden) 
                            FROM nuevo_pedido_orden po
                            INNER JOIN nuevo_pedido_orden_estados poe ON poe.id_pedido_orden = po.id_pedido_orden AND po.estado_actual = poe.id_estado_orden AND poe.estado_mrcb
                            LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                            WHERE po.estado_mrcb AND poe.id_usuario_registro = :0 
                                AND p.tipo_pedido = 1 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) 
                                AND p.id_cliente = :3
                                AND po.estado_actual IN (:4,:5)";

                $data["max_items"] = $this->BD->consultarValor($sql, $params);
            }

            $sql = "SELECT
                    p.id_cliente,
                    po.id_pedido_orden,
                    codigo_numero_orden,
                    codigo_guia,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion,
                    UPPER(referencia) as referencia,
                    numero_visitas,
                    poe.fecha_hora_registro as fecha_hora_atendido,
                    po.estado_actual,
                    (numero_orden_asignado + 1) as numero_orden,
                    DATE_FORMAT(poe.fecha_hora_registro, '%d-%m-%Y') as fecha_asignado
                    FROM nuevo_pedido_orden po
                    INNER JOIN nuevo_pedido_orden_estados poe ON poe.id_pedido_orden = po.id_pedido_orden AND po.estado_actual = poe.id_estado_orden AND poe.estado_mrcb
                    LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                    WHERE po.estado_mrcb 
                        AND poe.id_usuario_registro = :0 
                        AND p.tipo_pedido = 1 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) 
                        AND p.id_cliente = :3
                        AND po.estado_actual IN (:4,:5)
                    ORDER BY poe.fecha_hora_registro, po.numero_orden_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;

            $data["registros"] = $this->BD->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leerXIdApp(){
        try{

            if (!$this->id_usuario_responsable){
                return null;
            }

            $sql = "SELECT
                    p.id_cliente,
                    po.id_pedido_orden,
                    codigo_numero_orden,
                    codigo_guia,
                    COALESCE(numero_documento_destinatario,'') as numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion,
                    COALESCE(UPPER(referencia),'') as referencia,
                    numero_visitas,
                    estado_actual,
                    COALESCE(DATE_FORMAT(poe1.fecha_hora_registro, '%d-%m-%Y'),'') as fecha_asignado,
                    COALESCE(DATE_FORMAT(poe2.fecha_hora_registro, '%d-%m-%Y %r'),'') as fecha_hora_atendido
                    FROM nuevo_pedido_orden po
                    LEFT JOIN nuevo_pedido_orden_estados poe1 ON poe1.id_pedido_orden = po.id_pedido_orden AND (poe1.id_estado_orden IN (:2, :3)) AND poe1.estado_mrcb
                    LEFT JOIN nuevo_pedido_orden_estados poe2 ON poe2.id_pedido_orden = po.id_pedido_orden AND (poe2.id_estado_orden IN (:4, :5)) AND poe2.estado_mrcb
                    LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                    WHERE po.estado_mrcb AND poe1.id_usuario_registro = :0 AND po.id_pedido_orden = :1";

            $data = $this->BD->consultarFila($sql, [$this->id_usuario_responsable, $this->id_pedido_orden, $this->ESTADO_ZONA_REPARTO, $this->ESTADO_ZONA_REENVIO, $this->ESTADO_ENTREGADO, $this->ESTADO_MOTIVADO]);

            if ($data == false){
                $data = null;
            }

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    
    public function registrarVisitaApp(){
        try{
            if (!$this->id_usuario_responsable){
                return null;
            }
            
            /*Verifica si ya existe un registro HOY.
                1. mismo orden_pedido
                2. mismo usuario
                3. valido
                4. mismo estado
                5. mismo día
            */ 

            $sql = "SELECT npo.id_pedido_orden
                        FROM nuevo_pedido_orden npo
                        WHERE npo.id_pedido_orden = :0 AND npo.estado_mrcb 
                            AND npo.estado_actual IN ('E','M')";
            
            $pedidoOrdenYaEntregado = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);

            if ($pedidoOrdenYaEntregado){
                throw new Exception("Se está intentando enviar dos veces un registro ya  enviado. Paciencia por favor.");
           }
            
            $sql = "SELECT COUNT(id_pedido_orden_visita) 
                    FROM nuevo_pedido_orden_visita 
                    WHERE estado_mrcb AND id_usuario_responsable = :0 
                    AND estado_actual = :1 AND id_pedido_orden = :2 AND DATE(fecha_hora_registro) = current_date";
                    
            $existeRegistro = $this->BD->consultarValor($sql, [$this->id_usuario_responsable, $this->tipo_visita, $this->id_pedido_orden]);
    
            if ($existeRegistro){
                 throw new Exception("Se está intentando enviar dos veces un registro ya  enviado. Paciencia por favor.");
            }

            $sql = "SELECT COALESCE(MAX(numero_visita) + 1, 1) 
                    FROM nuevo_pedido_orden_visita 
                    WHERE id_pedido_orden = :0 AND estado_mrcb";
            $numero_visita = $this->BD->consultarValor($sql, [$this->id_pedido_orden]);

            $sql = "SELECT npo.id_pedido, np.fecha_ingreso, npo.estado_actual
                        FROM nuevo_pedido_orden npo
                        INNER JOIN nuevo_pedido np ON np.id_pedido = npo.id_pedido
                        WHERE npo.id_pedido_orden = :0 AND npo.estado_mrcb";
            
            $pedidoRegistro = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);

            if ($pedidoRegistro === false){
                throw new Exception("Esta orden no tiene un pedido asociado. Consultar con el administrador del sistema.", 1);
            }

            $this->BD->beginTransaction();

            $this->BD->update("nuevo_pedido_orden_visita", ["ultima_visita"=>"0"], ["id_pedido_orden"=>$this->id_pedido_orden]);

            if ($this->tipo_visita == "E" && $this->es_receptor_destinatario){
                $sql = "SELECT numero_documento_destinatario, UPPER(destinatario) as destinatario 
                        FROM nuevo_pedido_orden WHERE id_pedido_orden = :0";
                $nuevo_pedido_orden = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);
                $this->numero_documento_receptor = $nuevo_pedido_orden["numero_documento_destinatario"];
                $this->nombres_receptor = $nuevo_pedido_orden["destinatario"];
            }

            $campos_valores = [
                "id_pedido_orden"=>$this->id_pedido_orden,
                "numero_visita"=>$numero_visita,
                "estado_actual"=>$this->tipo_visita,
                "observaciones"=>mb_strtoupper($this->observaciones, 'UTF-8'),
                "es_receptor_destinatario"=>$this->es_receptor_destinatario,
                "numero_documento_receptor"=>$this->numero_documento_receptor,
                "nombres_receptor"=>$this->nombres_receptor == NULL ? NULL : mb_strtoupper($this->nombres_receptor, 'UTF-8'),
                "id_usuario_responsable"=>$this->id_usuario_responsable,
                "ultima_visita"=>"1"
            ];
            $this->BD->insert("nuevo_pedido_orden_visita", $campos_valores);

            $id_pedido_orden_visita = $this->BD->getLastID();

            if ($this->motivaciones && count($this->motivaciones) > 0){
                $campos = ["id_pedido_orden_visita", "id_motivacion"];
                $valores = [];
                foreach ($this->motivaciones as $key => $value) {
                    array_push($valores, [$id_pedido_orden_visita, $value]);
                }

                $this->BD->insertMultiple("nuevo_pedido_orden_visita_motivacion", $campos, $valores);
            }   

            if ($this->imagenes && count($this->imagenes) > 0){
                $nombreCarpetaImgVisitas = "../img/imagenes_visitas_nuevo";
                $nombreCarpeta = str_replace("-", "",$pedidoRegistro["fecha_ingreso"]);
                $urlDirectorioGuardar = $nombreCarpetaImgVisitas."/".$nombreCarpeta;
                if (!file_exists($urlDirectorioGuardar)) { 
                    mkdir($urlDirectorioGuardar, 0777, true); 
                }

                $campos = ["id_pedido_orden_visita", "numero_imagen", "url_img", "nombre_imagen_original", "tamano", "tipo_imagen"];
                $valores = [];
                $numero_imagen = 1;

                foreach ($this->imagenes as $key => $value) {
                    $url_img = $nombreCarpeta."/".$id_pedido_orden_visita."_".$numero_imagen."_".md5($value["nombre"]);
                    $nombre_imagen_original = $value["nombre"];
                    $tamano = $value["tamano"];
                    $tipo_imagen = $value["tipo"];
                    array_push($valores, [$id_pedido_orden_visita, $numero_imagen, $url_img, $nombre_imagen_original, $tamano, $tipo_imagen]);
                    if (!move_uploaded_file($value["archivo"], $nombreCarpetaImgVisitas."/".$url_img)) {
                        $this->BD->rollBack();
                        throw new Exception("Error al subir la imagen ".$numero_imagen.".");
                    }
                    $numero_imagen++;
                }
                $this->BD->insertMultiple("nuevo_pedido_orden_visita_imagen", $campos, $valores);
            }

            $estado_nuevo  = "";

            switch ($this->tipo_visita){
                case "G":
                    $estado_nuevo = $this->ESTADO_ALMACEN_AGENCIA;
                    break;
                case "M":
                    $estado_nuevo = $this->ESTADO_MOTIVADO;
                    break;
                case "E":
                    $estado_nuevo = $this->ESTADO_ENTREGADO;
                    break;
            }

            if ($estado_nuevo === ""){
                throw new Exception("No hay un estado de pedido válido asignado.", 1);
            }

            $retrocediendoEnElProceso = $this->ESTADO_ALMACEN_AGENCIA === $estado_nuevo;
           
            $campos_valores = [
                "estado_actual"=>$estado_nuevo,
                "numero_visitas"=>$numero_visita
            ];

            if ($retrocediendoEnElProceso){
                $campos_valores["id_usuario_asociado"] = NULL;
            }

            $this->BD->update("nuevo_pedido_orden", 
                                $campos_valores, [
                                    "id_pedido_orden"=>$this->id_pedido_orden
                                ]);

            $id_usuario_registro_estado = $this->id_usuario_responsable;
            $ESTADO_ZONA_PREVIO = $pedidoRegistro["estado_actual"];

            if ($retrocediendoEnElProceso){
                //$sql = "SELECT id_usuario_registro FROM nuevo_pedido_orden_estados WHERE id_pedido_orden = :0 AND id_estado_orden = :1 AND estado_mrcb";
                //$id_usuario_registro_estado = $this->BD->consultarValor($sql, [$this->id_pedido_orden, $this->ESTADO_ALMACEN_AGENCIA]);

                $sql = "UPDATE nuevo_pedido_orden_estados 
                        SET estado_mrcb = 0
                        WHERE id_pedido_orden = :0 AND id_estado_orden IN (:1, :2) AND estado_mrcb";
                $this->BD->ejecutar_raw($sql, [$this->id_pedido_orden, $ESTADO_ZONA_PREVIO, $this->ESTADO_ALMACEN_AGENCIA]);
                //agencia ++
                //repartidor --
            }

            $sql = "UPDATE nuevo_pedido_cantidad 
                    SET cantidad = cantidad - 1
                    WHERE id_pedido = :0  AND id_estado_orden = :1";
            $this->BD->ejecutar_raw($sql, [$pedidoRegistro["id_pedido"], $ESTADO_ZONA_PREVIO]);

            $sql = "UPDATE nuevo_pedido_cantidad 
                        SET cantidad = cantidad + 1
                        WHERE id_pedido = :0 AND id_estado_orden = :1";
            $this->BD->ejecutar_raw($sql, [$pedidoRegistro["id_pedido"], $estado_nuevo]);

            $this->BD->insert("nuevo_pedido_orden_estados", [
                "id_pedido_orden"=>$this->id_pedido_orden,
                "id_estado_orden"=>$estado_nuevo,
                "id_usuario_registro"=>$id_usuario_registro_estado
            ]);

            $this->BD->commit();
            return ["msj"=>"Registrado correctamente.", "id"=>$this->id_pedido_orden];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leerXOrdenCodigoRemitoApp(){
        try{
            
            if (!$this->id_usuario_responsable){
                return null;
            }

            $sql  = "SELECT id_pedido_orden, estado_actual FROM nuevo_pedido_orden po
                            INNER JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido AND p.estado_mrcb
                            WHERE codigo_guia = :0 AND po.estado_mrcb";
            $pedidoOrden = $this->BD->consultarFila($sql, [$this->codigo_remito]);

            if (!$pedidoOrden){
                return null;
            }
            
            if (in_array($pedidoOrden["estado_actual"],['E'])){
                throw new Exception("El pedido YA esta ENTREGADO.");
            }

            $this->id_pedido_orden = $pedidoOrden["id_pedido_orden"];

            return $this->leerXIdApp();
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    /*
    public function leerXOrdenId(){
        try{

            $sql = "SELECT
                    id_pedido_orden,
                    codigo_numero_orden,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion,
                    UPPER(referencia) as referencia,
                    distrito,
                    CONCAT(provincia,' - ',departamento) as provincia_departamento,
                    telefono_uno_destinatario as telefono,
                    celular_destinatario as celular,
                    (CASE estado_actual 
                        WHEN 'A' THEN 'EN AGENCIA' 
                        WHEN 'R' THEN 'ENTREGANDO'
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'EN RUTA' END) as estado,
                    (CASE estado_actual 
                        WHEN 'A' THEN 'warning' 
                        WHEN 'R' THEN 'info'
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                    (SELECT COUNT(id_pedido_orden_correccion) 
                        FROM nuevo_pedido_orden_correccion
                        WHERE id_pedido_orden = po.id_pedido_orden) as veces_corregido,
                    (numero_orden_asignado + 1) as numero_orden
                    FROM nuevo_pedido_orden po
                    LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente
                    WHERE po.estado_mrcb AND po.id_pedido_orden = :0                  
                    ORDER BY po.numero_orden_asignado, po.id_pedido_orden";

            $data = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);

            if ($data == false){
                return null;
            }

            $sql  = "SELECT numero_visita, observaciones, 
                        numero_documento_receptor as numero_documento_receptor, 
                        nombres_receptor as nombres_receptor, 
                        DATE_FORMAT(fecha_hora_registro,'%d-%m-%Y') as fecha,
                        DATE_FORMAT(fecha_hora_registro,'%H:%i:%s') as hora,
                        (SELECT COALESCE(GROUP_CONCAT(mot.descripcion),'')
                            FROM nuevo_pedido_orden_visita_motivacion povm 
                            LEFT JOIN motivacion mot ON mot.id_motivacion = povm.id_motivacion
                            WHERE povm.id_pedido_orden_visita = pov.id_pedido_orden_visita) as motivaciones,
                        (SELECT COALESCE(GROUP_CONCAT(povi.url_img),'') FROM nuevo_pedido_orden_visita_imagen povi 
                            WHERE povi.id_pedido_orden_visita = pov.id_pedido_orden_visita) as urls
                        FROM nuevo_pedido_orden_visita pov
                        WHERE pov.id_pedido_orden = :0 AND pov.estado_mrcb AND pov.id_pedido_orden IS NOT NULL
                        ORDER BY pov.numero_visita DESC";

            $data["visitas"] = $this->BD->consultarFilas($sql, [$this->id_pedido_orden]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    */

    public function corregirEstado($descripcion, $estado_nuevo){
        try{
            $this->BD->beginTransaction();

            /*
                1.- registrar correccion (Sirve para reversi�n)
                    id_usuario
                    id_pedido_orden

                2.- actualizar el nuevo_pedido orden
                    estado_actual => estado_nuevo
                    recalcular cantidades
            */

            $id_usuario_registro = $_SESSION["sesion"]["id_usuario"];

            $sql  ="SELECT po.id_pedido, po.id_pedido_orden, pov.id_pedido_orden_visita, 
                            pov.estado_actual as estado_anterior
                    FROM nuevo_pedido_orden po
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.id_pedido_orden = :0";

            $nuevo_pedido_orden = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);

            if ($nuevo_pedido_orden == false){
                throw new Exception("No se ha encontrado órden que corregir.");
            }

            if ($nuevo_pedido_orden["estado_anterior"] == "A"){
                throw new Exception("No se puede corregir una órden que ya se está GESTIONANDO.");
            }

            if ($nuevo_pedido_orden["estado_anterior"] == "N"){
                throw new Exception("No se puede corregir una órden que ya se está NO ASIGNADO.");
            }

            $campos_valores = [
                "id_pedido_orden"=>$this->id_pedido_orden,
                "id_pedido_orden_visita"=>$nuevo_pedido_orden["id_pedido_orden_visita"],
                "correccion_descripcion"=>$descripcion,
                "estado_anterior"=>$nuevo_pedido_orden["estado_anterior"],
                "estado_nuevo"=>$estado_nuevo,
                "id_usuario_registro"=>$id_usuario_registro
            ];

            $this->BD->insert("pedido_orden_correccion", $campos_valores);

            $campos_valores = [
                "id_usuario_atendido"=>NULL,
                "fecha_hora_atendido"=>NULL,
                "estado_actual"=>$estado_nuevo,
            ];

            $campos_valores_where = [
                "id_pedido_orden"=>$this->id_pedido_orden
            ];

            $this->BD->update("nuevo_pedido_orden", $campos_valores, $campos_valores_where);

            $campos_valores = [
                "estado_actual"=>$estado_nuevo,
            ];

            $campos_valores_where = [
                "id_pedido_orden_visita"=>$nuevo_pedido_orden["id_pedido_orden_visita"]
            ];

            $this->BD->update("pedido_orden_visita", $campos_valores, $campos_valores_where);

            if ($nuevo_pedido_orden["estado_anterior"] == "E"){
                $sqlExtraCantidad = " cantidad_entregadas = cantidad_entregadas - 1";
            } else {
                $sqlExtraCantidad = " cantidad_motivadas = cantidad_motivadas - 1";
            }

            $sql = "UPDATE nuevo_pedido 
                        SET cantidad_enagencia = cantidad_enagencia + 1,
                            $sqlExtraCantidad
                    WHERE id_pedido IN (:0)";

            $this->BD->ejecutar_raw($sql, [$this->id_pedido]);
            $this->BD->commit();
            
            return ["msj"=>"Orden corregida.", "id"=>$this->id_pedido_orden];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function asignarMasivoPorCodigo($arregloOrdenes){
        try{
            /* Asignas desde Estado 3 a estado 4*/
            $cantidadOrdenesActualizadas = 0;
            $cantidadOrdenes = count($arregloOrdenes);
            
            $this->BD->beginTransaction();

            $ID_ESTADO_NUEVO = $this->ESTADO_ZONA_REPARTO;

            if ($this->es_reenvio == "1"){
                $ID_ESTADO_NUEVO = $this->ESTADO_ZONA_REENVIO;
            } else {
                $ID_ESTADO_NUEVO = $this->id_usuario_responsable == NULL 
                                    ? $this->ESTADO_ALMACEN_AGENCIA 
                                    : $this->ESTADO_ZONA_REPARTO;
            }

            $estadosPorActualizar = [];
            $estadoNuevo  = $this->BD->consultarFila("SELECT id_estado_orden, numero_orden, estado_color, estado_color_rotulo
                                                        FROM estado_orden 
                                                        WHERE id_estado_orden = :0 AND estado_mrcb", [$ID_ESTADO_NUEVO]);

            $estadoNuevo["cantidad"] = 0;

            $estadosPorActualizar[$estadoNuevo["id_estado_orden"]] = $estadoNuevo;

            foreach ($arregloOrdenes as $key => $codigo) {
                /*1. verificar si no tiene visitas. O si estado actual != N o G*/
                $sql = "SELECT po.id_pedido_orden, eo.numero_orden,  po.estado_actual,
                            (SELECT COUNT(id_pedido_orden_visita) 
                                FROM nuevo_pedido_orden_visita pov 
                                WHERE pov.id_pedido_orden = po.id_pedido_orden AND pov.estado_mrcb)  as numero_visitas
                        FROM nuevo_pedido_orden po
                        INNER JOIN estado_orden eo ON eo.id_estado_orden = po.estado_actual
                        WHERE TRIM(po.codigo_guia) = :0 AND po.estado_mrcb AND po.id_pedido = :1";
                $nuevoPedidoOrden = $this->BD->consultarFila($sql, [trim($codigo), $this->id_pedido]);

                if ($nuevoPedidoOrden == false){
                    continue;
                }

                /* Tiene que tener el estado ALMACEN, REENVIO O REPARTO */
                if (!in_array($nuevoPedidoOrden["estado_actual"], [$this->ESTADO_ALMACEN_AGENCIA, $this->ESTADO_ZONA_REENVIO, $this->ESTADO_ZONA_REPARTO])) {
                    continue;
                }

                if ($nuevoPedidoOrden["numero_visitas"] > 0){
                    continue;
                }

                if (!isset($estadosPorActualizar[$nuevoPedidoOrden["estado_actual"]])){
                    $estadosPorActualizar[$nuevoPedidoOrden["estado_actual"]] = ["id_estado_orden"=>$nuevoPedidoOrden["estado_actual"], "cantidad"=>0];
                }

                $estadosPorActualizar[$nuevoPedidoOrden["estado_actual"]]["cantidad"]--;
                $estadosPorActualizar[$estadoNuevo["id_estado_orden"]]["cantidad"]++;

                /*
                if ($nuevoPedidoOrden["numero_orden"] == $ESTADO_PREVIO) {
                    $cantidadPost++;
                    $cantidadPrevios--;
                }

                if ($nuevoPedidoOrden["numero_orden"] == $ESTADO_POST) {
                    $cantidadPost--;
                    $cantidadPrevios++;
                }
                */
                $id_usuario_asociado = $this->id_usuario_responsable;

                $fueActualizado = $this->BD->update("nuevo_pedido_orden", 
                    [   "estado_actual"=>$estadoNuevo["id_estado_orden"], 
                        "id_usuario_asociado"=>$id_usuario_asociado,
                        "numero_orden_asignado" => $key
                        ], 
                    ["id_pedido_orden"=>$nuevoPedidoOrden["id_pedido_orden"]]); 
                    
                //SI R -> R o sea solo está ajustando el responsable, entonces, se debería anular el anterior R.
                
                $this->BD->insert("nuevo_pedido_orden_estados", [
                            "id_pedido_orden"=>$nuevoPedidoOrden["id_pedido_orden"],
                            "id_estado_orden"=>$estadoNuevo["id_estado_orden"], 
                            "id_usuario_registro"=>$id_usuario_asociado
                        ]);

                if ($fueActualizado) 
                    $cantidadOrdenesActualizadas++;
            }

            foreach ($estadosPorActualizar as $key => $estadoPorActualizar) {
                $sql = "UPDATE nuevo_pedido_cantidad 
                            SET cantidad = cantidad + (".$estadoPorActualizar["cantidad"].")
                            WHERE id_pedido IN (:0) AND id_estado_orden IN ('".$estadoPorActualizar["id_estado_orden"]."')";

                $this->BD->ejecutar_raw($sql, [$this->id_pedido]);
            }

            $this->BD->commit();

            return ["msj"=>"Asignaciones (".$cantidadOrdenesActualizadas."/".$cantidadOrdenes.") registradas correctamente.", 
                        "estado"=>[
                            "estado_actual" => $estadoNuevo["id_estado_orden"],
                            "estado_color" => $estadoNuevo["estado_color"],
                            "estado_color_rotulo" => $estadoNuevo["estado_color_rotulo"]
                        ],
                        "id"=>$this->id_pedido, 
                        "cantidad_asignados"=>$cantidadOrdenesActualizadas];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function asignarEstadosPorDepartamento($arregloDepartamentos, $estadoNuevo){
        try {

            $this->BD->beginTransaction();

            $resultadosProceso = [];
            $ordenesAsociadasEstado = [];
            $cantidadTotalActualizadas = 0;

            $sql = "SELECT descripcion, id_estado_orden, estado_color_rotulo as estado_color 
                    FROM estado_orden WHERE id_estado_orden = :0"; 
            $estado = $this->BD->consultarFila($sql, [$estadoNuevo]);

            foreach ($arregloDepartamentos as $i => $departamento) {
                $sql = "SELECT id_pedido_orden, estado_actual 
                            FROM nuevo_pedido_orden 
                            WHERE id_pedido = :0 AND departamento = :1 
                                    AND estado_mrcb AND estado_actual IN ('A','P')";
                $pedidosOrdenes = $this->BD->consultarFilas($sql, [$this->id_pedido, $departamento]);

                $cantidad = count($pedidosOrdenes);
                $cantidadOrdenesActualizadas = $cantidad;

                foreach ($pedidosOrdenes as $j => $pedidoOrden) {
                    if ($pedidoOrden["estado_actual"] == $estadoNuevo){
                        $cantidadOrdenesActualizadas--;
                        continue;
                    }
    
                    $this->BD->update("nuevo_pedido_orden", [
                                            "estado_actual"=>$estadoNuevo
                                        ], [
                                            "id_pedido_orden"=>$pedidoOrden["id_pedido_orden"]
                                        ]);
    
                    $this->BD->insert("nuevo_pedido_orden_estados", [
                        "id_pedido_orden"=>$pedidoOrden["id_pedido_orden"],
                        "id_estado_orden"=>$estadoNuevo, 
                        "id_usuario_registro"=> $this->id_usuario_responsable
                    ]);


                    if (!isset($ordenesAsociadasEstado[$pedidoOrden["estado_actual"]])){
                        $ordenesAsociadasEstado[$pedidoOrden["estado_actual"]] = 0;
                    }

                    $ordenesAsociadasEstado[$pedidoOrden["estado_actual"]]++;
                }

                $cantidadTotalActualizadas += $cantidadOrdenesActualizadas;

                array_push($resultadosProceso, [
                    "key"=>$departamento,
                    "cantidad"=>$cantidad,
                    "cantidad_actualizados"=>$cantidadOrdenesActualizadas
                ]);
            }

            foreach ($ordenesAsociadasEstado as $idEstadoOrden => $cantidadOrdenesAsociados) {
                $sql = "UPDATE nuevo_pedido_cantidad 
                            SET cantidad = cantidad - ($cantidadOrdenesAsociados) 
                            WHERE id_pedido IN (:0) AND id_estado_orden  = :1";
                $this->BD->ejecutar_raw($sql, [$this->id_pedido, $idEstadoOrden]);      
            }

            $sql = "UPDATE nuevo_pedido_cantidad 
                            SET cantidad = cantidad + ($cantidadTotalActualizadas) 
                            WHERE id_pedido IN (:0) AND id_estado_orden  = :1";
            $this->BD->ejecutar_raw($sql, [$this->id_pedido, $estadoNuevo]);

            $this->BD->commit();
            return ["registros"=> $resultadosProceso, "estado" => $estado];
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function asignarEstadosPorProvincia($arregloProvincias, $estadoNuevo){
        try {

            $this->BD->beginTransaction();

            $resultadosProceso = [];
            $ordenesAsociadasEstado = [];
            $cantidadTotalActualizadas = 0;
            $estado = $this->BD->consultarFila("SELECT descripcion, id_estado_orden, estado_color_rotulo as estado_color 
                                FROM estado_orden 
                                WHERE id_estado_orden = :0", [$estadoNuevo]);

            foreach ($arregloProvincias as $i => $provinciaDepartamento) {
                $temp  = explode("_", $provinciaDepartamento);
                $departamento = $temp[0];
                $provincia = $temp[1];

                $sql = "SELECT id_pedido_orden, estado_actual 
                                    FROM nuevo_pedido_orden 
                                    WHERE id_pedido = :0 AND departamento = :1  AND provincia = :2 AND estado_mrcb
                                            AND estado_actual IN ('A','P')";
                $pedidosOrdenes = $this->BD->consultarFilas($sql, [$this->id_pedido, $departamento, $provincia]);

                $cantidad = count($pedidosOrdenes);
                $cantidadOrdenesActualizadas = $cantidad;

                foreach ($pedidosOrdenes as $j => $pedidoOrden) {
                    if ($pedidoOrden["estado_actual"] == $estadoNuevo){
                        $cantidadOrdenesActualizadas--;
                        continue;
                    }
    
                    $this->BD->update("nuevo_pedido_orden", [
                                            "estado_actual"=>$estadoNuevo
                                        ], [
                                            "id_pedido_orden"=>$pedidoOrden["id_pedido_orden"]
                                        ]);
    
                    $this->BD->insert("nuevo_pedido_orden_estados", [
                        "id_pedido_orden"=>$pedidoOrden["id_pedido_orden"],
                        "id_estado_orden"=>$estadoNuevo, 
                        "id_usuario_registro"=> $this->id_usuario_responsable
                    ]);


                    if (!isset($ordenesAsociadasEstado[$pedidoOrden["estado_actual"]])){
                        $ordenesAsociadasEstado[$pedidoOrden["estado_actual"]] = 0;
                    }

                    $ordenesAsociadasEstado[$pedidoOrden["estado_actual"]]++;
                }

                $cantidadTotalActualizadas += $cantidadOrdenesActualizadas;

                array_push($resultadosProceso, [
                    "keyDep"=>$departamento,
                    "keyProv"=>$provincia,
                    "cantidad"=>$cantidad,
                    "cantidad_actualizados"=>$cantidadOrdenesActualizadas
                ]);
            }

            foreach ($ordenesAsociadasEstado as $idEstadoOrden => $cantidadOrdenesAsociados) {
                $sql = "UPDATE nuevo_pedido_cantidad 
                            SET cantidad = cantidad - ($cantidadOrdenesAsociados) 
                            WHERE id_pedido IN (:0) AND id_estado_orden  = :1";
                $this->BD->ejecutar_raw($sql, [$this->id_pedido, $idEstadoOrden]);      
            }

            $sql = "UPDATE nuevo_pedido_cantidad 
                            SET cantidad = cantidad + ($cantidadTotalActualizadas) 
                            WHERE id_pedido IN (:0) AND id_estado_orden  = :1";
            $this->BD->ejecutar_raw($sql, [$this->id_pedido, $estadoNuevo]);

            $this->BD->commit();
            return ["registros"=> $resultadosProceso, "estado" => $estado];
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function leerXId(){
        try{
            $sql = "SELECT
                    po.id_pedido_orden,
                    codigo_numero_orden,
                    codigo_guia,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion,
                    UPPER(referencia) as referencia,
                    distrito,
                    provincia,
                    departamento,
                    telefono_uno_destinatario as telefono,
                    celular_destinatario as celular,
                    eo.descripcion as estado,
                    eo.estado_color_rotulo,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as repartidor_asignado,
                    COALESCE(DATE_FORMAT(npoe.fecha_hora_registro, '%d/%m/%Y %r'),'') as fecha_hora_asignado,
                    (SELECT COUNT(id_pedido_orden_correccion) 
                        FROM nuevo_pedido_orden_correccion
                        WHERE id_pedido_orden = po.id_pedido_orden) as veces_corregido,
                    (numero_orden_asignado + 1) as numero_orden
                    FROM nuevo_pedido_orden po
                    INNER JOIN estado_orden eo ON po.estado_actual = eo.id_estado_orden
                    LEFT JOIN nuevo_pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN nuevo_pedido_orden_estados npoe ON npoe.id_pedido_orden = po.id_pedido_orden 
                                AND npoe.id_estado_orden = 'R'
                                AND npoe.estado_mrcb
                    LEFT JOIN usuario u ON u.id_usuario = npoe.id_usuario_registro
                    WHERE po.estado_mrcb AND po.id_pedido_orden = :0                  
                    ORDER BY po.numero_orden_asignado, po.id_pedido_orden";

            $data = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);

            if ($data == false){
                return null;
            }

            $sql  = "SELECT numero_visita, observaciones, 
                        numero_documento_receptor as numero_documento_receptor, 
                        nombres_receptor as nombres_receptor, 
                        DATE_FORMAT(fecha_hora_registro,'%d-%m-%Y') as fecha,
                        DATE_FORMAT(fecha_hora_registro,'%H:%i:%s') as hora,
                        (SELECT COALESCE(GROUP_CONCAT(mot.descripcion),'')
                            FROM nuevo_pedido_orden_visita_motivacion povm 
                            LEFT JOIN motivacion mot ON mot.id_motivacion = povm.id_motivacion
                            WHERE povm.id_pedido_orden_visita = pov.id_pedido_orden_visita) as motivaciones,
                        (SELECT COALESCE(GROUP_CONCAT(povi.url_img),'') FROM nuevo_pedido_orden_visita_imagen povi 
                            WHERE povi.id_pedido_orden_visita = pov.id_pedido_orden_visita) as urls
                        FROM nuevo_pedido_orden_visita pov
                        WHERE pov.id_pedido_orden = :0 AND pov.estado_mrcb AND pov.id_pedido_orden IS NOT NULL
                        ORDER BY pov.numero_visita DESC";

            $data["visitas"] = $this->BD->consultarFilas($sql, [$this->id_pedido_orden]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function buscarOrdenesInterno($id_cliente, $tipo, $cadenaBuscar){
        try{
            $columnaBusqueda = "";
            if ($tipo == "dni"){
                $columnaBusqueda = "npo.numero_documento_destinatario";
            }

            if ($tipo == "codigo_guia"){
                $columnaBusqueda = "npo.codigo_guia";
            }

            if ($columnaBusqueda == ""){
                throw new Exception("No se ha enviado los parámetros de búsqueda correcta.", 422);
            }
          
            $sql = "SELECT 
                    npo.id_pedido_orden, 
                    npo.codigo_numero_orden, 
                    DATE_FORMAT(np.fecha_ingreso,'%d-%m-%Y') as fecha_registro,
                    npo.codigo_guia, 
                    npo.numero_documento_destinatario, 
                    npo.destinatario,
                    npo.distrito, npo.provincia, npo.departamento, 
                    npo.estado_actual, 
                    eo.estado_color_rotulo,
                    eo.estado_color,
                    npo.numero_visitas,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as repartidor_asignado
                    FROM nuevo_pedido_orden npo
                    INNER JOIN nuevo_pedido np ON np.id_pedido = npo.id_pedido
                    INNER JOIN estado_orden eo ON eo.id_estado_orden = npo.estado_actual
                    LEFT JOIN usuario u ON u.id_usuario = npo.id_usuario_asociado
                    WHERE np.estado_mrcb AND np.id_cliente = :0 
                            AND npo.estado_mrcb 
                            AND $columnaBusqueda LIKE '%$cadenaBuscar%'
                    ORDER BY npo.numero_orden_asignado";

            $registros = $this->BD->consultarFilas($sql, [$id_cliente]);

            return $registros;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function cambiarMostrarExcelXId($mostrar_excel){
        try{
          
            $this->BD->update(
                "nuevo_pedido_orden",
                ["mostrar_excel"=>$mostrar_excel],
                ["id_pedido_orden"=>$this->id_pedido_orden]
            );

            return $this->id_pedido_orden;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    
}