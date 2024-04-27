<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/Ubigeo.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new Ubigeo();

try {

    switch ($op) {
        case "listar_departamentos":
            $data = $obj->listarDepartamentos();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "listar_provincias":
            $obj->id_departamento = isset($_POST["id_dp"]) ? $_POST["id_dp"] : NULL;
            if (!$obj->id_departamento){
                throw new Exception("ID Departamento no válido.", 1);
            }
            $data = $obj->listarProvincias();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "listar_distritos":
            $obj->id_departamento = isset($_POST["id_dp"]) ? $_POST["id_dp"] : "";
            $obj->id_provincia = isset($_POST["id_pr"]) ? $_POST["id_pr"] : "";
            $data = $obj->listarDistritos();
            Funciones::imprimeJSON(200, "", $data);
        break;
        default:
            Funciones::imprimeJSON(500,  GlobalVariables::STR_OPERACION_VALIDA  , "");
    }    

    exit;
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}