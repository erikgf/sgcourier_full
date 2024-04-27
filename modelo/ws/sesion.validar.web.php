<?php

require_once "../negocio/Sesion.php";
require_once "../util/funciones/Funciones.php";

// Verificar que el usuario / app está enviando email y clave
if(!isset($_POST["p_numero_documento"]) || !isset($_POST["p_clave"]))
{
    Funciones::imprimeJSON(500, "Faltan completar los datos requeridos", "");
    exit();
}

// Setear datos en variables
$p_numero_documento = $_POST["p_numero_documento"];
$clave = $_POST["p_clave"];

// Verificar credenciales de acceso
try
{
    $objSesion = new Sesion();
    $objSesion->setNumeroDocumento($p_numero_documento);
    $objSesion->setClave($clave);
    $resultado = $objSesion->iniciarSesionWeb();
    require_once 'token.generar.php';
    $token = generarToken(json_encode($resultado), 60 * 60);
    $resultado["token"] =  $token;

    $_SESSION["sesion"] = $resultado;
    Funciones::imprimeJSON(200, "Bienvenido a la aplicación", $resultado);
} catch (Exception $ex) 
{
    // Mostrar respuesta JSON, con el error que se ha generado
    Funciones::imprimeJSON(500, $ex->getMessage(), "");
}
