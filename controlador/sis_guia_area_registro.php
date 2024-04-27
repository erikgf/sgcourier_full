<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../datos/variables.vista.php';
require_once "../modelo/SisGuiaAreaRegistro.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisGuiaAreaRegistro();

try {

    switch ($op) {
        case "listar_x_id_area":
            $obj->id_area = isset($_POST["p_id_area"]) ? $_POST["p_id_area"] : "";
            if ($obj->id_area == ""){
               Funciones::imprimeJSON(200, "", []);
               exit;
            }

            $hoy = date("Y-m-d");
            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;

            $data = $obj->listarXIdArea($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "listar_x_areas":
            $data = $obj->listarXAreas();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;
            $obj->id_area_registro = isset($_POST["p_id_area_registro"]) ? $_POST["p_id_area_registro"] : NULL;
            $obj->id_area = (isset($_POST["p_id_area"]) && $_POST["p_id_area"] != "") ? strtoupper($_POST["p_id_area"]) : "";

            if (!$obj->id_area){
                throw new Exception("No se ha enviado área.", 1);
            }
            $obj->fecha_recepcion = (isset($_POST["p_fecha_recepcion"]) && $_POST["p_fecha_recepcion"] != "") ? $_POST["p_fecha_recepcion"] : "";
            $obj->numero_guia = (isset($_POST["p_numero_guia"]) && $_POST["p_numero_guia"] != "") ? $_POST["p_numero_guia"] : "";
            $obj->dependencia = (isset($_POST["p_dependencia"]) && $_POST["p_dependencia"] != "") ? strtoupper($_POST["p_dependencia"]) : "";
            $obj->remitente = (isset($_POST["p_remitente"]) && $_POST["p_remitente"] != "") ? strtoupper($_POST["p_remitente"]) : "";
            $obj->consignatario = (isset($_POST["p_consignatario"]) && $_POST["p_consignatario"] != "") ? strtoupper($_POST["p_consignatario"]) : "";
            $obj->destino = (isset($_POST["p_destino"]) && $_POST["p_destino"] != "") ? strtoupper($_POST["p_destino"]) : "";

            $data = $obj->registrar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar_fecha_entrega":
            $obj->id_area_registro = isset($_POST["p_id_area_registro"]) ? $_POST["p_id_area_registro"] : NULL;
            $obj->fecha_entrega = (isset($_POST["p_fecha_entrega"]) && $_POST["p_fecha_entrega"] != "") ? $_POST["p_fecha_entrega"] : "";

            $data = $obj->registrarFechaEntrega();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "eliminar":
            $obj->id_area_registro = isset($_POST["p_id_area_registro"]) ? $_POST["p_id_area_registro"] : "";
            if ($obj->id_area_registro == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->eliminar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "consultar_x_guia":
            /*devolver info + fotos de guías*/
            $obj->numero_guia = isset($_POST["p_numero_guia"]) ? $_POST["p_numero_guia"] : "";
            $obj->numero_mes = isset($_POST["p_numero_mes"]) ? $_POST["p_numero_mes"] : "";
            $obj->numero_anio = isset($_POST["p_numero_anio"]) ? $_POST["p_numero_anio"] : "";
            $obj->sucursal = isset($_POST["p_sucursal"]) ? $_POST["p_sucursal"] : "";
            
            $data = $obj->consultarXGuia();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "leer":
            $obj->id_area_registro = isset($_POST["p_id_area_registro"]) ? $_POST["p_id_area_registro"] : "";
            if ($obj->id_area_registro == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->leer();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "obtener_imagenes_x_id_registro":
            $obj->id_area_registro = isset($_POST["p_id_area_registro"]) ? $_POST["p_id_area_registro"] : "";
            if ($obj->id_area_registro == ""){
                throw new Exception("ID no enviado", 1);
            }
            $data = $obj->obtenerImagenesXIdRegistro();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar_imagen_entrega":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;
            
            $MAXIMOS_MB_POR_IMAGEN = 3;
            $obj->id_area_registro = (isset($_POST["p_id_area_registro"]) && $_POST["p_id_area_registro"] != "") ? $_POST["p_id_area_registro"] : NULL;

            if ($obj->id_area_registro == NULL){
                throw new Exception("No se ha enviado un ID registro de guía", 1);
            }

            $obj->id_area_registro_imagen = (isset($_POST["p_id_area_registro_imagen"]) && $_POST["p_id_area_registro_imagen"] != "") ? $_POST["p_id_area_registro_imagen"] : NULL;

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
                    throw new Exception("No se puede procesar la imagen ".($i+1).". Seleccione un formato válido; jpg, png, bmp o gif.");
                }

                if ($value["size"] > $MAXIMOS_MB_POR_IMAGEN * 1024 * 1024){ /*Nax 3MB*/
                    throw new Exception("No se puede procesar la imagen ".($i+1)." El tamaño máximo por foto es de 2.5MB");
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

            $obj->imagenes = $imagenes;
            $data = $obj->registrarImagenesEntrega();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "eliminar_imagen_entrega":
            $obj->id_area_registro_imagen = isset($_POST["p_id_area_registro_imagen"]) ? $_POST["p_id_area_registro_imagen"] : "";
            if ($obj->id_area_registro_imagen == ""){
                throw new Exception("ID de imagen no enviado", 1);
            }

            $data = $obj->eliminarImagenEntrega();
            Funciones::imprimeJSON(200, "", $data);
        break;
        case "registrar_masivo":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;
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

            $obj->archivo = [
                        "nombre"=>$excel_file["name"],
                        "tipo"=>$excel_file["type"],
                        "tamano"=>$excel_file["size"],
                        "archivo"=>$excel_file["tmp_name"]
                    ];

            $data = $obj->registrarMasivo();
            Funciones::imprimeJSON(200, "", $data);     
            exit;
        break;

        default:
            Funciones::imprimeJSON(500, "Operación no válida.", "");
    }    

    exit;
} 
catch (Exception $e) {
  Funciones::imprimeJSON(500, $e->getMessage(), "");
}