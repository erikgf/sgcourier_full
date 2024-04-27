<?php 

require_once 'Modelo.clase.php';

class SisCliente extends Modelo{
    public $id_cliente;
    public $numero_documento;
    public $nombres;
    public $celular;

	public function __construct($BD = null){
		try {
			parent::__construct("sis_cliente", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

    public function leer(){
        try{

            $sql = "SELECT
                    id_cliente,
                    numero_documento,
                    nombres,
                    celular
                    FROM sis_cliente
                    WHERE estado_mrcb AND id_cliente = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_cliente]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerRegistro(){
        try{

            $sql = "SELECT
                    id_cliente as id,
                    nombres as descripcion,
                    celular,
                    numero_documento
                    FROM sis_cliente
                    WHERE estado_mrcb AND id_cliente = :0
                    ORDER BY nombres";
            $data = $this->BD->consultarFila($sql, [$this->id_cliente]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function buscar($texto_buscar){
        try{

            $sql = "SELECT
                    id_cliente as id,
                    nombres as descripcion
                    FROM sis_cliente
                    WHERE estado_mrcb AND nombres LIKE '%".$texto_buscar."%';
                    ORDER BY nombres";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtener(){
        try{

            $sql = "SELECT
                    id_cliente as id,
                    CONCAT(nombres,' Cel.: ',celular) as descripcion,
                    celular,
                    nombres,
                    numero_documento
                    FROM sis_cliente
                    WHERE estado_mrcb
                    ORDER BY nombres";
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
                $sql = "SELECT id_cliente
                        FROM sis_cliente
                        WHERE estado_mrcb 
                                AND numero_documento = :0  
                                AND ".($editando ? "id_cliente <> ".$this->id_cliente : "true");

                $existe = $this->BD->consultarFila($sql, [$this->numero_documento]);
                
                if ($existe == false){ //No existe
                    return false;
                }
                
            }
            
            $this->id_cliente = $existe["id_cliente"];
            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            
            $editando = $this->id_cliente != NULL;
            $esRepetido = $this->esRepetido($editando);
            if ($esRepetido){
                return [];
            }

            $campos_valores = [
                "nombres"=>$this->nombres,
                "celular"=>$this->celular,
                "numero_documento"=>$this->numero_documento
            ];
    
            $this->BD->beginTransaction();
            
            if ($editando){
                $campos_valores_where = ["id_cliente" => $this->id_cliente];
                $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);  
            } else {
                $this->BD->insert($this->main_table, $campos_valores);
                $this->id_cliente = $this->BD->getLastID();
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
                "id_cliente"=>$this->id_cliente
            ];

            $this->BD->update($this->main_table, $campos_valores, $campos_valores_where);
            return ["msj"=>"Anulado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}

