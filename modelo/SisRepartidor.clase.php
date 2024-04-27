<?php 

require_once 'Modelo.clase.php';

class SisRepartidor extends Modelo{
    public $id_repartidor;
    public $numero_documento;
    public $razon_social;
    public $costo_entrega;
    public $celular;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_repartidor", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function leer(){
        try{

            $sql = "SELECT
                    id_repartidor,
                    numero_documento,
                    razon_social,
                    costo_entrega,
                    celular
                    FROM sis_repartidor
                    WHERE estado_mrcb AND id_repartidor = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_repartidor]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerRegistro(){
        try{

            $sql = "SELECT
                    id_repartidor as id,
                    razon_social as descripcion,
                    costo_entrega
                    FROM sis_repartidor
                    WHERE estado_mrcb AND id_repartidor = :0
                    ORDER BY razon_social";
            $data = $this->BD->consultarFila($sql, [$this->id_repartidor]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function buscar($texto_buscar){
        try{

            $sql = "SELECT
                    id_repartidor as id,
                    razon_social as descripcion,
                    costo_entrega
                    FROM sis_repartidor
                    WHERE estado_mrcb AND razon_social LIKE '%".$texto_buscar."%';
                    ORDER BY razon_social";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtener(){
        try{

            $sql = "SELECT
                    id_repartidor as id,
                    razon_social as descripcion,
                    costo_entrega
                    FROM sis_repartidor
                    WHERE estado_mrcb;
                    ORDER BY razon_social";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function esRepetido($editando){
        //SI DEVUELVE true: es válido, puede registrarse o editarse, sino es false.
        try{

            if (!($this->numero_documento == "" || $this->numero_documento == NULL)){
                $sql = "SELECT id_repartidor
                        FROM sis_repartidor
                        WHERE estado_mrcb AND numero_documento = :0  AND ".($editando ? "id_repartidor <> ".$this->id_repartidor : "true");

                $existe = $this->BD->consultarFila($sql, [$this->numero_documento]);
                
                if ($existe == false){ //No existe
                    return false;
                }
                
            }
            
            $this->id_repartidor = $existe["id_repartidor"];
            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            
            $editando = $this->id_repartidor != NULL;
            $esRepetido = $this->esRepetido($editando);
            if ($esRepetido){
                return [];
            }

            $campos_valores = [
                "razon_social"=>$this->razon_social,
                "celular"=>$this->celular,
                "numero_documento"=>$this->numero_documento,
                "costo_entrega"=>$this->costo_entrega,
            ];
    
            $this->BD->beginTransaction();
            
            if ($editando){
                $campos_valores_where = ["id_repartidor" => $this->id_repartidor];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            } else {
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_repartidor = $this->BD->getLastID();
            }

            $this->BD->commit();

            $registro = $this->obtenerRegistro();
            return ["msj"=>$editando ? "Editado correctamente" : "Registrado correctamente.", "registro"=>$registro];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function anular(){
        try
        {   
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_repartidor"=>$this->id_repartidor
            ];

            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Anulado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

