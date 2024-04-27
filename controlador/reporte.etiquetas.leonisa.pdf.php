<?php 

include_once '../datos/variables.vista.php';
include_once "../fpdf/fpdf_barcode.php";
require_once "../modelo/Pedido.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

class PDF extends PDF_Code128
{
	private $MARGEN_SUPERIOR = 5;
	private $MARGEN_LATERAL = 5;
	private $ANCHO_TOTAL = 297;

	private $ALTO_RECIBO = 92.5;
	private $ANCHO_RECIBO = 65; /* [210 - 10(margenes) - 5 (margen interior)] / 3*/
	private $ALTO_CELDA= 4.1;
	private $ESPACIO_CENTRO_SEPARACION = 2.5;

	/*
	private $MITAD_ANCHO_RECIBO = 63.75;
	private $MITAD_MITAD_ANCHO_RECIBO = 31.875;
	*/
	private $ENCUADRADO = 0;
	private $ALTURA_BARCODE = 9;
	private $TAMAÑO_ESPACIO_PUNTEADO_LINEA = 0.8;
	private $ALTO_CUADRADO_CENTRAL  = 18.5;
	private $MARGEN_CAJA_CENTRAL = 8;
	private $MARGEN_CAJA_CENTRAL_MITAD;
	private $MARGEN_CAJA_CENTRAL_MITAD_MITAD;

	private $RAZON_SOCIAL = "SIN CLIENTE";

	private $STR_ROTULO_SUPERIOR_IZQUIERDA = "SG COURIER & CARGO";
	private $STR_REMITE = "Remite: ";
	private $STR_TELEFONO = "Telf: ";
	private $STR_ZONA= "ZONA ";
	private $STR_CAMPAÑA = "CAMPAÑA ";
	private $STR_FIRMA = "FIRMA:";
	private $STR_DNI = "DNI:";
	private $STR_NOMBRE = "NOM:";
	private $STR_PARENTESCO = "PARENTESCO:";
	private $STR_FECHA = "FECHA:";
	private $STR_HORA = "HORA:";

	function __construct($orientation='P', $unit='mm', $format='A4') {
	    parent::__construct($orientation,$unit,$format);
		$this->SetMargins($this->MARGEN_LATERAL,$this->MARGEN_SUPERIOR,$this->MARGEN_LATERAL);

		$this->MARGEN_CAJA_CENTRAL_MITAD = $this->MARGEN_CAJA_CENTRAL / 2;
		$this->MARGEN_CAJA_CENTRAL_MITAD_MITAD = $this->MARGEN_CAJA_CENTRAL_MITAD / 4;
	}

	private function PrintSangria(){
		return "  ";
	}

	public function SetRazonSocialCliente($razon_social){
		$this->RAZON_SOCIAL = $razon_social;
	}

	function CalcularXYEtiqueta($PosicionI, $PosicionJ){
		$BASE_X = 5;
		$BASE_Y = 5;

		$X = (($this->ANCHO_RECIBO + $this->ESPACIO_CENTRO_SEPARACION) * $PosicionI) + $BASE_X;
		$Y = ($this->ALTO_RECIBO * $PosicionJ) + $BASE_Y;

		return ["X"=>$X, "Y"=>$Y];
	}

	function Celda($ancho_celda, $alto_celda, $cadena, $encuadrado, $ln, $alineado){
		$esLnTres = $ln == 3;
		$temporalX = $this->GetX();

		$this->Cell($ancho_celda, $alto_celda, $cadena, $encuadrado, $esLnTres ? 1 : $ln,$alineado);
		if ($ln != 0){
			$this->SetX($temporalX);
		}
	}

	function SaltoLinea($h = NULL){
		$this->SetXY($this->GetX(), $this->GetY() + ($h == NULL ? 1 : $h));	
	}

