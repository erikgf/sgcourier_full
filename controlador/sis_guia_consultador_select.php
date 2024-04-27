<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$texto_buscar =  isset($_POST["p_buscar_texto"]) ? $_POST["p_buscar_texto"] : "";
try {

    $mensaje = "";
    $data = [];

    switch ($op) {
        case 'listar_select_area_rango_fechas':
            require_once "../modelo/SisGuiaArea.clase.php";
            $obj = new SisGuiaArea();
            $hoy = date("Y-m-d");
            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;

            $data = $obj->obtenerAreasRangoFechas($fecha_inicio, $fecha_fin);
            break;
        case 'listar_select_area':
            require_once "../modelo/SisGuiaArea.clase.php";
            $obj = new SisGuiaArea();
            $data = $obj->obtenerAreas($texto_buscar);
            break;
        case 'listar_select_remitente':
            require_once "../modelo/SisGuiaRemitente.clase.php";
            $obj = new SisGuiaRemitente();
            $data = $obj->obtenerRemitentes($texto_buscar);
            break;
        case 'listar_select_consignatario':
            require_once "../modelo/SisGuiaConsignatario.clase.php";
            $obj = new SisGuiaConsignatario();
            $data = $obj->obtenerConsignatarios($texto_buscar);
            break;
        case 'listar_select_destino':
            require_once "../modelo/SisGuiaDestino.clase.php";
            $obj = new SisGuiaDestino();
            $data = $obj->obtenerDestinos($texto_buscar);
            break;
        default:
            throw new Exception("Operación no válida.");
    }

    Funciones::imprimeJSON(200, $mensaje, $data);
    exit;
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}