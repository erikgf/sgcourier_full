var TABLA,
	COL_DEFS = [85, 320,100,100, 100];
var DATA,
	objSelect2 = {language: "es", 
							minimumInputLength: 3,
							minimumResultsForSearch: 10,
							allowClear : true,
							dropdownParent: $("#frm-registro")};

$(function(){
	//$(".select2").select2(objSelect2);

	$mdl = $("#mdl-registro");
	$frmRegistro = $("#frm-registro");
	$txtDepartamentos = $("#txt-departamentos");
	$txtProvincias = $("#txt-provincias");
	$txtDistritos = $("#txt-distritos");
	$blkAlertModal = $("#blk-alert-modal");

    eventos();
    listar();

    cargarDepartamentos();
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
		      order: [[ 1, "asc" ]],
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

		$("#txtubigeo").select2(objSelect2).trigger('change');
	});

	$frmRegistro.on("submit", function (e) {
		e.preventDefault();
		guardar();
	});

	$txtDepartamentos.on("change", (e)=>{
		cargarProvincias(e.currentTarget.value);
	});

	$txtProvincias.on("change", (e)=>{
		cargarDistritos(e.currentTarget.value);
	});
};

var listar = function() {
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
	  $.post("../../controlador/agencias.php?op=listar")
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);
};

const cargarDepartamentos = () => {
	var fnFail = function(xhr){
		if (xhr.responseJSON && xhr.responseJSON.mensaje){
			Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
			return;
		}
	};

	const fn = (xhr) => {
		const { datos } = xhr;
		$txtDepartamentos.html(templateCombo(datos.map(item => {
			return {
				id: item.id,
				descripcion : item.name
			}
		})));
	};

	$.post("../../controlador/ubigeos.php?op=listar_departamentos")
		.done(fn)
		.fail(fnFail);
};

const cargarProvincias = (idDepartamento, idProvinciaSeleccionada = null) => {
	var fnFail = function(xhr){
		if (xhr.responseJSON && xhr.responseJSON.mensaje){
			Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
			return;
		}
	};

	const fn = (xhr) => {
		const { datos } = xhr;
		$txtProvincias.html(templateCombo(datos.map(item => {
			return {
				id: item.id,
				descripcion : item.name
			}
		}), idProvinciaSeleccionada));
	};

	$.post("../../controlador/ubigeos.php?op=listar_provincias", {id_dp : idDepartamento})
		.done(fn)
		.fail(fnFail);
};

const cargarDistritos = (idProvincia, idDistritoSeleccionado = null) => {
	const idDepartamento = $txtDepartamentos.val();
	var fnFail = function(xhr){
		if (xhr.responseJSON && xhr.responseJSON.mensaje){
			Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
			return;
		}
	};

	const fn = (xhr) => {
		const { datos } = xhr;
		$txtDistritos.html(templateCombo(datos.map(item => {
			return {
				id: item.id,
				descripcion : item.name
			}
		}), idDistritoSeleccionado));
	};

	$.post("../../controlador/ubigeos.php?op=listar_distritos", {id_dp : idDepartamento, id_pr :idProvincia})
		.done(fn)
		.fail(fnFail);
};


var renderTabla = function (data) {
	var html = "";

	if (data == null || !data.length){
		return html;
	}

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<tr data-id="`+o.id_agencia+`">
					<th class="text-center" scope="row">
						<button title="Editar" onclick="ver($(this),`+o.id_agencia+`)" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></button>
						<button title="Eliminar" onclick="eliminar($(this),`+o.id_agencia+`)" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
					</th>
					<td>`+o.descripcion+`</td>
					<td >`+o.distrito+`</td>
					<td	>`+o.provincia+`</td>
					<td >`+o.departamento+`</td>
				 </tr>`;
	};
	
	return html;
};

var guardando = false;
var guardar = function(){
	var $btnGuardar, accion = "registrar";
	if (guardando){
		return;
	}

	guardando =  true;
	$btnGuardar = $mdl.find("#btnguardar");
	var temporalHTML = $btnGuardar.html();
	$btnGuardar.html(CADENAS.CARGANDO);

	  var postData = {
	  	p_descripcion : $("#txtdescripcion").val(),
	  	p_departamento : $txtDepartamentos.val(),
		p_provincia : $txtProvincias.val(),
		p_distrito : $txtDistritos.val()
	  };

	  if (ID_EDITAR != null){
	  	accion = "editar";
	  	postData.id_agencia = ID_EDITAR;
	  }

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

	  $.post("../../controlador/agencias.php?op="+accion, postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);	
};

var cargando = false,
	ID_EDITAR = null;
var ver = function($btn, id_agencia){
	if (cargando){
		return;
	}

	var postData = {
	  	id_agencia : id_agencia
	  };

	ID_EDITAR = id_agencia;
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

	  $.post("../../controlador/agencias.php?op=leer", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);
};

var eliminando = false;
var eliminar = function($btn, id_agencia){
	if (!confirm("¿Está seguro de realizar esta acción?")){
		return;
	}

	var $btnGuardar,
		$blkAlert = $("#blk-alert");

	if (id_agencia == null){
		return;
	}

	if (eliminando){
		return;
	}

	eliminando =  true;
	var temporalHTML = $btn.html();
	$btn.html(CADENAS.CARGANDO);

	  var postData = {
	  	id_agencia : id_agencia
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


var templateCombo = function (data, idSeleccionado = null) {
	var html = `<option value="">Seleccionar</option>`;

	for (var i = 0; i < data.length; i++){
		const {id, descripcion} = data[i];
		html += `<option value="`+id+`" ${id == idSeleccionado ? 'selected' : ''}>`+descripcion+`</option>`;
	};

	return html;
};


var htmlSelect = function (data) {
	var html = `<option value="">Seleccionar</option>`;

	for (var i = 0; i < data.length; i++){
		var o = data[i],
			id = o.cod_ubigeo_sunat,
			descripcion = o.desc_dep_sunat+" / "+o.desc_prov_sunat+" / "+o.desc_ubigeo_sunat;
		html += `<option value="`+id+`" data-distrito="`+o.desc_ubigeo_sunat+`">`+descripcion+`</option>`;
	};

	return html;
};

var renderDataModal = function (datosUsuario) {
	$("#txtdescripcion").val(datosUsuario.descripcion);
	$txtDepartamentos.val(datosUsuario.ubigeo_departamento);
	cargarProvincias(datosUsuario.ubigeo_departamento, datosUsuario.ubigeo_provincia);
	cargarDistritos(datosUsuario.ubigeo_provincia, datosUsuario.ubigeo_distrito);
};

