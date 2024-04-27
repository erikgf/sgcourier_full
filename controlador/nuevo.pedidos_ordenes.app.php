<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/NuevoPedidoOrden.clase.php";
require_once "../modelo/util/funciones/Funciones.php";
require_once "../modelo/GlobalVariables.clase.php";

$app_user_key = isset($_POST["app_user_key"]) ? $_POST["app_user_key"] : null;

if ($app_user_key == null){
    Funciones::imprimeJSON(401, "No tiene permisos suficientes.", "");
    exit;
}

$op = isset($_GET["op"]) ? $_GET["op"] : "";
$objPedido = new PedidoOrden();

try {
    switch ($op) {
        case "listar_pendientes":
            $pagina_actual =  isset($_POST["p_pagina_actual"]) ? $_POST["p_pagina_actual"] : 0;
            $items_per_load =  isset($_POST["p_items_per_load"]) ? $_POST["p_items_per_load"] : 15;
            $primera_carga =  isset($_POST["p_primera_carga"]) ? $_POST["p_primera_carga"] : 1;
            
            $objPedido->id_usuario_responsable = $app_user_key;

            $id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : GlobalVariables::$ID_FUXION_SAC;
            $objPedido->id_cliente = $id_cliente;

            $data = $objPedido->listarPedidosPendientesXApp($pagina_actual, $items_per_load, $primera_carga);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "listar_completados":
            $pagina_actual =  isset($_POST["p_pagina_actual"]) ? $_POST["p_pagina_actual"] : 0;
            $items_per_load =  isset($_POST["p_items_per_load"]) ? $_POST["p_items_per_load"] : 15;
            $primera_carga =  isset($_POST["p_primera_carga"]) ? $_POST["p_primera_carga"] : 1;
            
            $id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : GlobalVariables::$ID_FUXION_SAC;
            $objPedido->id_cliente = $id_cliente;

            $objPedido->id_usuario_responsable = $app_user_key;
            $data = $objPedido->listarPedidosCompletadosXApp($pagina_actual, $items_per_load, $primera_carga);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "leer_x_id":
            $id_pedido_orden =  isset($_POST["p_id_pedido_orden"]) ? $_POST["p_id_pedido_orden"] : "";
            $objPedido->id_usuario_responsable = $app_user_key;
            $objPedido->id_pedido_orden = $id_pedido_orden;

            $data = $objPedido->leerXIdApp();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar_visita":
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

            $objPedido->id_usuario_responsable = $app_user_key;
            $objPedido->id_pedido_orden = $id_pedido_orden;
            $objPedido->tipo_visita = $tipo_visita;

            if ($tipo_visita != "G"){//siguiente visita
                $objPedido->es_receptor_destinatario = $es_receptor_destinatario == "true" ? 1 : 0;

                if ($tipo_visita == "M"){//motivado / entregado
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
            $data = $objPedido->registrarVisitaApp();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "buscar_codigo_remito":
            $objPedido->id_usuario_responsable = $app_user_key;
            $codigo =  isset($_POST["p_codigo_remito"]) ? $_POST["p_codigo_remito"] : "";

            if ($codigo == ""){
                throw new Exception("No se ha encontrado código de consulta.", 1);
            }

            $objPedido->codigo_remito = $codigo;
            $objPedido->id_cliente = isset($_POST["p_id_cliente"]) ? $_POST["p_id_cliente"] : GlobalVariables::$ID_FUXION_SAC;
            $data = $objPedido->leerXOrdenCodigoRemitoApp();
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