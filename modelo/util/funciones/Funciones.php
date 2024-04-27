<?php

require_once 'json.hpack.php';

class Funciones
{
    public static $DIRECCION_WEB_SERVICE = "http://p3final.cixeisc.tk/ws/";
    private $SALT = "nbaysuiwqeo2";

    public static function imprimeJSON($estado, $mensaje, $datos)
    {
        header("HTTP/1.1 ".$estado." ".$mensaje);
        //header("HTTP/1.1 ".$estado);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header("Access-Control-Allow-Headers: X-Requested-With");
        
        $response["estado"]	= $estado;
        $response["mensaje"]	= $mensaje;
        $response["datos"]	= $datos;
	
        echo json_encode($response);
    }

    public static function encriptar($cadena){
        return crypt($cadena, '$6$rounds=5000$'.$SALT.'$');
    }

    public static function GET_ACTUAL_IGV(){
        return .18;
    }

    public static function nombreMes($numMes){
        switch ($numMes){
            case 1:
                return "ENERO";
            case 2:
                return "FEBRERO";
            case 3:
                return "MARZO";
            case 4:
                return "ABRIL";
            case 5:
                return "MAYO";
            case 6:
                return "JUNIO";
            case 7:
                return "JULIO";
            case 8:
                return "AGOSTO";   
            case 9:
                return "SEPTIEMBRE";   
            case 10:
                return "OCTUBRE";   
            case 11:
                return "NOVIEMBRE";   
            case 12:
                return "DICIEMBRE";   
        }
    }
}