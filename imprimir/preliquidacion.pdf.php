<?php 
	
	require_once '../modelo/SisPreliquidacion.clase.php';
    $id_usuario = isset($_SESSION)  ? $_SESSION["sesion"]["id_usuario"] : NULL;
    if ($id_usuario == NULL){
        throw new Exception("No tiene permisos para ver esto.");
    }

	$id =  isset($_GET["p_id"]) ? $_GET["p_id"] : '';

	if ($id == ""){
		echo("ID de Preliquidacion no enviado.");
		exit;
	}

	try {
		$obj = new SisPreliquidacion();
		$obj->id_preliquidacion = $id;
		$data = $obj->obtenerImprimir();	
	} catch (Exception $e) {
		var_dump($e);
		exit;
	}
 ?>

<html lang="es">
<head>
	<title>Imprimir Preliquidación</title>
	<style type="text/css">
		body {
	        width: 100%;
	        height: 100%;
	        margin: 0;
	        padding: 0;
	        background-color: #FAFAFA;
	        font: 12pt "Tahoma";
	    }

	    * {
	        box-sizing: border-box;
	        -moz-box-sizing: border-box;
	    }

	    .page {
	        width: 210mm;
	        min-height: 297mm;
	        padding: 20mm 15mm;
	        margin: 10mm auto;
	        border: 1px #D3D3D3 solid;
	        border-radius: 5px;
	        background: white;
	        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
	    }
	    .subpage {
	        padding: 1cm;
	        border: 5px red solid;
	        height: 257mm;
	        outline: 2cm #FFEAEA solid;
	    }
	    
	    @page {
	        size: A4;
	        margin: 0;
	    }


	    @media print {
	        html, body {
	            width: 210mm;
	            height: 297mm;        
	        }
	        .page {
	            margin: 0;
	            border: initial;
	            border-radius: initial;
	            width: initial;
	            min-height: initial;
	            box-shadow: initial;
	            background: initial;
	            page-break-after: always;
	        }

	        .btnimprimir{
	        	display: none;
	        }
 
	    }


	    .btnimprimir{
	       position: absolute;
	       left: 50%;
	       top: 2cm;
	    }

		.flex{
			display: flex;
		}

		.flex-auto{
			flex: auto;
		}

		.logo{
			width: 7.5cm;
    		flex: auto;
		}

		.preliquidacion{
			flex: auto;
		    text-align: center;
		    font-size: 15pt;
		    font-weight: bold;
		    border: 1px solid black;
		    margin-left: 3cm;
		}

		.fila-cabecera{
			padding: .15cm 0px;
			font-size: 11pt;
		}

		table {
			font-size: 8.5pt;
		    margin-bottom: 1rem;
		    background-color: transparent;
		    width: 100%;
		    clear: both;
		}

		table thead{
			font-size: 7.5pt;
		}

		table thead th{
			text-align: left;
			border-bottom: 1px solid black;
		}

		table tbody td{
		    vertical-align: top;
		    padding: 4px;
		    border-left: 1px solid black;
		    border-bottom: 1px solid black;
		    text-align: justify;
		}

		.text-right{
			text-align: right;
		}

		.responsable{
			font-size: 9pt;
		}

	</style>
</head>
<body>
	<div class="book">
		<a href="#" class="btnimprimir" onclick="print();">Imprimir</a>
	    <div class="page">
	    	<div class="flex" style="padding-top: 1cm;">
	    		<img class="logo" src="../img/logo_main.jpg">
		    	<div class="preliquidacion">
		    		<p>PRELIQUIDACIÓN</p>
		    		<p><?php echo $data["codigo"]; ?></p>
		    	</div>
		    </div>

	    	<div style="padding:35px 0px 20px 0px">
	    		<div class="fila-cabecera"><b>Repartidor:</b> <?php echo $data["repartidor"]; ?></div>
	    		<div class="fila-cabecera"><b>Agencia:</b> <?php echo $data["agencia"]; ?></div>
	    		<div class="fila-cabecera"><b>Fecha Registro:</b> <?php echo $data["fecha_registro"]; ?></div>
	    	</div>	

	    	<table>
	    		<thead>
	    			<tr>
	    				  <th scope="col"></th>
	                      <th scope="col">Cliente</th>
	                      <th class="td-numeroguia" scope="col" >Doc./N. Guía</th>
	                      <th class="td-cantidad" scope="col" style="">Cant.</th>
	                      <th class="td-tipopaquete" scope="col" style="" title="Tipo de Paquete">Tipo</th>
	                      <th class="td-direccion" scope="col" style="">Dirección</th>
	                      <th class="td-ciudad" scope="col" style="">Zona/Ciudad</th>
	                      <th class="td-costosubtotal" scope="col" style="">Subtotal(S/)</th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<?php foreach ($data["registros_detalle"] as $key => $registro_detalle) :  ?>
	    			<tr>
	    				<td>
	    					<input type="checkbox"/>
	    				</td>
	    				<td><?php echo $registro_detalle["cliente"]; ?></td>
	    				<td><?php echo $registro_detalle["documento_guia"]; ?></td>
	    				<td><?php echo $registro_detalle["cantidad"]; ?></td>
	    				<td><?php echo $registro_detalle["tipo_paquete"]; ?></td>
	    				<td><?php echo $registro_detalle["direccion_entrega"]; ?></td>
	    				<td><?php echo $registro_detalle["lugar_entrega"]; ?></td>
	    				<td class="text-center"><?php echo ($registro_detalle["subtotal"] == 0.00 ? "-" : $registro_detalle["subtotal"]); ?></td>
	    			</tr>
	    			<?php endforeach;?>
	    		</tbody>
	    		<tfoot>
	    		    <tr style="font-size:16px;">
	    		        <td colspan="7" class="text-right">Costo Extra: </td>
	    		        <td class="text-center"><?php echo $data["costo_global"]; ?></td>
	    		    </tr>
	    		</tfoot>
	    		
	    	</table>

	    	<div class="flex" style="padding-top:24px;flex-wrap: wrap;">
	    		<div class="flex-auto responsable"><b>Observaciones: </b><?php echo ($data["observaciones"] == "" ? "-" : $data["observaciones"]); ?></div>
	    		<div class="flex-auto text-right"><b>Total Costo: </b>S/ <?php echo $data["costo_entrega"]; ?></div>
	    	</div>
	    	
	    	<div class="flex" style="padding-top:12px">
	    		<div class="flex-auto responsable"><b>Responsable: </b><?php echo $data["responsable"]; ?></div>
	    	</div>
	    </div>
	</div>

	<script type="text/javascript">
		setTimeout(function(){
			print();
		}, 1000);
	</script>
</body>
</html>