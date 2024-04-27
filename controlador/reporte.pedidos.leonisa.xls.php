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
	   	$dataGeneral = $pedido->reporteOrdenesCompletadasLeonisa($fecha_inicio, $fecha_final);

		$objPHPExcel->setActiveSheetIndex(0);

		$alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$sheetActivo = $objPHPExcel->getActiveSheet();

		$actualFila = 1;

		$arregloCabecera = [
							["ancho"=>10,"rotulo"=>"Campaña"],
							["ancho"=>12,"rotulo"=>"Número Orden"],
							["ancho"=>12,"rotulo"=>"Guía"],
							["ancho"=>12,"rotulo"=>"Cédula"],
							["ancho"=>50,"rotulo"=>"Nombre"],
							["ancho"=>14,"rotulo"=>"Fecha Embarque"],
							["ancho"=>14,"rotulo"=>"Hora Embarque"],
							["ancho"=>15,"rotulo"=>"Tercera Visita"],
							["ancho"=>50,"rotulo"=>"Observaciones"],
							["ancho"=>13,"rotulo"=>"Telefono 1"],
							["ancho"=>13,"rotulo"=>"Telefono 2"],
							["ancho"=>10,"rotulo"=>"Zona"],
							["ancho"=>25,"rotulo"=>"Departamento"],
							["ancho"=>25,"rotulo"=>"Ciudad"],
							["ancho"=>50,"rotulo"=>"Direccion"],
							["ancho"=>12,"rotulo"=>"Numero Guia Mas."],
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
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["campana"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["numero_orden"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["guia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["cedula"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["fecha_embarque"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["hora_embarque"]);
			
			/*
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
			*/
    
            $sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["status"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["observaciones"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["telefono_uno_destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["telefono_dos_destinatario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["zona"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["departamento"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["ciudad"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["direccion_uno"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["numero_guia_mas"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["entrega_porteria"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["valor"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["fecha_entrega"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["hora_entrega"]);

			$actualFila++;
		}
		
		
		$titulo_xls = 'REP.COURIER-LEONISA';
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
