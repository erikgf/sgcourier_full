<?php
require_once '../negocio/UsuarioSistema.php';
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
        $obj = new UsuarioSistema();
        $operacion = $_GET["op"];

        switch ($operacion) {
            case 'registrar':
                /*mandatorios*/
                if (!isset($_POST["txtnumerodocumento"]) || empty($_POST["txtnumerodocumento"])){
                    Funciones::imprimeJSON(500, "Número documento inválido.","");
                    exit;
                }
                $obj->setNumeroDocumento($_POST["txtnumerodocumento"]);

                if (!isset($_POST["txtnombresapellidos"]) || empty($_POST["txtnombresapellidos"])){
                    Funciones::imprimeJSON(500, "Nombres y apellidos inválidos.","");
                    exit;
                }

                $obj->setNombresApellidos($_POST["txtnombresapellidos"]);

                if (!isset($_POST["txtclave"]) || empty($_POST["txtclave"])){
                    Funciones::imprimeJSON(500, "Clave inválida.","");
                    exit;
                }

                $obj->setClave($_POST["txtclave"]);
                $obj->setEstadoAcceso($_POST["txtestadoacceso"]);
                $data = $obj->registrar();
            break;
            case 'editar':
                /*mandatorios*/
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])) {
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setId($_POST["p_id"]);

                 /*mandatorios*/
                if (!isset($_POST["txtnumerodocumento"]) || empty($_POST["txtnumerodocumento"])){
                    Funciones::imprimeJSON(500, "Número documento inválido.","");
                    exit;
                }
                $obj->setNumeroDocumento($_POST["txtnumerodocumento"]);

                if (!isset($_POST["txtnombresapellidos"]) || empty($_POST["txtnombresapellidos"])){
                    Funciones::imprimeJSON(500, "Nombres y apellidos inválidos.","");
                    exit;
                }

                $obj->setNombresApellidos($_POST["txtnombresapellidos"]);
                $obj->setClave($_POST["txtclave"]);
                $obj->setEstadoAcceso($_POST["txtestadoacceso"]);
                $data = $obj->editar();
            break;
            case 'leer';
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setId($_POST["p_id"]);
                $data = $obj->leer();
            break;
            case 'obtener_data_interfaz';
                $p_listar_usuarios = isset($_POST["p_listar_usuarios_sistema"]) ? $_POST["p_listar_usuarios_sistema"] : "1";
                $data = $obj->obtenerDataInterfaz($p_listar_usuarios);     
            break;
            case 'listar';
                $data = $obj->listar();     
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