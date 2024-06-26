<?php 

require_once 'Modelo.clase.php';

class SisGuiaDestino extends Modelo{
    public $descripcion;
    public $id_destino;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_guia_destino", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function leer(){
        try{

            $sql = "SELECT
                    id_destino as id,
                    descripcion as text
                    FROM sis_guia_destino
                    WHERE estado_mrcb AND id_destino = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_destino]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerDestinos($texto_buscar){
        try{

            $sql = "SELECT
                    descripcion as id,
                    descripcion as text
                    FROM sis_guia_destino
                    WHERE estado_mrcb AND descripcion LIKE '%".$texto_buscar."%';
                    ORDER BY descripcion";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function validarRepetido($editando){
        try{

            if (!($this->descripcion == "" || $this->descripcion == NULL)){
                $sql = "SELECT COUNT(id_destino) as c 
                        FROM sis_guia_destino
                        WHERE estado_mrcb AND descripcion = :0  AND ".($editando ? "id_destino <> ".$this->id_destino : "true");

                $existe = $this->BD->consultarValor($sql, [$this->descripcion]);
                if ($existe > 0){
                    return false;
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
            

            $editando = $this->id_destino != NULL;

            if (!$this->validarRepetido($editando)){
                return [];
            }

            $campos_valores = [
                "descripcion"=>$this->descripcion,
            ];
            
            $this->BD->beginTransaction();
            
            if ($editando){
                $campos_valores_where = ["id_destino" => $this->id_destino];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            } else {
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_destino = $this->BD->getLastID();
            }

            $this->BD->commit();

            $registro = $this->leer();
            return ["msj"=>"Registrado correctamente.", "data"=>$registro];
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
                "id_destino"=>$this->id_destino
            ];

            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

