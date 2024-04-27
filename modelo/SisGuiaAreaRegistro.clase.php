<?php 
include_once "../phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

require_once 'Modelo.clase.php';

class SisGuiaAreaRegistro extends Modelo{
    public $id_area_registro;
    public $id_area;
	public $fecha_recepcion;
	public $dependencia;
	public $numero_guia;
	public $remitente;
    public $consignatario;
    public $destino;
    public $imagenes;
    public $id_usuario_registro;
    public $fecha_hora_registro;

    public $id_area_registro_imagen;
    
    public $numero_mes;
    public $numero_anio;
    public $sucursal;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_guia_area_registro", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function listarXIdArea($fecha_inicio, $fecha_fin){
        try{

            $sql = "SELECT
                    id_area_registro,
                    fecha_recepcion,
                    numero_guia,
                    remitente,
                    dependencia,
                    consignatario,
                    destino,
                    COALESCE(fecha_entrega,'-') as fecha_entrega
                    FROM sis_guia_area_registro
                    WHERE estado_mrcb AND id_area = :0 AND (fecha_recepcion BETWEEN :1 AND :2)
                    ORDER BY fecha_recepcion, numero_guia";
            $data = $this->BD->consultarFilas($sql, [$this->id_area, $fecha_inicio, $fecha_fin]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarXAreas(){
        try{

            $sql = "SELECT id_area, descripcion,
                        (SELECT COUNT(id_area_registro) FROM sis_guia_area_registro 
                            WHERE id_area = s.id_area AND estado_mrcb) as cantidad_registros
                        FROM sis_guia_area s
                        WHERE s.estado_mrcb 
                        GROUP BY id_area, descripcion
                        HAVING cantidad_registros > 0
                        ORDER BY descripcion";
            $areas = $this->BD->consultarFilas($sql);

            foreach ($areas as $key => $area) {
               $sql = "SELECT
                    id_area_registro,
                    fecha_recepcion,
                    numero_guia,
                    remitente,
                    dependencia,
                    consignatario,
                    destino,
                    COALESCE(fecha_entrega,'-') as fecha_entrega
                    FROM sis_guia_area_registro
                    WHERE estado_mrcb AND id_area = :0
                    ORDER BY fecha_recepcion, numero_guia";
                $areas[$key]["registros"] = $this->BD->consultarFilas($sql, [$area->id_area]); 
            }

            return $areas;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try{

            $sql = "SELECT 
                    id_area_registro,
                    a.id_area,
                    a.descripcion as area,
                    fecha_recepcion,
                    numero_guia,
                    remitente,
                    dependencia,
                    consignatario,
                    destino,
                    COALESCE(fecha_entrega,'') as fecha_entrega
                    FROM sis_guia_area_registro ar 
                    INNER JOIN sis_guia_area a ON ar.id_area = a.id_area
                    WHERE ar.estado_mrcb AND ar.id_area_registro = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_area_registro]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerImagenesXIdRegistro(){
        try{

            $sql = "SELECT 
                    COALESCE(fecha_entrega,'') as fecha_entrega
                    FROM sis_guia_area_registro ar 
                    WHERE ar.estado_mrcb AND ar.id_area_registro = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_area_registro]);

            $sql = "SELECT id_registro_imagen, url_imagen
                    FROM sis_guia_area_registro_imagen
                    WHERE estado_mrcb AND id_area_registro = :0";
            $data["imagenes"] =  $this->BD->consultarFilas($sql, [$this->id_area_registro]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function validarGuiaRepetida($editando){
        try{

            if (!($this->numero_guia == "" || $this->numero_guia == NULL)){
                $sql = "SELECT COUNT(id_area_registro) as c 
                        FROM sis_guia_area_registro 
                        WHERE estado_mrcb AND numero_guia = :0  AND ".($editando ? "id_area_registro <> ".$this->id_area_registro : "true");

                $existe = $this->BD->consultarValor($sql, [$this->numero_guia]);
                if ($existe > 0){
                    throw new Exception("Número de guía ingresado ya existe.");
                }
            }

            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            $this->BD->beginTransaction();

            $this->fecha_hora_registro = date("Y-m-d H:i:s");

            $editando = $this->id_area_registro != NULL;

            $this->validarGuiaRepetida($editando);

            if (!ctype_digit(strval($this->id_area))){
                require_once 'SisGuiaArea.clase.php';
                $objArea = new SisGuiaArea($this->BD);
                $objArea->descripcion = $this->id_area;
                $objArea->registrar();

                $this->id_area = $objArea->id_area;
            }

            require_once 'SisGuiaConsignatario.clase.php';
            $objConsignatario = new SisGuiaConsignatario($this->BD);
            $objConsignatario->descripcion = $this->consignatario;
            $objConsignatario->registrar();

            require_once 'SisGuiaDestino.clase.php';
            $objDestino = new SisGuiaDestino($this->BD);
            $objDestino->descripcion = $this->destino;
            $objDestino->registrar();

            require_once 'SisGuiaRemitente.clase.php';
            $objRemitente = new SisGuiaRemitente($this->BD);
            $objRemitente->descripcion = $this->remitente;
            $objRemitente->registrar();

            $campos_valores = [
                "fecha_recepcion"=>$this->fecha_recepcion,
                "id_area"=>$this->id_area,
                "numero_guia"=>$this->numero_guia,
                "dependencia"=>$this->dependencia,
                "remitente"=>$this->remitente,
                "consignatario"=>$this->consignatario,
                "destino"=>$this->destino,
                "id_usuario_registro"=>$this->id_usuario_registro,
                "fecha_hora_registro"=>$this->fecha_hora_registro
            ];

            if ($editando){
                $campos_valores_where = ["id_area_registro" => $this->id_area_registro];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            } else {
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_area_registro = $this->BD->getLastID();
            }

            $this->BD->commit();

            $registro = $this->leer();
            return ["msj"=>"Registrado correctamente.", "registro"=>$registro];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminar(){
        try
        {   
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_area_registro"=>$this->id_area_registro
            ];

            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarFechaEntrega(){
        try
        {   
            $this->BD->beginTransaction();

            $campos_valores = [
                "fecha_entrega"=>$this->fecha_entrega,
            ];

            $campos_valores_where = ["id_area_registro" => $this->id_area_registro];
            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            $this->BD->commit();

            return ["msj"=>"Fecha de entrega actualizada."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarImagenesEntrega(){
        try
        {   
            $this->BD->beginTransaction();

            $this->fecha_hora_registro = date("Y-m-d H:i:s");

            if (count($this->imagenes) > 0){
                $campos = ["id_area_registro", "url_imagen", "numero_imagen","tipo_imagen", "id_usuario_registro", "fecha_hora_registro"];
                $valores = [];

                $sql = "SELECT COALESCE(MAX(numero_imagen) + 1, 1) FROM sis_guia_area_registro_imagen WHERE estado_mrcb AND id_area_registro = :0";
                $numero_imagen = $this->BD->consultarValor($sql, [$this->id_area_registro]);
                foreach ($this->imagenes as $key => $value) {
                    $url_img = $this->id_area_registro."_".$numero_imagen."_".date("YmdHis")."_".md5($value["nombre"]);
                    $nombre_imagen_original = $value["nombre"];
                    $tamano = $value["tamano"];
                    $tipo_imagen = $value["tipo"];
                    array_push($valores, [$this->id_area_registro, 
                                        $url_img, 
                                        $numero_imagen,
                                        $tipo_imagen,
                                        $this->id_usuario_registro,
                                        $this->fecha_hora_registro]);
                    if (!move_uploaded_file($value["archivo"], "../img/imagenes_sis_guia/$url_img")) {
                        $this->BD->rollBack();
                        throw new Exception("Error al subir la imagen ".$numero_imagen.".");
                    }
                    $numero_imagen++;
                }

                $this->BD->insertMultiple("sis_guia_area_registro_imagen", $campos, $valores);
                $this->id_area_registro_imagen = $this->BD->getLastID();
            }


            $this->BD->commit();
            return ["msj"=>"Imagen(es) subida(s).", "id"=> $this->id_area_registro_imagen];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminarImagenEntrega(){
        try
        {   
            $sql = "SELECT url_imagen FROM sis_guia_area_registro_imagen WHERE id_registro_imagen = :0";
            $url_imagen = $this->BD->consultarValor($sql, [$this->id_area_registro_imagen]);
            
            $campos_valores_where = [
                "id_registro_imagen"=>$this->id_area_registro_imagen
            ];

            $this->BD->delete("sis_guia_area_registro_imagen", $campos_valores_where);

            if ($url_imagen){
                 if (file_exists("../img/imagenes_sis_guia/".$url_imagen)) {
                   unlink("../img/imagenes_sis_guia/".$url_imagen);
                }
            }

            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function consultarXGuia(){
        try{
            
            $params = [$this->numero_guia];
            $indexParam = 0;
            $sqlExtra = "";
            
            if ($this->numero_mes != ""){
                array_push($params, $this->numero_mes);
                $sqlExtra .= " AND MONTH(fecha_recepcion) = :".(++$indexParam);
            }
            
            if ($this->numero_anio != ""){
                array_push($params, $this->numero_anio);
                $sqlExtra .= " AND YEAR(fecha_recepcion) = :".(++$indexParam);
            }
            
            if ($this->sucursal != ""){
                array_push($params, $this->sucursal);
                $sqlExtra .= " AND ar.id_area = :".(++$indexParam);
            }
            
            $sql = "SELECT 
                    id_area_registro,
                    DATE_FORMAT(fecha_recepcion,'%d-%m-%Y') as fecha_recepcion,
                    numero_guia,
                    remitente,
                    dependencia,
                    consignatario,
                    CONCAT(a.descripcion, ' - ',destino) as destino,
                    COALESCE(DATE_FORMAT(fecha_entrega,'%d-%m-%Y'),'') as fecha_entrega
                    FROM sis_guia_area_registro ar 
                    INNER JOIN sis_guia_area a ON ar.id_area = a.id_area
                    WHERE ar.estado_mrcb AND ar.numero_guia = :0".$sqlExtra;
            $data = $this->BD->consultarFila($sql, $params);

            if ($data == false){
                throw new Exception("Registro no encontrado.", 1);
            }

            $sql = "SELECT url_imagen
                    FROM sis_guia_area_registro_imagen
                    WHERE estado_mrcb AND id_area_registro = :0";
            $data["imagenes"] =  $this->BD->consultarFilas($sql, [$data["id_area_registro"]]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarMasivo($necesito_excel_formato = true){
        try
        {   
            $this->BD->beginTransaction();
            /*validar has repetido*/
            $campos_valores = [
                "id_usuario_registrado"=>$this->id_usuario_registro,
                "fecha_hora_registrado"=>date("Y-m-d H:i:s"),
                "hash_file"=> $necesito_excel_formato ? hash_file("md5",$this->archivo["archivo"]) : NULL
            ];
            
            $this->BD->insert("sis_guia_archivo_subida", $campos_valores);
            
            $partes_ruta = pathinfo($this->archivo["nombre"]);
            $extension = $partes_ruta["extension"];

            if ($necesito_excel_formato){
                $partes_ruta = pathinfo($this->archivo["nombre"]);
                $extension = $partes_ruta["extension"];
                $objReader = IOFactory::createReader($extension == "xls" ? "Xls" : 'Xlsx');
                //$objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load($this->archivo["archivo"]);

                $cantidadPaginas = $objPHPExcel->getSheetCount();
                for ($i=0; $i < $cantidadPaginas; $i++) { 
                    $objWorksheet = $objPHPExcel->getSheet($i);

                    $highestRow = $objWorksheet->getHighestRow();
                    $highestColumn = $objWorksheet->getHighestColumn();
                    $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                    $data_de_filas_excel = [];
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $rowObject = [];
                        for ($col = 1; $col <= $highestColumnIndex; $col++) {
                                $celda = $objWorksheet->getCellByColumnAndRow($col, $row);
                                $valor = $celda->getValue();
                                array_push($rowObject, $valor);
                        }

                        if ($rowObject[0] != NULL){
                            array_push($data_de_filas_excel, $rowObject);
                        }
                    }

                    $celda = null;
                    $valor = null;

                    require_once 'ProcesarExcel.clase.php';
                    $objProcesar = new ProcesarExcel;
                    $objProcesar->data_de_filas_excel = $data_de_filas_excel;
                    $titulo_sheet = $objWorksheet->getTitle();

                    require_once 'SisGuiaArea.clase.php';
                    $objArea = new SisGuiaArea($this->BD);
                    $objArea->descripcion = $titulo_sheet;
                    $objArea->registrar();

                    $id_area = $objArea->id_area;

                    $objProcesar->id_usuario_registro = $this->id_usuario_registro;
                    $campos_valores_registro = $objProcesar->poder_judicial($id_area);

                    if (!$campos_valores_registro["formato_valido"]){
                          throw new Exception("Formato no reconocible para el cliente seleccionado.");
                    }
                
                    $campos_valores = $campos_valores_registro["campos_valores"];
                    $cantidad_ordenes = count($campos_valores);
                    
                    if ( $cantidad_ordenes > 0){
                        for ($x= 0; $x < $cantidad_ordenes; $x++) {
                            $this->BD->insert("sis_guia_area_registro", $campos_valores[$x]); 
                        }   
                    }
                }
            } 

            $this->BD->commit();
            return ["msj"=>"Registrado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