	function ImprimirEtiqueta($DataEtiqueta, $EtiquetaX, $EtiquetaY){
		/*Las etiquetas siempre deben imprimirse máximo 3 vertical y 3 horizontal, así la etiqueta puede 
		tomar la posicion 0,0;  y la última etiqueta: 2,2*/

		//var_dump($this->CalcularXYEtiqueta($EtiquetaX, $EtiquetaY));
		//return;
		$coordenadas = $this->CalcularXYEtiqueta($EtiquetaX, $EtiquetaY);
		$this->SetXY($coordenadas["X"], $coordenadas["Y"]);


		$X1LineaVertical = $this->GetX() + $this->ANCHO_RECIBO;
		$Y1LineaVertical = $this->GetY();

		//var_dump($Y1LineaVertical); exit;
		$this->SetFont('Arial','B',9.5);
		$this->Celda($this->ANCHO_RECIBO, $this->ALTO_CELDA, $this->STR_ROTULO_SUPERIOR_IZQUIERDA, $this->ENCUADRADO,3,"L");
		$this->SaltoLinea(0.5);
		$this->SetDash($this->TAMAÑO_ESPACIO_PUNTEADO_LINEA, $this->TAMAÑO_ESPACIO_PUNTEADO_LINEA);
		$this->Line($this->GetX(), $this->GetY(), $this->GetX() + $this->ANCHO_RECIBO, $this->GetY());

		$this->SetFont('Arial','',6.5);
		$this->Celda($this->ANCHO_RECIBO, $this->ALTO_CELDA, $this->STR_REMITE.$this->RAZON_SOCIAL,$this->ENCUADRADO,3,"L");
		$this->Celda($this->ANCHO_RECIBO - 3.25, $this->ALTO_CELDA * .5, $this->STR_ZONA.$DataEtiqueta["zona"], $this->ENCUADRADO,3,"C");
		$this->Code128($this->GetX() + $this->MARGEN_CAJA_CENTRAL_MITAD, $this->GetY(), $DataEtiqueta["codigo_remito"], $this->ANCHO_RECIBO - 10, $this->ALTURA_BARCODE);

		$this->SetFont('Arial','B',8);
		$this->SetXY($this->GetX(), $this->GetY() + $this->ALTURA_BARCODE);
		$this->Celda($this->ANCHO_RECIBO - 3.25, $this->ALTO_CELDA, $DataEtiqueta["codigo_remito"], $this->ENCUADRADO,3,"C");

		$this->SaltoLinea(0.5);

		$XTemporal = $this->GetX();
		$YTemporal = $this->GetY();

		$this->SetFont('Arial','',7.75);
		$this->Celda($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA, utf8_decode($this->STR_CAMPAÑA.$DataEtiqueta["campaña"]),$this->ENCUADRADO,3,"C");
		$this->Celda($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA, utf8_decode($DataEtiqueta["destinatario"]),$this->ENCUADRADO,3,"C");

		$this->SetFont('Arial','',8);
		$this->MultiCell($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA * .75, utf8_decode($DataEtiqueta["direccion"]),$this->ENCUADRADO,"C");
		$this->SetXY($coordenadas["X"], $this->GetY());

		$this->SetFont('Arial','B',7.75);
		$this->SetXY($this->GetX(), $YTemporal + $this->ALTO_CUADRADO_CENTRAL);	

		$arreglo_telefonos = [];
		if ($DataEtiqueta["telefono_uno"] != "" && $DataEtiqueta["telefono_uno"] != "0"){
			array_push($arreglo_telefonos, $DataEtiqueta["telefono_uno"]);
		}
		if ($DataEtiqueta["telefono_dos"] != "" && $DataEtiqueta["telefono_dos"] != "0"){
			array_push($arreglo_telefonos, $DataEtiqueta["telefono_dos"]);
		}
		if ($DataEtiqueta["celular"] != "" && $DataEtiqueta["celular"] != "0"){
			array_push($arreglo_telefonos, $DataEtiqueta["celular"]);
		}

		$cadena_telefonos = join(" - ", $arreglo_telefonos);
		$this->Celda($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA, utf8_decode($this->STR_TELEFONO.$cadena_telefonos),$this->ENCUADRADO,3,"C");
		$this->SetFont('Arial','',7.5);
		$this->Celda($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA - 0.5, utf8_decode($DataEtiqueta["distrito"]),$this->ENCUADRADO,3,"C");
		$this->Celda($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA - 0.5, utf8_decode($DataEtiqueta["provincia"]),$this->ENCUADRADO,3,"C");
		$this->Celda($this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD + 2, $this->ALTO_CELDA - 0.5, utf8_decode($DataEtiqueta["region"]),$this->ENCUADRADO,3,"C");

		$this->Rect($XTemporal + $this->MARGEN_CAJA_CENTRAL_MITAD_MITAD , $YTemporal, 
					$this->ANCHO_RECIBO - $this->MARGEN_CAJA_CENTRAL_MITAD , 33.5);

		$this->SaltoLinea(3.5);

		$ALTO_CELDA_POST =$this->ALTO_CELDA + 1.25;

		$ANCHO_ETIQUETA_IZQUIERDA = 15;
		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode($this->STR_FIRMA),$this->ENCUADRADO,0,"L");
		$this->Celda($this->ANCHO_RECIBO - $ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode("________________________________"),$this->ENCUADRADO,3,"L");
		$this->SetXY($coordenadas["X"], $this->GetY());

