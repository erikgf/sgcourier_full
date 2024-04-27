 <?php
require_once '../negocio/PropiedadFacturacion.php';
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
        $obj = new PropiedadFacturacion();
        $operacion = $_GET["op"];

        switch ($operacion) {
            case 'procesar':
                $data = $obj->_procesar();
            break;    
            case 'calcular_deudas':
                $arregloDeudas = $obj->_obtenerFechas(0);
                $data = $obj->calcularDeudas($arregloDeudas);
            break;
            case 'registrar_auto':
                /*
                    Se registrar una facturacion el día NUMERO 1  del mes, 
                    Los parametros principales son: Ninguno, es un proceso interno.
                */
                $data = $obj->registrarAuto();
            break;
             /*
            case 'anular';
                 if (!isset($_POST["p_id"]) || empty($_POST["p_id"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }

                $obj->setId($_POST["p_id"]);
                $data = $obj->anular();
            break;
            case 'leer';
                $data = "";
                break;
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setId($_POST["p_id"]);
                $data = $obj->leer();
                break;
                */
            case "listar_anio_total": //listar todos los usuarios_junta por año (necesito más filtros.)
                /*mandatorios*/
                if (!isset($_POST["p_anio"]) || empty($_POST["p_anio"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setAnioFacturacion($_POST["p_anio"]);
                $data = $obj->listarAnioTotal();     
            break;
            case "listar_cronograma_anual": //listar cronograma: año, zona: ES LA FUNCION DE CONSULTA PRINCIPAL
                /*mandatorios*/
                if (!isset($_POST["p_anio"]) || empty($_POST["p_anio"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setAnioFacturacion($_POST["p_anio"]);
                $idZona = (!isset($_POST["p_idzona"]) || empty($_POST["p_idzona"])) ? "" : $_POST["p_idzona"];

                $data = $obj->listarCronogramaAnual($idZona);     
            break;
            case "listar_recibos_al_dia_hoy": /*recibos al día de hoy */
                $data = $obj->listarRecibosAlDiaHoy();     
            break;
            case "listar_recibos": /*recibos al día de hoy (PENDIENTES- TODOS- PAGADOS se muestran TODOS las propiedades)*/
                /*usuario: "" = todos, "id"=> usuario en específico*/
                /*opcion:pagado = "" todos, 0: pagados, 1: pendientes*/
                /*anio : "" */
                if (!isset($_POST["p_anio"]) || empty($_POST["p_anio"])){
                    Funciones::imprimeJSON(500, "ID inválido.","");
                    exit;
                }
                $obj->setAnioFacturacion($_POST["p_anio"]);
                $idusuario = (!isset($_POST["p_idusuario"]) || empty($_POST["p_idusuario"])) ? "" : $_POST["p_idusuario"];
                $estadoPago = (!isset($_POST["p_estadopago"]) || empty($_POST["p_estadopago"])) ? "" : $_POST["p_estadopago"];
                $data = $obj->listarRecibos($idusuario, $estadoPago);     
            break;
            case "listar_recibos_x_usuario": /*recibos por usuario (deberia ser recibos por usuario por año. necesito más filtros (propiedad y año)) */
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])){
                    Funciones::imprimeJSON(500, "ID USUARIO inválido.","");
                    exit;
                }
                $this->setIdUsuarioJunta($_POST["id"]);
                $data = $obj->listarRecibosXUsuario();      
            break;
            case "listar_recibo_x_propiedad":
                if (!isset($_POST["p_id"]) || empty($_POST["p_id"])){
                    Funciones::imprimeJSON(500, "ID PROPIEDAD inválido.","");
                    exit;
                }

                $obj->setIdUsuarioJuntaPropiedad($_POST["p_id"]);
                $data = $obj->listarFacturaXPropiedad();     
            break;
            case "obtener_interfaz_cronograma":
                $obj->setAnioFacturacion(date("Y"));
                $data = [];
                $data["cronograma"] =  $obj->listarCronogramaAnual("");     
                $data["lista_zonas"] = $obj->listarZonas();
            break;
            case "obtener_interfaz_recibos":
                $obj->setAnioFacturacion(date("Y"));
                $data = [];
                $data["lista_usuarios_junta"] = $obj->listarUsuariosJunta();
                $data["recibos"] =  $obj->listarRecibos("","");     
            break;
            case "obtener_interfaz_pagos":
                $obj->setAnioFacturacion(date("Y"));
                $obj->setMesFacturacion(date("m"));

                $data = [];
                $data["lista_usuarios_junta"] = $obj->listarUsuariosJunta();
                $data["lista_facturaciones"] =  $obj->listarFacturacionesUsuario("","");     
            break;
            case "listar_facturaciones": 
                /*usuario: "" = todos, "id"=> usuario en específico*/
                /*opcion:pagado = "" todos, 0: Cancelas, 1: pEndientes*/
                /*anio : "" */
                if (!isset($_POST["p_anio"]) || empty($_POST["p_anio"])){
                    Funciones::imprimeJSON(500, "Año inválido.","");
                    exit;
                }

                if (!isset($_POST["p_mes"]) || empty($_POST["p_mes"])){
                    Funciones::imprimeJSON(500, "Mes inválido.","");
                    exit;
                }
                $obj->setMesFacturacion($_POST["p_mes"]);
                $obj->setAnioFacturacion($_POST["p_anio"]);

                $idusuario = (!isset($_POST["p_idusuario"]) || empty($_POST["p_idusuario"])) ? "" : $_POST["p_idusuario"];
                $estadoPago = (!isset($_POST["p_estadopago"]) || empty($_POST["p_estadopago"])) ? "" : $_POST["p_estadopago"];
                $data = $obj->listarFacturacionesUsuario($idusuario, $estadoPago);     
            break;
            case "obtener_meses_pagar": 
                if (!isset($_POST["p_anio"]) || empty($_POST["p_anio"])){
                    Funciones::imprimeJSON(500, "Año inválido.","");
                    exit;
                }

                if (!isset($_POST["p_mes"]) || empty($_POST["p_mes"])){
                    Funciones::imprimeJSON(500, "Mes inválido.","");
                    exit;
                }


                if (!isset($_POST["p_mes"]) || empty($_POST["p_mes"])){
                    Funciones::imprimeJSON(500, "Usuario inválido.","");
                    exit;
                }

                $obj->setMesFacturacion($_POST["p_mes"]);
                $obj->setAnioFacturacion($_POST["p_anio"]);

                $idusuario = (!isset($_POST["p_idusuario"]) || empty($_POST["p_idusuario"])) ? "" : $_POST["p_idusuario"];
                $data = $obj->obtenerMesesPagar($idusuario);     
            break;
            case "guardar_pagos": 
                if (!isset($_POST["p_usuariopagando"]) || empty($_POST["p_usuariopagando"])){
                    Funciones::imprimeJSON(500, "Usuario inválido.","");
                    exit;
                }

                if (!isset($_POST["p_opciones"]) || empty($_POST["p_opciones"])){
                    Funciones::imprimeJSON(500, "No se ha ingresado opciones válidas.","");
                    exit;
                }

                $idusuario = $_POST["p_usuariopagando"];
                $arregloOpciones = json_decode($_POST["p_opciones"]);
                $data = $obj->guardarPagos($idusuario, $arregloOpciones);
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

    