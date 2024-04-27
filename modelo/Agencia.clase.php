<?php 

require_once 'Modelo.clase.php';

class Agencia extends Modelo{
    public $id_agencia;
    public $descripcion;
    public $id_departamento;
    public $id_provincia;
    public $id_distrito;

	public function __construct($BD = null){
		try {
			parent::__construct("agencia", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}

    public function listar(){
        try{
            $sql = "SELECT id_agencia, descripcion, 
                            COALESCE(ud.name,'-') as departamento,
                            COALESCE(up.name,'-') as provincia,
                            COALESCE(udis.name,'-') as distrito
                        FROM agencia u
                        LEFT JOIN ubigeo_peru_departments ud ON u.ubigeo_departamento = ud.id
                        LEFT JOIN ubigeo_peru_provinces up ON u.ubigeo_provincia = up.id
                        LEFT JOIN ubigeo_peru_districts udis ON u.ubigeo = udis.id
                        WHERE u.estado_mrcb
                        ORDER BY descripcion";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try{

            $sql = "SELECT id_agencia, 
                        descripcion,
                        ubigeo as ubigeo_distrito,
                        ubigeo_provincia,
                        ubigeo_departamento
                        FROM agencia
                        WHERE id_agencia = :0 AND estado_mrcb";
            $data = $this->BD->consultarFila($sql, [$this->id_agencia]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function validarRepetido($tipo_accion){
        try{

            $editando = $tipo_accion == "editar";

            if (!($this->descripcion == "" || $this->descripcion == NULL)){
                $sql = "SELECT COUNT(id_agencia) as c 
                        FROM agencia 
                        WHERE estado_mrcb AND descripcion = :0  AND ".($editando ? "id_agencia <> ".$this->id_agencia : "true");

                $existe = $this->BD->consultarValor($sql, [$this->descripcion]);
                if ($existe > 0){
                    throw new Exception("El nombre de agencia ingresado ya existe.");
                }
            }

            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }   

    public function getset($tipo_accion){
        try
        {   

            $this->validarRepetido($tipo_accion);

            $campos_valores = [
                "descripcion"=>$this->descripcion,
                "ubigeo_departamento"=>$this->id_departamento,
                "ubigeo_provincia"=>$this->id_provincia,
                "ubigeo"=>$this->id_distrito,
            ];

            $campos_valores_where = [];
            if ($tipo_accion == "editar"){
                $campos_valores_where = ["id_agencia"=>$this->id_agencia];
            }

            return ["valores"=>$campos_valores, "valores_where"=>$campos_valores_where];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrar(){
        try
        {   
            $this->BD->beginTransaction();

            $campos = $this->getset("registrar");
            $this->BD->insert("agencia", $campos["valores"]);

            $this->BD->commit();
            return ["msj"=>"Registrado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function editar(){
        try
        {   
            $this->BD->beginTransaction();

            $campos = $this->getset("editar");
            $this->BD->update("agencia", $campos["valores"], $campos["valores_where"]);

            $this->BD->commit();
            return ["msj"=>"Actualizado correctamente."];
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
                "id_agencia"=>$this->id_agencia
            ];

            $this->BD->update("agencia", $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

	public function listarSelect(){
        try{

            $sql = "SELECT
                    id_agencia as id,
                    descripcion
                    FROM agencia
                    WHERE  estado_mrcb
                    ORDER BY descripcion";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    
    public function buscarSelect($texto_buscar){
        try{

            $sql = "SELECT
                    id_agencia as id,
                    descripcion as text
                    FROM agencia
                    WHERE estado_mrcb AND descripcion LIKE '%".$texto_buscar."%';
                    ORDER BY descripcion";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function getAgenciaUsuario($idUsuarioSesion){
        try{

            $sql = "SELECT 
                        a.descripcion
                    FROM usuario u
                    INNER JOIN agencia a ON u.id_agencia = a.id_agencia
                    WHERE u.id_usuario = :0 AND u.estado_mrcb
                    ORDER BY descripcion";

            $data = $this->BD->consultarFila($sql, [$idUsuarioSesion]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function getAgenciasUsuario($idUsuarioSesion){
        try{

            $sql = "SELECT 
                        a.descripcion
                    FROM usuario_agencia ua
                    INNER JOIN agencia a ON ua.id_agencia = a.id_agencia
                    WHERE ua.id_usuario = :0 AND u.estado_mrcb
                    ORDER BY descripcion";

            $data = $this->BD->consultarFila($sql, [$idUsuarioSesion]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    
}