		$ANCHO_ETIQUETA_IZQUIERDA = 15;
		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode($this->STR_DNI),$this->ENCUADRADO,0,"L");
		$this->Celda($this->ANCHO_RECIBO - $ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode("________________________________"),$this->ENCUADRADO,3,"L");
		$this->SetXY($coordenadas["X"], $this->GetY());

		$ANCHO_ETIQUETA_IZQUIERDA = 15;
		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode($this->STR_NOMBRE),$this->ENCUADRADO,0,"L");
		$this->Celda($this->ANCHO_RECIBO - $ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode("________________________________"),$this->ENCUADRADO,3,"L");
		$this->SetXY($coordenadas["X"], $this->GetY());

		$ANCHO_ETIQUETA_IZQUIERDA = 22.5;
		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode($this->STR_PARENTESCO),$this->ENCUADRADO,0,"L");
		$this->Celda($this->ANCHO_RECIBO - $ANCHO_ETIQUETA_IZQUIERDA, $ALTO_CELDA_POST, utf8_decode("___________________________"),$this->ENCUADRADO,3,"L");
		$this->SetXY($coordenadas["X"], $this->GetY());

		$ANCHO_ETIQUETA_IZQUIERDA_FECHA = 15;
		$ANCHO_ETIQUETA_IZQUIERDA_FECHA_LLENAR = 22.5;
		$ANCHO_ETIQUETA_IZQUIERDA_HORA = 10;

		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA_FECHA, $this->ALTO_CELDA, utf8_decode($this->STR_FECHA),$this->ENCUADRADO,0,"L");
		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA_FECHA_LLENAR, $this->ALTO_CELDA, utf8_decode("____/____/______"),$this->ENCUADRADO,0,"L");

		$this->Celda($ANCHO_ETIQUETA_IZQUIERDA_HORA, $this->ALTO_CELDA, utf8_decode($this->STR_HORA),$this->ENCUADRADO,0,"L");
		$this->Celda($this->ANCHO_RECIBO - ($ANCHO_ETIQUETA_IZQUIERDA_HORA + $ANCHO_ETIQUETA_IZQUIERDA_FECHA_LLENAR + $ANCHO_ETIQUETA_IZQUIERDA_FECHA), $this->ALTO_CELDA, utf8_decode("_________"),$this->ENCUADRADO,3,"L");
		$this->SetXY($coordenadas["X"], $this->GetY());

		$this->SaltoLinea(1.5);

		$X2LineaVertical = $this->GetX() + $this->ANCHO_RECIBO;
		$Y2LineaVertical = $this->GetY();
		$this->Line($this->GetX(), $this->GetY(), $X2LineaVertical, $Y2LineaVertical);

		/*Linea Vertical Derecha*/
		$this->Line($X1LineaVertical, $Y1LineaVertical, $X2LineaVertical, $Y2LineaVertical);	

		$this->SetDash();
	}

	function SetDash($black=null, $white=null){
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
}

$id_pedido =  isset($_GET["p_id"]) ? $_GET["p_id"] : NULL;

if ($id_pedido == NULL){
	echo "Pedido no válido";
	exit();
}

