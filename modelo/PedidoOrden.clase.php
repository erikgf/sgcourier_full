<?php 

require_once 'Modelo.clase.php';

class PedidoOrden extends Modelo{
    public $id_usuario_responsable;
    public $id_pedido;
    public $id_pedido_orden;
    public $codigo_remito;
    public $id_tipo_usuario;
    public $tipo_visita;
    public $es_receptor_destinatario;
    public $numero_documento_receptor;
    public $nombres_receptor;
    public $observaciones;
    public $motivaciones;
    public $imagenes;
    public $ultima_visita;
    public $estado;

    private $DIAS_MAXIMOS_APP = 30;

    public function __construct($BD = null){
        try {
            parent::__construct("pedido_orden", $BD);   
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
    }

    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXAppFuxion($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga){
        try{

            if (!$this->id_usuario_responsable){
                return [];
            }

            $ff = date("Y-m-d");
            $fi = date('Y-m-d', strtotime('-'.$this->DIAS_MAXIMOS_APP.' days'));

            $data = [];
    
            $sqlEstadoWhere = " AND (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            if ($tipo_pedido_app == "1"){
                $sqlEstadoWhere = " AND NOT (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            }
            

            $sqlClienteWhere = " AND p.id_cliente IN (".GlobalVariables::$ID_FUXION_SAC.")";

            if ($primera_carga == 1){
                $data["max_items"] = $this->BD->consultarValor("SELECT COUNT(id_pedido_orden) 
                                FROM pedido_orden po
                                LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                                WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                                        AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) $sqlEstadoWhere $sqlClienteWhere",
                                     [$this->id_usuario_responsable, $fi, $ff]);
            }

            $sql = $this->_SQLLeerXOrdenIdApp(GlobalVariables::$ID_FUXION_SAC).
                    " WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                        AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) 
                        $sqlEstadoWhere
                        $sqlClienteWhere
                    ORDER BY po.fecha_hora_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;
                    

            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_usuario_responsable, $fi, $ff]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXAppLeonisa($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga){
        try{

            if (!$this->id_usuario_responsable){
                return [];
            }

            $ff = date("Y-m-d");
            $fi = date('Y-m-d', strtotime('-'.$this->DIAS_MAXIMOS_APP.' days'));

            $data = [];
    
            $sqlEstadoWhere = " AND (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            if ($tipo_pedido_app == "1"){
                $sqlEstadoWhere = " AND NOT (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            }
            

            $sqlClienteWhere = " AND p.id_cliente IN (".GlobalVariables::$ID_LEONISA.")";

            if ($primera_carga == 1){
                $data["max_items"] = $this->BD->consultarValor("SELECT COUNT(id_pedido_orden) 
                                FROM pedido_orden po
                                LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                                WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                                       AND p.tipo_pedido = 1 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) $sqlEstadoWhere $sqlClienteWhere",
                                     [$this->id_usuario_responsable, $fi, $ff]);
            }

            $sql = $this->_SQLLeerXOrdenIdApp(GlobalVariables::$ID_LEONISA).
                    " WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                        AND p.tipo_pedido = 1 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2) 
                        $sqlEstadoWhere
                        $sqlClienteWhere
                    ORDER BY po.fecha_hora_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;
                    

            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_usuario_responsable, $fi, $ff]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXAppMinedu($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga){
        try{

            if (!$this->id_usuario_responsable){
                return [];
            }


            $data = [];
    
            $sqlEstadoWhere = " AND (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            if ($tipo_pedido_app == "1"){
                $sqlEstadoWhere = " AND NOT (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            }
            

            $sqlClienteWhere = " AND p.id_cliente IN (".GlobalVariables::$ID_MINEDU.")";

            if ($primera_carga == 1){
                $data["max_items"] = $this->BD->consultarValor("SELECT COUNT(id_pedido_orden) 
                                FROM pedido_orden po
                                LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                                WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 $sqlEstadoWhere $sqlClienteWhere",
                                     [$this->id_usuario_responsable]);
            }

            $sql = $this->_SQLLeerXOrdenIdApp(GlobalVariables::$ID_MINEDU).
                    " WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                        $sqlEstadoWhere
                        $sqlClienteWhere
                    ORDER BY po.fecha_hora_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;

            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_usuario_responsable]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXAppPronabec($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga){
        try{

            if (!$this->id_usuario_responsable){
                return [];
            }


            $data = [];
    
            $sqlEstadoWhere = " AND (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            if ($tipo_pedido_app == "1"){
                $sqlEstadoWhere = " AND NOT (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            }
            

            $sqlClienteWhere = " AND p.id_cliente IN (".GlobalVariables::$ID_PRONABEC.")";

            if ($primera_carga == 1){
                $data["max_items"] = $this->BD->consultarValor("SELECT COUNT(id_pedido_orden) 
                                FROM pedido_orden po
                                LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                                WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 $sqlEstadoWhere $sqlClienteWhere",
                                     [$this->id_usuario_responsable]);
            }

            $sql = $this->_SQLLeerXOrdenIdApp(GlobalVariables::$ID_PRONABEC).
                    " WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                        $sqlEstadoWhere
                        $sqlClienteWhere
                    ORDER BY po.fecha_hora_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;

            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_usuario_responsable]);
            
            
             /*repartidores*/
            $sql = "SELECT
                    id_usuario as id,
                    CONCAT(nombres,' ',apellidos) as nombres_apellidos
                    FROM usuario u
                    WHERE u.estado_mrcb AND (u.id_tipo_usuario = 3  OR (u.id_tipo_usuario = 2 AND u.es_ejecutivo_repartidor))
                    ORDER BY 2";
            $data["usuarios_asignar"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            /*ciudades*/
            $sql = "SELECT
                    distinct(distrito) as ciudad
                    FROM pedido_orden po
                    WHERE po.estado_mrcb AND po.id_pedido = :0
                    ORDER BY 1";
            $data["ciudades"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXAppPronied($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga){
        try{

            if (!$this->id_usuario_responsable){
                return [];
            }


            $data = [];
    
            $sqlEstadoWhere = " AND (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            if ($tipo_pedido_app == "1"){
                $sqlEstadoWhere = " AND NOT (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            }
            

            $sqlClienteWhere = " AND p.id_cliente IN (".GlobalVariables::$ID_PRONIED.")";

            if ($primera_carga == 1){
                $data["max_items"] = $this->BD->consultarValor("SELECT COUNT(id_pedido_orden) 
                                FROM pedido_orden po
                                LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                                WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 $sqlEstadoWhere $sqlClienteWhere",
                                     [$this->id_usuario_responsable]);
            }

            $sql = $this->_SQLLeerXOrdenIdApp(GlobalVariables::$ID_PRONIED).
                    " WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                        $sqlEstadoWhere
                        $sqlClienteWhere
                    ORDER BY po.fecha_hora_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;

            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_usuario_responsable]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXAppAtu($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga){
        try{

            if (!$this->id_usuario_responsable){
                return [];
            }


            $data = [];
    
            $sqlEstadoWhere = " AND (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            if ($tipo_pedido_app == "1"){
                $sqlEstadoWhere = " AND NOT (po.fecha_hora_atendido IS NULL OR (DATE(po.fecha_hora_atendido) <= NOW()) AND po.estado_actual = 'G') ";
            }

            $sqlClienteWhere = " AND p.id_cliente IN (".GlobalVariables::$ID_ATU.")";

            if ($primera_carga == 1){
                $data["max_items"] = $this->BD->consultarValor("SELECT COUNT(id_pedido_orden) 
                                FROM pedido_orden po
                                LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                                WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 $sqlEstadoWhere $sqlClienteWhere",
                                     [$this->id_usuario_responsable]);
            }

            $sql = $this->_SQLLeerXOrdenIdApp(GlobalVariables::$ID_ATU).
                    " WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 
                        $sqlEstadoWhere
                        $sqlClienteWhere
                    ORDER BY po.fecha_hora_asignado
                    LIMIT ".($pagina_actual * $items_per_load)." , ".$items_per_load;

            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_usuario_responsable]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    /*$tipo_pedido_app: 0: Pendientes, 1: Completados */
    private function listarPedidosXApp($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga, $id_cliente){
        try{
            if (!$this->id_usuario_responsable){
                return [];
            }
            switch ($id_cliente) {
                case GlobalVariables::$ID_LEONISA:
                    return $this->listarPedidosXAppLeonisa($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga);
                case GlobalVariables::$ID_ATU:
                    return $this->listarPedidosXAppAtu($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga);
                case GlobalVariables::$ID_MINEDU:
                    return $this->listarPedidosXAppMinedu($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga);
                case GlobalVariables::$ID_PRONABEC:
                    return $this->listarPedidosXAppPronabec($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga);
                case GlobalVariables::$ID_PRONIED:
                    return $this->listarPedidosXAppPronied($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga);
                default:
                    return $this->listarPedidosXAppFuxion($tipo_pedido_app, $pagina_actual, $items_per_load, $primera_carga);
            }
            return [];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarPedidosPendientesXApp($pagina_actual, $items_per_load, $primera_carga, $id_cliente){
        return $this->listarPedidosXApp("0", $pagina_actual, $items_per_load, $primera_carga, $id_cliente);
    }

    public function listarPedidosCompletadosXApp($pagina_actual, $items_per_load, $primera_carga, $id_cliente){
        return $this->listarPedidosXApp("1", $pagina_actual, $items_per_load, $primera_carga, $id_cliente);
    }

    public function leerXOrdenIdApp(){
        try{

            if (!$this->id_usuario_responsable){
                return null;
            }

            $sqlClienteWhere = " AND u.id_tipo_usuario = 4 ";
            $sql = "SELECT
                    p.id_cliente,
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    COALESCE(numero_documento_destinatario,'') as numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    COALESCE(UPPER(direccion_dos),'') as direccion_dos,
                    COALESCE(UPPER(referencia),'') as referencia,
                    region,
                    numero_paquetes,
                    numero_visitas,
                    fecha_hora_atendido,
                    estado_actual,
                    DATE_FORMAT(po.fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    COALESCE(CONCAT(minedu_tipo_documento,' - ',minedu_prioridad),'') as minedu_tipo_documento,
                    COALESCE(minedu_oficina,'') as minedu_oficina,
                    COALESCE(pronabec_sigedo,'') as pronabec_sigedo,
                    COALESCE(pronabec_oficina,'') as pronabec_oficina,
                    COALESCE(pronied_unidad_organica,'') as pronied_unidad_organica,
                    COALESCE(forma_envio,'') as forma_envio,
                    COALESCE(tipo_envio,'') as tipo_envio,
                    COALESCE(ticket_factura,'') as ticket_factura
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente
                    WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 AND po.id_pedido_orden = :1 $sqlClienteWhere                  
                    ORDER BY po.id_pedido_orden";

            $data = $this->BD->consultarFila($sql, [$this->id_usuario_responsable, $this->id_pedido_orden]);

            if ($data == false){
                $data = null;
            }

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarVisita(){
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
            
            $sql = "SELECT COUNT(id_pedido_orden_visita) FROM pedido_orden_visita 
                    WHERE estado_mrcb AND id_usuario_responsable = :0 
                    AND estado_actual = :1 AND id_pedido_orden = :2 AND DATE(fecha_hora_registro) = current_date";
                    
            $existeRegistro = $this->BD->consultarValor($sql, [$this->id_usuario_responsable, $this->tipo_visita, $this->id_pedido_orden]);
    
            if ($existeRegistro){
                 throw new Exception("Se está intentando enviar dos veces un registro ya  enviado. Paciencia por favor.");
            }

                        $sql = "SELECT COALESCE(MAX(numero_visita) + 1, 1) FROM pedido_orden_visita WHERE id_pedido_orden = :0 AND estado_mrcb";
            $numero_visita = $this->BD->consultarValor($sql, [$this->id_pedido_orden]);
            
            $this->BD->beginTransaction();

            $this->BD->update("pedido_orden_visita", ["ultima_visita"=>"0"], ["id_pedido_orden"=>$this->id_pedido_orden]);

            if ($this->tipo_visita == "E" && $this->es_receptor_destinatario){
                $sql = "SELECT numero_documento_destinatario, UPPER(destinatario) as destinatario FROM pedido_orden WHERE id_pedido_orden = :0";
                $pedido_orden = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);
                $this->numero_documento_receptor = $pedido_orden["numero_documento_destinatario"];
                $this->nombres_receptor = $pedido_orden["destinatario"];
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
            $this->BD->insert("pedido_orden_visita", $campos_valores);

            $id_pedido_orden_visita = $this->BD->getLastID();

            if (count($this->motivaciones) > 0){
                $campos = ["id_pedido_orden_visita", "id_motivacion"];
                $valores = [];
                foreach ($this->motivaciones as $key => $value) {
                    array_push($valores, [$id_pedido_orden_visita, $value]);
                }

                $this->BD->insertMultiple("pedido_orden_visita_motivacion", $campos, $valores);
            }

            if (count($this->imagenes) > 0){
                $campos = ["id_pedido_orden_visita", "numero_imagen", "url_img", "nombre_imagen_original", "tamano", "tipo_imagen"];
                $valores = [];
                $numero_imagen = 1;
                foreach ($this->imagenes as $key => $value) {
                    $url_img = $id_pedido_orden_visita."_".$numero_imagen."_".md5($value["nombre"]);
                    $nombre_imagen_original = $value["nombre"];
                    $tamano = $value["tamano"];
                    $tipo_imagen = $value["tipo"];
                    array_push($valores, [$id_pedido_orden_visita, $numero_imagen, $url_img, $nombre_imagen_original, $tamano, $tipo_imagen]);
                    if (!move_uploaded_file($value["archivo"], "../img/imagenes_visitas/$url_img")) {
                        $this->BD->rollBack();
                        throw new Exception("Error al subir la imagen ".$numero_imagen.".");
                    }
                    $numero_imagen++;
                }
                $this->BD->insertMultiple("pedido_orden_visita_imagen", $campos, $valores);
            }

            $this->BD->update("pedido_orden", 
                                [   "id_usuario_atendido"=>$this->id_usuario_responsable,
                                    "fecha_hora_atendido"=>date("Y-m-d H:i:s"),
                                    "estado_actual"=>$this->tipo_visita,
                                    "numero_visitas"=>$numero_visita], 
                                ["id_pedido_orden"=>$this->id_pedido_orden]);

            if ($this->tipo_visita != "G"){
                if ($this->tipo_visita == "E"){
                    $str = "cantidad_entregadas";
                } else {
                    $str = "cantidad_motivadas";
                }
                $sql = "UPDATE pedido SET cantidad_gestionando = cantidad_gestionando - 1, $str = $str + 1 
                        WHERE id_pedido IN (SELECT id_pedido FROM pedido_orden WHERE id_pedido_orden =:0)";

                $this->BD->ejecutar_raw($sql, [$this->id_pedido_orden]);
            }
    
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

            $sqlClienteWhere = " AND u.id_tipo_usuario = 4";

            $sql = "SELECT
                    p.id_cliente,
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    COALESCE(numero_documento_destinatario,'') as numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    COALESCE(UPPER(direccion_dos),'') as direccion_dos,
                    COALESCE(UPPER(referencia),'') as referencia,
                    region,
                    numero_paquetes,
                    numero_visitas,
                    fecha_hora_atendido,
                    estado_actual,
                    DATE_FORMAT(po.fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    COALESCE(CONCAT(minedu_tipo_documento,' - ',minedu_prioridad),'') as minedu_tipo_documento,
                    COALESCE(minedu_oficina,'') as minedu_oficina
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente
                    WHERE po.estado_mrcb AND po.id_usuario_asignado = :0 AND UPPER(po.codigo_remito) = UPPER(:1)  $sqlClienteWhere             
                    ORDER BY po.id_pedido_orden";

            $data = $this->BD->consultarFila($sql, [$this->id_usuario_responsable, $this->codigo_remito]);

            if ($data == false){
                $data = "";
            }

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leerXOrdenId(){
        try{

            $sql = "SELECT
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    UPPER(referencia) as referencia,
                    distrito,
                    region,
                    celular_contacto as celular,
                    (CASE estado_actual 
                        WHEN 'G' THEN 'GESTIONANDO' 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'EN RUTA' END) as estado,
                    (CASE estado_actual 
                        WHEN 'G' THEN 'info' 
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                    numero_paquetes,
                    (SELECT COUNT(id_pedido_orden_correccion) 
                        FROM pedido_orden_correccion
                        WHERE id_pedido_orden = po.id_pedido_orden) as veces_corregido
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente
                    WHERE po.estado_mrcb AND po.id_pedido_orden = :0                  
                    ORDER BY po.id_pedido_orden";

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
                            FROM pedido_orden_visita_motivacion povm 
                            LEFT JOIN motivacion mot ON mot.id_motivacion = povm.id_motivacion
                            WHERE povm.id_pedido_orden_visita = pov.id_pedido_orden_visita) as motivaciones,
                        (SELECT COALESCE(GROUP_CONCAT(povi.url_img),'') FROM pedido_orden_visita_imagen povi 
                            WHERE povi.id_pedido_orden_visita = pov.id_pedido_orden_visita) as urls
                        FROM pedido_orden_visita pov
                        WHERE pov.id_pedido_orden = :0 AND pov.estado_mrcb AND pov.id_pedido_orden IS NOT NULL
                        ORDER BY pov.numero_visita DESC";

            $data["visitas"] = $this->BD->consultarFilas($sql, [$this->id_pedido_orden]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function asignar($arregloOrdenes){
        try{
            /*armar órdenes (1,23,3)*/
            $cantidadOrdenes = count($arregloOrdenes);
            $this->BD->beginTransaction();

            if ($this->id_usuario_responsable == NULL){
                $estado_nuevo = "N";
            } else {
                $estado_nuevo = "G";
            }

            $hoy = date("Y-m-d H:i:s");

            $cantidad_noasignadas = 0;
            $cantidad_gestionando = 0;

            foreach ($arregloOrdenes as $key => $id_pedido_orden) {
                /*1. verificar si no tiene visitas. O si estado actual != N o G*/
                $sql = "SELECT po.estado_actual, 
                            (SELECT COUNT(id_pedido_orden_visita) 
                                FROM pedido_orden_visita pov 
                                WHERE pov.id_pedido_orden = po.id_pedido_orden AND pov.estado_mrcb)  as numero_visitas
                        FROM pedido_orden po 
                        WHERE po.id_pedido_orden = :0 AND po.estado_mrcb";
                $pedido_orden = $this->BD->consultarFila($sql, [$id_pedido_orden]);
                if ($pedido_orden["estado_actual"] != "N" && $pedido_orden["estado_actual"] != "G"){
                    continue;
                }
                
                if ($pedido_orden["numero_visitas"] > 0){
                    continue;
                }

                if ($pedido_orden["estado_actual"] == "G" && $estado_nuevo == "N") {/*antes gestionado y ahora N*/
                    /*nada*/
                    $cantidad_gestionando--;
                    $cantidad_noasignadas++;
                }

                if ($pedido_orden["estado_actual"] == "N" && $estado_nuevo == "G") {/*antes nada y ahora G*/
                    $cantidad_gestionando++;
                    $cantidad_noasignadas--;
                }   

                $this->BD->update("pedido_orden", 
                    [   "estado_actual"=>$estado_nuevo, 
                        "id_usuario_asignado"=>($estado_nuevo == "N" ? NULL : $this->id_usuario_responsable),
                        "fecha_hora_asignado"=>($estado_nuevo == "N" ? NULL : $hoy)
                        ], 
                    ["id_pedido_orden"=>$id_pedido_orden]);   

            }

            $sql = "UPDATE pedido 
                        SET cantidad_gestionando = cantidad_gestionando + $cantidad_gestionando,
                            cantidad_noasignadas = cantidad_noasignadas + $cantidad_noasignadas
                    WHERE id_pedido IN (:0)";

            $this->BD->ejecutar_raw($sql, [$this->id_pedido]);
    
            $this->BD->commit();
            return ["msj"=>"Asignaciones registradas correctamente.", "id"=>$this->id_pedido];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function corregirEstado($descripcion, $estado_nuevo){/*E,M a Gestionando de nuevo*/
        try{
            $this->BD->beginTransaction();

            /*
                1.- registrar correccion (Sirve para reversi�n)
                    id_usuario
                    id_pedido_orden

                2.- actualizar el pedido orden
                    estado_actual => estado_nuevo
                    recalcular cantidades
            */

            $id_usuario_registro = $_SESSION["sesion"]["id_usuario"];

            $sql  ="SELECT po.id_pedido, po.id_pedido_orden, pov.id_pedido_orden_visita, 
                            pov.estado_actual as estado_anterior
                    FROM pedido_orden po
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.id_pedido_orden = :0";

            $pedido_orden = $this->BD->consultarFila($sql, [$this->id_pedido_orden]);

            if ($pedido_orden == false){
                throw new Exception("No se ha encontrado órden que corregir.");
            }

            if ($pedido_orden["estado_anterior"] == "G"){
                throw new Exception("No se puede corregir una órden que ya se está GESTIONANDO.");
            }

            if ($pedido_orden["estado_anterior"] == "N"){
                throw new Exception("No se puede corregir una órden que ya se está NO ASIGNADO.");
            }

            $campos_valores = [
                "id_pedido_orden"=>$this->id_pedido_orden,
                "id_pedido_orden_visita"=>$pedido_orden["id_pedido_orden_visita"],
                "correccion_descripcion"=>$descripcion,
                "estado_anterior"=>$pedido_orden["estado_anterior"],
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

            $this->BD->update("pedido_orden", $campos_valores, $campos_valores_where);

            $campos_valores = [
                "estado_actual"=>$estado_nuevo,
            ];

            $campos_valores_where = [
                "id_pedido_orden_visita"=>$pedido_orden["id_pedido_orden_visita"]
            ];

            $this->BD->update("pedido_orden_visita", $campos_valores, $campos_valores_where);

            if ($pedido_orden["estado_anterior"] == "E"){
                $sqlExtraCantidad = " cantidad_entregadas = cantidad_entregadas - 1";
            } else {
                $sqlExtraCantidad = " cantidad_motivadas = cantidad_motivadas - 1";
            }

            $sql = "UPDATE pedido 
                        SET cantidad_gestionando = cantidad_gestionando + 1,
                            $sqlExtraCantidad
                    WHERE id_pedido IN (:0)";

            $this->BD->ejecutar_raw($sql, [$this->id_pedido]);
            $this->BD->commit();
            
            return ["msj"=>"Orden corregida.", "id"=>$this->id_pedido_orden];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function _SQLLeerXOrdenIdApp($id_cliente){
        switch($id_cliente){
            case GlobalVariables::$ID_MINEDU:
                $sql = "SELECT
                    p.id_cliente,
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    CONCAT(minedu_tipo_documento,' - ',minedu_prioridad) as minedu_tipo_documento,
                    minedu_oficina,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    CONCAT(region,' ',provincia,' ',distrito) as region,
                    numero_paquetes,
                    numero_visitas,
                    fecha_hora_atendido,
                    estado_actual,
                    DATE_FORMAT(po.fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    '' as numero_documento_destinatario,
                    '' as direccion_dos,
                    '' as referencia,
                    '' as pronabec_sigedo,
                    '' as pronabec_oficina,
                    '' as pronied_unidad_organica,
                    '' as forma_envio,
                    '' as tipo_envio
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente";
                break;
            case GlobalVariables::$ID_PRONABEC:
                $sql = "SELECT
                    p.id_cliente,
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    CONCAT(region,' ',provincia,' ',distrito) as region,
                    numero_paquetes,
                    numero_visitas,
                    fecha_hora_atendido,
                    estado_actual,
                    DATE_FORMAT(po.fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    '' as minedu_tipo_documento,
                    '' as minedu_oficina,
                    '' as numero_documento_destinatario,
                    '' as direccion_dos,
                    '' as referencia,
                    '' as pronied_unidad_organica,
                    '' as forma_envio,
                    '' as tipo_envio,
                    pronabec_sigedo,
                    pronabec_oficina
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente";
                break;
            case GlobalVariables::$ID_PRONIED:
                $sql = "SELECT
                    p.id_cliente,
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    CONCAT(region,' ',provincia,' ',distrito) as region,
                    numero_paquetes,
                    numero_visitas,
                    fecha_hora_atendido,
                    estado_actual,
                    DATE_FORMAT(po.fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    '' as minedu_tipo_documento,
                    '' as minedu_oficina,
                    '' as numero_documento_destinatario,
                    '' as direccion_dos,
                    '' as referencia,
                    '' as pronabec_sigedo,
                    '' as pronabec_oficina,
                    pronied_unidad_organica,
                    forma_envio,
                    tipo_envio
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente";
                break;
            default:
                $sql = "SELECT
                    p.id_cliente,
                    id_pedido_orden,
                    codigo_remito,
                    COALESCE(u.razon_social, CONCAT(u.nombres,' ',u.apellidos)) as cliente,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    COALESCE(UPPER(direccion_dos),'') as direccion_dos,
                    UPPER(referencia) as referencia,
                    region,
                    numero_paquetes,
                    numero_visitas,
                    fecha_hora_atendido,
                    estado_actual,
                    DATE_FORMAT(po.fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    '' as minedu_tipo_documento,
                    '' as minedu_oficina,
                    '' as pronabec_sigedo,
                    '' as pronabec_oficina,
                    '' as pronied_unidad_organica,
                    '' as forma_envio,
                    '' as tipo_envio
                    FROM pedido_orden po
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario u ON u.id_usuario = p.id_cliente";
                break;
        }

        return $sql;
    }
    
    public function buscarPorCodigoTracking($texto){
        /*consulta realizada mediante el app, */
        try{

            $sql = "SELECT 
                    po.id_pedido_orden as id,
                    po.codigo_remito,
                    COALESCE(CONCAT(cl.nombres,' ',cl.apellidos), cl.razon_social) as cliente,
                    UPPER(po.destinatario) as destinatario,
                    UPPER(po.direccion_uno) as direccion,
                    CONCAT(po.distrito,', ',po.provincia,', ',po.region) as ubigeo,
                    po.numero_paquetes,
                    DATE_FORMAT(p.fecha_ingreso, '%d-%m-%Y') as fecha_ingreso,
                    DATE_FORMAT(fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    DATE_FORMAT(fecha_hora_asignado, '%H:%i-%s') as  hora_asignado,
                    DATE_FORMAT(fecha_hora_atendido, '%d-%m-%Y') as fecha_atendido,
                    DATE_FORMAT(fecha_hora_atendido, '%H:%i-%s') as  hora_atendido,
                    po.estado_actual as estado,
                    COALESCE(pov.nombres_receptor,po.destinatario) as receptor,
                    pov.observaciones
                    FROM pedido_orden po 
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario cl ON p.id_cliente = cl.id_usuario
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita = 1
                    WHERE po.codigo_remito = :0";

            $pedido = $this->BD->consultarFila($sql, [$texto]);

            if ($pedido == false){
                return null;
            }

            $NOMBRE_ESTADOS = ["EN RUTA", "GESTIONANDO", "ENTREGADO", "MOTIVADO"];
        

            switch($pedido["estado"]){
                case "N":
                    $pedido["estado_actual"] = $NOMBRE_ESTADOS[0];
                    $pedido["estado_color"] = "0";
                break;
                case "G":
                    $pedido["estado_actual"] =  $NOMBRE_ESTADOS[1];
                    $pedido["estado_color"] = "1";
                break;

                case "E":
                case "M":
                    $pedido["estado_actual"] = $pedido["estado"] == "E" ? $NOMBRE_ESTADOS[2] : $NOMBRE_ESTADOS[3];
                    $pedido["estado_color"] = $pedido["estado"] == "E" ? "2" : "3";
                break;
            }

            return $pedido;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function buscarPorCodigoTrackingClientesGrandesWeb($texto){
        /*consulta realizada mediante el app, */
        try{

            $sql = "SELECT 
                    po.id_pedido_orden as id,
                    po.codigo_remito,
                    COALESCE(CONCAT(cl.nombres,' ',cl.apellidos), cl.razon_social) as cliente,
                    UPPER(po.destinatario) as destinatario,
                    UPPER(po.referencia) as direccion,
                    CONCAT(po.distrito,', ',po.region) as ubigeo,
                    po.numero_paquetes,
                    DATE_FORMAT(p.fecha_ingreso, '%d-%m-%Y') as fecha_ingreso,
                    DATE_FORMAT(fecha_hora_asignado, '%d-%m-%Y') as fecha_asignado,
                    DATE_FORMAT(fecha_hora_asignado, '%H:%i-%s') as  hora_asignado,
                    DATE_FORMAT(fecha_hora_atendido, '%d-%m-%Y') as fecha_atendido,
                    DATE_FORMAT(fecha_hora_atendido, '%H:%i-%s') as  hora_atendido,
                    po.estado_actual as estado,
                    COALESCE(pov.nombres_receptor,po.destinatario) as receptor,
                    pov.observaciones
                    FROM pedido_orden po 
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    LEFT JOIN usuario cl ON p.id_cliente = cl.id_usuario
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita = 1
                    WHERE po.codigo_remito = :0";

            $pedido = $this->BD->consultarFila($sql, [$texto]);

            if ($pedido == false){
                return null;
            }

            $NOMBRE_ESTADOS = ["EN RUTA", "GESTIONANDO", "ENTREGADO", "MOTIVADO"];
        
            $estados = [
                    ["nombre_estado"=>"EN RUTA", "estado"=>"0", "color"=>"dark"],
                    ["nombre_estado"=>"GESTIONANDO", "estado"=>"0", "color"=>"info"],
                    ["nombre_estado"=>"ENTREGADO", "estado"=>"0", "color"=>"success"]
                ];
                
            switch($pedido["estado"]){
                case "N":
                    $estados[0]["estado"] = "1";
                break;
                case "G":
                    $estados[0]["estado"] = "1";
                    $estados[1]["estado"] = "1";
                break;

                case "E":
                case "M":
                    $estados[0]["estado"] = "1";
                    $estados[1]["estado"] = "1";
                    
                    if ($pedido["estado"] != "E"){
                        $estados[2]["nombre_estado"] = "MOTIVADO";
                        $estados[2]["nombre_estado"] = "danger";
                    }
                    $estados[2]["estado"] = "1";
                break;
            }
            
            if ($pedido){
                $pedido["estados"] = $estados;
            }
            
            return $pedido;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function asignarMasivoPorCodigoRemito($arregloOrdenesRemito){
        try{
            /*armar órdenes (1,23,3)*/
            $cantidadOrdenes = count($arregloOrdenesRemito);
            
            $this->BD->beginTransaction();

            if ($this->id_usuario_responsable == NULL){
                $estado_nuevo = "N";
            } else {
                $estado_nuevo = "G";
            }

            $hoy = date("Y-m-d H:i:s");

            $cantidad_noasignadas = 0;
            $cantidad_gestionando = 0;

            foreach ($arregloOrdenesRemito as $key => $codigo_remito) {
                /*1. verificar si no tiene visitas. O si estado actual != N o G*/
                $sql = "SELECT po.id_pedido_orden, po.estado_actual, 
                            (SELECT COUNT(id_pedido_orden_visita) 
                                FROM pedido_orden_visita pov 
                                WHERE pov.id_pedido_orden = po.id_pedido_orden AND pov.estado_mrcb)  as numero_visitas
                        FROM pedido_orden po 
                        WHERE TRIM(po.codigo_remito) = :0 AND po.estado_mrcb AND po.id_pedido = :1";
                $pedido_orden = $this->BD->consultarFila($sql, [trim($codigo_remito), $this->id_pedido]);
                if ($pedido_orden["estado_actual"] != "N" && $pedido_orden["estado_actual"] != "G"){
                    continue;
                }
                
                if ($pedido_orden["numero_visitas"] > 0){
                    continue;
                }

                if ($pedido_orden["estado_actual"] == "G" && $estado_nuevo == "N") {/*antes gestionado y ahora N*/
                    /*nada*/
                    $cantidad_gestionando--;
                    $cantidad_noasignadas++;
                }

                if ($pedido_orden["estado_actual"] == "N" && $estado_nuevo == "G") {/*antes nada y ahora G*/
                    $cantidad_gestionando++;
                    $cantidad_noasignadas--;
                }   

                $this->BD->update("pedido_orden", 
                    [   "estado_actual"=>$estado_nuevo, 
                        "id_usuario_asignado"=>($estado_nuevo == "N" ? NULL : $this->id_usuario_responsable),
                        "fecha_hora_asignado"=>($estado_nuevo == "N" ? NULL : $hoy)
                        ], 
                    ["id_pedido_orden"=>$pedido_orden["id_pedido_orden"]]);   
            }

            $sql = "UPDATE pedido 
                        SET cantidad_gestionando = cantidad_gestionando + $cantidad_gestionando,
                            cantidad_noasignadas = cantidad_noasignadas + $cantidad_noasignadas
                    WHERE id_pedido IN (:0)";

            $this->BD->ejecutar_raw($sql, [$this->id_pedido]);
    
            $this->BD->commit();
            return ["msj"=>"Asignaciones registradas correctamente.", "id"=>$this->id_pedido, "cantidad_asignados"=>$cantidad_gestionando];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}