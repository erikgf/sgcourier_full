<?php
require_once '../negocio/UsuarioPago.php';
require_once '../util/funciones/Funciones.php';
require_once 'token.validar.php';

$_POST["token"] = "1";
//Validar si se recibe el parámetro token
if ( !isset($_POST["token"]) ){
    Funciones::imprimeJSON(500, "Falta completar datos", "");
    exit; //Detiene el avance del programa
}

//Recibir el token
$token = $_POST["token"];
try {
    //$objValidarToken = validarToken($token);
    $objValidarToken["r"] = TRUE;

    if ($objValidarToken["r"]){ //Si devuelve TRUE, significa que el token es válido
        $dataTOKEN = $objValidarToken["data"];
        $obj = new UsuarioPago();
        $operacion = $_GET["op"];

        switch ($operacion) {
            case 'registrar_pagos':
                if (!isset($_POST["p_opciones"]) || empty($_POST["p_opciones"])){
                    throw new Exception("No se ha ingresado opciones válidas.");
                }

                $arregloOpciones = json_decode($_POST["p_opciones"]);
                $data = $obj->registrarPagoRecibo($arregloOpciones);
            break;
            default:
            Funciones::imprimeJSON(500, "Operación no válida.", "");
            exit;
        }

        Funciones::imprimeJSON(200, "OK", $data);    
    }else{
        Funciones::imprimeJSON(500, "Token no es válido", "");
    }
} catch (Exception $exc) {
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}