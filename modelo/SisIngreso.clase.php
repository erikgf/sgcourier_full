<?php 

require_once 'Modelo.clase.php';

class SisIngreso extends Modelo{
    public $id_ingreso;
    public $id_cliente;
    public $id_agencia_transporte;
    public $fecha_registro;
    public $id_origen;
    public $id_destino;
    public $costo;
    public $cobrar;
    public $fue_pagado;

    public $registros_detalle;
    public $estado;

    public $id_usuario_registro;

    public function __construct($BD = null){
        try {
            parent::__construct("sis_ingreso", $BD);
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
                $sqlAgenciaWhere = " AND si.id_origen = :2";
            } else {
                if ($this->id_agencia != "*"){
                    array_push($params, $this->id_agencia);
                    $sqlAgenciaWhere = " AND si.id_origen = :2";
                }
            }

            $sql = "SELECT
                    id_ingreso as id,
                    ao.descripcion as origen,
                    ad.descripcion as destino,
                    scl.nombres as cliente,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    IF(((SELECT SUM(monto_pagado) FROM sis_ingreso_pagos WHERE id_ingreso = si.id_ingreso) = si.cobrar),'SI','NO') as fue_pagado,
                    si.cobrar,
                    si.costo,
                    (CASE si.estado 
                            WHEN 'E' THEN 'ENVIADO' 
                            WHEN 'R' THEN 'RECEPCIONADO' 
                            WHEN 'N' THEN 'ENTREGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_ingreso si
                    INNER JOIN agencia ao ON si.id_origen = ao.id_agencia
                    INNER JOIN agencia ad ON si.id_destino = ad.id_agencia
                    INNER JOIN sis_cliente scl ON scl.id_cliente = si.id_cliente
                    INNER JOIN usuario u ON u.id_usuario = si.id_usuario_responsable
                    WHERE si.estado_mrcb AND (si.fecha_registro BETWEEN :0 AND :1) ".$sqlAgenciaWhere. " 
                    ORDER BY si.fecha_hora_registro DESC";
            $data = $this->BD->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarPorAgencia($fecha_inicio, $fecha_fin){
        try{
            $sql = "SELECT COALESCE(id_agencia,1) as id_agencia FROM usuario WHERE id_usuario = :0";
            $id_agencia = $this->BD->consultarValor($sql, [$this->id_usuario_registro]);

            $sql = "SELECT
                    id_ingreso as id,
                    ao.descripcion as origen,
                    ad.descripcion as destino,
                    scl.nombres as cliente,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    IF(((SELECT SUM(monto_pagado) FROM sis_ingreso_pagos WHERE id_ingreso = si.id_ingreso) = si.cobrar),'SI','NO') as fue_pagado,
                    si.cobrar,
                    si.costo,
                    (CASE si.estado 
                            WHEN 'E' THEN 'ENVIADO' 
                            WHEN 'R' THEN 'RECEPCIONADO' 
                            WHEN 'N' THEN 'ENTREGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_ingreso si
                    INNER JOIN agencia ao ON si.id_origen = ao.id_agencia
                    INNER JOIN agencia ad ON si.id_destino = ad.id_agencia
                    INNER JOIN sis_cliente scl ON scl.id_cliente = si.id_cliente
                    INNER JOIN usuario u ON u.id_usuario = si.id_usuario_responsable
                    WHERE si.estado_mrcb AND (si.fecha_registro BETWEEN :0 AND :1) AND si.id_origen = :2
                    ORDER BY si.fecha_hora_registro DESC";
            $origen = $this->BD->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $id_agencia]);

            $sql = "SELECT
                    id_ingreso as id,
                    ao.descripcion as origen,
                    ad.descripcion as destino,
                    scl.nombres as cliente,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    IF(((SELECT SUM(monto_pagado) FROM sis_ingreso_pagos WHERE id_ingreso = si.id_ingreso) = si.cobrar),'SI','NO') as fue_pagado,
                    si.cobrar,
                    si.costo,
                    (CASE si.estado 
                            WHEN 'E' THEN 'ENVIADO' 
                            WHEN 'R' THEN 'RECEPCIONADO' 
                            WHEN 'N' THEN 'ENTREGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_ingreso si
                    INNER JOIN agencia ao ON si.id_origen = ao.id_agencia
                    INNER JOIN agencia ad ON si.id_destino = ad.id_agencia
                    INNER JOIN sis_cliente scl ON scl.id_cliente = si.id_cliente
                    INNER JOIN usuario u ON u.id_usuario = si.id_usuario_responsable
                    WHERE si.estado_mrcb AND (si.fecha_registro BETWEEN :0 AND :1) AND si.id_destino = :2
                    ORDER BY si.fecha_hora_registro DESC";
            $destino = $this->BD->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $id_agencia]);

            return ["origen"=>$origen, "destino"=>$destino];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try{

            $sql = "SELECT
                    si.id_ingreso,
                    si.id_cliente,
                    cl.nombres as cliente_descripcion,
                    cl.numero_documento as cliente_numero_documento,
                    cl.celular as cliente_celular,
                    si.id_agencia_transporte,
                    at.nombre as agencia_transporte_descripcion,
                    si.costo,
                    si.cobrar,
                    si.fue_pagado,
                    si.id_origen,
                    ao.descripcion as origen_descripcion,
                    si.id_destino,
                    ad.descripcion as destino_descripcion,
                    si.fecha_registro,
                    COALESCE((SELECT SUM(monto_pagado) FROM sis_ingreso_pagos WHERE id_ingreso = si.id_ingreso),'0.00') as total_pagado,
                    si.estado,
                    (CASE si.estado 
                            WHEN 'E' THEN 'ENVIADO' 
                            WHEN 'R' THEN 'RECEPCIONADO' 
                            WHEN 'N' THEN 'ENTREGADO' 
                            ELSE 'ANULADO' END) as estado_descripcion
                    FROM sis_ingreso si
                    INNER JOIN sis_cliente cl ON cl.id_cliente = si.id_cliente
                    INNER JOIN sis_agenciatransporte at ON at.id_agencia_transporte = si.id_agencia_transporte
                    INNER JOIN agencia ao ON si.id_origen = ao.id_agencia
                    INNER JOIN agencia ad ON si.id_destino = ad.id_agencia
                    WHERE si.estado_mrcb AND si.id_ingreso = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_ingreso]);

            $sql = "SELECT  producto, producto_descripcion,
                            documento_guia, cantidad, 
                            tipo_paquete, peso, volumen
                            FROM sis_ingreso_detalle 
                            WHERE id_ingreso = :0 AND estado_mrcb";
            $registros_detalle = $this->BD->consultarFilas($sql, [$this->id_ingreso]);
            $data["registros_detalle"] = $registros_detalle;




            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerRegistro(){
        try{

            $sql = "SELECT
                    id_ingreso as id,
                    ao.descripcion as origen,
                    ad.descripcion as destino,
                    scl.nombres as cliente,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    sat.nombre as agencia_transporte,
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,                    
                    IF(si.fue_pagado,'SI','NO') as fue_pagado,
                    si.cobrar,
                    si.costo,
                    (CASE si.estado 
                            WHEN 'E' THEN 'ENVIADO' 
                            WHEN 'R' THEN 'RECEPCIONADO' 
                            WHEN 'N' THEN 'ENTREGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_ingreso si
                    INNER JOIN agencia ao ON si.id_origen = ao.id_agencia
                    INNER JOIN agencia ad ON si.id_destino = ad.id_agencia
                    INNER JOIN sis_cliente scl ON scl.id_cliente = si.id_cliente
                    INNER JOIN sis_agenciatransporte sat ON si.id_agencia_transporte = sat.id_agencia_transporte
                    INNER JOIN usuario u ON u.id_usuario = si.id_usuario_responsable
                    WHERE si.estado_mrcb AND si.id_ingreso = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_ingreso]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            $editando = $this->id_ingreso != NULL;

            $sql = "SELECT COALESCE(id_agencia,1)  FROM usuario WHERE id_usuario = :0";
            $this->id_origen = $this->BD->consultarValor($sql, [$this->id_usuario_registro]);

            $campos_valores = [
                "id_cliente"=>$this->id_cliente,
                "id_agencia_transporte"=>$this->id_agencia_transporte,
                "id_origen"=>$this->id_origen,
                "id_destino"=>$this->id_destino,
                "fecha_registro"=>$this->fecha_registro,
                "costo"=>$this->costo,
                "cobrar"=>$this->cobrar,
                "id_usuario_responsable"=>$this->id_usuario_registro,
            ];


            $this->BD->beginTransaction();

            if ($editando){
                $campos_valores_where = ["id_ingreso" => $this->id_ingreso];
                $campos_valores["estado"] = $this->estado;
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where); 
                $this->BD->delete("sis_ingreso_detalle", $campos_valores_where);

                $sql = "SELECT i.estado,
                            COALESCE((SELECT SUM(monto_pagado) FROM sis_ingreso_pagos WHERE id_ingreso = i.id_ingreso),'0.00') as total_pagado
                            FROM sis_ingreso i WHERE i.id_ingreso  = :0";
                $objIngreso = $this->BD->consultarFila($sql, [$this->id_ingreso]);

                if ($this->pagado > 0){
                    $campos_valores_pagos = [
                        "id_ingreso"=>$this->id_ingreso,
                        "monto_pendiente"=>($this->cobrar - $objIngreso["total_pagado"]),
                        "monto_pagado"=>$this->pagado,
                        "id_usuario_registro"=>$this->id_usuario_registro
                    ];

                    $this->BD->insert("sis_ingreso_pagos", $campos_valores_pagos);    
                }

                if ($this->estado <> $objIngreso["estado"]){
                    $campos_valores_estados = [
                        "id_ingreso"=>$this->id_ingreso,
                        "estado_previo"=>$objIngreso["estado"],
                        "estado_actual"=>$this->estado,
                        "id_usuario_registro"=>$this->id_usuario_registro
                    ];

                    $this->BD->insert("sis_ingreso_estados", $campos_valores_estados); 
                }

            } else {
                $sql = "SELECT COALESCE(MAX(correlativo_agencia) + 1, 1) FROM sis_ingreso WHERE id_origen = :0";
                $correlativo_agencia = $this->BD->consultarValor($sql, [$this->id_origen]);
                $campos_valores["correlativo_agencia"] = $correlativo_agencia;
                $campos_valores["estado"] = "E"; //ENVIADO, RECEPCIONADO, E(N)TREGADO,

                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_ingreso = $this->BD->getLastID();

                $campos_valores_pagos = [
                    "id_ingreso"=>$this->id_ingreso,
                    "monto_pendiente"=>$this->cobrar,
                    "monto_pagado"=>$this->pagado,
                    "id_usuario_registro"=>$this->id_usuario_registro
                ];

                $this->BD->insert("sis_ingreso_pagos", $campos_valores_pagos);

                $campos_valores_estados = [
                    "id_ingreso"=>$this->id_ingreso,
                    "estado_previo"=>NULL,
                    "estado_actual"=>"E",
                    "id_usuario_registro"=>$this->id_usuario_registro
                ];

                $this->BD->insert("sis_ingreso_estados", $campos_valores_estados);
            }

            $data_bitacora = [
                "campos_valores"=>$campos_valores,
                "campos_valores_detalle"=>$this->registros_detalle,
                "campos_valores_pagos"=>$campos_valores_pagos,
                "campos_valores_estados"=>$campos_valores_estados
            ];

            foreach ($this->registros_detalle as $key => $detalle) {
                $campos_valores = [
                    "id_ingreso"=>$this->id_ingreso,
                    "producto"=>mb_strtoupper($detalle["producto"],'UTF-8'),
                    "producto_descripcion"=>mb_strtoupper($detalle["producto_descripcion"],'UTF-8'),
                    "documento_guia"=>mb_strtoupper($detalle["documento_guia"],'UTF-8'),
                    "cantidad"=>$detalle["cantidad"],
                    "tipo_paquete"=>$detalle["tipo_paquete"],
                    "peso"=>(isset($detalle["peso"]) && $detalle["peso"] != "") ? $detalle["peso"] : NULL,
                    "volumen"=>(isset($detalle["volumen"]) && $detalle["volumen"] != "") ? $detalle["volumen"] : NULL
                ];
                $this->BD->insert("sis_ingreso_detalle", $campos_valores);
            }

            $campos_valores_bitacora = [
                "data"=>json_encode($data_bitacora)
            ];
            $this->BD->insert("sis_ingreso_bitacora", $campos_valores_bitacora);
            $this->BD->commit();

            $registro = $this->obtenerRegistro();
            return ["msj"=>$editando ? "Editado correctamente" : "Registrado correctamente.", "registro"=>$registro];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarDestino(){
        try
        {   
            $editando = $this->id_ingreso != NULL;

            $campos_valores = [
                "id_usuario_responsable"=>$this->id_usuario_registro,
                "estado"=>$this->estado
            ];

            $campos_valores_where = ["id_ingreso" => $this->id_ingreso];

            $this->BD->beginTransaction();
            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where); 

             $sql = "SELECT i.estado,
                        i.cobrar,
                        COALESCE((SELECT SUM(monto_pagado) FROM sis_ingreso_pagos WHERE id_ingreso = i.id_ingreso),'0.00') as total_pagado
                        FROM sis_ingreso i WHERE i.id_ingreso  = :0";
            $objIngreso = $this->BD->consultarFila($sql, [$this->id_ingreso]);

            if ($this->pagado > 0){
                $campos_valores_pagos = [
                    "id_ingreso"=>$this->id_ingreso,
                    "monto_pendiente"=>($objIngreso["cobrar"] - $objIngreso["total_pagado"]),
                    "monto_pagado"=>$this->pagado,
                    "id_usuario_registro"=>$this->id_usuario_registro
                ];

                $this->BD->insert("sis_ingreso_pagos", $campos_valores_pagos);    
            }

            if ($this->estado <> $objIngreso["estado"]){
                $campos_valores_estados = [
                    "id_ingreso"=>$this->id_ingreso,
                    "estado_previo"=>$objIngreso["estado"],
                    "estado_actual"=>$this->estado,
                    "id_usuario_registro"=>$this->id_usuario_registro
                ];

                $this->BD->insert("sis_ingreso_estados", $campos_valores_estados); 
            }

            $data_bitacora = [
                "campos_valores"=>$campos_valores,
                "campos_valores_pagos"=>$campos_valores_pagos,
                "campos_valores_estados"=>$campos_valores_estados
            ];

            $campos_valores_bitacora = [
                "data"=>json_encode($data_bitacora)
            ];
            $this->BD->insert("sis_ingreso_bitacora", $campos_valores_bitacora);
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
                "id_ingreso"=>$this->id_ingreso   
            ];  
            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);   
            return ["msj"=>"Eliminado correctamente."]; 
        } catch (Exception $exc) {  
            throw new Exception($exc->getMessage());    
        }   
    }

    /*
    public function obtenerImprimir(){
        try{

            $sql = "SELECT
                    id_ingreso as id,
                    CONCAT(LPAD(sr.id_agencia,3,'0'),'-',LPAD(correlativo_agencia,6,'0')) as codigo,
                    a.descripcion as agencia,
                    sre.razon_social as cliente,
                    DATE_FORMAT(fecha_registro, '%d-%m-%Y') as fecha_registro,
                    fecha_registro as fecha_registro_raw,
                    stv.descripcion as tipo_vehiculo,
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,
                    (SELECT SUM(spd.subtotal) FROM sis_ingreso_detalle spd WHERE spd.id_ingreso = sr.id_ingreso) + sr.costo_global as costo_entrega,
                    sr.costo_global,
                    sr.observaciones,
                    (CASE estado 
                            WHEN 'P' THEN 'PENDIENTE' 
                            WHEN 'R' THEN 'RUTA' 
                            WHEN 'E' THEN 'ENTREGADO' 
                            WHEN 'C' THEN 'MOTIVADO' 
                            WHEN 'A' THEN 'PAGADO' 
                            ELSE 'ANULADO' END) as estado
                    FROM sis_ingreso sr
                    INNER JOIN agencia a ON sr.id_agencia = a.id_agencia
                    INNER JOIN sis_tipo_vehiculo stv ON stv.id_tipo_vehiculo = sr.id_tipo_vehiculo
                    INNER JOIN sis_cliente sre ON sre.id_cliente = sr.id_cliente
                    INNER JOIN usuario u ON u.id_usuario = sr.id_usuario_responsable
                    WHERE sr.estado_mrcb AND sr.id_ingreso = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_ingreso]);

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
                    FROM sis_ingreso_detalle  spd
                    INNER JOIN sis_tipo_paquete tp ON tp.id_tipo_paquete = spd.tipo_paquete
                    WHERE id_ingreso = :0 AND spd.estado_mrcb";
            $registros_detalle = $this->BD->consultarFilas($sql, [$this->id_ingreso]);
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
                    sre.numero_documento as numero_documento_cliente,    
                    sre.razon_social as cliente, 
                    fecha_registro, 
                    stv.descripcion as tipo_vehiculo, 
                    CONCAT(u.nombres,' ',u.apellidos) as responsable,   
                    (SELECT SUM(spd.subtotal) FROM sis_ingreso_detalle spd WHERE spd.id_ingreso = sr.id_ingreso) as costo_total_subtotales,
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
                    FROM sis_ingreso sr  
                    INNER JOIN agencia a ON sr.id_agencia = a.id_agencia    
                    INNER JOIN sis_tipo_vehiculo stv ON stv.id_tipo_vehiculo = sr.id_tipo_vehiculo  
                    INNER JOIN sis_cliente sre ON sre.id_cliente = sr.id_cliente   
                    INNER JOIN usuario u ON u.id_usuario = sr.id_usuario_responsable    
                    INNER JOIN sis_ingreso_detalle spd ON spd.id_ingreso = sr.id_ingreso   
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
                $arreglo_registros = $objProcesar->ingresoes_importacion();

                if (!$arreglo_registros["formato_valido"]){
                      throw new Exception("Formato no reconocible seleccionado.");
                }
            }

            return $arreglo_registros["registros"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    } 
    */
    
}

