<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/PedidoOrden.clase.php";
require_once "../modelo/util/funciones/Funciones.php";
require_once "../modelo/GlobalVariables.clase.php";

$texto = isset($_GET["q"]) ? $_GET["q"] : "";
$objPedido = new PedidoOrden();

try {
    if ($texto == ""){
         Funciones::imprimeJSON(200, "", "");
         exit;
    }

    $data = $objPedido->buscarPorCodigoTracking($texto);
    Funciones::imprimeJSON(200, "", $data);
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}