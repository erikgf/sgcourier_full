<?php 

include_once '../datos/variables.vista.php';
require_once '../datos/Conexion.clase.php';
require_once 'GlobalVariables.clase.php';

class Modelo{
	private $estado_mrcb;
	protected $main_table;
	protected $BD;

	public function __construct($main_table, $BD = null){
		try {
			if (!$BD){
				$this->BD = new Conexion(); 
			} else {
				$this->BD = $BD;
			}

			$this->main_table = $main_table;	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	protected function _registrar($campos_valores){
		try
        {   
            $this->BD->beginTransaction();
           	$this->BD->insert($this->main_table, $campos_valores);
            $this->BD->commit();
            return ["msj"=>"Registrado correctamente.", "id"=>$this->getLastID()];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
	}

	protected function _editar($campos_valores, $campos_valores_where){
		try
        {   
           	$this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Actualizado correctamente.", "id"=>$this->getLastID()];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
	}

	protected function _eliminar($campos_valores_where){
		try
        {   
            $campos_valores = [
            	"estado_mrcb"=>"0"
            ];

           	$this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente.", "id"=>$this->getLastID()];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
	}

	/*
	public function validarRepetido($id, $validaciones, $editando = false){
		try {
			$mensaje = "";
			foreach ($validaciones as $key => $value) {
				if ($editando){
					$sqlWhere = $id["key"].' <> :0 AND '.$key.' = :1';
					$params = [$id["valor"], $value["valor"]];
				} else {
					$sqlWhere .= ($key.' = :0');
					$params = [$value["valor"]];
				}
				$sql = "SELECT COUNT(".$id["key"].") FROM ".$this->main_table." WHERE ".$sqlWhere;
				$repetido = $this->BD->consultarValor($sql, $params);
				if ($repetido){
					$mensaje = $value["mensaje"];
					break;
				}
			}

			if ($repetido > 0){
				throw new Exception($mensaje);
				return ["r"=>false, "mensaje">$mensaje];
			}

			return ["r"=>true, "mensaje"=>"OK"];

		} catch (Exception $exc) {
			throw new Exception($exc->getMessage());	
		}
	}
	*/
}
