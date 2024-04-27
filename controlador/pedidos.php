<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../datos/variables.vista.php';
require_once "../modelo/Pedido.clase.php";
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
        case 'listar':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_fuxion':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_FUXION_SAC);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_leonisa':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_LEONISA, 1);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_leonisa_catalogos':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_LEONISA, 2);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_minedu':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_MINEDU);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_pronabec':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_PRONABEC);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_pronied':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_PRONIED);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_atu':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;

            $data = $objPedido->listar($fi, $ff, GlobalVariables::$ID_ATU);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case 'listar_x_usuario':
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            $fi = isset($_POST["p_fechainicio"]) ? $_POST["p_fechainicio"] : $hoy;
            $ff = isset($_POST["p_fechafin"]) ? $_POST["p_fechafin"] : $hoy;


            $data = $objPedido->listarXUsuario($fi, $ff);
            Funciones::imprimeJSON(200, "", $data);
            exit;
        case "leer_x_id":
            $id_tipo_usuario = $_sesion["id_tipo_usuario"];
            $id_usuario = $_sesion["id_usuario"];
            $objPedido->id_usuario = $id_usuario;

            switch($id_tipo_usuario){
                case "3": /*REPARTIDOR*/
                    $data = $objPedido->leerXIdApp();
                break;
                case "4": /*CLIENTE*/
                    $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
                    if ($id_pedido == ""){
                        Funciones::imprimeJSON(500, "ID de pedido no válido.", "");
                        exit;
                    }
                    $objPedido->id_pedido = $id_pedido;
                    $data = $objPedido->leerXIdCliente();
                break;
                case "2":
                    $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
                    if ($id_pedido == ""){
                        Funciones::imprimeJSON(500, "ID de pedido no válido.", "");
                        exit;
                    }
                    $objPedido->id_pedido = $id_pedido;
                    $data = $objPedido->leerXIdAgencia();
                break;
                default:
                    $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
                    $id_cliente_especifico = isset($_POST["p_id_cliente_especifico"]) ? $_POST["p_id_cliente_especifico"] : NULL;
                    
                    if ($id_pedido == ""){
                        Funciones::imprimeJSON(500, "ID de pedido no válido.", "");
                        exit;
                    }
                    $objPedido->id_pedido = $id_pedido;

                    $data = $objPedido->leerXId($id_cliente_especifico);    

                break;
            }

            Funciones::imprimeJSON(200,"", $data);
            exit;
        case "listar_ordenes_x_id":
            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            $id_tipo_usuario = $_sesion["id_tipo_usuario"];
            if ($id_pedido == ""){
                Funciones::imprimeJSON(500, "ID de pedido no válido.", "");
                exit;
            }

            $estado = isset($_POST["p_estado"]) ? $_POST["p_estado"] : "";

            $objPedido->id_pedido = $id_pedido;
            $objPedido->id_tipo_usuario = $id_tipo_usuario;
            $objPedido->estado = $estado;


            $id_cliente_especifico = isset($_POST["p_id_cliente_especifico"]) ? $_POST["p_id_cliente_especifico"] : NULL;

            switch($id_tipo_usuario){
                case "3": /*REPARTIDOR*/
                    $data = [];
                break;
                case "4": /*CLIENTE*/
                   $data = $objPedido->listarOrdenesXId();
                break;
                default:
                   $data = $objPedido->listarOrdenesXIdAdmin($id_cliente_especifico);
                break;
            }
            Funciones::imprimeJSON(200,"", $data);
            exit;        
        case "reporte_ordenes_completadas_fuxion":
            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            $fi = isset($_POST["p_fi"]) ? $_POST["p_fi"] : "";
            $ff = isset($_POST["p_ff"]) ? $_POST["p_ff"] : "";

            $objPedido->id_pedido = $id_pedido;
            $data = $objPedido->reporteOrdenesCompletadasFuxion($fi, $ff);
            Funciones::imprimeJSON(200,"", $data);
            exit;
        break;
        case "leer_x_id_listar":
            $id_usuario = $_sesion["id_usuario"];
            $id_pedido = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            $objPedido->id_usuario = $id_usuario;
            $objPedido->id_pedido = $id_pedido; 
            $data = $objPedido->leerXIdListar();

            Funciones::imprimeJSON(200,"", $data);
            exit;
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

            $objPedido->dias_ruta =  (isset($_POST["txtdiasruta"]) || $_POST["txtdiasruta"] != "") ? $_POST["txtdiasruta"] : "1";
            $objPedido->dias_gestionando = (isset($_POST["txtdiasentregar"]) || $_POST["txtdiasentregar"] != "") ? $_POST["txtdiasentregar"] : "1";
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