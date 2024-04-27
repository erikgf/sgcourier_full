<?php 

require_once 'Modelo.clase.php';

class SisAgenciaTransporte extends Modelo{
    public $id_agencia_transporte;
    public $nombre;
    public $celular;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_agenciatransporte", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function leer(){
        try{

            $sql = "SELECT
                    id_agencia_transporte,
                    nombre,
                    celular
                    FROM sis_agenciatransporte
                    WHERE estado_mrcb AND id_agencia_transporte = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_agencia_transporte]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerRegistro(){
        try{

            $sql = "SELECT
                    id_agencia_transporte as id,
                    nombre as descripcion,
                    celular
                    FROM sis_agenciatransporte
                    WHERE estado_mrcb AND id_agencia_transporte = :0
                    ORDER BY nombre";
            $data = $this->BD->consultarFila($sql, [$this->id_agencia_transporte]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function buscar($texto_buscar){
        try{

            $sql = "SELECT
                    id_agencia_transporte as id,
                    nombre as descripcion
                    FROM sis_agenciatransporte
                    WHERE estado_mrcb AND nombre LIKE '%".$texto_buscar."%';
                    ORDER BY nombre";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtener(){
        try{

            $sql = "SELECT
                    id_agencia_transporte as id,
                    nombre as descripcion
                    FROM sis_agenciatransporte
                    WHERE estado_mrcb;
                    ORDER BY nombre";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function esRepetido($editando){
        //SI DEVUELVE true: es válido, puede registrarse o editarse, sino es false.
        try{

            if (!($this->nombre == "" || $this->nombre == NULL)){
                $sql = "SELECT id_agencia_transporte
                        FROM sis_agenciatransporte
                        WHERE estado_mrcb 
                                AND nombre = :0  
                                AND ".($editando ? "id_agencia_transporte <> ".$this->id_agencia_transporte : "true");

                $existe = $this->BD->consultarFila($sql, [$this->nombre]);
                
                if ($existe == false){ //No existe
                    return false;
                }
                
            }
            
            $this->id_agencia_transporte = $existe["id_agencia_transporte"];
            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            
            $editando = $this->id_agencia_transporte != NULL;
            $esRepetido = $this->esRepetido($editando);
            if ($esRepetido){
                return [];
            }

            $campos_valores = [
                "nombre"=>$this->nombre,
                "celular"=>$this->celular
            ];
    
            $this->BD->beginTransaction();
            
            if ($editando){
                $campos_valores_where = ["id_agencia_transporte" => $this->id_agencia_transporte];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            } else {
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_agencia_transporte = $this->BD->getLastID();
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
                "id_agencia_transporte"=>$this->id_agencia_transporte
            ];

            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Anulado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

