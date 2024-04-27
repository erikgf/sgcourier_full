var TABLA,
	COL_DEFS = [75, 100, 150, 150,150,150];
var DATA;

var $tbl;

$(function(){
	$(".select2").select2();

    eventos();
    listar();

});

var initDT = function(registros) {
	if (TABLA){
    	TABLA.destroy();
    }

	$('#tbllistado').find("tbody").html(renderDetalleTabla(registros));

    if (registros.length){
    	/*
    	var columnDefs = [];
    	for (var i = 0; i < COL_DEFS.length; i++) {
    		columnDefs.push({width: COL_DEFS[i]+"px", targets : i});
    	};
    	*/
    } 

    TABLA = $('#tbllistado').DataTable({
    	language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
    });
};

var eventos = function() {
	$tbl = $("#tbllistado");

	$("#btnbuscar").on("click", function(e){
		e.preventDefault();
		listar();
	})
};

var listar = function() {
	var postData = {
	  	p_fechainicio : $("#txtfechainicio").val(),
	    p_fechafin : $("#txtfechafin").val()
	  };

	  var fn = function(xhr){
	      var datos = xhr.datos;
		  initDT(datos);
	  };

	  var fnFail = function(xhr){
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	  };

	  $.post("../../controlador/pedidos.php?op=listar_x_usuario", postData)
	      .done(fn)
	      .fail(fnFail);
};


var renderDetalleTabla = function (data) {
	var html = "";

	for (var i = 0; i < data.length; i++){
		var o = data[i],
			porc_noasignado = parseFloat(o.cantidad_noasignadas / o.cantidad) * 100,
			porc_gestionando = parseFloat(o.cantidad_gestionando / o.cantidad) * 100,
			porc_entregado  = parseFloat(o.cantidad_entregadas / o.cantidad) * 100,
			porc_motivado = parseFloat(100 - (porc_noasignado + porc_gestionando + porc_entregado));

		html += `<tr>
					<th scope="row" class="text-center">
						<small style="display:none;">`+o.codigo_remitos+`</small>
						<a href="../gestionar_ordenes/index.php?p_id=`+o.id+`" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> VER</a>	
						<a href="../../controlador/reporte.pedido.xls.php?p_id`+o.id+`" target="_blank" class="btn btn-xs btn-success"><i class="fa fa-file-excel"></i> EXCEL¿</a>	
					</th>
					<td class="text-center">`+o.id_pedido_log+`</td>
					<td class="text-center"><small>`+o.fecha_ingreso+`</small></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad+`</td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_noasignadas+` <b class="text-`+colorPorcentaje(porc_noasignado, false)+`">(`+porc_noasignado.toFixed(2)+` %)</b></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_gestionando+` <b class="text-`+colorPorcentaje(porc_gestionando, false)+`">(`+porc_gestionando.toFixed(2)+` %)</b></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_entregadas+` <b class="text-`+colorPorcentaje(porc_entregado, true)+`">(`+porc_entregado.toFixed(2)+` %)</b></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_motivadas+` <b class="text-`+colorPorcentaje(porc_motivado, false)+`">(`+porc_motivado.toFixed(2)+` %)</b></td>
				</tr>`;
	};

	return html;
};


var colorPorcentaje = function(valorPorcentual, mejorElAlto){
	var RESPUESTAS_COLOR;
	if (mejorElAlto){
		RESPUESTAS_COLOR = {
			"bajo" : "danger",
			"medio" : "warning",
			"alto" : "success"
		};
	} else {
		RESPUESTAS_COLOR = {
			"bajo" : "success",
			"medio" : "warning",
			"alto" : "danger"
		};
	}

	if (valorPorcentual < 40.00){
		return RESPUESTAS_COLOR.bajo;
	}

	if (valorPorcentual < 70.00){
		return RESPUESTAS_COLOR.medio;
	}

	return RESPUESTAS_COLOR.alto;
};


var generarExcel = function(){
	window.open("../../controlador/reporte.pedido.xls.php?p_fi="+$("#txtfechainicio").val()+"&p_ff="+$("#txtfechafin").val(),"_blank");
};