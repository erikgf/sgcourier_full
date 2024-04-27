<?php 
require_once 'Modelo.clase.php';

class EstadoOrden extends Modelo{

    public function __construct($BD = null){
        try {
            parent::__construct("estado_orden", $BD); 
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function obtener(){
        try{

            $sql = "SELECT id_estado_orden, descripcion, estado_color_rotulo as estado_color, numero_orden
                    FROM estado_orden
                    WHERE estado_mrcb 
                    ORDER BY numero_orden"; 
            return $this->BD->consultarFilas($sql);
            
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }


}