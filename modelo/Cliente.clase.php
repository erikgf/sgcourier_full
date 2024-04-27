<?php 

require_once 'Modelo.clase.php';

class Cliente extends Modelo{
	public function __construct($BD = null){
		try {
			parent::__construct("usuario", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}

	public function listarSelect(){
        try{

            $sql = "SELECT
                    id_usuario as id,
                    CONCAT('[',numero_documento,'] ',razon_social) as descripcion
                    FROM usuario
                    WHERE id_tipo_usuario = :0 AND estado_mrcb";
            $data = $this->BD->consultarFilas($sql, [GlobalVariables::$ID_TIPO_USUARIO_CLIENTE]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function listarClientesApp(){
        try{

            $sql = "SELECT
                    id_usuario as id,
                    razon_social as descripcion
                    FROM usuario
                    WHERE id_tipo_usuario = :0 AND estado_mrcb AND mostrar_en_app = 1";
            $data = $this->BD->consultarFilas($sql, [GlobalVariables::$ID_TIPO_USUARIO_CLIENTE]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

