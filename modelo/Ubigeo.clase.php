<?php 

require_once 'Modelo.clase.php';

class Ubigeo extends Modelo{

    public $id_departamento;
    public $id_provincia;
    public $id_distrito;

	public function __construct($BD = null){
		try {
			parent::__construct("ubigeos", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}

    public function listarDepartamentos(){
        try{
            $sql = "SELECT id, name
                        FROM ubigeo_peru_departments 
                        ORDER BY name";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarProvincias(){
        try{
            $sql = "SELECT id, name
                        FROM ubigeo_peru_provinces
                        WHERE department_id = :0
                        ORDER BY name";
            $data = $this->BD->consultarFilas($sql, [$this->id_departamento]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarDistritos(){
        try{
            $sql = "SELECT id, name
                        FROM ubigeo_peru_districts
                        WHERe province_id = :0
                        ORDER BY name";
            $data = $this->BD->consultarFilas($sql, [$this->id_provincia]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    
}

