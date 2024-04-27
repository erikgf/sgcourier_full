<?php 

require_once 'Modelo.clase.php';

class SisGuiaArea extends Modelo{
    public $descripcion;
    public $id_area;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_guia_area", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function obtenerAreasRangoFechas($fecha_inicio, $fecha_fin){
        try{

            $sql = "SELECT id_area as id, descripcion,
                        (SELECT COUNT(id_area_registro) FROM sis_guia_area_registro 
                            WHERE id_area = s.id_area AND estado_mrcb
                            AND (fecha_recepcion BETWEEN :0 AND :1)) as cantidad_registros
                        FROM sis_guia_area s
                        WHERE s.estado_mrcb 
                        GROUP BY s.id_area, s.descripcion
                        HAVING cantidad_registros > 0
                        ORDER BY s.descripcion";
            $data = $this->BD->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try{

            $sql = "SELECT
                    id_area as id,
                    descripcion as text
                    FROM sis_guia_area
                    WHERE estado_mrcb AND id_area = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_area]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerAreas($texto_buscar){
        try{

            $sql = "SELECT
                    id_area as id,
                    descripcion as text
                    FROM sis_guia_area
                    WHERE estado_mrcb AND descripcion LIKE '%".$texto_buscar."%';
                    ORDER BY descripcion";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function esRepetido($editando){
        //SI DEVUELVE true: es válido, puede registrarse o editarse, sino es false.
        try{

            if (!($this->descripcion == "" || $this->descripcion == NULL)){
                $sql = "SELECT id_area
                        FROM sis_guia_area
                        WHERE estado_mrcb AND descripcion = :0  AND ".($editando ? "id_area <> ".$this->id_area : "true");

                $existe = $this->BD->consultarFila($sql, [$this->descripcion]);
                
                if ($existe == false){ //No existe
                    return false;
                }
                
            }
            
            $this->id_area = $existe["id_area"];
            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            
            $editando = $this->id_area != NULL;
            $esRepetido = $this->esRepetido($editando);
            if ($esRepetido){
                return [];
            }

            $campos_valores = [
                "descripcion"=>$this->descripcion,
            ];
    
            
            $this->BD->beginTransaction();
            
            if ($editando){
                $campos_valores_where = ["id_area" => $this->id_area];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            } else {
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_area = $this->BD->getLastID();
            }

            $this->BD->commit();

            $registro = $this->leer();
            return ["msj"=>$editando ? "Editado correctamente" : "Registrado correctamente.", "data"=>$registro];
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
                "id_area"=>$this->id_area
            ];

            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

