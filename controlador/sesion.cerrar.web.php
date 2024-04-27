<?php
require_once "../modelo/Usuario.clase.php";
require_once "../modelo/util/funciones/Funciones.php";
try
{   

    if (session_status() == PHP_SESSION_ACTIVE) { 
        $_SESSION = [];
        session_destroy();
    }
    Funciones::imprimeJSON(200, "", "");
} catch (Exception $ex) 
{
    Funciones::imprimeJSON(500, $ex->getMessage(), "");
}

