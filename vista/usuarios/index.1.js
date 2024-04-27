var TABLA,
	COL_DEFS = [100, 120,420, 120, 120,100];
var DATA;

$(function(){
	$mdl = $("#mdl-registro");
	$frmRegistro = $("#frm-registro");

	$mdlCambiarClave = $("#mdl-cambiarclave");
	$frmCambiarClave = $("#frm-cambiarclave");

    eventos();
    listar();

    cargarAgencias();
	//initDT(); $('#zero_config').DataTable();
});

var initDT = function(registros){
	if (TABLA){
    	TABLA.destroy();
    }

	$('#tbllistado').find("tbody").html(renderTabla(registros));

	setTimeout(function(){
	    	var columnDefs = [];
	    	for (var i = 0; i < COL_DEFS.length; i++) {
	    		columnDefs.push({width: COL_DEFS[i]+"px", targets : i});
	    	};
	    	TABLA = $('#tbllistado').DataTable({
		      order: [[ 1, "desc" ]],
	    	  scrollX : true,
	    	  sScrollXInner: "100%",
		      columnDefs: columnDefs,
		      language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
		    });
	},100);
};

var eventos = function() {
	var $tbl = $("#tbllistado");

	$("#btnnuevo").on("click", function(e){
		e.preventDefault();
		$mdl.modal("show");
	});

	$mdl.on("hidden.bs.modal", function(e){
		e.preventDefault();
		$mdl.find(".modal-title").html("Nuevo Registro");
		ID_EDITAR = null;
		$frmRegistro[0].reset();
		$("#blkclave").show();
		$("#txtclave").prop("required", true);
	});

	$mdlCambiarClave.on("hidden.bs.modal", function (e) {
		e.preventDefault();
		ID_CAMBIAR_CLAVE = null;
		$frmCambiarClave[0].reset();
	});

	$frmRegistro.on("submit", function (e) {
		e.preventDefault();
		guardar();
	});

	$frmCambiarClave.on("submit", function (e) {
		e.preventDefault();
		guardarCambioClave();
	});

	$mdl.on("shown.bs.modal", (e) => {
		$(".select2").select2({language: "es", dropdownParent: $("#frm-registro")});
	});

};

var listar = function() {
	var postData = {};

	  var fn = function(xhr){
	  	var datos = xhr.datos;
		initDT(datos);
	  };

	  var fnFail = function(xhr){
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	  };

	  var fnAlways = function(){
	  	$("#lblcargando").hide();
	  	$("#blkmain").show(300);
	  };

	  $("#lblcargando").show();
	  $.post("../../controlador/usuarios.php?op=listar", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);
};

var cargarAgencias = function() {
	  var fn = function(xhr){
	  	var datos = xhr.datos;
	  	$("#txtagencia").html(htmlSelect(datos));
	  };

	  var fnFail = function(xhr){
	  	alert(xhr.responseJSON.mensaje);
	  };

	  var fnAlways = function(){};

	  $.post("../../controlador/agencias.php?op=listar_select")
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);
};

