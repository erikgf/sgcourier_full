<?php

//Requerir al archivo Funciones.php que contiene los métodos para generar el JSON
require_once '../util/funciones/Funciones.php';

if (! isset($_POST["metodo"]) ){
    Funciones::imprimeJSON(500, "Falta el método del API.", "");
    exit;
}

if (! isset($_POST["modelo"]) ){
    Funciones::imprimeJSON(500, "Falta el modelo para el API.", "");
    exit;
}

$modelo  = $_POST["modelo"];
require_once "../negocio/".utf8_decode($modelo).".php";
$obj = new $modelo;
$metodo = $_POST["metodo"];
//Recibir el token


$data_in = isset($_POST["data_in"]) ? $_POST["data_in"] : null; //parametros que son parte de la clase.
$data_out = isset($_POST["data_out"]) ? $_POST["data_out"] : null; //parámetros q no son parte de la clase.

if(is_callable(array($obj, $metodo))){
    try {
            if ($data_out == "formulario"){
                $data_out = null;       
                parse_str($data_in , $datosFormularioArray);        
                foreach ($datosFormularioArray as $key=>$valor) {
                    $str = "set".ucfirst(substr($key, 3));
                    if (method_exists($obj,$str)){
                        $obj->$str($valor);
                    }
                }
            } 
            else {
                if ($data_in != null){
                    //recorrer el arreglo y asignar todo lo posible.
                    foreach ($data_in as $key=>$valor) {
                        $str = "set".ucfirst(substr($key, 2));
                            $obj->$str($valor);            
                    }
                }
            }
   
        $rpta = call_user_func_array(
          array($obj, $metodo), $data_out == null ? array() : $data_out
        );
            // $obj->$metodo($data_out);
        Funciones::imprimeJSON(200, $rpta["msj"], $rpta["data"] == null ? "" : $rpta["data"]);

    } catch (Exception $exc) {
        Funciones::imprimeJSON(500, $exc->getMessage(), "");
    }
}else{
    Funciones::imprimeJSON(500, "El método '$metodo' de la clase '$modelo' no existe.", "");
}