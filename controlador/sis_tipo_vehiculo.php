<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/SisTipoVehiculo.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisTipoVehiculo();

try {

    switch ($op) {
        case 'buscar_select':
            $texto_buscar =  isset($_POST["p_buscar_texto"]) ? $_POST["p_buscar_texto"] : "";
            $data = $obj->buscarSelect($texto_buscar);
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