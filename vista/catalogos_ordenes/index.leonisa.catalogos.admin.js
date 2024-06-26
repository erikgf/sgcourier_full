var TABLA,
	COL_DEFS = [85, 100, 200, 100, 50,250,150,120,100,85,85,85];
var DATA;

var COLOR_MIO = "blk-misasignaciones";

$(function(){
	$(".select2").select2();
	$mdlVerdetalle =$("#mdlverdetalle")
	$mdlFotos = $("#mdlfotos");
    eventos();

    if (_ID == null){
    	alert("No se ha encontrado pedido registrado.");
    	return;
    }
    obtenerDatos();
});


var initDT = function(registros) {
	if (TABLA){
    	TABLA.destroy();
    }

	$('#tbllistado').find("tbody").html(renderDetalleTabla(registros));

	setTimeout(function(){
		//if (registros.length){
    	var columnDefs = [];
    	for (var i = 0; i < COL_DEFS.length; i++) {
    		columnDefs.push({width: COL_DEFS[i]+"px", targets : i});
    	};
    	TABLA = $('#tbllistado').DataTable({
	      "scrollX": true,
	      "columnDefs": columnDefs,
	      language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
	    });
   // }
	},100);
	
};

var eventos = function() {
	var $tbl = $("#tbllistado");

	$tbl.on("change", "tbody tr .txtasignado", function() {
		var valor =this.value,	
			$tr = $(this.parentElement.parentElement);

		if (valor != ""){
			$tr.addClass(COLOR_MIO);
		} else {
			$tr.removeClass(COLOR_MIO);
		}
	});


	$("#btn-asignar").on("click", function(e){
		e.preventDefault();
		guardarAsignaciones();
	});

	$("#txtciudad").on("change", function(e){
		filtrar(this.value);
	});

	$mdlVerdetalle.on("hidden.bs.modal", function(e){
		e.preventDefault();
		$mdlVerdetalle.find(".modal-title").empty();		
		$mdlVerdetalle.find(".modal-body").empty();
	});

	$("#btn-etiquetas").on("click", function(e){
		generarEtiquetas();
	});
};

var obtenerDatos = function() {
	var postData = {
	  	p_id : _ID,
	  	p_id_cliente_especifico: _KEYLEONISA
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

		fnAlways();
	  };
	
	var fnFail = function(xhr){
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	  	fnAlways();
	  };

	var fnAlways = function(){
	  	$("#lblcargando").hide();
	  	$("#blkmain").show(300);
	  };

	  $("#lblcargando").show();
	  $.post("../../controlador/pedidos.php?op=leer_x_id", postData)
	      .done(fn)
	      .fail(fnFail);
};

