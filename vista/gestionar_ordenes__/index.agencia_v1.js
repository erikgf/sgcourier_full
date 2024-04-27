var TABLA,
	COL_DEFS = [45, 80, 110, 200, 100, 200, 50,250,250,150,120,100,85,85];
var DATA;
var HTML_SEARCH = '<i class="fa fa-search"></i>',
	HTML_OK =  '<i class="fa fa-check text-success"></i>',
	HTML_CHANGE =  '<i class="fa fa-redo text-info"></i>',
	HTML_ERROR = '<i class="fa fa-times text-danger"></i>';

var COLOR_MIO = "blk-misasignaciones",
	ARREGLO_CODIGOS_REMITO = [];

$(function(){
	$mdlVerdetalle =$("#mdlverdetalle")
	$tblListado = $('#tbllistado');

    eventos();
    obtenerDatos();
});

var initDT = function(registros) {
	if (TABLA){
    	TABLA.destroy();
    }

	$tblListado.find("tbody").html(renderDetalleTabla(registros));

	setTimeout(function(){
    	var columnDefs = [];
    	for (var i = 0; i < COL_DEFS.length; i++) {
    		columnDefs.push({width: COL_DEFS[i]+"px", targets : i});
    	};
    	TABLA = $tblListado.DataTable({
	      "scrollX": true,
	      "columnDefs": columnDefs,
	      language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
	    });
	},100);
};

var eventos = function() {
	$txtColaborador = $("#txtcolaborador");
	$txtCiudad = $("#txtciudad");
	$txtCodigoRemitoBuscar = $("#txtcodigoremitobuscar");
	$blkAlert = $("#blk-alert");
	$txtCodigoRemitoBuscarZona = $("#txtcodigoremitobuscarzona");

	$tblListado.on("change", "tbody tr .chkseleccion", function() {
		var $inputCheck = this,
			$tr = $inputCheck.parentElement.parentElement,
			estado_actual = $tr.dataset.estadoactual;

		cambiarTr($tr, estado_actual, $inputCheck, true);
	});

	$("#btnasignar").on("click", function(e){
		e.preventDefault();
		guardarAsignaciones();
	});

	$mdlVerdetalle.on("hidden.bs.modal", function(e){
		e.preventDefault();
		$mdlVerdetalle.find(".modal-title").empty();		
		$mdlVerdetalle.find(".modal-body").empty();
	});

	$mdlVerdetalle.on("change", ".txtcorreccionestado", function(e){	
		var $txtDescripcion = $mdlVerdetalle.find(".txtcorrecciondescripcion");
		e.preventDefault();
		if (this.value == ""){
			return;
		}

		guardarCorreccion(_TMP_DATA_ORDEN.id_pedido_orden, $txtDescripcion, $(this));
	});

	
	$("#btnactualizar").on("click" ,function(e){
	   e.preventDefault();
	   	$("#btnactualizar").prop("disabled", true);
	   obtenerDatos();
	});
};

