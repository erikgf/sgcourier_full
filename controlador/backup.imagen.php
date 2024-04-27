<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../datos/variables.vista.php';
require_once "../modelo/BackupImagen.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

/*
$_sesion = isseT($_SESSION["sesion"]) ? $_SESSION["sesion"] : null;

if ($_sesion == null){
    Funciones::imprimeJSON(401, "No tiene permisos suficientes.", "");
    exit;
}
*/

$op = isset($_GET["op"]) ? $_GET["op"] : "";
$obj = new BackupImagen();
$hoy = date("Y-m-d");

try {
    switch ($op) {
        case 'generar_zip_mensual':
            /*
            $id_usuario = $_sesion["id_usuario"];
            $obj->id_usuario = $id_usuario;
            */

            $dia = isset($_POST["p_dia"]) ? $_POST["p_dia"] : $hoy;
            $mes = isset($_POST["p_mes"]) ? $_POST["p_mes"] : $hoy;
            $anio = isset($_POST["p_anio"]) ? $_POST["p_anio"] : $hoy;

            $data = $obj->generarZipImagenesPorMesAnio($dia, $mes, $anio);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'eliminar_imagenes_mensual':
            $dia = isset($_POST["p_dia"]) ? $_POST["p_dia"] : $hoy;
            $mes = isset($_POST["p_mes"]) ? $_POST["p_mes"] : $hoy;
            $anio = isset($_POST["p_anio"]) ? $_POST["p_anio"] : $hoy;

            $data = $obj->eliminarImagenes($dia, $mes, $anio);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        default:
            Funciones::imprimeJSON(500, "Operaci¨®n no v¨¢lida.", "");
            exit;
    }    
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}