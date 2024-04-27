<?php 

include_once "../phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

require_once 'Modelo.clase.php';

class Pedido extends Modelo{
    public $id_usuario; /*cliente*/
    public $id_pedido;

    public $dias_ruta;
    public $dias_gestionando;
    public $fecha_ingreso;
    public $id_cliente;
    public $id_usuario_registro;
    public $archivo;

    public $usuario;
    public $id_tipo_usuario;
    public $estado;
    public $tipo_pedido;

    private $PREFIJO_CODIGO_REMITO_LEONISA;

    public function __construct($BD = null){
        try {
            parent::__construct("nuevo_pedido", $BD); 
            $this->PREFIJO_CODIGO_REMITO_LEONISA = "0".GlobalVariables::$ID_LEONISA;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function listar($fi, $ff, $id_cliente_especifico = NULL, $tipo_pedido = NULL){
        try{

            if (!$this->id_usuario){
                return [];
            }

            $params = [$fi, $ff];
            $sql_cliente_especifico = "";
            if ($id_cliente_especifico != NULL){
                $nParam = count($params);
                array_push($params, $id_cliente_especifico);
                $sql_cliente_especifico = " AND p.id_cliente = :".$nParam;
            }
    
            $sql_tipo_pedido = "";
            if ($tipo_pedido != NULL){
                $nParam = count($params);
                array_push($params, $tipo_pedido);
                $sql_tipo_pedido = " AND p.tipo_pedido = :".$nParam;
            }

            $sql = "SELECT  LPAD(id_pedido,7,'0') as id_pedido_log, 
                            id_pedido as id, 
                            DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                            numero_documento,
                            COALESCE(c.razon_social, CONCAT(c.nombres,' ',c.apellidos)) as razon_social,
                            COALESCE(c.celular,'-') as celular,
                            cantidad,
                            (SELECT GROUP_CONCAT(id_estado_orden,'|',cantidad) 
                                FROM nuevo_pedido_cantidad WHERE id_pedido = p.id_pedido
                                GROUP BY id_pedido
                                ORDER BY id_pedido_cantidad) as cantidades
                            FROM nuevo_pedido p
                            LEFT JOIN usuario c ON c.id_usuario = p.id_cliente
                            WHERE p.estado_mrcb
                                AND (p.fecha_ingreso >= :0 
                                AND p.fecha_ingreso <= :1) ".$sql_cliente_especifico." ".$sql_tipo_pedido;
                            
            $data = $this->BD->consultarFilas($sql, $params);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerCabecera(){
        try {

            $sql = "SELECT  LPAD(id_pedido,7,'0') as id_pedido_log, 
                id_pedido as id, 
                DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                numero_documento,
                COALESCE(c.razon_social, CONCAT(c.nombres,' ',c.apellidos)) as razon_social,
                COALESCE(c.celular,'-') as celular,
                cantidad
                FROM nuevo_pedido p
                LEFT JOIN usuario c ON c.id_usuario = p.id_cliente
                WHERE p.estado_mrcb AND p.id_pedido = :0";

            $registro = $this->BD->consultarFila($sql, [$this->id_pedido]);

            if (!$registro){
                throw new Exception("No existe el registro consultado.", 1);
            }

            $sql = "SELECT  
                    npc.id_estado_orden,
                    npc.cantidad,
                    eo.descripcion,
                    eo.estado_color,
                    eo.estado_color_rotulo
                    FROM nuevo_pedido_cantidad npc
                    INNER JOIN estado_orden eo ON eo.id_estado_orden = npc.id_estado_orden
                    WHERE npc.id_pedido = :0
                    ORDER BY eo.numero_orden";

            $estados = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $registro["estados"] = $estados;
                
            return $registro; 
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerOrdenesNivelUno(){
        try {
            $sql = "SELECT id_pedido FROM nuevo_pedido WHERE id_pedido = :0 AND estado_mrcb";
            $registro = $this->BD->consultarFila($sql, [$this->id_pedido]);

            $sqlWhereAgenciaDep = " true ";
            $sqlWhereAgenciaProv = " true ";

            if ($this->usuario["id_tipo_usuario"] == GlobalVariables::$ID_TIPO_USUARIO_EJECUTIVO){
                $sqlWhereAgenciaDep = " npo.departamento IN (
                            SELECT  ud.name as departamento
                            FROM usuario_agencia ua
                            INNER JOIN agencia a ON a.id_agencia = ua.id_agencia
                            INNER JOIN ubigeo_peru_departments ud ON ud.id = a.ubigeo_departamento
                            WHERE id_usuario = ".$this->usuario["id_usuario"]." AND a.ubigeo_provincia IS NULL
                        ) ";

                $sqlWhereAgenciaProv = " npo.provincia IN (
                    SELECT  upro.name
                    FROM usuario_agencia ua
                    INNER JOIN agencia a ON a.id_agencia = ua.id_agencia
                    INNER JOIN ubigeo_peru_provinces upro ON upro.id = a.ubigeo_provincia
                    WHERE id_usuario = ".$this->usuario["id_usuario"]."
                ) ";
            }

            if (!$registro){
                return [];
            }

            $sql = "SELECT 
                    npo.departamento as descripcion,
                    COUNT(npo.id_pedido_orden) as cantidad 
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb  
                            AND estado_actual IN (SELECT id_estado_orden FROM estado_orden WHERE numero_orden IN (1,2)) 
                            AND id_pedido = :0 
                            AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento
                    ORDER BY npo.departamento";
            $departamentos =  $this->BD->consultarFilas($sql, [$this->id_pedido]);

            foreach ($departamentos as $key => $departamento) {
                $sql = "SELECT 
                    npo.provincia as descripcion,
                    COUNT(npo.id_pedido_orden) as cantidad, 
                    npo.estado_actual,
                    eo.descripcion as estado,
                    eo.estado_color_rotulo as estado_color
                    FROM nuevo_pedido_orden npo
                    INNER JOIN estado_orden eo ON eo.id_estado_orden = npo.estado_actual
                    WHERE npo.estado_mrcb  
                            AND estado_actual IN (SELECT id_estado_orden FROM estado_orden WHERE numero_orden IN (1,2)) 
                            AND id_pedido = :0
                            AND npo.departamento = :1
                            AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento, npo.provincia, npo.estado_actual
                    ORDER BY npo.departamento, npo.provincia";

                $departamentos[$key]["subregistros"] =  $this->BD->consultarFilas($sql, [$this->id_pedido, $departamento["descripcion"]]);
            }

            return $departamentos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerOrdenesNivelDos(){
        try {
            $sql = "SELECT id_pedido FROM nuevo_pedido WHERE id_pedido = :0 AND estado_mrcb";
            $registro = $this->BD->consultarFila($sql, [$this->id_pedido]);
                
            $sqlWhereAgenciaDep = " true ";
            $sqlWhereAgenciaProv = " true ";

            if ($this->usuario["id_tipo_usuario"] == GlobalVariables::$ID_TIPO_USUARIO_EJECUTIVO){
                $sqlWhereAgenciaDep = " npo.departamento IN (
                            SELECT  ud.name as departamento
                            FROM usuario_agencia ua
                            INNER JOIN agencia a ON a.id_agencia = ua.id_agencia
                            INNER JOIN ubigeo_peru_departments ud ON ud.id = a.ubigeo_departamento
                            WHERE id_usuario = ".$this->usuario["id_usuario"]." AND a.ubigeo_provincia IS NULL
                        ) ";

                $sqlWhereAgenciaProv = " npo.provincia IN (
                    SELECT  upro.name
                    FROM usuario_agencia ua
                    INNER JOIN agencia a ON a.id_agencia = ua.id_agencia
                    INNER JOIN ubigeo_peru_provinces upro ON upro.id = a.ubigeo_provincia
                    WHERE id_usuario = ".$this->usuario["id_usuario"]."
                ) ";
            }
            
            if (!$registro){
                return [];
            }

            $sqlPartialEstado = "(SELECT id_estado_orden FROM estado_orden WHERE numero_orden >= 2)";

            $sql = "SELECT 
                    npo.id_pedido_orden, 
                    npo.codigo_numero_orden, npo.codigo_guia, 
                    npo.numero_documento_destinatario, 
                    npo.destinatario,
                    npo.direccion_uno as direccion,
                    npo.distrito, npo.provincia, npo.departamento, 
                    npo.estado_actual, 
                    eo.estado_color_rotulo,
                    eo.estado_color,
                    npo.numero_visitas
                    FROM nuevo_pedido_orden npo
                    INNER JOIN estado_orden eo ON eo.id_estado_orden = npo.estado_actual
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0   AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    ORDER BY npo.departamento, npo.provincia, npo.distrito, npo.destinatario";

            $registros = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $sql = "SELECT 
                    npo.departamento
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0   AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento
                    ORDER BY npo.departamento";

            $departamentos = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $sql = "SELECT 
                    npo.departamento, npo.provincia
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0  AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento, npo.provincia
                    ORDER BY npo.departamento, npo.provincia";

            $provincias = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $sql = "SELECT 
                    npo.departamento, npo.provincia, npo.distrito
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0   AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento, npo.provincia, npo.distrito
                    ORDER BY npo.departamento, npo.provincia, npo.distrito";

            $distritos = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return ["registros"=>$registros, "departamentos"=>$departamentos, "provincias"=>$provincias, "distritos" => $distritos];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerOrdenesNivelTres(){
        try {
            $sql = "SELECT id_pedido FROM nuevo_pedido WHERE id_pedido = :0 AND estado_mrcb";
            $registro = $this->BD->consultarFila($sql, [$this->id_pedido]);

            $sqlWhereAgenciaDep = " true ";
            $sqlWhereAgenciaProv = " true ";

            if ($this->usuario["id_tipo_usuario"] == GlobalVariables::$ID_TIPO_USUARIO_EJECUTIVO){
                $sqlWhereAgenciaDep = " npo.departamento IN (
                            SELECT  ud.name as departamento
                            FROM usuario_agencia ua
                            INNER JOIN agencia a ON a.id_agencia = ua.id_agencia
                            INNER JOIN ubigeo_peru_departments ud ON ud.id = a.ubigeo_departamento
                            WHERE id_usuario = ".$this->usuario["id_usuario"]." AND a.ubigeo_provincia IS NULL
                        ) ";

                $sqlWhereAgenciaProv = " npo.provincia IN (
                    SELECT  upro.name
                    FROM usuario_agencia ua
                    INNER JOIN agencia a ON a.id_agencia = ua.id_agencia
                    INNER JOIN ubigeo_peru_provinces upro ON upro.id = a.ubigeo_provincia
                    WHERE id_usuario = ".$this->usuario["id_usuario"]."
                ) ";
            }

            if (!$registro){
                return [];
            }

            $sqlPartialEstado = "(SELECT id_estado_orden FROM estado_orden WHERE numero_orden >= 2)";

            $sql = "SELECT 
                    npo.id_pedido_orden, 
                    npo.codigo_numero_orden, npo.codigo_guia, 
                    npo.numero_documento_destinatario, 
                    npo.destinatario,
                    npo.distrito, npo.provincia, npo.departamento, 
                    npo.estado_actual, 
                    eo.estado_color_rotulo,
                    eo.estado_color,
                    npo.numero_visitas,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as repartidor_asignado
                    FROM nuevo_pedido_orden npo
                    INNER JOIN estado_orden eo ON eo.id_estado_orden = npo.estado_actual
                    LEFT JOIN usuario u ON u.id_usuario = npo.id_usuario_asociado
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0 AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    ORDER BY npo.numero_orden_asignado";

            $registros = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $sql = "SELECT 
                    npo.departamento
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0 AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento
                    ORDER BY npo.departamento";

            $departamentos = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $sql = "SELECT 
                    npo.departamento, npo.provincia
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0 AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento, npo.provincia
                    ORDER BY npo.departamento, npo.provincia";

            $provincias = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            $sql = "SELECT 
                    npo.departamento, npo.provincia, npo.distrito
                    FROM nuevo_pedido_orden npo
                    WHERE npo.estado_mrcb AND npo.estado_actual IN $sqlPartialEstado AND npo.id_pedido = :0 AND ($sqlWhereAgenciaDep OR $sqlWhereAgenciaProv)
                    GROUP BY npo.departamento, npo.provincia, npo.distrito
                    ORDER BY npo.departamento, npo.provincia, npo.distrito";

            $distritos = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return ["registros"=>$registros, "departamentos"=>$departamentos, "provincias"=>$provincias, "distritos" => $distritos];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar($necesito_excel_formato = true){
        try
        {   

            $this->BD->beginTransaction();
            /*validar que no se después de hoy*/

            $hashFile = $necesito_excel_formato ? hash_file("md5",$this->archivo["archivo"]) : NULL;

            if ($hashFile != null){
                $sql = "SELECT COUNT(id_pedido) FROM nuevo_pedido WHERE hash_file = :0 AND estado_mrcb";
                $existe = $this->BD->consultarValor($sql, [$hashFile]);

                if ($existe > 0){
                    throw new Exception("Ya existe este archivo de EXCEL registrado en el sistema.", 1);
                }
            }
            
            $campos_valores = [
                "fecha_ingreso"=>$this->fecha_ingreso,
                "id_cliente"=>$this->id_cliente,
                "id_usuario_registro"=>$this->id_usuario_registro,
                "tipo_pedido"=>$this->tipo_pedido,
                "hash_file"=> $hashFile
            ];
            
            $this->BD->insert("nuevo_pedido", $campos_valores);
            $this->id_pedido = $this->BD->getLastID();

            $cantidad_ordenes = 1;

            $sql = "SELECT id_estado_orden, numero_orden FROM estado_orden WHERE estado_mrcb ORDER BY numero_orden, descripcion";
            $estados = $this->BD->consultarFilas($sql);

            if (count($estados) <= 0){
                throw new Exception("No hay ESTADOS registrados en el sistema.");
            }
            
            if ($necesito_excel_formato){
                //require_once "../phpexcel/PHPExcel.php";
                
                $partes_ruta = pathinfo($this->archivo["nombre"]);
                $extension = $partes_ruta["extension"];
                $objReader = IOFactory::createReader($extension == "xls" ? "Xls" : 'Xlsx');
                //$objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load($this->archivo["archivo"]);
                
                if ($this->id_cliente == GlobalVariables::$ID_LEONISA){
                    $objWorksheet = $objPHPExcel->getSheet(1);
                } else {
                    $objWorksheet = $objPHPExcel->getActiveSheet();
                }

                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $data_de_filas_excel = [];
                for ($row = 1; $row <= $highestRow; ++$row) {
                    $rowObject = [];
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $celda = $objWorksheet->getCellByColumnAndRow($col, $row);
                        $valor = $celda->getValue();
                        array_push($rowObject, $valor);
                    }
                    array_push($data_de_filas_excel, $rowObject);
                }

                $celda = null;
                $valor = null;

                require_once 'ProcesarExcel.clase.php';
                
                $objProcesar = new ProcesarExcel;
                $objProcesar->data_de_filas_excel = $data_de_filas_excel;
                $objProcesar->id_pedido =  $this->id_pedido;

                switch($this->id_cliente){
                    case GlobalVariables::$ID_LEONISA:
                        $campos_valores_registro = 
                                $this->tipo_pedido == "1"
                                        ? $objProcesar->nuevoLeonisa()
                                        : $objProcesar->leonisa_catalogos();
                        break;
                    default:
                        $campos_valores_registro = ["valores"=>[], "formato_valido"=>false];
                        break;
                }

                if (!$campos_valores_registro["formato_valido"]){
                      throw new Exception("Formato no reconocible para el cliente seleccionado.");
                }

                $cantidad_ordenes = count($campos_valores_registro["valores"]);

                if ( $cantidad_ordenes <= 0){
                    throw new Exception("No hay registros de órdenes por registrar.");
                }

                $campos = $campos_valores_registro["campos"];

                foreach ($campos_valores_registro["valores"] as $key => $campos_valores) {
                    $valores = [];
                    foreach ($campos_valores as $i => $value) {
                        if ($campos[$i] != "")
                            $valores[$campos[$i]] = $value;
                    }
                    
                    $valores["estado_actual"] = $estados[0]["id_estado_orden"];
                    
                    $this->BD->insert("nuevo_pedido_orden",  $valores);

                    $idPedidoOrden = $this->BD->getLastID();

                    $this->BD->insert("nuevo_pedido_orden_estados" , [
                        "id_pedido_orden"=>$idPedidoOrden,
                        "id_estado_orden"=>$valores["estado_actual"],
                        "id_usuario_registro"=>$this->id_usuario_registro
                    ]);
                }
            }
            
            $objPHPExcel->disconnectWorksheets();
            //$objPHPExcel->garbageCollect();
            unset($objPHPExcel);

            $this->BD->update("nuevo_pedido",  ["cantidad"=>$cantidad_ordenes], ["id_pedido"=>$this->id_pedido]);

            foreach ($estados as $key => $estado) {
                $this->BD->insert("nuevo_pedido_cantidad", [
                    "id_pedido"=>$this->id_pedido,
                    "id_estado_orden"=>$estado["id_estado_orden"],
                    "cantidad"=>$estado["numero_orden"] == 1 ? $cantidad_ordenes : 0
                ]);

                $idPedidoOrden = $this->BD->getLastID();
            }

            $this->BD->commit();
            return ["msj"=>"Registrado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminar(){
        try
        {   
            $this->BD->beginTransaction();
             
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_pedido"=>$this->id_pedido
            ];

            $this->BD->update("nuevo_pedido", $campos_valores, $campos_valores_where);
            
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_pedido"=>$this->id_pedido
            ];

            $this->BD->update("nuevo_pedido_orden", $campos_valores, $campos_valores_where);
            
            $this->BD->commit();
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
     public function reporteNuevasOrdenesCompletadasLeonisa($fi, $ff){ /*Porfecha*/
        try{

            $sqlIDPedido  = " true ";
            $sqlEstado = " true "; 
            $params = [];
            $id_pedido = $this->id_pedido;       

            if (!($id_pedido == NULL || $id_pedido == "")){
                $sqlIDPedido = " p.id_pedido = :0";
                array_push($params, $id_pedido);
            } else {
                $sqlEstado = " p.fecha_ingreso >= :0 AND p.fecha_ingreso <= :1 ";
                array_push($params, $fi);
                array_push($params, $ff);
            }
            
            

            $sql = "SELECT
                    npo.zona as zona,
                    npo.codigo_numero_orden, 
                    npo.codigo_guia, 
                    npo.numero_documento_destinatario, 
                    npo.destinatario,
                    npo.direccion_uno as direccion,
                    npo.distrito, 
                    npo.provincia, 
                    npo.departamento, 
                    (CASE npo.estado_actual 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN (SELECT COALESCE(GROUP_CONCAT(m.descripcion),'MOTIVADO')
                                        FROM nuevo_pedido_orden_visita_motivacion povm 
                                        LEFT JOIN motivacion m ON povm.id_motivacion = m.id_motivacion
                                        WHERE pov.id_pedido_orden_visita = povm.id_pedido_orden_visita)
                        ELSE 'GESTIONANDO' END) as status,
                    UPPER(pov.observaciones) as observaciones,
                    telefono_uno_destinatario,
                    telefono_dos_destinatario,
                    '0' as numero_guia_mas,
                    'N' as entrega_porteria,
                    '0.00' as valor,
                    DATE(pov.fecha_hora_registro) as fecha_entrega,
                    TIME(pov.fecha_hora_registro) as hora_entrega,
                    pov.fecha_hora_registro as fecha_entrega,
                    referencia as barrio,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'-') as repartidor
                    FROM nuevo_pedido_orden npo
                    LEFT JOIN nuevo_pedido_orden_visita pov ON pov.id_pedido_orden = npo.id_pedido_orden AND pov.ultima_visita
                    LEFT JOIN nuevo_pedido p ON p.id_pedido = npo.id_pedido
                    LEFT JOIN usuario u ON npo.id_usuario_asociado = u.id_usuario
                    WHERE npo.estado_mrcb AND $sqlIDPedido AND $sqlEstado
                    ORDER BY npo.id_pedido_orden";
            $data = $this->BD->consultarFilas($sql, $params);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }


}