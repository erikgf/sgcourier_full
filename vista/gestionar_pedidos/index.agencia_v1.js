var TABLA;
var DATA;

var $tbl,
	$mdl,
	$blkAlert,
	$frmRegistro;

$(function(){
    eventos();
    listar();
    //obtenerDatos();
});

var initDT = function(registros) {
	if (TABLA){
    	TABLA.destroy();
    }

	$tbl.find("tbody").html(renderDetalleTabla(registros));

    if (registros.length){
      	TABLA = $tbl.DataTable({
      		language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
      	});
    }
};

var eventos = function() {
	$tbl = $("#tbllistado");

	$("#btnbuscar").on("click", function(e){
		e.preventDefault();
		listar();
	});
};

var obtenerDatos = function() {
	$.post("registros.admin.json","json")
		.done(function(data) {
			DATA = data;

			initDT(data.registros);
		});
};

var renderDetalleTabla = function (data) {
	var html = "";

	for (var i = 0; i < data.length; i++){
		var o = data[i],
			porc_noasignado = parseFloat(o.cantidad_noasignado / o.cantidad) * 100,
			porc_gestionando = parseFloat(o.cantidad_gestionando / o.cantidad) * 100,
			porc_entregado  = parseFloat(o.cantidad_entregado / o.cantidad) * 100,
			porc_motivado = parseFloat(100 - (porc_noasignado + porc_gestionando + porc_entregado));

		html += `<tr>
					<th scope="row" class="text-center">
						<a href="../gestionar_ordenes/index.agencia.php?p_id=`+o.id+`" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> VER</a>	
					</th>
					<td class="text-center">`+o.id_pedido_log+`</td>
					<td class="text-center"><small>`+o.fecha_ingreso+`</small></td>
					<td title="Celular: `+o.celular+`"><small>[`+o.numero_documento+`]</small> `+o.razon_social+`</td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad+`</td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_noasignado+` <b class="text-`+colorPorcentaje(porc_noasignado, false)+`">(`+porc_noasignado.toFixed(2)+` %)</b></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_gestionando+` <b class="text-`+colorPorcentaje(porc_gestionando, false)+`">(`+porc_gestionando.toFixed(2)+` %)</b></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_entregado+` <b class="text-`+colorPorcentaje(porc_entregado, true)+`">(`+porc_entregado.toFixed(2)+` %)</b></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad_motivado+` <b class="text-`+colorPorcentaje(porc_motivado, false)+`">(`+porc_motivado.toFixed(2)+` %)</b></td>
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


var alert = function($blk, mensaje, tipoMensaje){
	var $tmpHtml = $(`<div class="alert alert-`+tipoMensaje+`" role="alert">`+mensaje+`</div>`);
	$blk.html($tmpHtml);
	setTimeout(function() {
		if ($tmpHtml){
			$tmpHtml.remove();
		}
	},4000);
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

	  $.post("../../controlador/pedidos.php?op=listar", postData)
	      .done(fn)
	      .fail(fnFail);
};

