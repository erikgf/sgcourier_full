<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/SisAgenciaTransporte.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisAgenciaTransporte();

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
            $obj->id_agencia_transporte = isset($_POST["p_id_agenciatransporte"]) ? $_POST["p_id_agenciatransporte"] : NULL;

            $obj->nombre = (isset($_POST["p_nombre"]) && $_POST["p_nombre"] != "") ? strtoupper($_POST["p_nombre"]) : "";
            if (!$obj->nombre){
                throw new Exception("No se ha enviado razón social o nombre de agencia de transporte.", 1);
            }

            $obj->celular = (isset($_POST["p_celular"]) && $_POST["p_celular"] != "") ? $_POST["p_celular"] : NULL;
            $data = $obj->registrar();
            
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "anular":
            $obj->id_agencia_transporte = isset($_POST["p_id_agenciatransporte"]) ? $_POST["p_id_agenciatransporte"] : "";
            if ($obj->id_agencia_transporte == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->anular();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "leer":
            $obj->id_agencia_transporte = isset($_POST["p_id_agenciatransporte"]) ? $_POST["p_id_agenciatransporte"] : "";
            if ($obj->id_agencia_transporte == ""){
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