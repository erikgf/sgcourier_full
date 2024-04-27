<?php 

require_once 'Modelo.clase.php';

class SisTipoVehiculo extends Modelo{
    public $id_vehiculo;
    public $descripcion;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_tipo_vehiculo", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function buscarSelect($texto_buscar){
        try{

            $sql = "SELECT
                    id_tipo_vehiculo as id,
                    descripcion as text
                    FROM sis_tipo_vehiculo
                    WHERE estado_mrcb AND descripcion LIKE '%".$texto_buscar."%';
                    ORDER BY descripcion";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

