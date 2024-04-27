<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/Agencia.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new Agencia();

try {

    switch ($op) {
        case 'listar_select':
            $data = $obj->listarSelect();
            Funciones::imprimeJSON(200, "", $data);
            break;
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "leer":
            $obj->id_agencia = isset($_POST["id_agencia"]) ? $_POST["id_agencia"] : "";
            $data = $obj->leer();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar":
            $obj->descripcion = (isset($_POST["p_descripcion"]) && $_POST["p_descripcion"] != "") ? $_POST["p_descripcion"] : NULL;
            $obj->id_departamento = (isset($_POST["p_departamento"]) && $_POST["p_departamento"] != "") ? $_POST["p_departamento"] : NULL;
            $obj->id_provincia = (isset($_POST["p_provincia"]) && $_POST["p_provincia"] != "") ? $_POST["p_provincia"] : NULL;
            $obj->id_distrito = (isset($_POST["p_distrito"]) && $_POST["p_distrito"] != "") ? $_POST["p_distrito"] : NULL;

            $data = $obj->registrar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "editar":
            $obj->id_agencia = isset($_POST["id_agencia"]) ? $_POST["id_agencia"] : "";
            $obj->descripcion = (isset($_POST["p_descripcion"]) && $_POST["p_descripcion"] != "") ? $_POST["p_descripcion"] : "";
            $obj->id_departamento = (isset($_POST["p_departamento"]) && $_POST["p_departamento"] != "") ? $_POST["p_departamento"] : NULL;
            $obj->id_provincia = (isset($_POST["p_provincia"]) && $_POST["p_provincia"] != "") ? $_POST["p_provincia"] : NULL;
            $obj->id_distrito = (isset($_POST["p_distrito"]) && $_POST["p_distrito"] != "") ? $_POST["p_distrito"] : NULL;

            $data = $obj->editar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "eliminar":
            $obj->id_agencia = isset($_POST["id_agencia"]) ? $_POST["id_agencia"] : "";
            $data = $obj->eliminar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case 'buscar_select':
            $texto_buscar =  isset($_POST["p_buscar_texto"]) ? $_POST["p_buscar_texto"] : "";
            $data = $obj->buscarSelect($texto_buscar);
            Funciones::imprimeJSON(200, "", $data);
            break;

        case "get_agencia_usuario":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            $data = $obj->getAgenciaUsuario($id_usuario);
            Funciones::imprimeJSON(200, "", $data);
            break;

        case "get_agencias_usuario":
                $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;
    
                $data = $obj->getAgenciaUsuario($id_usuario);
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