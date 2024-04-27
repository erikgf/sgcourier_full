<?php 

include_once '../datos/variables.vista.php';
include_once "../phspreadsheet/vendor/autoload.php";
require_once "../modelo/NuevoPedido.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    try {

    	$hoy = date('Y-m-d');
	    $id_pedido = isset($_GET["p_id"]) ? $_GET["p_id"] : NULL;
        
	    $fecha_inicio = isset($_GET["p_fi"]) ? $_GET["p_fi"] :  $hoy;
	    $fecha_final = isset($_GET["p_ff"]) ? $_GET["p_ff"] : $hoy;
	   
    	$objPHPExcel = new Spreadsheet();    
	    $pedido = new Pedido();

	    $pedido->id_pedido = $id_pedido;
	   	$dataGeneral = $pedido->reporteNuevasOrdenesCompletadasLeonisa($fecha_inicio, $fecha_final);
	   	
		$objPHPExcel->setActiveSheetIndex(0);

		$alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$sheetActivo = $objPHPExcel->getActiveSheet();

		$actualFila = 1;

		$arregloCabecera = [
		                    ["ancho"=>9,"rotulo"=>"Zona"],
							["ancho"=>12,"rotulo"=>"Número Orden"],
							["ancho"=>12,"rotulo"=>"Guía"],
							["ancho"=>12,"rotulo"=>"Número Documento Destinatario"],
							["ancho"=>50,"rotulo"=>"Destinatario"],
							["ancho"=>50,"rotulo"=>"Direccion"],
							["ancho"=>20,"rotulo"=>"Barrio"],
							["ancho"=>14,"rotulo"=>"Distrito"],
							["ancho"=>14,"rotulo"=>"Provincia"],
							["ancho"=>14,"rotulo"=>"Departamento"],
							["ancho"=>15,"rotulo"=>"Status"],
							["ancho"=>50,"rotulo"=>"Observaciones"],
							["ancho"=>13,"rotulo"=>"Telefono 1"],
							["ancho"=>13,"rotulo"=>"Telefono 2"],
							["ancho"=>12,"rotulo"=>"Numero Guia Mas."],
							["ancho"=>15,"rotulo"=>"Repartidor"],
							["ancho"=>12,"rotulo"=>"Entrega Porteria"],
							["ancho"=>10,"rotulo"=>"Valor"],
							["ancho"=>14,"rotulo"=>"Fecha Entrega"],
							["ancho"=>14,"rotulo"=>"Hora Entrega"]
						];

		foreach ($arregloCabecera as $key => $value) {
			$columna = $alfabeto[$key];
			$sheetActivo->setCellValue($columna.$actualFila, $value["rotulo"]);
			$sheetActivo->getColumnDimension($columna)->setWidth($value["ancho"]);
		}

		$cabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 9));
		$sheetActivo->getStyle('A'.$actualFila.':'.$columna.$actualFila)->applyFromArray($cabeceraEstilos);

		$actualFila++;

		foreach ($dataGeneral as $key => $fila) {
			$col = 0;
			
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["zona"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["codigo_numero_orden"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["codigo_guia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["numero_documento_destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["direccion"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["barrio"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["distrito"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["provincia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["departamento"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["status"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["observaciones"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["telefono_uno_destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["telefono_dos_destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["numero_guia_mas"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["repartidor"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["entrega_porteria"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["valor"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["fecha_entrega"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["hora_entrega"]);

			$actualFila++;
		}
		
		
		$titulo_xls = 'REP.COURIER-LEONISA';
		
	    $objPHPExcel->getActiveSheet()->setTitle($titulo_xls);
	    
        $writer = new Xlsx($objPHPExcel);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
        $writer->save('php://output');
		exit;
    } catch (Exception $exc) {
    	print_r(["state"=>500,"msj"=>$exc->getMessage()]);
    }   

    /*
  } else {
  	print_r(["state"=>400,"msj"=>"Faltan parámetros"]);
  }
    
	*/
