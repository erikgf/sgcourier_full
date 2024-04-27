<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/PedidoOrden.clase.php";
require_once "../modelo/util/funciones/Funciones.php";
require_once "../modelo/GlobalVariables.clase.php";

$op = isset($_GET["op"]) ? $_GET["op"] : "";
$objPedido = new PedidoOrden();

try {
    switch ($op) {
        case "buscar_codigo_tracking":
            $texto =  isset($_POST["p_buscar_texto"]) ? $_POST["p_buscar_texto"] : "";
            if ($texto == ""){
                 Funciones::imprimeJSON(200, "", "");
                 exit;
            }

            $data = $objPedido->buscarPorCodigoTracking($texto);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "buscar_codigo_tracking_web":
            $texto =  isset($_POST["p_buscar_texto"]) ? $_POST["p_buscar_texto"] : "";
            if ($texto == ""){
                 Funciones::imprimeJSON(200, "", "");
                 exit;
            }

            $data = $objPedido->buscarPorCodigoTrackingClientesGrandesWeb($texto);
            Funciones::imprimeJSON(200, "", $data);
        break;    
        
        default:
            Funciones::imprimeJSON(500, utf8_decode("Operación no válida."), "");
            exit;
    }    
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}