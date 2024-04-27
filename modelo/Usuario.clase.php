<?php 

require_once 'Modelo.clase.php';

class Usuario extends Modelo{
	public $id_usuario;
	public $tipo_documento;
	public $numero_documento;
	public $nombres;
	public $apellidos;
	public $celular;
	public $correo;
    public $id_agencias;
	public $username;
	public $password;
	public $id_tipo_usuario;
	public $es_ejecutivo_repartidor;
    public $estado_acceso;
    
    private $TIPO_USUARIO_EJECUTIVO_REPARTIDOR = 99;

	public function __construct($BD = null){
		try {
			parent::__construct("usuario", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}

	public function listar(){
        try{
            $sql = "SELECT id_usuario, 
                        COALESCE(numero_documento,'-') as numero_documento, 
                        CONCAT(nombres,' ',apellidos) as nombres_apellidos,
                        COALESCE(celular,'-') as celular,
                        COALESCE(correo,'-') as correo,
                        -- COALESCE(a.descripcion, '-') as agencia,
                        IF(u.es_ejecutivo_repartidor = 1, 'EJECUTIVO Y REPARTIDOR', tu.descripcion) as tipo_usuario,
                        estado_acceso as estado_acceso_key,
                        IF(estado_acceso = 'I', 'INACTIVO' , 'ACTIVO') as estado_acceso
                        FROM usuario u
                        LEFT JOIN tipo_usuario tu ON tu.id_tipo_usuario = u.id_tipo_usuario
                       -- LEFT JOIN agencia a ON a.id_agencia = u.id_agencia
                        WHERE u.estado_mrcb AND tu.colaborador_interno AND u.id_tipo_usuario <> 7";
            $data = $this->BD->consultarFilas($sql);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try{

            $sql = "SELECT id_usuario, 
                        numero_documento,
                        nombres,
                        apellidos, 
                        celular,
                        correo,
                        IF(es_ejecutivo_repartidor = 1, '99', id_tipo_usuario) as  id_tipo_usuario,
                        username,
                        estado_acceso
                        FROM usuario
                        WHERE id_usuario = :0 AND estado_mrcb AND id_tipo_usuario <> 4";
            $data = $this->BD->consultarFila($sql, [$this->id_usuario]);

            if ($data){
                $sql= "SELECT distinct id_agencia  as id_agencia FROM usuario_agencia WHERE id_usuario = :0";
                $data["id_agencias"] = $this->BD->consultarFilas($sql, [$this->id_usuario]);
            }

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function validarRepetido($tipo_accion){
        try{

            $editando = $tipo_accion == "editar";

            if (!($this->numero_documento == "" || $this->numero_documento == NULL)){
                $sql = "SELECT COUNT(id_usuario) as c 
                        FROM usuario 
                        WHERE estado_mrcb AND id_tipo_usuario <> 4 AND numero_documento = :0  AND ".($editando ? "id_usuario <> ".$this->id_usuario : "true");

                $existe = $this->BD->consultarValor($sql, [$this->numero_documento]);
                if ($existe > 0){
                    throw new Exception("El número de documento ingresado ya existe.");
                }
            }

            if (!($this->celular == "" || $this->celular == NULL)){
                $sql = "SELECT COUNT(id_usuario) as c 
                        FROM usuario 
                        WHERE estado_mrcb AND id_tipo_usuario <> 4 AND celular = :0  AND ".($editando ? "id_usuario <> ".$this->id_usuario : "true");

                $existe = $this->BD->consultarValor($sql, [$this->celular]);
                if ($existe > 0){
                    throw new Exception("El celular ingresado ya existe.");
                }
            }

            if (!($this->correo == "" || $this->correo == NULL)){
                $sql = "SELECT COUNT(id_usuario) as c 
                        FROM usuario 
                        WHERE estado_mrcb AND id_tipo_usuario <> 4 AND correo = :0  AND ".($editando ? "id_usuario <> ".$this->id_usuario : "true");

                $existe = $this->BD->consultarValor($sql, [$this->correo]);
                if ($existe > 0){
                    throw new Exception("El correo ingresado ya existe.");
                }
            }

            if (!($this->username == "" || $this->username == NULL)){
                $sql = "SELECT COUNT(id_usuario) as c 
                        FROM usuario 
                        WHERE estado_mrcb AND id_tipo_usuario <> 4 AND username = :0  AND ".($editando ? "id_usuario <> ".$this->id_usuario : "true");

                $existe = $this->BD->consultarValor($sql, [$this->username]);
                if ($existe > 0){
                    throw new Exception("El nombre de usuario ingresado ya existe.");
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
            
            if ($this->id_tipo_usuario == $this->TIPO_USUARIO_EJECUTIVO_REPARTIDOR){
                $this->id_tipo_usuario = 2;
                $this->es_ejecutivo_repartidor  = 1;
            } else{
                $this->es_ejecutivo_repartidor  = NULL;
            }

            $campos_valores = [
                "numero_documento"=>$this->numero_documento,
                "nombres"=>$this->nombres,
                "apellidos"=>$this->apellidos,
                "celular"=>$this->celular,
                "correo"=>$this->correo,
                "id_tipo_usuario"=>$this->id_tipo_usuario,
                "es_ejecutivo_repartidor"=>$this->es_ejecutivo_repartidor,
                "username" => $this->username,
                "estado_acceso"=>$this->estado_acceso
            ];

            $campos_valores_where = [];
            if ($tipo_accion == "editar"){
                $campos_valores_where = ["id_usuario"=>$this->id_usuario];
            } else {
                $campos_valores["password"] = hash("sha256",$this->password);
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
            $this->BD->insert("usuario", $campos["valores"]);
            $this->id_usuario = $this->BD->getLastID();
            
            foreach ($this->id_agencias as $key => $id_agencia) {
                $this->BD->insert("usuario_agencia", [
                    "id_usuario"=>$this->id_usuario,
                    "id_agencia"=>$id_agencia
                ]);
            }

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
            $this->BD->update("usuario", $campos["valores"], $campos["valores_where"]);
            $this->BD->delete("usuario_agencia", ["id_usuario"=>$this->id_usuario]);
            foreach ($this->id_agencias as $key => $id_agencia) {
                $this->BD->insert("usuario_agencia", [
                    "id_usuario"=>$this->id_usuario,
                    "id_agencia"=>$id_agencia
                ]);
            }

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
                "id_usuario"=>$this->id_usuario
            ];

            $this->BD->update("usuario", $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function cambiarClave($password_anterior, $password_nueva){
        try
        {   
            $sql = "SELECT COUNT(id_usuario) FROM usuario WHERE password = :0 AND id_usuario = :1";
            $existeClaveAnterior = $this->BD->consultarValor($sql, [hash("sha256",$password_anterior), $this->id_usuario]);

            if ($existeClaveAnterior <= 0){
                throw new Exception("Debe ingresar su clave anterior.");
            }
            
            $this->BD->beginTransaction();

            $campos_valores = [
               "password"=>hash("sha256",$password_nueva)
            ];

            $campos_valores_where = [
                "id_usuario"=>$this->id_usuario
            ];

            $this->BD->update("usuario", $campos_valores, $campos_valores_where);
            $this->BD->commit();
            return ["msj"=>"Clave cambiada correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function cambiarClaveAdmin($password_nueva){
        try
        {   
            $sql = "SELECT COUNT(id_usuario) FROM usuario WHERE id_usuario = :0 AND estado_mrcb";
            $existe = $this->BD->consultarValor($sql, [$this->id_usuario]);

            if ($existe <= 0){
                throw new Exception("Usuario no encontrado.");
            }
            
            $campos_valores = [
                "password"=>hash("sha256",$password_nueva)
            ];

            $campos_valores_where = [
                "id_usuario"=>$this->id_usuario
            ];

            $this->BD->update("usuario", $campos_valores, $campos_valores_where);

            $sql = "SELECT CONCAT(nombres, apellidos) as nombres_apellidos 
                    FROM usuario WHERE id_usuario = :0";
            $nombres_apellidos = $this->BD->consultarValor($sql, [$this->id_usuario]);

            return ["msj"=>"Clave cambiada correctamente, usuario: ".$nombres_apellidos];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarTiposUsuario(){
        try{
            $sql = "SELECT id_tipo_usuario as id, 
                        descripcion
                        FROM tipo_usuario
                        WHERE estado_mrcb";
            $data = $this->BD->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function iniciarSesionWeb(){
        try
        {   
            $ID_TIPO_USUARIO_COLABORADOR = 3;
            $sql  = "SELECT id_usuario, COALESCE(razon_social, CONCAT(nombres,' ',apellidos)) as nombre_usuario, password, estado_acceso,
                        tu.descripcion as tipo_usuario,
                        tu.id_tipo_usuario
                        FROM usuario u
                        LEFT JOIN tipo_usuario tu ON tu.id_tipo_usuario = u.id_tipo_usuario
                        WHERE u.username = :0 AND u.id_tipo_usuario = :1 AND u.estado_mrcb AND u.id_tipo_usuario <> :2 AND u.estado_acceso = 'A'";

            $registro = $this->BD->consultarFila($sql, [$this->username, $this->id_tipo_usuario, $ID_TIPO_USUARIO_COLABORADOR]);

            if ($registro == false){
                throw new Exception("Usuario no encontrado.");
            }

            if ($registro["password"] != hash("sha256", $this->password)){
                throw new Exception("Contraseña incorrecta.");
            }

            if ($registro["estado_acceso"]  != "A"){
                throw new Exception("Usuario inhabilitado.");
            }

            switch ($this->id_tipo_usuario) {
                case '0':
                case '1':
                    $url_go = "gestionar_pedidos/index.leonisa.admin.nuevo.php";
                    break;
                case '2':
                    $url_go = "gestionar_pedidos/index.leonisa.admin.nuevo.php";
                    break;
                case '4':
                    $url_go = "gestionar_pedidos/";
                    break;
                case '6':
                    $url_go = "gestionar_poder_judicial/";
                    break;
                case '7':
                    $url_go = "gestionar_preliquidaciones_admin/";
                    break;
                default:
                    break;
            }

           $data = [
                        "id_usuario"=>$registro["id_usuario"],
                        "nombre_usuario"=>$registro["nombre_usuario"],
                        "tipo_usuario"=>$registro["tipo_usuario"],
                        "id_tipo_usuario"=>$registro["id_tipo_usuario"]];

            return ["msj"=>"OK", "data"=>$data, "url_go"=>$url_go];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function iniciarSesionApp(){
        try
        {   
            $ID_TIPO_USUARIO_ADMIN = 1;
            $ID_TIPO_USUARIO_EJECUTIVO = 2;
            $ID_TIPO_USUARIO_COLABORADOR = 3;

            $sql  = "SELECT id_usuario, COALESCE(razon_social, CONCAT(nombres,' ',apellidos)) as nombre_usuario, password, estado_acceso,
                        tu.descripcion as tipo_usuario,
                        tu.id_tipo_usuario
                        FROM usuario u
                        LEFT JOIN tipo_usuario tu ON tu.id_tipo_usuario = u.id_tipo_usuario
                        WHERE u.username = :0 AND (u.id_tipo_usuario = :1  OR (u.id_tipo_usuario = :2 AND u.es_ejecutivo_repartidor) OR u.id_tipo_usuario = :3)
                                AND u.estado_mrcb AND u.estado_acceso = 'A'";

            $registro = $this->BD->consultarFila($sql, [$this->username, $ID_TIPO_USUARIO_COLABORADOR, $ID_TIPO_USUARIO_EJECUTIVO, $ID_TIPO_USUARIO_ADMIN]);

            if ($registro == false){
                throw new Exception("Usuario no encontrado.");
            }

            if ($registro["password"] != hash("sha256", $this->password)){
                throw new Exception("Contraseña incorrecta.");
            }

            if ($registro["estado_acceso"]  != "A"){
                throw new Exception("Usuario inhabilitado.");
            }

           $data = [
                        "id_usuario"=>$registro["id_usuario"],
                        "nombre_usuario"=>$registro["nombre_usuario"],
                        "tipo_usuario"=>$registro["tipo_usuario"],
                        "id_tipo_usuario"=>$registro["id_tipo_usuario"]];

            return ["msj"=>"OK", "data"=>$data];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}


