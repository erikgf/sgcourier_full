<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/Usuario.clase.php";
//require_once '../modelo/GlobalVariables.clase.php';
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new Usuario();

try {

    switch ($op) {
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "leer":
            $obj->id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : "";
            $data = $obj->leer();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar":
            $obj->numero_documento = (isset($_POST["p_numero_documento"]) && $_POST["p_numero_documento"] != "") ? $_POST["p_numero_documento"] : "";
            $obj->nombres = (isset($_POST["p_nombres"]) && $_POST["p_nombres"] != "") ? $_POST["p_nombres"] : "";
            $obj->apellidos = (isset($_POST["p_apellidos"]) && $_POST["p_apellidos"] != "") ? $_POST["p_apellidos"] : "";
            $obj->correo = (isset($_POST["p_correo"]) && $_POST["p_correo"] != "") ? $_POST["p_correo"] : "";
            $obj->celular = (isset($_POST["p_celular"]) && $_POST["p_celular"] != "") ? $_POST["p_celular"] : "";
            $obj->id_tipo_usuario = (isset($_POST["p_tipo_usuario"]) && $_POST["p_tipo_usuario"] != "") ? $_POST["p_tipo_usuario"] : "";
            $obj->id_agencias = (isset($_POST["p_agencias"]) && $_POST["p_agencias"] != "") ? $_POST["p_agencias"] : [];
            $obj->estado_acceso = (isset($_POST["p_estado_acceso"]) && $_POST["p_estado_acceso"] != "") ? $_POST["p_estado_acceso"] : "";
            $obj->username = (isset($_POST["p_username"]) && $_POST["p_username"] != "") ? $_POST["p_username"] : "";
            $obj->password = (isset($_POST["p_clave"]) && $_POST["p_clave"] != "") ? $_POST["p_clave"] : "";

            $data = $obj->registrar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "editar":
            $obj->id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : "";
            $obj->numero_documento = (isset($_POST["p_numero_documento"]) && $_POST["p_numero_documento"] != "") ? $_POST["p_numero_documento"] : "";
            $obj->nombres = (isset($_POST["p_nombres"]) && $_POST["p_nombres"] != "") ? $_POST["p_nombres"] : "";
            $obj->apellidos = (isset($_POST["p_apellidos"]) && $_POST["p_apellidos"] != "") ? $_POST["p_apellidos"] : "";
            $obj->correo = (isset($_POST["p_correo"]) && $_POST["p_correo"] != "") ? $_POST["p_correo"] : "";
            $obj->celular = (isset($_POST["p_celular"]) && $_POST["p_celular"] != "") ? $_POST["p_celular"] : "";
            $obj->id_tipo_usuario = (isset($_POST["p_tipo_usuario"]) && $_POST["p_tipo_usuario"] != "") ? $_POST["p_tipo_usuario"] : "";
            $obj->id_agencias = (isset($_POST["p_agencias"]) && $_POST["p_agencias"] != "") ? $_POST["p_agencias"] : [];
            $obj->estado_acceso = (isset($_POST["p_estado_acceso"]) && $_POST["p_estado_acceso"] != "") ? $_POST["p_estado_acceso"] : "";
            $obj->username = (isset($_POST["p_username"]) && $_POST["p_username"] != "") ? $_POST["p_username"] : "";

            $data = $obj->editar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "eliminar":
            $obj->id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : "";
            $data = $obj->eliminar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "cambiar_clave":
            $obj->id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;
            if ($obj->id_usuario == NULL){
                throw new Exception("Usuario no válido");
            }

            $clave_anterior = isset($_POST["p_clave_anterior"]) ? $_POST["p_clave_anterior"] : "";
            $clave_nueva = isset($_POST["p_clave_nueva"]) ? $_POST["p_clave_nueva"] : "";

            if ($clave_anterior == $clave_nueva){
                throw new Exception("La clave nueva debe ser distinta a la antigua.");
            }

            $data = $obj->cambiarClave($clave_anterior, $clave_nueva);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "cambiar_clave_admin":
            $obj->id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : "";
            if ($obj->id_usuario == ""){
                throw new Exception("Usuario no válido");
            }
            
            $clave_nueva = isset($_POST["p_nueva_clave"]) ? $_POST["p_nueva_clave"] : "";
             if ($obj->id_usuario == ""){
                throw new Exception("Clave no válida");
            }
            $data = $obj->cambiarClaveAdmin($clave_nueva);
            Funciones::imprimeJSON(200, "", $data);
        break;
        default:
            Funciones::imprimeJSON(500,  GlobalVariables::STR_OPERACION_VALIDA  , "");
    }    

    exit;
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}