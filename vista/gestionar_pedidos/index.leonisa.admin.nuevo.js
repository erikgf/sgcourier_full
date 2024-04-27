var TABLA;
var DATA;

var $tbl,
	$mdl,
	$blkAlert,
	$frmRegistro;

var loadedEstados = false;

$(function(){
    eventos();
    listarEstados();
});

const initDT = function(registros) {

	/*
	if (TABLA){
    	TABLA.destroy();
    }
	*/

	$tbl.find("tbody").html(renderDetalleTabla(registros.map(r => {
		return {
			...r, 
			cantidades : r.cantidades.split(",").map(r => {
				const [ id, cantidad ] = r.split("|");
				return {
					id , cantidad: parseInt(cantidad)
				}
			})
		}
	})));

	/*
    if (registros.length){
      	TABLA = $tbl.DataTable({
      		language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
      	});
    }}
	*/
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

const renderDetalleTabla = function (data) {
	let html = "";

	for (let i = 0; i < data.length; i++){
		const o = data[i];
		let htmlCantidades = "";
		o.cantidades.forEach(c => {
			const porcentaje = parseFloat(c.cantidad / o.cantidad) * 100;
			htmlCantidades += `<td style="font-size:.925em" class="text-center">`+c.cantidad+` <b class="text-`+colorPorcentaje(porcentaje, false)+`">(`+porcentaje.toFixed(2)+` %)</b></td>`;
		});

		html += `<tr>
					<th scope="row" class="text-center">
						<a title="VER" target="blank" href="../gestionar_ordenes_nuevo/index.leonisa.admin.nuevo.php?p_id=`+o.id+`" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>	
						<button title="ELIMINAR" onclick="eliminar($(this),`+o.id+`)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>	
					</th>
					<td class="text-center">`+o.id_pedido_log+`</td>
					<td class="text-center"><small>`+o.fecha_ingreso+`</small></td>
					<td style="font-size:.925em" class="text-center">`+o.cantidad+`</td>
					${htmlCantidades}
				</tr>`;
	};

	return html;
};

const colorPorcentaje = function(valorPorcentual, mejorElAlto){
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
const guardar = function(){
	const $blkAlertModal = $mdl.find("#blk-alert-modal");

	if (guardando){
		return;
	}

	guardando =  true;
	const $btnGuardar = $mdl.find("#btn-guardar");
	const temporalHTML = $btnGuardar.html();
	$btnGuardar.html(CADENAS.CARGANDO);

	  const fn = function(xhr){
	  	const datos = xhr.datos,
	  		$blkAlert = $("#blk-alert");

	  	$mdl.modal("hide");
		Util.alert($blkAlert, datos.msj, "success");
		$blkAlert.focus();

		listar();
	  };

	  const fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($blkAlertModal, xhr.response, "danger");	
	  };

	  const fnAlways = function(){
  		guardando = false;
  		$btnGuardar.html(temporalHTML);
	  };

	  const datos_frm = new FormData($frmRegistro[0]);
	  	  /*
	       datos_frm.append("p_id_registro_mp_producto", id_registro_mp_producto);
	       datos_frm.append("p_img", imagen.img_file);
	       */
	   $.ajax({
		    url: "../../controlador/nuevo.pedidos.php?op=registrar",
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

var listando = false;
const listar = function() {
	const $btnBuscar = $("#btnbuscar");
	const temporalHTML = $btnBuscar.html();
	const postData = {
	  	p_fechainicio : $("#txtfechainicio").val(),
	    p_fechafin : $("#txtfechafin").val()
	};
	const fn = function(xhr){
		initDT(xhr.datos);
	};
	const fnFail = function(xhr){
		Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	};

	const fnAlways = function(){
		listando = false;
		$btnBuscar.html(temporalHTML);
	};

	if (loadedEstados === false){
		return;
	}

	if (listando === true){
		return;
	}

	listando = true;
	$btnBuscar.html(CADENAS.CARGANDO);

	$.post("../../controlador/nuevo.pedidos.php?op=listar_leonisa", postData)
		.done(fn)
		.fail(fnFail)
		.always(fnAlways);
};

var listarEstados = function() {
	const fn = function(xhr){
		const {datos} = xhr;
		let htmlEstados = ``;

		datos.forEach(e => {
			htmlEstados += `<td scope="col" class="text-center bg-${e.estado_color} text-white">${e.descripcion}</td>`;
		});

		$("#tbllistado thead tr").append(htmlEstados);

		loadedEstados = true;
		listar(); 
	}

	const fnFail = function(xhr){
		Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	};

	$.post("../../controlador/estado.orden.php?op=obtener")
		.done(fn)
		.fail(fnFail);
};

const htmlSelect = function (data) {
	let html = `<option value="">Seleccionar</option>`;

	for (let i = 0; i < data.length; i++){
		const o = data[i];
		html += `<option value="`+o.id+`">`+o.descripcion+`</option>`;
	};

	return html;
};

var eliminando = false;
var eliminar = function($btn, id_pedido){
	if (!confirm("¿Está seguro de realizar esta acción?")){
		return;
	}

	const $blkAlert = $("#blk-alert");

	if (id_pedido == null){
		return;
	}

	if (eliminando){
		return;
	}

	eliminando =  true;
	const temporalHTML = $btn.html();
	$btn.html(CADENAS.CARGANDO);

	const postData = {
		id_pedido : id_pedido
	};

	const fn = function(xhr){
		const datos = xhr.datos;
		Util.alert($("#blk-alert"), datos.msj, "success");	
		listar();
	};

	const fnFail = function(xhr){
	if (xhr.responseJSON && xhr.responseJSON.mensaje){
		Util.alert($blkAlert, xhr.responseJSON.mensaje, "danger");	
		return;
	}
	
	Util.alert($blkAlert, xhr.response, "danger");	
	};

	const fnAlways = function(){
		eliminando = false;
		$btn.html(temporalHTML);
	};

	$.post("../../controlador/nuevo.pedidos.php?op=eliminar", postData)
		.done(fn)
		.fail(fnFail)
		.always(fnAlways);	
};