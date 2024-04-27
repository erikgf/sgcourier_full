<?php 

include_once '../datos/variables.vista.php';
require_once "../phpexcel/PHPExcel.php";
require_once "../modelo/SisPreliquidacion.clase.php";
require_once "../modelo/util/funciones/Funciones.php";

    try {
    	$hoy = date('Y-m-d');
	    $id_agencia = isset($_GET["p_id"]) ? $_GET["p_id"] : "*";
	    $fecha_inicio = isset($_GET["p_fi"]) ? $_GET["p_fi"] :  $hoy;
	    $fecha_final = isset($_GET["p_ff"]) ? $_GET["p_ff"] : $hoy;
	        
	    ini_set('memory_limit', '512M');
    	$objPHPExcel = new PHPExcel();    
	    $preliquidaciones = new SisPreliquidacion();

	   	$dataGeneral = $preliquidaciones->reportePreliquidacionesXLS($id_agencia, $fecha_inicio, $fecha_final);
	   	

		$objPHPExcel->setActiveSheetIndex(0);

		$alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$sheetActivo = $objPHPExcel->getActiveSheet();

		$actualFila = 1;

		$arregloCabecera = [
							["ancho"=>16,"rotulo"=>"Código"],
							["ancho"=>16,"rotulo"=>"Fecha Registro"],
							["ancho"=>16,"rotulo"=>"Num. Doc Repartidor"],
							["ancho"=>40,"rotulo"=>"Repartidor"],
							["ancho"=>15,"rotulo"=>"Tipo Vehiculo"],
							["ancho"=>22,"rotulo"=>"Agencia"],
							["ancho"=>36,"rotulo"=>"Responsable"],
							["ancho"=>16,"rotulo"=>"Costo Subtotales"],
							["ancho"=>16,"rotulo"=>"Costo Extra"],
							["ancho"=>16,"rotulo"=>"Costo Entrega Total"],
							["ancho"=>16,"rotulo"=>"Estado"],
							["ancho"=>40,"rotulo"=>"Cliente"],
							["ancho"=>40,"rotulo"=>"Cliente Interno"],
							["ancho"=>20,"rotulo"=>"Doc. Guía"],
							["ancho"=>10,"rotulo"=>"Cantidad"],
							["ancho"=>10,"rotulo"=>"Peso"],
							["ancho"=>10,"rotulo"=>"Volumen"],
							["ancho"=>20,"rotulo"=>"Tipo Paquete"],
							["ancho"=>45,"rotulo"=>"Dirección"],
							["ancho"=>22,"rotulo"=>"Zona/Ciudad"],
							["ancho"=>30,"rotulo"=>"Motivación Observación"],
							["ancho"=>16,"rotulo"=>"Costo Unitario"],
							["ancho"=>16,"rotulo"=>"Subtotal"]
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
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["codigo"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["fecha_registro"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["numero_documento_repartidor"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["repartidor"]));
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["tipo_vehiculo"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["agencia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["responsable"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["costo_global_extra"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["costo_total_subtotales"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["costo_total_subtotales"] +  $fila["costo_global_extra"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["estado"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["cliente"]));
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["cliente_interno"]));
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["documento_guia"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["cantidad"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["peso"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["volumen"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["tipo_paquete"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["direccion_entrega"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["lugar_entrega"]));
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, strtoupper($fila["descripcion_motivacion"]));
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["costo_unitario"]);
			$sheetActivo->setCellValue($alfabeto[$col++].$actualFila, $fila["subtotal"]);

			$actualFila++;
		}
		
		
		$titulo_xls = 'REPORTE PRELIQUIDACIONES - '.date("Ymd");
		$sheetActivo->setTitle("PRELIQUIDACIONES");	
		
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
		
		ini_set('memory_limit', '128M');
		exit;
		
		
    } catch (Exception $exc) {
    	print_r(["state"=>500,"msj"=>$exc->getMessage()]);
    }   

    /*
  } else {
  	print_r(["state"=>400,"msj"=>"Faltan parámetros"]);
  }
    
	*/
