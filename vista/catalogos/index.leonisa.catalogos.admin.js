var TABLA;
var DATA;

var $tbl,
	$mdl,
	$blkAlert,
	$frmRegistro;

$(function(){

    eventos();
    listar();
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
	$mdl = $("#mdl-registro");
	$blkAlert = $("#blk-alert");
	$frmRegistro = $("#frm-registro");

	$("#btnnuevo").on("click", function(e){
		e.preventDefault();
		$mdl.modal("show");
	});

	$mdl.on("hidden.bs.modal", function(e){
		e.preventDefault();
		$frmRegistro[0].reset();
	});

	$frmRegistro.on("submit", function(e){
		e.preventDefault();
		guardar();
	});

	$("#btnbuscar").on("click", function(e){
		e.preventDefault();
		listar();
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
						<a title="VER" target="blank" href="../catalogos_ordenes/index.leonisa.admin.php?p_id=`+o.id+`" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>	
						<button title="ELIMINAR" onclick="eliminar($(this),`+o.id+`)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>	
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


var guardando = false;
var guardar = function(){
	var $btnGuardar,
		$blkAlertModal = $mdl.find("#blk-alert-modal"),
		temporalHTML;
	if (guardando){
		return;
	}

	guardando =  true;
	$btnGuardar = $mdl.find("#btn-guardar");
	temporalHTML = $btnGuardar.html();
	$btnGuardar.html(CADENAS.CARGANDO);

	  var fn = function(xhr){
	  	var datos = xhr.datos,
	  		$blkAlert = $("#blk-alert");

	  	$mdl.modal("hide");
		Util.alert($blkAlert, datos.msj, "success");
		$blkAlert.focus();

		listar();
	  };

	  var fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($blkAlertModal, xhr.response, "danger");	
	  };

	  var fnAlways = function(){
  		guardando = false;
  		$btnGuardar.html(temporalHTML);
	  };

	  var datos_frm = new FormData($frmRegistro[0]);
	  	  /*
	       datos_frm.append("p_id_registro_mp_producto", id_registro_mp_producto);
	       datos_frm.append("p_img", imagen.img_file);
	       */
	   $.ajax({
		    url: "../../controlador/pedidos.php?op=registrar",
		    type: "POST",
		    data: datos_frm,
		  	contentType: false,
            processData: false,
		   	cache: false
		 })
		 	.done(fn)
	     	.fail(fnFail)
	     	.always(fnAlways);	
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

	  $.post("../../controlador/pedidos.php?op=listar_leonisa_catalogos", postData)
	      .done(fn)
	      .fail(fnFail);
};


var htmlSelect = function (data) {
	var html = `<option value="">Seleccionar</option>`;

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<option value="`+o.id+`">`+o.descripcion+`</option>`;
	};

	return html;
};

var eliminando = false;
var eliminar = function($btn, id_pedido){
	if (!confirm("¿Está seguro de realizar esta acción?")){
		return;
	}

	var $blkAlert = $("#blk-alert");

	if (id_pedido == null){
		return;
	}

	if (eliminando){
		return;
	}

	eliminando =  true;
	var temporalHTML = $btn.html();
	$btn.html(CADENAS.CARGANDO);

	  var postData = {
	  	id_pedido : id_pedido
	  };

	  var fn = function(xhr){
	  	var datos = xhr.datos;
		Util.alert($("#blk-alert"), datos.msj, "success");	
		listar();
	  };

	  var fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($blkAlert, xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($blkAlert, xhr.response, "danger");	
	  };

	  var fnAlways = function(){
  		eliminando = false;
  		$btn.html(temporalHTML);
	  };

	  $.post("../../controlador/pedidos.php?op=eliminar", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);	
};