var renderTabla = function (data) {
	var html = "";

	if (data == null || !data.length){
		return html;
	}

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<tr data-id="${o.id_usuario}">
					<th class="text-center" scope="row">
						<button title="Editar" onclick="ver($(this),${o.id_usuario})" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></button>
						<button title="Cambiar Clave" onclick="verCambiarClave(${o.id_usuario},'${o.nombres_apellidos}')" class="btn btn-primary btn-xs"><i class="fa fa-lock"></i></button>
						<button title="Eliminar" onclick="eliminar($(this),${o.id_usuario})" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
					</th>
					<td class="text-center">${o.numero_documento}</td>
					<td>${o.nombres_apellidos}</td>
					<td>${o.tipo_usuario}</td>
					<td><small>Cel: ${o.celular}</small></td>
					<td><span class="badge badge-${(o.estado_acceso_key == "I" ? "danger" : "success")}">${o.estado_acceso}</td>
				 </tr>`;
	};
	
	return html;
};

var guardando = false;
var guardar = function(){
	var $btnGuardar, accion = "registrar",
		$blkAlertModal = $mdl.find("#blk-alert-modal");
	if (guardando){
		return;
	}

	guardando =  true;
	$btnGuardar = $mdl.find("#btnguardar");
	var temporalHTML = $btnGuardar.html();
	$btnGuardar.html(CADENAS.CARGANDO);

	  var postData = {
	  	p_numero_documento : $("#txtnumerodocumento").val(),
	  	p_nombres : $("#txtnombres").val(),
	  	p_apellidos : $("#txtapellidos").val(),
	  	p_tipo_usuario : $("#txttipousuario").val(),
	  	p_celular : $("#txtcelular").val(),
	  	p_correo : $("#txtcorreo").val(),
	  	p_agencias : $("#txtagencia").val(),
	  	p_estado_acceso : $("#txtestadoacceso").val(),
	  	p_username : $("#txtnombreusuario").val()
	  };

	  if (ID_EDITAR != null){
	  	accion = "editar";
	  	postData.id_usuario = ID_EDITAR;
	  } else {
	  	postData.p_clave = $("#txtclave").val();
	  }

	  var fn = function(xhr){
	  	var datos = xhr.datos,
	  		$blkAlert = $("#blk-alert");

	  	$mdl.modal("hide");
		Util.alert($blkAlert, datos?.msj, "success");
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

	  $.post("../../controlador/usuarios.php?op="+accion, postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);	
};

var cargando = false,
	ID_EDITAR = null;
var ver = function($btn, id_usuario){
	if (cargando){
		return;
	}

	var postData = {
	  	id_usuario : id_usuario
	  };

	ID_EDITAR = id_usuario;
	cargando =  true;
	var temporalHTML = $btn.html();
	$btn.html(CADENAS.CARGANDO);

	  var fn = function(xhr){
	  	var datosUsuario = xhr.datos;

	  	$mdl.find(".modal-title").html("Editando Registro");
		$mdl.find(".modal-body").html(renderDataModal(datosUsuario));

		$mdl.modal("show");
	  };

	  var fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($("#blk-alert"), xhr.response, "danger");	
	  };

	  var fnAlways = function(){
  		cargando = false;
  		$btn.html(temporalHTML);
	  };

	  $.post("../../controlador/usuarios.php?op=leer", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);
};

var verCambiarClave = function(id_usuario, nombres_apellidos_usuario){
	if (!id_usuario){
		return;
	}

	ID_CAMBIAR_CLAVE = id_usuario;

	$("#lblusuario").val(nombres_apellidos_usuario);
	$("#txtnuevaclave").val("");
	$mdlCambiarClave.modal("show");
};


var guardandoClave = false;
var ID_CAMBIAR_CLAVE = null;
var guardarCambioClave = function(){
	var $btnGuardar,
		$blkAlertModal = $mdlCambiarClave.find("#blk-alert-modal");

	if (ID_CAMBIAR_CLAVE == null){
		return;
	}

	if (guardandoClave){
		return;
	}

	guardandoClave =  true;
	$btnGuardar = $mdlCambiarClave.find("#btnguardar");
	var temporalHTML = $btnGuardar.html();
	$btnGuardar.html(CADENAS.CARGANDO);

	  var postData = {
	  	id_usuario : ID_CAMBIAR_CLAVE,
	  	p_nueva_clave : $("#txtnuevaclave").val(),
	  };

	  var fn = function(xhr){
	  	var datos = xhr.datos;
	  	$mdlCambiarClave.modal("hide");
		Util.alert($("#blk-alert"), datos.msj, "success");	
	  };

	  var fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($blkAlertModal, xhr.response, "danger");	
	  };

	  var fnAlways = function(){
  		guardandoClave = false;
  		$btnGuardar.html(temporalHTML);
	  };

	  $.post("../../controlador/usuarios.php?op=cambiar_clave_admin", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);	
};

var eliminando = false;
var eliminar = function($btn, id_usuario){
	if (!confirm("¿Está seguro de realizar esta acción?")){
		return;
	}

	var $blkAlert = $("#blk-alert");

	if (id_usuario == null){
		return;
	}

	if (eliminando){
		return;
	}

	eliminando =  true;
	var temporalHTML = $btn.html();
	$btn.html(CADENAS.CARGANDO);

	  var postData = {
	  	id_usuario : id_usuario
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

	  $.post("../../controlador/usuarios.php?op=eliminar", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);	
};

var htmlSelect = function (data) {
	var html = `<option value="">Seleccionar</option>`;

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<option value="`+o.id+`">`+o.descripcion+`</option>`;
	};

	return html;
};

var renderDataModal = function (datosUsuario) {
	$("#txtnumerodocumento").val(datosUsuario.numero_documento);
  	$("#txtnombres").val(datosUsuario.nombres);
  	$("#txtapellidos").val(datosUsuario.apellidos);
  	$("#txtcelular").val(datosUsuario.celular);
  	$("#txtagencia").val(datosUsuario.id_agencias.map(item => item.id_agencia)).select2().trigger('change');
  	$("#txttipousuario").val(datosUsuario.id_tipo_usuario);
  	$("#txtnombreusuario").val(datosUsuario.username);
  	$("#txtestadoacceso").val(datosUsuario.estado_acceso);

  	$("#blkclave").hide();
  	$("#txtclave").prop("required", false);
};