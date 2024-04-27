<?php

require_once 'configuracion.php';

class Conexion 
{
    protected $dblink;
    protected $transactionCounter = 0;
    /* Permite que cuando se cree un objeto de tipo
     * conexion, automáticamente se establezca una
     * conexion a la base de datos.
    */
    public function __construct() 
    {
        $this->abrirConexion();
    }
    
    /**
     * Cuando se deje de utilizar la conexion, liberar
     * la conexion para evitar que esta se mantenga activa
     * y consuma recursos de nuestra base de datos
     */
    public function __destruct() 
    {
        $this->dblink = NULL;
    }
    
    /**
     * Permite abrir la conexion a la base de datos.
     * Setea la variable $dblink con la conexion
     */
    protected function abrirConexion()
    {
        // Cadena de conexion
        $servidor = TIPO_BD == "postgres" ?
                         "pgsql:host=".SERVIDOR_BD.";port=".PUERTO_BD.";dbname=".NOMBRE_BD : //PGSQL
                         'mysql:host='.SERVIDOR_BD.';port='.PUERTO_BD.';dbname='.NOMBRE_BD; //MYSQL

        $usuario = USUARIO_BD;
        $clave = CLAVE_BD;
        
        try {
            $this->dblink = TIPO_BD == "postgres" ?
                            new PDO($servidor, $usuario, $clave) : //PGSQL
                            new PDO($servidor, $usuario, $clave,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); //MYSQL

            $this->dblink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $ex) 
        {   
            throw $ex;
        }
        
        // Retornar conexion exitosa
        return $this->dblink;
    }

    public function setDBLink($dblink){
        if ($this->dblink){
            $this->dblink = NULL;
        }
        $this->dblink = $dblink;
    }

    public function getLastID(){
         return $this->dblink->lastInsertId();
    }

    public function getDB(){
        return $this->dblink;
    }

    public function beginTransaction()
    {
       if(!$this->transactionCounter++)
            return  $this->dblink->beginTransaction();    
       return $this->transactionCounter >= 0;
    }
    
    public function commit()
    {
       if(!--$this->transactionCounter)
           return $this->dblink->commit();
       return $this->transactionCounter >= 0;
    }
    
    public function rollBack()
    {
        if($this->transactionCounter >= 0)
        {
            $this->transactionCounter = 0;
            return $this->dblink->rollback();
        }
        $this->transactionCounter = 0;
        return false;
    }

