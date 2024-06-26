<?php 

include_once '../datos/variables.vista.php';
require_once "../phpexcel/PHPExcel.php";
require_once "../modelo/Pedido.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

    try {

    	$hoy = date('Y-m-d');
	    $id_pedido = isset($_GET["p_id"]) ? $_GET["p_id"] : NULL;

	    $fecha_inicio = isset($_GET["p_fi"]) ? $_GET["p_fi"] :  $hoy;
	    $fecha_final = isset($_GET["p_ff"]) ? $_GET["p_ff"] : $hoy;
	   
    	$objPHPExcel = new PHPExcel();    
	    $pedido = new Pedido();

	    $pedido->id_pedido = $id_pedido;
	   	$dataGeneral = $pedido->reporteOrdenesCompletadasFuxion($fecha_inicio, $fecha_final);

		$objPHPExcel->setActiveSheetIndex(0);

		$alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$sheetActivo = $objPHPExcel->getActiveSheet();

		$actualFila = 1;

		$arregloCabecera = [
							["ancho"=>16,"rotulo"=>"Remito"],
							["ancho"=>16,"rotulo"=>"Código Tracking"],
							["ancho"=>16,"rotulo"=>"Código Exigo"],
							["ancho"=>16,"rotulo"=>"Envoltura"],
							["ancho"=>20,"rotulo"=>"Fecha Courier"],
							["ancho"=>45,"rotulo"=>"Destinatario"],
							["ancho"=>65,"rotulo"=>"Dirección Entrega"],
							["ancho"=>16,"rotulo"=>"Fecha Visita 1"],
							["ancho"=>50,"rotulo"=>"Status Visita 1"],
							["ancho"=>16,"rotulo"=>"Fecha Visita 2"],
							["ancho"=>50,"rotulo"=>"Status Visita 2"],
							["ancho"=>50,"rotulo"=>"Observaciones Entrega"],
							["ancho"=>20,"rotulo"=>"Status"],
							["ancho"=>45,"rotulo"=>"Datos Receptor"],
							["ancho"=>16,"rotulo"=>"Doc. Receptor"],
							["ancho"=>22,"rotulo"=>"Distrito"],
							["ancho"=>22,"rotulo"=>"Provinca"],
							["ancho"=>16,"rotulo"=>"Región"],
							["ancho"=>20,"rotulo"=>"País"],
							["ancho"=>25,"rotulo"=>"Referencia"],
							["ancho"=>20,"rotulo"=>"Correo"],
							["ancho"=>16,"rotulo"=>"Teléfono"],
							["ancho"=>16,"rotulo"=>"Contacto"],
							["ancho"=>20,"rotulo"=>"Documentos"],
							["ancho"=>16,"rotulo"=>"Courier"],
							["ancho"=>10,"rotulo"=>"Volumen"]
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
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["codigo_remito"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["codigo_tracking"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["codigo_exigo"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["envoltura"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["fecha_courier"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["destinatario"]));
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["direccion_uno"]));

			$visita_uno = strtoupper($fila["visita_uno"]);
			$visita_dos = strtoupper($fila["visita_dos"]);

			if ($visita_uno == ""){
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, "");
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, "");	
			} else {
				$ar_ = explode('|', $visita_uno);
				
				$estado  = ($ar_[0] == "G" ? "VISITADO - AUSENTE" :  ($ar_[0] == "E" ? "ENTREGADO" :  "VISITADO - MOTIVADO"));
				$observacion  = $ar_[1];
				$fecha  = $ar_[2];
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fecha);
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $estado);	
			
			}

			if ($visita_dos == ""){
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, "");
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, "");	
			} else {
			    
				$ar_ = explode('|', $visita_dos);
				
				$estado  = ($ar_[0] == "G" ? "VISITADO - AUSENTE" :  ($ar_[0] == "E" ? "ENTREGADO" :  "VISITADO - MOTIVADO"));
				$observacion  = $ar_[1];
				$fecha  = $ar_[2];
				
				
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fecha);
				$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $estado);	
			}
			

			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["observaciones"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["status"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["receptor"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["numero_documento_receptor"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["distrito"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["provincia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["region"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["pais"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["referencia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["correo_contacto"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["telefono_contacto"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["contacto"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["documentos"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["rotulo_courier"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["volumen"]);

			$actualFila++;
		}
		
		
		$titulo_xls = 'REPORTE COURIER';
		$sheetActivo->setTitle($titulo_xls);	
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$titulo_xls.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
    } catch (Exception $exc) {
    	print_r(["state"=>500,"msj"=>$exc->getMessage()]);
    }   

    /*
  } else {
  	print_r(["state"=>400,"msj"=>"Faltan parámetros"]);
  }
    
	*/
