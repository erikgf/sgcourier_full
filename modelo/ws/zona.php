<?php
require_once '../negocio/Zona.php';
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
        $obj = new Zona();
        $operacion = $_GET["op"];

        switch ($operacion) {
            case 'registrar':
                /*mandatorios*/
                if (!isset($_POST["txtdescripcion"]) || empty($_POST["txtdescripcion"])){
                    Funciones::imprimeJSON(500, "Descripción inválida.","");
                    exit;
                }
                $obj->setDescripcion($_POST["txtdescripcion"]);
                $data = $obj->registrar();
            break;
            case 'editar':
                /*mandatorios*/
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])) {
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setIdzona($_POST["p_id"]);

                if (!isset($_POST["txtdescripcion"]) || empty($_POST["txtdescripcion"])){
                    Funciones::imprimeJSON(500, "Descripción inválida.","");
                    exit;
                }
                $obj->setDescripcion($_POST["txtdescripcion"]);
                $data = $obj->editar();
            break;
            case 'leer';
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setIdzona($_POST["p_id"]);
                $data = $obj->leer();
            break;
            case 'eliminar':
                /*mandatorios*/
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])) {
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setIdzona($_POST["p_id"]);
                $data = $obj->eliminar();
            break;
            case 'obtener_data_interfaz';
                $data = $obj->obtenerDataInterfaz("1");     
            break;
            case 'listar';
                $data = $obj->listar();     
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