    public function consulta($p_consulta)
    {
        $consulta = $this->dblink->prepare($p_consulta);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consulta_raw($p_consulta)
    {
        $consulta = $this->dblink->prepare($p_consulta);
        return $consulta->execute();
    }

    private function reformatEne($key){
        $r = str_replace("ñ","n",$key);
        return $r;
    }
    
    public function insert($p_nombre_tabla, $p_campos_valores)
    {
        $p_campos = array_keys($p_campos_valores);
        $p_valores = array_values($p_campos_valores);

        $sql_campos = implode(",", $p_campos);
        $sql_valores = $this->sentenciaPreparada($p_campos);
        
        $consulta = $this->dblink->prepare("INSERT INTO $p_nombre_tabla ($sql_campos) VALUES ($sql_valores)");
        
        for ($i = 0; $i < count($p_campos); $i++) {
            $consulta->bindParam(':'.$this->reformatEne($p_campos[$i]), $p_valores[$i]);
        }
        
        return $consulta->execute();
    }

    /*un insert que hara muchos registros.*/
    public function insertMultiple($p_nombre_tabla, $p_campos, $p_valores_arreglo)
    {
        /*
        "INSERT INTO XXX(a,b,c,d) VALUES
        (:a00,:b01,:c02,:d13),
        (:a10,:b11,:c12,:d13);";
        */

        $sql_campos = implode(",", $p_campos);
        $sql_valores = "";

        $cantidadValores = count($p_valores_arreglo);
        $cantidadCols = count($p_campos);

         for ($i=0; $i < $cantidadValores ; $i++) { 
            if ($i > 0){
              $sql_valores .= ",\n";
            }
            $sql_valores .= "(";
            for ($j=0; $j <count($p_campos); $j++) { 
                if ($j > 0){
                    $sql_valores .= ",";
                }
                $sql_valores .= (":".$this->reformatEne($p_campos[$j])).$i.$j;
            }
            $sql_valores .= ")";
        }
        $sql_valores .= ";";

        $consulta = $this->dblink->prepare("INSERT INTO $p_nombre_tabla ($sql_campos) VALUES \n $sql_valores");

        for ($i=0; $i < $cantidadValores ; $i++) { 
            for ($j=0; $j < $cantidadCols; $j++) { 
                $consulta->bindParam(':'.$this->reformatEne($p_campos[$j]).$i.$j, $p_valores_arreglo[$i][$j]);
            }
        }

        return $consulta->execute();
    }

    public function update($p_nombre_tabla, $p_campos_valores, $p_campos_valores_where = null)
    {
        $p_campos = array_keys($p_campos_valores);
        $p_valores = array_values($p_campos_valores);
        $p_campos_where = isset($p_campos_valores_where) ? array_keys($p_campos_valores_where) : null;
        $p_valores_where = isset($p_campos_valores_where) ? array_values($p_campos_valores_where) : null;

        $sql_campos = $this->sentenciaPreparadaUpdate($p_campos);

        if (isset($p_campos_where)){
            $sql_campos_where = $this->sentenciaPreparadaAND($p_campos_where);
        } else {
            $sql_campos_where = true;
        }

        $sql = "UPDATE $p_nombre_tabla SET $sql_campos WHERE $sql_campos_where";
                
        $consulta = $this->dblink->prepare($sql);
        
        for ($i = 0; $i < count($p_campos); $i++) {
            $consulta->bindParam(':'.$this->reformatEne($p_campos[$i]), $p_valores[$i]);
        }

        
        if (isset($p_valores_where)){
            for ($i = 0; $i < count($p_campos_where); $i++) {
                $consulta->bindParam(':'.$this->reformatEne($p_campos_where[$i]), $p_valores_where[$i]);
            }
        } 
        
        return $consulta->execute();
    }

    public function delete($p_nombre_tabla, $p_campos_valores_where)
    {
        $p_campos_where = isset($p_campos_valores_where) ? array_keys($p_campos_valores_where) : null;
        $p_valores_where = isset($p_campos_valores_where) ? array_values($p_campos_valores_where) : null;

        if (isset($p_campos_where)){
            $sql_campos_where = $this->sentenciaPreparadaAND($p_campos_where);
        } else {
            $sql_campos_where = true;
        }

        $consulta = $this->dblink->prepare("DELETE FROM $p_nombre_tabla WHERE $sql_campos_where");
        
        if (isset($p_valores_where)){
            for ($i = 0; $i < count($p_campos_where); $i++) {                
                
                $consulta->bindParam(':'.$this->reformatEne($p_campos_where[$i]), $p_valores_where[$i]);
            }
        } 
        
        return $consulta->execute();
    }
    
    private function sentenciaPreparadaUpdate($array)
    {
        $sql_temp = "";
        for ($i = 0; $i < count($array); $i++) {
            if ($i == count($array)-1)
            {
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]);
            } else{
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]) . ", ";
            }
        }
        return $sql_temp;
    }
    
    private function sentenciaPreparadaAND($array)
    {
        $sql_temp = "";
        for ($i = 0; $i < count($array); $i++) {
            if ($i == count($array)-1)
            {
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]);
            } else{
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]) . " AND ";
            }
        }
        return $sql_temp;
    }
    
    private function sentenciaPreparada($array)
    {
        $sql_temp = "";
        for ($i = 0; $i < count($array); $i++) {
            if ($i == count($array)-1)
            {
                $sql_temp = $sql_temp . ":" .$this->reformatEne($array[$i]);
            } else{
                $sql_temp = $sql_temp . ":" .$this->reformatEne($array[$i]) . ", ";
            }
        }
        return $sql_temp;
    }

    private function consulta_x($sql,$valores,$tipo,$fech = null)
    {
        $consulta = $this->dblink->prepare($sql);

        if ($valores != null){
            $valores = is_array($valores) ? $valores : [$valores];
            for ($i = 0; $i < count($valores); $i++){
                $consulta->bindParam(":".$i, $valores[$i]);
            }
        }

        $consulta->execute();

        switch($tipo){
            case "*":
                return $consulta->fetchAll(PDO::FETCH_ASSOC);
            case "1*":
                return $consulta->fetch(PDO::FETCH_ASSOC);
            case "1":
                return $consulta->fetch(PDO::FETCH_NUM)[0];
        }

        return false;
    }

    public function consultarValor($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"1");
    }

    public function consultarFila($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"1*");
    }

    public function consultarFilas($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"*");
    }

    public function consultarExiste($tabla, $campo_valor)
    {
        $campo = key($campo_valor); $valor = $campo_valor[key($campo_valor)];
        $sql = "SELECT COUNT(".$campo.") > 0 FROM ".$tabla." WHERE ".$campo." = :0";
        return $this->consultarValor($sql, array($valor));
    }

    public function ejecutar_raw($p_sql,  $params = null)
    {
        $consulta = $this->dblink->prepare($p_sql);

        if ($params != null){
            $params = is_array($params) ? $params : [$params];
            for ($i = 0; $i < count($params); $i++){
                $consulta->bindParam(":".$i, $params[$i]);
            }
        }

       return $consulta->execute();
    }
    
}