var obtenerDatos = function() {
	var postData = {
	  	p_id : _ID
	  };

	  var fn = function(xhr){
	  	var data = xhr.datos;
	  	DATA = data;
 		var cantidades = {
			cantidad : data.cantidad,
			noasignados : data.cantidad_noasignadas,
			gestionando : data.cantidad_gestionando,
			entregados : data.cantidad_entregadas,
			motivados : data.cantidad_motivadas
		};

		initDT(data.registros);
		var series = [	{rotulo: "NO ASIGNADOS", cantidad: cantidades.noasignados, color: "#343a40"},
						{rotulo: "GESTIONADOS", cantidad: cantidades.gestionando, color: "#2255a4"},
						{rotulo: "ENTREGADOS", cantidad: cantidades.entregados, color: "#28b779"},
						{rotulo: "MOTIVADOS", cantidad: cantidades.motivados, color: "#da542e"}
						];
		$("#lblpedido").html(data.id_pedido);
		$("#lblfechaingreso").html(data.fecha_ingreso);

		$("#lblcliente").html("["+data.numero_documento+"] "+data.razon_social);
		$("#lbldireccion").html(data.direccion);
		$("#lblcelular").html(data.celular);

		$("#lblcantidadnoasignado").html(cantidades.noasignados);
		$("#lblcantidadgestionando").html(cantidades.gestionando);
		$("#lblcantidadentregados").html(cantidades.entregados);
		$("#lblcantidadmotivados").html(cantidades.motivados);

		$("#lblcantidad").html(parseInt(cantidades.noasignados) + parseInt(cantidades.gestionando) + parseInt(cantidades.entregados) + parseInt(cantidades.motivados));

		setTimeout(function(){
			renderGrafico(series);	
		},330);
    
        
        asignacionesInit(data.usuarios_asignar, data.ciudades);
		fnAlways();
	  };

	var fnFail = function(xhr){
	  	Util.alert($blkAlert, xhr.responseJSON.mensaje, "danger");
	  	fnAlways();
	  };

	var fnAlways = function(){
	  	$("#lblcargando").hide();
	  	$("#blkmain").show(300);
	  	
	  	$("#btnactualizar").prop("disabled",false);
	  };

	  $("#lblcargando").show();
	  $.post("../../controlador/pedidos.php?op=leer_x_id", postData)
	      .done(fn)
	      .fail(fnFail);
};

var listarOrdenes = function(){
	var postData = {
	  	p_id : _ID
	  };

	  var fn = function(xhr){
	  	var data = xhr.datos;
 		var cantidades = {
			cantidad : data.cantidad,
			noasignados : data.cantidad_noasignadas,
			gestionando : data.cantidad_gestionando,
			entregados : data.cantidad_entregadas,
			motivados : data.cantidad_motivadas
		};

		DATA.registros = data.registros;
		filtrar($txtCiudad.val());
		renderDataCantidades(cantidades);
	  };

	var fnFail = function(xhr){
	  	_alert($blkAlert, xhr.responseJSON.mensaje, "danger");
	  };

	  $("#lblcargando").show();
	  $.post("../../controlador/pedidos.php?op=leer_x_id_listar", postData)
	      .done(fn)
	      .fail(fnFail)
	      .fnAlways = function(){
		  	$("#lblcargando").hide();
		  };;
};

var renderDataCantidades = function(cantidades){
	var series = [	{rotulo: "NO ASIGNADOS", cantidad: cantidades.noasignados, color: "#343a40"},
					{rotulo: "GESTIONADOS", cantidad: cantidades.gestionando, color: "#2255a4"},
					{rotulo: "ENTREGADOS", cantidad: cantidades.entregados, color: "#28b779"},
					{rotulo: "MOTIVADOS", cantidad: cantidades.motivados, color: "#da542e"}
					];

	$("#lblcantidadnoasignado").html(cantidades.noasignados);
	$("#lblcantidadgestionando").html(cantidades.gestionando);
	$("#lblcantidadentregados").html(cantidades.entregados);
	$("#lblcantidadmotivados").html(cantidades.motivados);

	$("#lblcantidad").html(parseInt(cantidades.noasignados) + parseInt(cantidades.gestionando) + parseInt(cantidades.entregados) + parseInt(cantidades.motivados));

	renderGrafico(series);
};

