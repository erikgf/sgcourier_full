<?php 

require_once 'Modelo.clase.php';

class Colaborador extends Modelo{
	public $idusuario;
    
    private $TIPO_USUARIO_EJECUTIVO_REPARTIDOR = 99;

	public function __construct($BD = null){
		try {
			parent::__construct("usuario", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}

    public function obtenerRepartidores(){
        try{
            $sql = "SELECT
                        id_usuario as id,
                        CONCAT(nombres,' ',apellidos) as descripcion
                        FROM usuario u
                        WHERE u.estado_mrcb AND (u.id_tipo_usuario = 3  OR (u.id_tipo_usuario = 2 AND u.es_ejecutivo_repartidor))
                        ORDER BY 2";
            return  $this->BD->consultarFilas($sql);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}

