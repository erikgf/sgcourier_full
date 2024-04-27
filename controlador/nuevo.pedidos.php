<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../datos/variables.vista.php';
require_once "../modelo/NuevoPedido.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$_sesion = isseT($_SESSION["sesion"]) ? $_SESSION["sesion"] : null;

if ($_sesion == null){
    Funciones::imprimeJSON(401, "No tiene permisos suficientes.", "");
    exit;
}

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$objPedido = new Pedido();
$hoy = date("Y-m-d");

try {
    switch ($op) {
        case "registrar":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $objPedido->id_usuario_registro = $id_usuario;

            $id_cliente = (isset($_POST["txtclienteregistro"]) || $_POST["txtclienteregistro"] != "") ? $_POST["txtclienteregistro"] : NULL;
            if ($id_cliente == NULL){
                throw new Exception("Debe seleccionarse cliente.");
            }

            $objPedido->id_cliente = $id_cliente;

            $fecha_ingreso = (isset($_POST["txtfechaprocesoregistro"]) || $_POST["txtfechaprocesoregistro"] != "") ? $_POST["txtfechaprocesoregistro"] : NULL;
            if ($fecha_ingreso == NULL){
                throw new Exception("Debe ingresar fecha de proceso.");
            }
            
            $tipo_pedido = (isset($_POST["txttipopedido"]) || $_POST["txttipopedido"] != "") ? $_POST["txttipopedido"] : "1";

            $objPedido->fecha_ingreso = $fecha_ingreso;
            $objPedido->tipo_pedido = $tipo_pedido;

            if (count($_FILES) <= 0){
                throw new Exception("Se debe ingresar un archivo.");
            }

            $excel_file = $_FILES["txtdatosregistro"];
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

            if ($excel_file["size"] > 4 * 1024 * 1024){ /*Nax 3MB*/
                throw new Exception("No se puede procesar el archivo. El tamaño máximo por archivo es de 3MB");
            }

            $objPedido->archivo = [
                        "nombre"=>$excel_file["name"],
                        "tipo"=>$excel_file["type"],
                        "tamano"=>$excel_file["size"],
                        "archivo"=>$excel_file["tmp_name"]
                    ];

            $data = $objPedido->registrar();
            Funciones::imprimeJSON(200, "", $data);     
            exit;
        break;

        case 'listar':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case "leer_cabecera":
            $id_tipo_usuario = $_sesion["id_tipo_usuario"];
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            
            if ($id_pedido == ""){
                Funciones::imprimeJSON(500, "ID de Pedido no Válido.", "");
                exit;
            }

            $objPedido->id_pedido = $id_pedido;
            $data = $objPedido->leerCabecera();    

            Funciones::imprimeJSON(200,"", $data);
            exit;

        case "leer_ordenes_nivel_uno":
            $objPedido->usuario = $_sesion;

            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            
            if ($id_pedido == ""){
                Funciones::imprimeJSON(500, "ID de Pedido no Válido.", "");
                exit;
            }

            $objPedido->id_pedido = $id_pedido;
            $data = $objPedido->leerOrdenesNivelUno();    

            Funciones::imprimeJSON(200,"", $data);
            exit;

        case "leer_ordenes_nivel_dos":
            $objPedido->usuario = $_sesion;

            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            
            if ($id_pedido == ""){
                Funciones::imprimeJSON(500, "ID de Pedido no Válido.", "");
                exit;
            }

            $objPedido->id_pedido = $id_pedido;
            $data = $objPedido->leerOrdenesNivelDos();    

            Funciones::imprimeJSON(200,"", $data);
            exit;

        case "leer_ordenes_nivel_tres":
            $objPedido->usuario = $_sesion;

            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            
            if ($id_pedido == ""){
                Funciones::imprimeJSON(500, "ID de Pedido no Válido.", "");
                exit;
            }

            $objPedido->id_pedido = $id_pedido;
            $data = $objPedido->leerOrdenesNivelTres();    

            Funciones::imprimeJSON(200,"", $data);
            exit;

        case 'listar_leonisa':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_LEONISA, 1);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        
        case "eliminar":
            $objPedido->id_pedido = isset($_POST["id_pedido"]) ? $_POST["id_pedido"] : "";
            $data = $objPedido->eliminar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        default:
            Funciones::imprimeJSON(500, "Operación no válida.", "");
            exit;
    }    
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}