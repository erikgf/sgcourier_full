<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/SisPreliquidacion.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisPreliquidacion();

try {

    switch ($op) {
        case "listar": //admin
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
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
                throw new Exception("Usuario no válido para hacer registros.");
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

            $data = $obj->listar($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;

            $obj->id_preliquidacion = (isset($_POST["p_id_preliquidacion"])) ? strtoupper($_POST["p_id_preliquidacion"]) : "";
           
            $obj->id_repartidor = (isset($_POST["p_id_repartidor"]) && $_POST["p_id_repartidor"] != "") ? strtoupper($_POST["p_id_repartidor"]) : "";
            if (!$obj->id_repartidor){
                throw new Exception("No se ha enviado REPARTIDOR.", 1);
            }
            $obj->fecha_registro = (isset($_POST["p_fecha_registro"]) && $_POST["p_fecha_registro"] != "") ? strtoupper($_POST["p_fecha_registro"]) : "";
            if (!$obj->fecha_registro){
                throw new Exception("No se ha enviado FECHA DE REGISTRO.", 1);
            }
            $obj->id_tipo_vehiculo = (isset($_POST["p_id_tipo_vehiculo"]) && $_POST["p_id_tipo_vehiculo"] != "") ? strtoupper($_POST["p_id_tipo_vehiculo"]) : "";
            if (!$obj->id_tipo_vehiculo){
                throw new Exception("No se ha enviado TIPO DE VEHÍCULO.", 1);
            }

            $obj->observaciones = (isset($_POST["p_observaciones"]) && $_POST["p_observaciones"] != "") ? strtoupper($_POST["p_observaciones"]) : "";
            $obj->costo_global = (isset($_POST["p_costo_global"]) && $_POST["p_costo_global"] != "") ? strtoupper($_POST["p_costo_global"]) : "0.00";
            
            $obj->registros_detalle = (isset($_POST["p_registros_detalle"]) && $_POST["p_registros_detalle"] != "") ? $_POST["p_registros_detalle"] : "[]";
            $obj->registros_detalle = json_decode($obj->registros_detalle, true);

            $data = $obj->registrar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "eliminar":
            $obj->id_preliquidacion = isset($_POST["p_id_preliquidacion"]) ? $_POST["p_id_preliquidacion"] : "";
            if ($obj->id_preliquidacion == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->eliminar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "leer":
            $obj->id_preliquidacion = isset($_POST["p_id_preliquidacion"]) ? $_POST["p_id_preliquidacion"] : "";
            if ($obj->id_preliquidacion == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->leer();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "obtener_imprimir":
            $obj->id_preliquidacion = isset($_POST["p_id_preliquidacion"]) ? $_POST["p_id_preliquidacion"] : "";
            if ($obj->id_preliquidacion == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->obtenerImprimir();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "importar_excel":
            if (count($_FILES) <= 0){
                throw new Exception("Se debe ingresar un archivo.");
            }
            $excel_file = $_FILES["excel_importar"];

            $tipo_archivo_valido = true;
            switch ($excel_file["type"]){
                case "application/vnd.ms-excel":
                case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                break;
                default:
                $tipo_archivo_valido = false;
                break;
            }

            if (!$tipo_archivo_valido){
                throw new Exception("No se puede procesar el archivo. Seleccione un formato válido; xls, xlsx.");
            }

            if ($excel_file["size"] > 5 * 1024 * 1024){ /*Nax 5 MB*/
                throw new Exception("No se puede procesar el archivo. El tamaño máximo por archivo es de 5MB");
            }

            $excel_file = [
                        "nombre"=>$excel_file["name"],
                        "tipo"=>$excel_file["type"],
                        "tamano"=>$excel_file["size"],
                        "archivo"=>$excel_file["tmp_name"]
                    ];

            $data = $obj->importarExcel($excel_file);
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