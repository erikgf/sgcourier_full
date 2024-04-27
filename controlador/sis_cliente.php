<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/SisCliente.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisCliente();

try {

    switch ($op) {
        case "obtener":
            $data = $obj->obtener();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "registrar":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;
            $obj->id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : NULL;
            $obj->numero_documento = (isset($_POST["p_numero_documento"]) && $_POST["p_numero_documento"] != "") ? $_POST["p_numero_documento"] : "";
            if (!$obj->numero_documento){
                throw new Exception("No se ha enviado razón social o nombres de cliente.", 1);
            }

            $obj->nombres = (isset($_POST["p_nombres"]) && $_POST["p_nombres"] != "") ? strtoupper($_POST["p_nombres"]) : "";
            if (!$obj->nombres){
                throw new Exception("No se ha enviado razón social o nombres de cliente.", 1);
            }

            $obj->celular = (isset($_POST["p_celular"]) && $_POST["p_celular"] != "") ? $_POST["p_celular"] : NULL;
            $data = $obj->registrar();
            
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "anular":
            $obj->id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : "";
            if ($obj->id_cliente == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->anular();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "leer":
            $obj->id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : "";
            if ($obj->id_cliente == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->leer();
            Funciones::imprimeJSON(200, "", $data);
        break;
        default:
            Funciones::imprimeJSON(500, "Operación no válida.", "");
    }    

    exit;
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}