<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/SisIngreso.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisIngreso();

try {

    switch ($op) {
        case "listar": //admin
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer consultas.");
            }

            $id_agencia = isset($_POST["p_id_agencia"]) ? $_POST["p_id_agencia"] : "*";
            $obj->id_agencia = $id_agencia;

            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : NULL;
            if (!$fecha_inicio){
                throw new Exception("No se ha enviado FECHA DE INICIO.", 1);
            }

            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : NULL;
            if (!$fecha_fin){
                throw new Exception("No se ha enviado FECHA DE FIN.", 1);
            }

            $data = $obj->listar($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "listar_x_agencia":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer consultas.");
            }

            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : NULL;
            if (!$fecha_inicio){
                throw new Exception("No se ha enviado FECHA DE INICIO.", 1);
            }

            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : NULL;
            if (!$fecha_fin){
                throw new Exception("No se ha enviado FECHA DE FIN.", 1);
            }

            $obj->id_usuario_registro = $id_usuario;

            $data = $obj->listarPorAgencia($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;

            $obj->id_ingreso = (isset($_POST["p_id_ingreso"])) ? strtoupper($_POST["p_id_ingreso"]) : "";
           
            $obj->id_cliente = (isset($_POST["p_id_cliente"]) && $_POST["p_id_cliente"] != "") ? strtoupper($_POST["p_id_cliente"]) : "";
            if (!$obj->id_cliente){
                throw new Exception("No se ha enviado REPARTIDOR.", 1);
            }

            $obj->id_agencia_transporte = (isset($_POST["p_id_agencia_transporte"]) && $_POST["p_id_agencia_transporte"] != "") ? strtoupper($_POST["p_id_agencia_transporte"]) : "";
            if (!$obj->id_agencia_transporte){
                throw new Exception("No se ha enviado AGENCIA TRANSPORTE.", 1);
            }

            $obj->id_destino = (isset($_POST["p_id_destino"]) && $_POST["p_id_destino"] != "") ? strtoupper($_POST["p_id_destino"]) : "";
            if (!$obj->id_destino){
                throw new Exception("No se ha enviado AGENCIA DESTINO.", 1);
            }


            $obj->fecha_registro = (isset($_POST["p_fecha_registro"]) && $_POST["p_fecha_registro"] != "") ? strtoupper($_POST["p_fecha_registro"]) : "";
            if (!$obj->fecha_registro){
                throw new Exception("No se ha enviado FECHA DE REGISTRO.", 1);
            }

            $obj->costo = (isset($_POST["p_costo"]) && $_POST["p_costo"] != "") ? $_POST["p_costo"] : "0.00";
            $obj->cobrar = (isset($_POST["p_cobrar"]) && $_POST["p_cobrar"] != "") ? $_POST["p_cobrar"] : "0.00";
            $obj->pagado = (isset($_POST["p_pagado"]) && $_POST["p_pagado"] != "") ? $_POST["p_pagado"] : "0.00";

            $obj->estado = (isset($_POST["p_estado"]) && $_POST["p_estado"] != "") ? $_POST["p_estado"] : "E";

            $obj->registros_detalle = (isset($_POST["p_registros_detalle"]) && $_POST["p_registros_detalle"] != "") ? $_POST["p_registros_detalle"] : "[]";
            $obj->registros_detalle = json_decode($obj->registros_detalle, true);

            $data = $obj->registrar();
            Funciones::imprimeJSON(200, "", $data);
        break;


        case "registrar_destino":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;

            $obj->id_ingreso = (isset($_POST["p_id_ingreso"])) ? strtoupper($_POST["p_id_ingreso"]) : "";
            $obj->pagado = (isset($_POST["p_pagado"]) && $_POST["p_pagado"] != "") ? $_POST["p_pagado"] : "0.00";
            $obj->estado = (isset($_POST["p_estado"]) && $_POST["p_estado"] != "") ? $_POST["p_estado"] : "E";

            $data = $obj->registrarDestino();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "eliminar":
            $obj->id_ingreso = isset($_POST["p_id_ingreso"]) ? $_POST["p_id_ingreso"] : "";
            if ($obj->id_ingreso == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->eliminar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "leer":
            $obj->id_ingreso = isset($_POST["p_id_ingreso"]) ? $_POST["p_id_ingreso"] : "";
            if ($obj->id_ingreso == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->leer();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "obtener_imprimir":
            $obj->id_ingreso = isset($_POST["p_id_ingreso"]) ? $_POST["p_id_ingreso"] : "";
            if ($obj->id_ingreso == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->obtenerImprimir();
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