var renderDetalleTabla = function (data) {
	var html = "";

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<tr data-indice="`+i+`">
					<th `+(o.id_usuario_asignado == "" ? "" : `onclick="verDetalle(`+o.id_pedido_orden+`)" style="cursor:pointer"`)+` class="text-center text-white bg-`+o.estado_color+`" scope="row">`+o.codigo_remito+`</th>
					<td class="text-center">`+o.fecha_hora_atencion+`</td>
					<td>`;
		if (o.id_usuario_asignado != ""){
 			html += `<span class="badge badge-pill badge-dark">`+o.numero_visitas+`</span> `+o.colaborador_asignado+`</td>`;
		} else {
			html += `<i>No asignado</i>`;
		}
		html += 	`</td>
					<td>`+o.observaciones+`</td>
					<td>`+o.numero_documento_destinatario+`</td>
					<td>`+o.destinatario+`</td>
					<td>`+o.direccion_uno+`</td>
					<td>`+o.referencia+`</td>
					<td>`+o.distrito_provincia+`</td>
					<td>`+o.region+`</td>
					<td>`+o.telefono+`</td>
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
                              <label for="txtciudad">Distrito / Provincia</label>
                              <p id="txtciudad">`+data_orden.distrito_provincia+`</p>
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
                              <p id="txttelefono">`+data_orden.telefono+`</p>
                          </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txttelefono">Celular</label>
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

var guardarAsignaciones = function(){
	var $trs = [].slice.call($("#tbllistado").find("tbody tr")),
		asignados = 0;
	for (var i = $trs.length - 1; i >= 0; i--) {
		var $tr = $($trs[i]),
			idAsignado = $tr.find(".txtasignado").val();
		if (idAsignado != ""){
			DATA.registros[$tr.data("indice")].id_usuario_asignado = idAsignado;
			$tr.children(0).eq(0).html('<button class="btn btn-xs btn-success"><i class="fa fa-check"></i> FINALIZAR</button>');
			asignados++;
		}
	};

	var $blkAlert = $("#blk-alert");

	if (asignados <= 0){
		Util.alert($blkAlert, "No se ha seleccionado colaboradores que asignar.", "danger");
		return;
	}

	Util.alert($blkAlert, "Asignaciones realizadas.", "success");
};

var filtrar = function(ciudadFiltrada){
	var tmpRegistros = [],
		registros = DATA.registros;

	if (ciudadFiltrada == ""){
		initDT(registros);
		return;
	}

	for (var i = registros.length - 1; i >= 0; i--) {
		var registro = registros[i];
		if (registro.ciudad  == ciudadFiltrada){
			tmpRegistros.push(registro);
		}
	};

	initDT(tmpRegistros);
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
	  	p_id_pedido_orden : id,
	  	p_id_cliente_especifico: _KEYLEONISA
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

var cargando = false;
var listar = function($btn, estado){
	var postData = {
	  	p_id : _ID,
	  	p_id_cliente_especifico: _KEYLEONISA,
	  	p_estado : estado
	  };
	  var fn = function(xhr){
	  	var data = xhr.datos;
		initDT(data);
	  };

	  var fnFail = function(xhr){
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	  };

	  var fnAlways = function(){
	  	$("#lblcargando").hide();
	  	cargando = false;
	  	$btn.disabled = false;
	  };

	  if (cargando == true){
	  	return;
	  }

	  cargando = true;
	  $btn.disabled = true;
	  $("#lblcargando").show();
	  $.post("../../controlador/pedidos.php?op=listar_ordenes_x_id", postData)
	      .done(fn)
	      .fail(fnFail)
	      .always(fnAlways);
};

var verFotos = function(urlsSinFormateo){
	var arregloUrl = [];
	if (urlsSinFormateo == ""){
		return;
	}

	arregloUrl = urlsSinFormateo.split(",");

	var htmlBloqueUno = "", htmlBloqueDos = "";
	for (var i = 0; i < arregloUrl.length; i++) {
		var img = arregloUrl[i];
		htmlBloqueUno += `<li data-target="#carruselFotos" data-slide-to="`+i+`" `+(i == 0 ? `class="active"` : ``)+`></li>`;
		htmlBloqueDos += `<img class="d-block w-100" src="../../img/imagenes_visitas/`+img+`" alt="Imagen `+(i+1)+`">`;
	};

	var html = `<ol class="carousel-indicators">
	               `+htmlBloqueUno+`
	              </ol>
	              <div class="carousel-inner">
	                <div class="carousel-item active">
	                  `+htmlBloqueDos+`
	                </div>
	              </div>
	              <a class="carousel-control-prev" href="#carruselFotos" role="button" data-slide="prev">
	                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
	                <span class="sr-only">Anterior</span>
	              </a>
	              <a class="carousel-control-next" href="#carruselFotos" role="button" data-slide="next">
	                <span class="carousel-control-next-icon" aria-hidden="true"></span>
	                <span class="sr-only">Siguiente</span>
	              </a>`;

	$("#carruselFotos").html(html);
	$("#carruselFotos").carousel();

	$mdlFotos.modal("show");
};


var generarEtiquetas = function(){
	window.open("../../controlador/reporte.etiquetas.leonisa.pdf.php?p_id="+_ID,"_blank");
};

var generarExcel = function(){
	//window.open("../../controlador/reporte.pedido.xls.php?p_id="+_ID,"_blank");
};