try {

	$sesion = isset($_SESSION) ? $_SESSION["sesion"] : NULL;

	if ($sesion == NULL){
		echo "Permiso insuficiente para ver este reporte";
		exit;
	}

	if ($sesion["id_tipo_usuario"] != "1"  && $sesion["id_tipo_usuario"] != "2"){
		echo "Permiso insuficiente para ver este reporte";
		exit;
	}

	$objPedido = new Pedido();
	$objPedido->id_pedido = $id_pedido;
	$request = $objPedido->obtenerPedidosOrdenParaEtiqueta();
	$registros = $request["registros"];
	$razon_social = $request["razon_social"];

	$total_registros = count($registros);
	if ($total_registros <= 0){
		echo "No hay registros que imprimir.";
		exit();
	}

	$pdf = new PDF('P','mm','A4');
	$pdf->SetRazonSocialCliente($razon_social);

	/*
	$data = [
		"codigo_remito" =>"050-44-000001",
		"campaña" => "C-16",
		"destinatario" =>"KLOPEZ EVARITOS AUREA ISABEL",
		"direccion" =>"PJE ANDRES ESTRADA H17 ENNRE UNA VENIUDAD Y OTRA AVENIDAD",
		"telefono_uno"=>"43411692",
		"telefono_dos"=>"968858313",
		"celular"=>"96885913",
		"distrito" =>"CASMA",
		"provincia" =>"CASMA",
		"region" =>"ANCASH",
	];
	*/
	$i = 0; $j = 0; $indiceContador = 0;
	while($total_registros--){
		if ($indiceContador % 9 == 0 || ($indiceContador == 0)){
			$pdf->AddPage();
			$i = 0;
			$j = 0;
		} else {
			if ($i >= 2){
				$j++;
				$i = 0;
			} else{
				$i++;
			}
		}
		$pdf->ImprimirEtiqueta($registros[$indiceContador], $i, $j);	
		$indiceContador++;
	}

	/*
	for ($i=0; $i < 3 ; $i++) { 
		for ($j=0; $j < 3; $j++) { 
			$pdf->ImprimirEtiqueta($data, $i, $j);
		}
	}
	*/
	
	//$pdf->ImprimirEtiqueta($data, 2,2);
	/*
	X.Y
	============
	_______________________________|
	===========
	QR
	QRQRQRQRQRQRQRQRQRQRQRQRQRQR
	          =========
	SQUEARE
			==============
		CLIENTE (MAX 1 LINEA)
	DIRECCION (MULTILINEA)=======
	==============================
	============================== (MAX 3 LINEAS)
	Telf 1,2 / cel
			distr
			provincia
			regi
	ENDQUARE
	Firma:		__________________
	DNI : 		__________________
	Nom:		__________________
	Parentesco:	__________________
	FECHA: __ / ___ /____ HORA: ___
	--------------------------------

    $p_anio  = isset($_GET["p_anio"]) ? $_GET["p_anio"] : null;
    $idUsuario  = isset($_GET["p_idu"]) ? $_GET["p_idu"] : "";
    $opcionPagado  = isset($_GET["p_ep"]) ? $_GET["p_ep"] : "";

    $objP = new PropiedadFacturacion();
    $objP->setAnioFacturacion($p_anio == null ? date("Y") : $p_anio);
	$lista_recibos = $objP->listarRecibos($idUsuario, $opcionPagado, true);

	if (count($lista_recibos) == 1) {
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->imprimirRecibo($lista_recibos[0], -1);
	} 
	else {
		$pdf = new PDF('L','mm','A4');
		$pdf->SetAutoPageBreak(false);
		$parimparer = 0;
		foreach ($lista_recibos as $key => $recibo) {
			if ($parimparer == 0){
				$pdf->AddPage();
				$pdf->imprimirRecibo($recibo, 0);		
				$parimparer++;
			} else {
				$pdf->imprimirRecibo($recibo, 1);
				$parimparer =0;
			}
		}
	}
	*/
	
	$pdf->Close();
	$pdf->Output();
} catch (Exception $exc) {
	echo $exc->getMessage();
}   

