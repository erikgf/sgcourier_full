<?php 

require_once 'Modelo.clase.php';

class SisPreliquidacion extends Modelo{
    public $id_preliquidacion;
    public $id_repartidor;
    public $fecha_registro;
    public $id_tipo_vehiculo;
    public $estado;
    public $registros_detalle;

    public $id_usuario_registro;
    public $observaciones;
    public $costo_global;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_preliquidacion", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}  

    public function listar($fecha_inicio, $fecha_fin){
        try{
            $params = [$fecha_inicio, $fecha_fin];
            $sqlAgenciaWhere = " ";

            if ($this->id_agencia == NULL){
                $sql = "SELECT COALESCE(id_agencia,1) as id_agencia FROM usuario WHERE id_usuario = :0";
                $id_agencia = $this->BD->consultarValor($sql, [$this->id_usuario_registro]);
                array_push($params, $id_agencia);
                $sqlAgenciaWhere = " AND sr.id_agencia = :2";
            } else {
                if ($this->id_agencia != "*"){
                    array_push($params, $this->id_agencia);
                    $sqlAgenciaWhere = " AND sr.id_agencia = :2";
                }
            }

            $sql = "SELECT
                    id_preliquidacion as id,
                    CONCAT(LPAD(sr.id_agencia,3,'0'),'-',LPAD(correlativo_agencia,6,'0')) as codigo,
                    sre.razon_social as repartidor,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    stv.descripcion as tipo_vehiculo,
                    a.descripcion as agencia,
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,
                    ((SELECT SUM(spd.subtotal) FROM sis_preliquidacion_detalle spd WHERE spd.id_preliquidacion = sr.id_preliquidacion) + costo_global)  as costo_entrega,
                    (CASE estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_preliquidacion sr
                    INNER JOIN agencia a ON sr.id_agencia = a.id_agencia
                    INNER JOIN sis_tipo_vehiculo stv ON stv.id_tipo_vehiculo = sr.id_tipo_vehiculo
                    INNER JOIN sis_repartidor sre ON sre.id_repartidor = sr.id_repartidor
                    INNER JOIN usuario u ON u.id_usuario = sr.id_usuario_responsable
                    WHERE sr.estado_mrcb AND (sr.fecha_registro BETWEEN :0 AND :1) ".$sqlAgenciaWhere. " 
                    ORDER BY fecha_hora_registro DESC";
            $data = $this->BD->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try{

            $sql = "SELECT
                    sp.id_preliquidacion,
                    sp.id_repartidor,
                    r.razon_social as repartidor_descripcion,
                    r.costo_entrega,
                    (SELECT SUM(spd.subtotal) FROM sis_preliquidacion_detalle spd WHERE spd.id_preliquidacion = sp.id_preliquidacion) as costo_entrega_total,
                    sp.costo_global,
                    sp.observaciones,
                    sp.id_agencia,
                    sp.fecha_registro,
                    sp.estado,
                    (CASE sp.estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado_descripcion,
                    sp.id_tipo_vehiculo
                    FROM sis_preliquidacion sp
                    INNER JOIN sis_repartidor r ON r.id_repartidor = sp.id_repartidor
                    WHERE sp.estado_mrcb AND sp.id_preliquidacion = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_preliquidacion]);

            $sql = "SELECT descripcion_cliente as cliente,
                    documento_guia, cantidad, 
                    tipo_paquete, peso, volumen, cliente_interno,
                    direccion as direccion_entrega,
                    zona as lugar_entrega, 
                    costo_unitario, 
                    subtotal, 
                    estado,
                    COALESCE(descripcion_motivacion,'') as descripcion_motivacion,
                    (CASE estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado_descripcion,
                    (CASE estado WHEN 'A' THEN '1' WHEN 'C' THEN '1' ELSE '0' END) as bloqueado
                    FROM sis_preliquidacion_detalle WHERE id_preliquidacion = :0 AND estado_mrcb";
            $registros_detalle = $this->BD->consultarFilas($sql, [$this->id_preliquidacion]);
            $data["registros_detalle"] = $registros_detalle;

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerRegistro(){
        try{

            $sql = "SELECT
                    id_preliquidacion as id,
                    CONCAT(LPAD(sr.id_agencia,3,'0'),'-',LPAD(correlativo_agencia,6,'0')) as codigo,
                    a.descripcion as agencia,
                    sre.razon_social as repartidor,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    stv.descripcion as tipo_vehiculo,
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,
                    (SELECT SUM(spd.subtotal) FROM sis_preliquidacion_detalle spd WHERE spd.id_preliquidacion = sr.id_preliquidacion) + sr.costo_global as costo_entrega,
                    sr.observaciones,
                    (CASE sr.estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_preliquidacion sr
                    INNER JOIN agencia a ON sr.id_agencia = a.id_agencia
                    INNER JOIN sis_tipo_vehiculo stv ON stv.id_tipo_vehiculo = sr.id_tipo_vehiculo
                    INNER JOIN sis_repartidor sre ON sre.id_repartidor = sr.id_repartidor
                    INNER JOIN usuario u ON u.id_usuario = sr.id_usuario_responsable
                    WHERE sr.estado_mrcb AND sr.id_preliquidacion = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_preliquidacion]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtener(){
        try{

            $sql = "SELECT
                    id_repartidor as id,
                    razon_social as descripcion,
                    costo_entrega
                    FROM sis_repartidor
                    WHERE estado_mrcb;
                    ORDER BY razon_social";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            $editando = $this->id_preliquidacion != NULL;

            $sql = "SELECT COALESCE(id_agencia,1)  FROM usuario WHERE id_usuario = :0";
            $this->id_agencia = $this->BD->consultarValor($sql, [$this->id_usuario_registro]);

            $campos_valores = [
                "id_repartidor"=>$this->id_repartidor,
                "id_agencia"=>$this->id_agencia,
                "fecha_registro"=>$this->fecha_registro,
                "observaciones"=>$this->observaciones,
                "costo_global"=>$this->costo_global,
                "id_tipo_vehiculo"=>$this->id_tipo_vehiculo,
                "id_usuario_responsable"=>$this->id_usuario_registro
            ];

            $this->BD->beginTransaction();

            if ($editando){
                $campos_valores_where = ["id_preliquidacion" => $this->id_preliquidacion];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where); 
                $this->BD->delete("sis_preliquidacion_detalle", $campos_valores_where);
            } else {
                $sql = "SELECT COALESCE(MAX(correlativo_agencia) + 1, 1) as id_agencia FROM sis_preliquidacion WHERE id_agencia = :0";
                $correlativo_agencia = $this->BD->consultarValor($sql, [$this->id_agencia]);

                $campos_valores["correlativo_agencia"] = $correlativo_agencia;
                $campos_valores["estado"] = "P"; //pendiente ,recien generado.
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_preliquidacion = $this->BD->getLastID();
            }

            foreach ($this->registros_detalle as $key => $detalle) {
                $campos_valores = [
                    "id_preliquidacion"=>$this->id_preliquidacion,
                    "descripcion_cliente"=>mb_strtoupper($detalle["descripcion_cliente"],'UTF-8'),
                    "documento_guia"=>mb_strtoupper($detalle["documento_guia"],'UTF-8'),
                    "cantidad"=>$detalle["cantidad"],
                    "direccion"=>mb_strtoupper($detalle["direccion_entrega"], 'UTF-8'),
                    "tipo_paquete"=>$detalle["tipo_paquete"],
                    "cliente_interno"=>(isset($detalle["cliente_interno"]) && $detalle["cliente_interno"] != "") ? $detalle["cliente_interno"] : 'PARTICULAR',
                    "peso"=>(isset($detalle["peso"]) && $detalle["peso"] != "") ? $detalle["peso"] : NULL,
                    "volumen"=>(isset($detalle["volumen"]) && $detalle["volumen"] != "") ? $detalle["volumen"] : NULL,
                    "zona"=>mb_strtoupper($detalle["lugar_entrega"],'UTF-8'),
                    "costo_unitario"=>$detalle["costo_unitario"],
                    "subtotal"=>$detalle["costo_unitario"] * $detalle["cantidad"],
                    "estado"=>$detalle["estado"],
                    "descripcion_motivacion"=>$detalle["descripcion_motivacion"]
                ];
                $this->BD->insert("sis_preliquidacion_detalle", $campos_valores);
            }


            if ($editando){
                $sql = "SELECT distinct estado, COUNT(estado) as estados_totales
                        FROM sis_preliquidacion_detalle
                        WHERE id_preliquidacion = :0
                        GROUP BY estado
                        HAVING estados_totales = :1";
                $estado_cambio = $this->BD->consultarFila($sql, [$this->id_preliquidacion, count($this->registros_detalle)]);

                if ($estado_cambio != false && $estado_cambio != NULL){
                    $campos_valores = ["estado"=>$estado_cambio["estado"]];
                    $campos_valores_where = ["id_preliquidacion" => $this->id_preliquidacion];
                    $this->BD->update($this->main_table, $campos_valores, $campos_valores_where); 
                }
            }

            $this->BD->commit();

            $registro = $this->obtenerRegistro();
            return ["msj"=>$editando ? "Editado correctamente" : "Registrado correctamente.", "registro"=>$registro];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminar(){ 
        try 
        {       
            $campos_valores = [ 
                "estado_mrcb"=>"0", 
                "fecha_hora_eliminado"=>date("Y-m-d H:i:s") 
            ];  
            $campos_valores_where = [   
                "id_preliquidacion"=>$this->id_preliquidacion   
            ];  
            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);   
            return ["msj"=>"Eliminado correctamente."]; 
        } catch (Exception $exc) {  
            throw new Exception($exc->getMessage());    
        }   
    }

    public function obtenerImprimir(){
        try{

            $sql = "SELECT
                    id_preliquidacion as id,
                    CONCAT(LPAD(sr.id_agencia,3,'0'),'-',LPAD(correlativo_agencia,6,'0')) as codigo,
                    a.descripcion as agencia,
                    sre.razon_social as repartidor,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    fecha_registro as fecha_registro_raw,
                    stv.descripcion as tipo_vehiculo,
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,
                    (SELECT SUM(spd.subtotal) FROM sis_preliquidacion_detalle spd WHERE spd.id_preliquidacion = sr.id_preliquidacion) + sr.costo_global as costo_entrega,
                    sr.costo_global,
                    sr.observaciones,
                    (CASE estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_preliquidacion sr
                    INNER JOIN agencia a ON sr.id_agencia = a.id_agencia
                    INNER JOIN sis_tipo_vehiculo stv ON stv.id_tipo_vehiculo = sr.id_tipo_vehiculo
                    INNER JOIN sis_repartidor sre ON sre.id_repartidor = sr.id_repartidor
                    INNER JOIN usuario u ON u.id_usuario = sr.id_usuario_responsable
                    WHERE sr.estado_mrcb AND sr.id_preliquidacion = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_preliquidacion]);

            $sql = "SELECT descripcion_cliente as cliente,
                    documento_guia, cantidad, 
                    tp.descripcion as tipo_paquete,
                    peso, direccion as direccion_entrega,
                    volumen, cliente_interno,
                    zona as lugar_entrega, 
                    costo_unitario, 
                    subtotal, 
                    estado,
                    (CASE estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado_descripcion,
                    COALESCE(descripcion_motivacion,'') as descripcion_motivacion
                    FROM sis_preliquidacion_detalle  spd
                    INNER JOIN sis_tipo_paquete tp ON tp.id_tipo_paquete = spd.tipo_paquete
                    WHERE id_preliquidacion = :0 AND spd.estado_mrcb";
            $registros_detalle = $this->BD->consultarFilas($sql, [$this->id_preliquidacion]);
            $data["registros_detalle"] = $registros_detalle;

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

     public function reportePreliquidacionesXLS($id_agencia = "*", $fecha_inicio, $fecha_fin){  
        try{    
            $params = [$fecha_inicio, $fecha_fin];  
            $sqlExtra = ""; 
            if ($id_agencia !== "*"){   
                $sqlExtra = " AND sr.id_agencia = :2";  
                array_push($params, $id_agencia);   
            }   
            
            $sql = "SELECT  
                    CONCAT(LPAD(sr.id_agencia,3,'0'),'-',LPAD(correlativo_agencia,6,'0')) as codigo,    
                    a.descripcion as agencia,   
                    sre.numero_documento as numero_documento_repartidor,    
                    sre.razon_social as repartidor, 
                    fecha_registro, 
                    stv.descripcion as tipo_vehiculo, 
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,   
                    (SELECT SUM(spd.subtotal) FROM sis_preliquidacion_detalle spd WHERE spd.id_preliquidacion = sr.id_preliquidacion) as costo_total_subtotales,
                    costo_global as costo_global_extra,
                    observaciones,
                    (CASE spd.estado     
                            WHEN 'P' THEN 'PENDIENTE'   
                            WHEN 'R' THEN 'RUTA'    
                            WHEN 'E' THEN 'ENTREGADO'   
                            WHEN 'C' THEN 'MOTIVADO'   
                            WHEN 'A' THEN 'PAGADO'  
                            ELSE 'ANULADO' END) as estado,  
                    spd.descripcion_cliente as cliente, 
                    spd.cliente_interno,
                    spd.documento_guia,     
                    cantidad,   
                    tp.descripcion as tipo_paquete,   
                    spd.peso, 
                    spd.volumen,
                    spd.cliente_interno,
                    spd.direccion as direccion_entrega,   
                    spd.descripcion_motivacion,
                    spd.zona as lugar_entrega, 
                    spd.costo_unitario,
                    spd.subtotal
                    FROM sis_preliquidacion sr  
                    INNER JOIN agencia a ON sr.id_agencia = a.id_agencia    
                    INNER JOIN sis_tipo_vehiculo stv ON stv.id_tipo_vehiculo = sr.id_tipo_vehiculo  
                    INNER JOIN sis_repartidor sre ON sre.id_repartidor = sr.id_repartidor   
                    INNER JOIN usuario u ON u.id_usuario = sr.id_usuario_responsable    
                    INNER JOIN sis_preliquidacion_detalle spd ON spd.id_preliquidacion = sr.id_preliquidacion   
                    INNER JOIN sis_tipo_paquete tp ON tp.id_tipo_paquete = spd.tipo_paquete
                    WHERE sr.estado_mrcb AND sr.fecha_registro BETWEEN :0 AND :1 ".$sqlExtra; 
            $data = $this->BD->consultarFilas($sql, $params);   
            
            return $data;   
        } catch (Exception $exc) {  
            throw new Exception($exc->getMessage());    
        }   
    }
    

    public function importarExcel($excelFile){
        try
        {   

            $partes_ruta = pathinfo($excelFile["nombre"]);
            $extension = $partes_ruta["extension"];

            require_once "../phpexcel/PHPExcel.php";
            $objReader = PHPExcel_IOFactory::createReader($extension == "xls" ? "Excel5" : 'Excel2007');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($excelFile["archivo"]);

            $cantidadPaginas = $objPHPExcel->getSheetCount();
            $arreglo_registros = [];
            for ($i=0; $i < $cantidadPaginas; $i++) { 
                $objWorksheet = $objPHPExcel->getSheet($i);

                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $data_de_filas_excel = [];
                $rows = [];
                for ($row = 1; $row <= $highestRow; ++$row) {
                  for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                    $celda = $objWorksheet->getCellByColumnAndRow($col, $row);
                    $valor = $celda->getValue();
                    $rows[$col] = $valor;
                  }
                  array_push($data_de_filas_excel, $rows);
                }

                $celda = null;
                $valor = null;

                require_once 'ProcesarExcel.clase.php';
                $objProcesar = new ProcesarExcel;
                $objProcesar->data_de_filas_excel = $data_de_filas_excel;
                $arreglo_registros = $objProcesar->preliquidaciones_importacion();

                if (!$arreglo_registros["formato_valido"]){
                      throw new Exception("Formato no reconocible seleccionado.");
                }
            }

            return $arreglo_registros["registros"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    } 

}