var renderDetalleTabla = function (data) {
	var html = "";

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<tr data-indice="`+o.id_pedido_orden+`" data-codigoremito="`+o.codigo_remito+`" data-asignado="`+o.id_usuario_asignado+`" data-estadoactual="`+o.estado_actual+`">
					<th class="text-center">`;
		if (o.estado_actual == "N" || o.estado_actual == "G"){
			html += `<input type="checkbox" style="width:24px;height;24px" class="chkseleccion"/>`;
		}
		html +=	`	</th>
					<td `+(o.id_usuario_asignado == "" ? "" : `onclick="verDetalle(`+o.id_pedido_orden+`)" style="cursor:pointer"`)+` class="text-center text-white bg-`+o.estado_color+`" scope="row">`+o.codigo_remito+`</td>
					<td>`+o.ciudad+`</td>
					<td>`;
		if (o.id_usuario_asignado != ""){
 			html += `<span class="badge badge-pill badge-dark">`+o.numero_visitas+`</span> `+o.colaborador_asignado;
		} else {
			html += `<i>No asignado</i>`;
		}
		html += 	`</td>
					<td class="text-center">`+o.fecha_hora_atencion+`</td>
					<td>`+o.observaciones+`</td>
					<td>`+o.numero_documento_destinatario+`</td>
					<td>`+o.destinatario+`</td>
					<td>`+o.direccion_uno+`</td>
					<td>`+o.direccion_dos+`</td>
					<td>`+o.referencia+`</td>
					<td>`+o.region+`</td>
					<td>`+o.celular+`</td>
					<td>`+o.numero_paquetes+`</td>
				</tr>`;
	};

	
	return html;
};

var renderDataModal = function(data_orden){
	var htmlDetalleTabla = "";
	var html = "";

	_TMP_DATA_ORDEN  = data_orden;
	if (data_orden.visitas.length){
		for (var i = 0; i < data_orden.visitas.length; i++) {
			var o = data_orden.visitas[i];
			htmlDetalleTabla += `<tr>
		      <th scope="row" class="text-center">`+o.numero_visita+`</th>
		      <td class="text-center">`+o.fecha+`<br>`+o.hora+`</td>
		      <td>`+(o.numero_documento_receptor ? o.numero_documento_receptor : '')+` `+(o.nombres_receptor ? o.nombres_receptor : '')+`</td>
		      <td>`+o.motivaciones+`</td>
		      <td>`+o.observaciones+`</td>
		      <td class="text-center">
		      	<button type="button" class="btn btn-sm btn-primary" title="Ver Foto" onclick="verFotos('`+o.urls+`');"><i class="fa fa-image"></i></button>
			  </td>
		    </tr>`;
		};
	} else {
		htmlDetalleTabla = "<tr><td  class='text-center' scope='row' colspan='6'>No hay visitas aún.</td></tr>";
	}

	html = `<div class="row">
                        <div class="col-sm-3">
                            <div class="form-group m-t-10">
                                <label for="txtnumerodocumento">Núm. Documento</label>
                                <p id="txtnumerodocumento">`+data_orden.numero_documento_destinatario+`</p>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group m-t-10">
                                <label for="txtdestinatario">Destinatario</label>
                                <p id="txtdestinatario">`+data_orden.destinatario+`</p>
                            </div>
                        </div>
                         <div class="col-sm-2">
                            <div class="form-group m-t-10">
                                <label for="txtestado">Estado</label>
                                <span id="txtestado" class="badge badge-`+data_orden.estado_color+`">`+data_orden.estado+`</span>
                            </div>
                        </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtdireccion">Dirección</label>
                              <p id="txtdireccion">`+data_orden.direccion_uno+`</p>
                          </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txturbanizacion">Urbanización</label>
                              <p id="txturbanizacion">`+data_orden.referencia+`</p>
                          </div>
                      </div>
                      <div class="col-sm-2">
                          <div class="form-group m-t-10">
                              <label for="txtciudad">Ciudad</label>
                              <p id="txtciudad">`+data_orden.distrito+`</p>
                          </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtdepartamento">Dpto.</label>
                              <p id="txtdepartamento">`+data_orden.region+`</p>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txttelefono">Teléfono</label>
                              <p id="txttelefono">`+data_orden.celular+`</p>
                          </div>
                      </div>
                      <div class="col-sm-2">
                          <div class="form-group m-t-10">
                              <label for="txtcajas">Paquetes</label>
                              <p id="txtcajas">`+data_orden.numero_paquetes+`</p>
                          </div>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtcorreccionestado">Corrección Estado</label>
                              <select name="txtcorreccionestado" class="form-control txtcorreccionestado">
                              	<option value="">Ninguno</option>
                              	<option value="G">GESTIONAR</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtcorrecciondescripcion">Corrección Razón (*)</label>
                              <textarea  title="Describir la razón por la cual se está corrigiendo la orden." name="txtcorrecciondescripcion" class="txtcorrecciondescripcion form-control"></textarea>
                          </div>
                      </div>
                      <div class="col-sm-2">
                          <div class="form-group m-t-10">
                              <label for="txtvecescorregido">Veces Corregido</label>
                              <p id="txtvecescorregido">`+data_orden.veces_corregido+`</p>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sn-12">
                          <div id="blk-alert-modal"></div>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <h4>Visitas</h4>
                      <div class="table-responsive">
                        <small>
                          <table lass="table table-condensed">
                                <thead class="thead-light">
                                  <tr>
                                    <th scope="col" style="width:75px" class="text-center">N°</th>
                                    <th scope="col" style="width:125px" class="text-center">Fecha atención</th>
                                    <th scope="col" style="width:200px">Receptor</th>
                                    <th scope="col">Motivaciones</th>
                                    <th scope="col">Observaciones</th>
                                    <th scope="col" style="width:75px" class="text-center">Fotos</th>
                                  </tr>
                                </thead>
                                <tbody id="tbdvisitas">`+htmlDetalleTabla+`</tbody>
                          </table>
                        </small>
                      </div>
                    </div>
                  </div>`;

       return html;
};


var _alert = function($blk, mensaje, tipoMensaje){
	var $tmpHtml = $(`<div class="alert alert-`+tipoMensaje+`" role="alert">`+mensaje+`</div>`);
	$blk.html($tmpHtml);
	setTimeout(function() {
		if ($tmpHtml){
			$tmpHtml.remove();
		}
	},4000);
};


var renderGrafico = function(SERIES){
	var data = [];
	for( var i = 0; i<SERIES.length; i++){	
		var _serie = SERIES[i];
		data[i] = { label: _serie.rotulo, data: parseInt(_serie.cantidad), color: _serie.color};
	};
	
    var pie = $.plot($(".pie"), data,{
        series: {
            pie: {
                show: true,
                radius: 3/4,
                label: {
                    show: true,
                    radius: 3/4,
                    formatter: function(label, series){
                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">('+Math.round(series.percent)+'%)</div>';
                    },
                    background: {
                        opacity: 0.5,
                        color: '#000'
                    }
                },
                innerRadius: 0.2
            }
		}
	});	

	$('.legend').hide();
};

var verDetalle = function(id){
	var postData = {
	  	p_id_pedido_orden : id
	  };

	  var fn = function(xhr){
	  	var data_orden = xhr.datos;
	  	$mdlVerdetalle.find(".modal-title").html("Código Remito: "+data_orden.codigo_remito);
		$mdlVerdetalle.find(".modal-body").html(renderDataModal(data_orden));

		$mdlVerdetalle.modal("show");
	  };

	  var fnFail = function(xhr){
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	  };

	  $.post("../../controlador/pedidos_ordenes.php?op=leer_x_orden_id", postData)
	      .done(fn)
	      .fail(fnFail);
};


var INDICE_COLABORADOR_ASIGNADO = 3;
var INDICE_ESTADO_ORDEN = 1;

var guardarAsignaciones = function(){
	var idUsuarioAsignar = $("#txtcolaborador").val(),
		$trs = [].slice.call($("#tbllistado").find("tbody tr.tr-seleccionado")),
		arregloOrdenesAsignados = [],
		$btnAsignar = $("#btnasignar"),
		$lblCargando = $("#lblcargando");

	if (!$trs.length){
		_alert( $blkAlert,"No hay órdenes seleccionadas.", "danger");
		return;
	}

	for (var i = $trs.length - 1; i >= 0; i--) {
		var $tr = $trs[i],
			id_orden = $tr.dataset.indice;		
		arregloOrdenesAsignados.push(id_orden);
	};

	var fnOK = function(xhr){
	  	var data = xhr.datos;
		$txtColaborador.val("").select2();
		Util.alert($blkAlert, data.msj, "success");

		listarOrdenes();
	};


	var fnFail = function(error){
	  	Util.alert($blkAlert, xhr.responseJSON.mensaje, "danger");
	};

	var postData = {
		p_idpedido : _ID,
		p_idcolaborador : idUsuarioAsignar,
		p_pedidoordenes : JSON.stringify(arregloOrdenesAsignados)
	};

	$btnAsignar.prop("disabled", true);
	$lblCargando.show();
	$.post("../../controlador/pedidos_ordenes.php?op=asignar", postData)
	      .done(fnOK)
	      .fail(fnFail)
	      .always(function(){
	      	$btnAsignar.prop("disabled", false);
	      	$lblCargando.hide();
	      });
};

var cambiarTr = function($tr,  estadoactual, $inputCheck, manual){
	if ($inputCheck.checked == true){
		$tr.classList.add("tr-seleccionado");
		if (!manual){
			if (estadoactual == "N"){
				$txtCodigoRemitoBuscarZona.html(HTML_OK);	
			} else {
				$txtCodigoRemitoBuscarZona.html(HTML_CHANGE);	
			}
		}
	} else {
		$tr.classList.remove("tr-seleccionado");
		if (!manual){
			$txtCodigoRemitoBuscarZona.html(HTML_CHANGE);	
		}
	}
};

var guardandoCorrecciones = false;
var guardarCorreccion = function(id_pedido_orden, $txtDescripcion, $txtEstadoNuevo){
	var $blkAlertModal = $mdlVerdetalle.find("#blk-alert-modal"),
		$vecesCorregido = $mdlVerdetalle.find("#txtvecescorregido"),
		descripcion = $txtDescripcion.val(),
		estado_nuevo = $txtEstadoNuevo.val(),
		temporalHTML;

	if (!_TMP_DATA_ORDEN){
		$txtEstadoNuevo.val("");
		Util.alert($blkAlertModal, "No se ha obtenido el ID de la órden.", "danger");
		return;
	}

	if ($txtDescripcion.val().length <= 0){
		$txtEstadoNuevo.val("");
		Util.alert($blkAlertModal, "Se debe ingresar una razón/descripción.", "danger");
		return;
	}

	var fnOK = function(xhr){
	  	var data = xhr.datos;
		$txtDescripcion.val("");
		$txtEstadoNuevo.val("");
		Util.alert($blkAlertModal, data.msj, "success");

		$vecesCorregido.html(parseInt(temporalHTML) + 1);
		var $txtEstado = $mdlVerdetalle.find("#txtestado");

		$txtEstado.removeClass("badge-success badge-danger").addClass("badge-info").html("GESTIONANDO");
		listarOrdenes();
	};

	var fnFail = function(xhr){
	    $vecesCorregido.html(temporalHTML);
	  	Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");
	};

	var postData = {
		p_id_pedido_orden : id_pedido_orden,
		p_descripcion : descripcion,
		p_estado_nuevo : estado_nuevo
	};

	temporalHTML = $vecesCorregido.html();	
	$vecesCorregido.html(CADENAS.CARGANDO);
	guardandoCorrecciones = true;
	$.post("../../controlador/pedidos_ordenes.php?op=corregir_estado", postData)
	      .done(fnOK)
	      .fail(fnFail)
	      .always(function(){
	      	guardandoCorrecciones = false;
	      });
};
