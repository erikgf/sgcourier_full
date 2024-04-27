<?php
require_once '../negocio/TipoServicio.php';
require_once '../util/funciones/Funciones.php';
require_once 'token.validar.php';

$_POST["token"] = "1";
//Validar si se recibe el parámetro token
if ( !isset($_POST["token"]) ){
    Funciones::imprimeJSON(500, "Falta completar datos", "");
    exit; //Detiene el avance del programa
}
//Recibir el token
$token = $_POST["token"];
try {
    //$objValidarToken = validarToken($token);
    $objValidarToken["r"] = TRUE;

    if ($objValidarToken["r"]){ //Si devuelve TRUE, significa que el token es válido
        $dataTOKEN = $objValidarToken["data"];
        $obj = new TipoServicio();
        $operacion = $_GET["op"];

        switch ($operacion) {
            case 'editar':
                /*mandatorios*/
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])) {
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setId($_POST["p_id"]);

                if (!isset($_POST["txtcostobase"]) || empty($_POST["txtcostobase"]) || $_POST["txtcostobase"] <= 0){
                    Funciones::imprimeJSON(500, "Costo inválido.","");
                    exit;
                }

                $obj->setCostoBase($_POST["txtcostobase"]);
                $data = $obj->editar();
            break;
            case 'listar';
                $data = $obj->listar();     
            break;
            case 'leer';
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])) {
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setId($_POST["p_id"]);

                $data = $obj->leer();     
            break;
            case 'eliminar':
                /*mandatorios*/
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])) {
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setId($_POST["p_id"]);
                $data = $obj->eliminar();
            break;
            case 'obtener_data_interfaz';
                $p_listar_tipo_servicios = isset($_POST["p_listar_tipo_servicios"]) ? $_POST["p_listar_tipo_servicios"] : "1";
                $data = $obj->obtenerDataInterfaz($p_listar_tipo_servicios);     
            break;
            default:
            Funciones::imprimeJSON(500, "Operación no válida.", "");
            exit;
        }

        Funciones::imprimeJSON(200, "OK", $data);    
    }else{
        Funciones::imprimeJSON(500, "Token no es válido", "");
    }
} catch (Exception $exc) {
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}