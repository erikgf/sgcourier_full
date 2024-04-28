<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/NuevoPedidoOrden.clase.php";
require_once "../modelo/util/funciones/Funciones.php";
require_once "../modelo/GlobalVariables.clase.php";

$_sesion = isset($_SESSION["sesion"]) ? $_SESSION["sesion"] : null;
$app_user_key = isset($_POST["app_user_key"]) ? $_POST["app_user_key"] : null;

if ($_sesion == null && $app_user_key == null){
    Funciones::imprimeJSON(401, "No tiene permisos suficientes.", "");
    exit;
}

$op = isset($_GET["op"]) ? $_GET["op"] : "";
$objPedido = new PedidoOrden();

try {
    switch ($op) {
        case "buscar_codigo_remito":
            //$id_usuario_responsable = $_sesion["id_usuario"];
            if ($_sesion){
                $id_usuario = $_sesion["id_usuario"];    
            } else {
                if ($app_user_key){
                    $id_usuario = $app_user_key;
                }
            }
            
            $codigo_remito =  isset($_POST["p_codigo_remito"]) ? $_POST["p_codigo_remito"] : "";
            $objPedido->codigo_remito = $codigo_remito;
            $objPedido->id_usuario_responsable = $id_usuario;

            if ($codigo_remito == ""){
                throw new Exception("No se ha encontrado codigo de remito.", 1);
            }

            $id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : GlobalVariables::$ID_FUXION_SAC;
            $data = $objPedido->leerXOrdenCodigoRemitoApp($id_cliente);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "buscar_ordenes_leonisa":
            $id_usuario_responsable = $_sesion["id_usuario"];
            $tipo =  isset($_POST["p_tipo"]) ? $_POST["p_tipo"] : "";
            $cadenaBuscar =  isset($_POST["p_cadena_buscar"]) ? $_POST["p_cadena_buscar"] : "";

            $id_cliente = GlobalVariables::$ID_LEONISA;
            $data = $objPedido->buscarOrdenesInterno($id_cliente, $tipo, $cadenaBuscar);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "leer_x_orden_id":
            if ($_sesion){
                $id_usuario = $_sesion["id_usuario"];    
            } else {
                if ($app_user_key){
                    $id_usuario = $app_user_key;
                }
            }
            
            $id_pedido_orden =  isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : "";
            $objPedido->id_pedido_orden = $id_pedido_orden;

            if ($id_pedido_orden == ""){
                throw new Exception("No se ha encontrado ID de órden.", 1);
            }

            $data = $objPedido->leerXOrdenId();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "asignar":
            $id_usuario = $_sesion["id_usuario"];
            $id_pedido = isset($_POST["p_idpedido"]) ? $_POST["p_idpedido"] : "";
            $id_usuario_responsable =  (isset($_POST["p_idcolaborador"]) || $_POST["p_idcolaborador"] != "") ? $_POST["p_idcolaborador"] : NULL;
            $arreglo_ordenes =  isset($_POST["p_pedidoordenes"]) ? $_POST["p_pedidoordenes"] : "";
            $objPedido->id_usuario_responsable = $id_usuario_responsable;
            $objPedido->id_pedido = $id_pedido;

            if ($arreglo_ordenes == ""){
                throw new Exception("No se ha enviado lista de órdenes.");
            }

            $arreglo_ordenes = json_decode($arreglo_ordenes);
            $data = $objPedido->asignar($arreglo_ordenes);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "corregir_estado":
            $objPedido->id_pedido_orden = isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : "";
            $descripcion = isset($_POST["p_descripcion"]) ? $_POST["p_descripcion"] : "";
            $estado_nuevo = isset($_POST["p_estado_nuevo"]) ? $_POST["p_estado_nuevo"] : "";

            $data = $objPedido->corregirEstado($descripcion, $estado_nuevo);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "buscar_codigo_tracking":
            $texto =  isset($_POST["p_buscar_texto"]) ? $_POST["p_buscar_texto"] : "";
            if ($texto == ""){
                 Funciones::imprimeJSON(200, "", "");
                 exit;
            }

            $data = $objPedido->buscarPorCodigoTracking($texto);
            Funciones::imprimeJSON(200, "", $data);
        break;

        //NUEVOS
        case "asignar_masivo_codigo":
            $id_usuario = $_sesion["id_usuario"];
            $id_pedido = isset($_POST["p_idpedido"]) ? $_POST["p_idpedido"] : "";
            $id_usuario_responsable =  (isset($_POST["p_idcolaborador"]) || $_POST["p_idcolaborador"] != "") ? $_POST["p_idcolaborador"] : NULL;
            $arreglo_ordenes_codigo_remito =  isset($_POST["p_pedidoordenes"]) ? $_POST["p_pedidoordenes"] : "";
            $es_reenvio =  isset($_POST["p_reenvio"]) ? $_POST["p_reenvio"] : "0";

            $objPedido->id_usuario_responsable = $id_usuario_responsable;
            $objPedido->id_pedido = $id_pedido;
            $objPedido->es_reenvio = $es_reenvio;

            if ($objPedido->es_reenvio == "1"){
                $objPedido->id_usuario_responsable = $id_usuario;
            }

            if ($arreglo_ordenes_codigo_remito == ""){
                throw new Exception("No se ha enviado lista de órdenes.");
            }

            $arreglo_ordenes_codigo_remito = json_decode($arreglo_ordenes_codigo_remito);

            $data = $objPedido->asignarMasivoPorCodigo($arreglo_ordenes_codigo_remito);
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "asignar_estados_x_departamento":
            $id_pedido = isset($_POST["p_idpedido"]) ? $_POST["p_idpedido"] : "";
            $arreglo_departamentos =  isset($_POST["p_departamentos"]) ? $_POST["p_departamentos"] : "";
            $objPedido->id_usuario_responsable = $_sesion["id_usuario"];
            $objPedido->id_pedido = $id_pedido;
            $estado = isset($_POST["p_estado"]) ? $_POST["p_estado"] : NULL;

            if (!$estado){
                throw new Exception("No se ha enviado estado seleccionado.");
            }

            if ($arreglo_departamentos == ""){
                throw new Exception("No se ha enviado lista de órdenes.");
            }

            $arreglo_departamentos = json_decode($arreglo_departamentos);

            $data = $objPedido->asignarEstadosPorDepartamento($arreglo_departamentos, $estado);
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "asignar_estados_x_provincia":
            $id_pedido = isset($_POST["p_idpedido"]) ? $_POST["p_idpedido"] : "";
            $arreglo_provincias =  isset($_POST["p_provincias"]) ? $_POST["p_provincias"] : "";
            $objPedido->id_usuario_responsable = $_sesion["id_usuario"];
            $objPedido->id_pedido = $id_pedido;
            $estado = isset($_POST["p_estado"]) ? $_POST["p_estado"] : NULL;

            if (!$estado){
                throw new Exception("No se ha enviado estado seleccionado.");
            }

            if ($arreglo_provincias == ""){
                throw new Exception("No se ha enviado lista de órdenes.");
            }

            $arreglo_provincias = json_decode($arreglo_provincias);

            $data = $objPedido->asignarEstadosPorProvincia($arreglo_provincias, $estado);
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "leer_x_id":
            $id_usuario = $_sesion["id_usuario"];
            $id_pedido_orden =  isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : "";
            $objPedido->id_pedido_orden = $id_pedido_orden;

            if ($id_pedido_orden == ""){
                throw new Exception("No se ha encontrado ID de órden.", 1);
            }

            $data = $objPedido->leerXId();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "cambiar_mostrar_excel_x_id":
            $id_pedido_orden =  isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : "";
            $objPedido->id_pedido_orden = $id_pedido_orden;

            if ($id_pedido_orden == ""){
                throw new Exception("No se ha encontrado ID de órden.", 1);
            }
            $mostrar_excel =  isset($_POST["p_mostrar_excel"]) ? $_POST["p_mostrar_excel"] : "";
            if ($mostrar_excel == ""){
                throw new Exception("No se ha mandado la opción de mostrar/no mostrar excel.", 1);
            }
            $data = $objPedido->cambiarMostrarExcelXId($mostrar_excel);
            Funciones::imprimeJSON(200, "", $data);
        break;

        default:
            Funciones::imprimeJSON(500, utf8_decode("Operación no válida."), "");
            exit;
    }    
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}