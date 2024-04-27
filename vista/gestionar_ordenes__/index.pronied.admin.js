var TABLA,
	COL_DEFS = [85, 100, 200, 100, 100,250,150,100,125,100,100,95, 85];
var DATA = {};
var HTML_SEARCH = '<i class="fa fa-search"></i>',
	HTML_OK =  '<i class="fa fa-check text-success"></i>',
	HTML_CHANGE =  '<i class="fa fa-redo text-info"></i>',
	HTML_ERROR = '<i class="fa fa-times text-danger"></i>';
		
var COLOR_MIO = "blk-misasignaciones";
var ID_CLIENTE_ESPECIFICO = _KEY;

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

var renderDetalleTabla = function (data) {
	var html = "";

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		console.log(o);
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
					<td>`+o.destinatario+`</td>
					<td>`+o.ubigeo+`</td>
					<td>`+o.direccion_uno+`</td>				
					<td>`+o.forma_envio+`</td>
					<td>`+o.pronied_unidad_organica+`</td>
					<td>`+o.costo_envio+`</td>
					<td>`+o.ticket_factura+`</td>
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


