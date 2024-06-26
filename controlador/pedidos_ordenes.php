<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/PedidoOrden.clase.php";
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
        case "listar_pendientes_app":
            //$id_usuario = $_sesion["id_usuario"];
            $id_usuario = $app_user_key;
            $pagina_actual =  isset($_POST["p_pagina_actual"]) ? $_POST["p_pagina_actual"] : 0;
            $items_per_load =  isset($_POST["p_items_per_load"]) ? $_POST["p_items_per_load"] : 15;
            $primera_carga =  isset($_POST["p_primera_carga"]) ? $_POST["p_primera_carga"] : 1;
            
            $objPedido->id_usuario_responsable = $id_usuario;

            $id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : GlobalVariables::$ID_FUXION_SAC;
            
            $data = $objPedido->listarPedidosPendientesXApp($pagina_actual, $items_per_load, $primera_carga, $id_cliente);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "listar_completados_app":
            //$id_usuario = $_sesion["id_usuario"];
            $id_usuario = $app_user_key;
            $pagina_actual =  isset($_POST["p_pagina_actual"]) ? $_POST["p_pagina_actual"] : 0;
            $items_per_load =  isset($_POST["p_items_per_load"]) ? $_POST["p_items_per_load"] : 15;
            $primera_carga =  isset($_POST["p_primera_carga"]) ? $_POST["p_primera_carga"] : 1;
            
            $id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : GlobalVariables::$ID_FUXION_SAC;

            $objPedido->id_usuario_responsable = $id_usuario;
            $data = $objPedido->listarPedidosCompletadosXApp($pagina_actual, $items_per_load, $primera_carga, $id_cliente);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "leer_x_orden_id_app":
            //$id_usuario = $_sesion["id_usuario"];
            if ($_sesion){
                $id_usuario = $_sesion["id_usuario"];    
            } else {
                if ($app_user_key){
                    $id_usuario = $app_user_key;
                }
            }
            $id_tipo_usuario = $_sesion["id_tipo_usuario"];
            $id_pedido_orden =  isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : "";
            $objPedido->id_usuario_responsable = $id_usuario;
            $objPedido->id_tipo_usuario = $id_tipo_usuario;
            $objPedido->id_pedido_orden = $id_pedido_orden;

            $data = $objPedido->leerXOrdenIdApp();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar_visita_app":
            //$id_usuario_responsable = $_sesion["id_usuario"];
            if ($_sesion){
                $id_usuario_responsable = $_sesion["id_usuario"];    
            } else {
                if ($app_user_key){
                    $id_usuario_responsable = $app_user_key;
                }
            }
            $id_pedido_orden = isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : NULL;
            $tipo_visita = isset($_POST["p_tipo_visita"]) ? $_POST["p_tipo_visita"] : NULL;
            $es_receptor_destinatario = isset($_POST["p_es_receptor_destinatario"]) ? $_POST["p_es_receptor_destinatario"] : "true";
            $numero_documento_receptor = isset($_POST["p_numero_documento_receptor"]) ? $_POST["p_numero_documento_receptor"] : "";
            $nombres_receptor = isset($_POST["p_nombre_receptor"]) ? $_POST["p_nombre_receptor"] : "";
            $observaciones = isset($_POST["p_observaciones"]) ? $_POST["p_observaciones"] : "";
            $motivaciones = isset($_POST["p_motivaciones"]) ? $_POST["p_motivaciones"] : "[]";

            if ($id_pedido_orden == NULL){
                throw new Exception("ID de registro no volida.");
            }

            if ($tipo_visita == NULL || $tipo_visita == ""){
                throw new Exception("Tipo de visita no válida.");
            }

            $objPedido->id_usuario_responsable = $id_usuario_responsable;
            $objPedido->id_pedido_orden = $id_pedido_orden;
            $objPedido->tipo_visita = $tipo_visita;

            if ($tipo_visita != "G"){
                $objPedido->es_receptor_destinatario = $es_receptor_destinatario == "true" ? 1 : 0;

                if ($tipo_visita == "M"){
                    $objPedido->es_receptor_destinatario = NULL;
                    $objPedido->numero_documento_receptor = NULL;
                    $objPedido->nombres_receptor = NULL;

                    $motivaciones = json_decode($motivaciones);
                    $cantidadMotivaciones = count($motivaciones);
                    if ($cantidadMotivaciones <= 0){
                        throw new Exception("En una visita motivada debe haber por lo menos 1 motivación seleccionada.");
                    }

                    $objPedido->motivaciones = $motivaciones;
                } else {
                   if (!$objPedido->es_receptor_destinatario){
                        $objPedido->numero_documento_receptor = $numero_documento_receptor;
                        $objPedido->nombres_receptor = $nombres_receptor;
                    } 
                }
            }

            $objPedido->observaciones = $observaciones;

            $imagenes = [];
            $imagenes_invalidas = 0;
            $i = 0;
            
            foreach ($_FILES as $key => $value) {
                switch ($value["type"]){
                    case image_type_to_mime_type(IMAGETYPE_GIF):
                    case image_type_to_mime_type(IMAGETYPE_JPEG):
                    case image_type_to_mime_type(IMAGETYPE_PNG):
                    case image_type_to_mime_type(IMAGETYPE_BMP):
                    break;
                    default:
                    $imagenes_invalidas++;
                    break;
                }

                if ($imagenes_invalidas > 0){
                    throw new Exception("No se puede procesar la imagen ".($i+1).". Seleccione un formato valido; jpg, png, bmp o gif.");
                }

                if ($value["size"] > 2.5 * 1024 * 1024){ /*Nax 2.5MB*/
                    throw new Exception("No se puede procesar la imagen ".($i+1)." El tamano maximo por foto es de 2.5MB. Te recomendamos bajes la calidad de la toma en la cámara.");
                }

                array_push($imagenes, 
                        [
                            "nombre"=>$value["name"],
                            "tipo"=>$value["type"],
                            "tamano"=>$value["size"],
                            "archivo"=>$value["tmp_name"]
                        ]
                    );
            }

            $objPedido->imagenes = $imagenes;
            $data = $objPedido->registrarVisita();
            Funciones::imprimeJSON(200, "", $data);
        break;
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
        case "leer_x_orden_id":
            
            //$id_usuario = $_sesion["id_usuario"];
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
        case "asignar_masivo_codigo_remito":
            $id_usuario = $_sesion["id_usuario"];
            $id_pedido = isset($_POST["p_idpedido"]) ? $_POST["p_idpedido"] : "";
            $id_usuario_responsable =  (isset($_POST["p_idcolaborador"]) || $_POST["p_idcolaborador"] != "") ? $_POST["p_idcolaborador"] : NULL;
            $arreglo_ordenes_codigo_remito =  isset($_POST["p_pedidoordenes"]) ? $_POST["p_pedidoordenes"] : "";
            $objPedido->id_usuario_responsable = $id_usuario_responsable;
            $objPedido->id_pedido = $id_pedido;

            if ($arreglo_ordenes_codigo_remito == ""){
                throw new Exception("No se ha enviado lista de órdenes.");
            }

            $arreglo_ordenes_codigo_remito = json_decode($arreglo_ordenes_codigo_remito);

            $data = $objPedido->asignarMasivoPorCodigoRemito($arreglo_ordenes_codigo_remito);
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