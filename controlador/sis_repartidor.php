<?php

include_once '../datos/variables.vista.php';
require_once "../modelo/SisRepartidor.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

$op = isseT($_GET["op"]) ? $_GET["op"] : "";
$obj = new SisRepartidor();

try {

    switch ($op) {
        case "obtener":
            $data = $obj->obtener();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "registrar":
            $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;

            if ($id_usuario == NULL){
                throw new Exception("Usuario no válido para hacer registros.");
            }

            $obj->id_usuario_registro = $id_usuario;
            $obj->id_repartidor = isset($_POST["p_id_repartidor"]) ? $_POST["p_id_repartidor"] : NULL;
            $obj->numero_documento = (isset($_POST["p_numero_documento"]) && $_POST["p_numero_documento"] != "") ? $_POST["p_numero_documento"] : "";
            if (!$obj->numero_documento){
                throw new Exception("No se ha enviado razón social o nombres de repartidor.", 1);
            }

            $obj->razon_social = (isset($_POST["p_razon_social"]) && $_POST["p_razon_social"] != "") ? strtoupper($_POST["p_razon_social"]) : "";
            if (!$obj->razon_social){
                throw new Exception("No se ha enviado razón social o nombres de repartidor.", 1);
            }

            $obj->celular = (isset($_POST["p_celular"]) && $_POST["p_celular"] != "") ? $_POST["p_celular"] : NULL;
            $obj->costo_entrega = (isset($_POST["p_costo_entrega"]) && $_POST["p_costo_entrega"] != "") ? $_POST["p_costo_entrega"] : "0.00";

            $data = $obj->registrar();
            Funciones::imprimeJSON(200, "", $data);
        break;
        
        case "anular":
            $obj->id_repartidor = isset($_POST["p_id_repartidor"]) ? $_POST["p_id_repartidor"] : "";
            if ($obj->id_repartidor == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->anular();
            Funciones::imprimeJSON(200, "", $data);
        break;

        case "leer":
            $obj->id_repartidor = isset($_POST["p_id_repartidor"]) ? $_POST["p_id_repartidor"] : "";
            if ($obj->id_repartidor == ""){
                throw new Exception("ID no enviado", 1);
            }

            $data = $obj->leer();
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