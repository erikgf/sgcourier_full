<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../datos/variables.vista.php';
require_once "../modelo/EstadoOrden.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$_sesion = isseT($_SESSION["sesion"]) ? $_SESSION["sesion"] : null;

if ($_sesion == null){
    Funciones::imprimeJSON(401, "No tiene permisos suficientes.", "");
    exit;
}

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$objEstadoOrden = new EstadoOrden();
$hoy = date("Y-m-d");

try {
    switch ($op) {
        case 'obtener':
            $data = $objEstadoOrden->obtener();
            Funciones::imprimeJSON(200, "", $data);
            exit;
        default:
            Funciones::imprimeJSON(500, "Operación no válida.", "");
            exit;
    }    
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}