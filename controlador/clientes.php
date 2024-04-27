<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/Cliente.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$objPedido = new Cliente();

try {

    switch ($op) {
        case 'listar_select':
            $data = $objPedido->listarSelect();
            Funciones::imprimeJSON(200, "", $data);
            break;
        case 'listar_clientes_app':
            $data = $objPedido->listarClientesApp();
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