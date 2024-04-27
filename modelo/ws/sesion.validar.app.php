<?php

require_once "../negocio/Sesion.php";
require_once "../util/funciones/Funciones.php";

// Verificar que el usuario / app está enviando email y clave
if(!isset($_POST["correo"]) || !isset($_POST["clave"]))
{
    Funciones::imprimeJSON(500, "Faltan completar los datos requeridos", "");
    exit();
}

// Setear datos en variables
$correo = $_POST["correo"];
$clave = $_POST["clave"];

// Verificar credenciales de acceso
try
{
    $objSesion = new Sesion();
    $objSesion->setCorreo($correo);
    $objSesion->setClave($clave);
    $resultado = $objSesion->iniciarSesionApp();
    require_once 'token.generar.php';
    $token = generarToken(null, 60 * 60);
    $resultado["token"] =  $token;
    Funciones::imprimeJSON(200, "Bienvenido a la aplicación móvil", $resultado);
} catch (Exception $ex) 
{
    // Mostrar respuesta JSON, con el error que se ha generado
    Funciones::imprimeJSON(500, $ex->getMessage(), "");
}
