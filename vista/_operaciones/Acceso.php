<?php 

require_once '../../modelo/GlobalVariables.clase.php';

class  Acceso{

	public static function VALIDAR(){
		$sesion = isset($_SESSION["sesion"]) ? $_SESSION["sesion"] : NULL;

		if ($sesion == NULL){
			header("Location: ../login");
			exit;
		}

		return true;
	}

	public static function DATA_USUARIO(){
		$sesion = isset($_SESSION["sesion"]) ? $_SESSION["sesion"] : NULL;

		if ($sesion == NULL){
			return ["usuario"=> "", "tipo_usuario"=> ""];
		}

		return ["usuario"=> $sesion["nombre_usuario"], "tipo_usuario"=> $sesion["tipo_usuario"]];
	}
}