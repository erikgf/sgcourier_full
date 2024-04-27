<?php

require_once "../modelo/Usuario.clase.php";
//require_once '../modelo/Token.clase.php';
require_once "../modelo/util/funciones/Funciones.php";

if(!isset($_POST["p_usuario"]) || !isset($_POST["p_clave"]))
{
    Funciones::imprimeJSON(500, "Faltan completar los datos requeridos", "");
    exit();
}

// Setear datos en variables
$id_tipo_usuario = "3";
$usuario = $_POST["p_usuario"];
$clave = $_POST["p_clave"];

// Verificar credenciales de acceso
try
{
    $objUsuario = new Usuario();
    $objUsuario->id_tipo_usuario = $id_tipo_usuario;
    $objUsuario->username = $usuario;
    $objUsuario->password = $clave;

    $resultado = $objUsuario->iniciarSesionApp();
    
    $_SESSION["sesion"] = $resultado["data"];
    
    Funciones::imprimeJSON(200, "", $resultado);
} catch (Exception $ex) 
{
    // Mostrar respuesta JSON, con el error que se ha generado
    Funciones::imprimeJSON(500, $ex->getMessage(), "");
}
