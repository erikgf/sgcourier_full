var TABLA,
	COL_DEFS = [75, 100, 120, 160, 160, 160, 160, 100],
	COL_DEFS_ALIGN = ["C","C","C",null,null,null,null,null,null],
	COLUMNS_DATATABLE = [];
var DATA;
var TR_FILA = null;

var $txtAreaBuscar,
	$txtFechaInicio,
	$txtFechaFin,
	$btnBuscar;
var $txtIdAreaRegistro,
	$frmRegistro,
	$mdlRegistro,
	$txtArea,
	$txtFechaRecepcion,
	$txtNumeroGuia,
	$txtRemitente,
	$txtDependencia,
	$txtConsignatario,
	$txtDestino,
	$chkVentana;

var $mdlRegistroEntrega,
    $txtIdAreaRegistroEntrega,
    $blkFotos,
    $txtFechaEntrega,
    $btnNuevaFoto,
    $btnRegistrarMasivo,
    $frmRegistrarMasivo;

var $tbl;

var ARREGLO_FOTOS = [];

var init = function(){
    setDOM();
    setEventos();

    cargarAreasCombo();
    setearSelects();
};

var setDOM = function(){
	$txtAreaBuscar = $("#txtareabuscar");
	$txtFechaInicio = $("#txtfechainicio");
	$txtFechaFin = $("#txtfechafin");
	$btnBuscar = $("#btnbuscar");

	$mdlRegistro = $("#mdl-registro");
	$tbl = $("#tbllistado");

	$frmRegistro = $("#frm-registro");
	$txtIdAreaRegistro = $("#txtidarearegistro");
	$txtArea = $("#txtarea");
	$txtFechaRecepcion = $("#txtfecharecepcion");
	$txtNumeroGuia = $("#txtnumeroguia");
	$txtRemitente = $("#txtremitente");
	$txtDependencia = $("#txtdependencia");
	$txtConsignatario = $("#txtconsignatario");
	$txtDestino = $("#txtdestino");

	$chkVentana = $("#chkcerrarventana");

    $mdlRegistroEntrega = $("#mdl-registroentrega");
    $txtIdAreaRegistroEntrega = $("#txtidarearegistroentrega");
    $blkFotos = $("#blk-fotos");
    $txtFechaEntrega = $("#txtfechaentrega");
    $btnNuevaFoto = $("#btnnuevafoto");
    $btnRegistrarMasivo =  $("#btnregistromasivo");

    $frmRegistrarMasivo  = $("#frm-registromasivo");
};


var setEventos = function() {
	$btnBuscar.on("click", function(e){
		e.preventDefault();
		cargarAreasCombo();
	});

	$txtAreaBuscar.on("change", function(e){
		listar();
	});

	$("#btnnuevo").on("click", function(e){
		e.preventDefault();
		$mdlRegistro.modal("show");
	});

    $btnRegistrarMasivo.on("click", function(e){
        e.preventDefault();
        $("#mdl-registromasivo").modal("show");
    });

	$frmRegistro.on("submit", function(e){
		e.preventDefault();
		registrar();
	});

    $frmRegistrarMasivo.on("submit", function(e){
        e.preventDefault();
        registrarMasivo();
    });

	$mdlRegistro.on("hide.bs.modal", function(e){
		limpiarModal();
	});

    $tbl.on("click", ".btneditar", function(e){
        e.preventDefault();
        leer(this.dataset.id, $(this).parents("tr"));
    });

    $tbl.on("click", ".btneliminar", function(e){
        e.preventDefault();
        eliminar(this.dataset.id, $(this).parents("tr"));
    });

    $tbl.on("click", ".btnregistrarentrega", function(e){
        e.preventDefault();
        leerFechaEntrega(this.dataset.id, this.dataset.ng,);
    });

    $txtFechaEntrega.on("change", function(){
        var fecha = this.value,
            anio = fecha.split("-")[0];

        if (anio.length == 4 && parseInt(anio) > 1900){
            actualizarFechaEntrega(fecha);
        }
    });

    $mdlRegistroEntrega.on("hide.bs.modal", function(e){
        $txtFechaEntrega.val("");
        for (var i = ARREGLO_FOTOS.length - 1; i >= 0; i--) {
            ARREGLO_FOTOS[i] = null;
        };
        ARREGLO_FOTOS = [];
    });

/*
    $mdlRegistroEntrega.on("change", ".iptfotos", function(e){
        e.preventDefault();
        //$tmpInputMultipleFotos = null;        
        procesarImagenes(e.target.files); // card.body
    });
    */

    $btnNuevaFoto.on("click", function(e){
        e.preventDefault();
        ARREGLO_FOTOS.push(new ImagenSubirComponente({
            id: "",
            id_area_registro: $txtIdAreaRegistroEntrega.val(),
            $root: $blkFotos
        }));
    });

	COLUMNS_DATATABLE = [];
	for (var i = 0; i < COL_DEFS.length; i++) {
		if (COL_DEFS[i] == null){
			COLUMNS_DATATABLE.push(null);
		} else {
			var obj = {"width": COL_DEFS[i]+"px"};
			if (COL_DEFS_ALIGN[i] != null){
				if (COL_DEFS_ALIGN[i] == "C"){
					obj.className = "text-center";
				}
			}
			COLUMNS_DATATABLE.push(obj);
		}
	};
};

var initDT = function(registros) {
	if (TABLA){
		TABLA.destroy();
	}

	$('#tbllistado').find("tbody").html(renderDetalleTabla(registros));

    TABLA = $tbl.DataTable({
        "responsive": true,
    	"ordering":true,
        "pageLength": 50,
        "columns": COLUMNS_DATATABLE,
        "scrollX": true
    	//language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
    });
};

var listar = function() {

	if ($txtAreaBuscar.val() == ""){
		return;
	}

	var postData = {
	  	p_fecha_inicio : $txtFechaInicio.val(),
	    p_fecha_fin : $txtFechaFin.val(),
	    p_id_area : $txtAreaBuscar.val()
	  };

	  var fn = function(xhr){
	      var datos = xhr.datos;
		  $btnBuscar.prop("disabled", false);
		  initDT(datos);
	  };

	  var fnFail = function(xhr){
	  	$btnBuscar.prop("disabled", false);
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
	  };


	  $btnBuscar.prop("disabled", true);
	  $.post("../../controlador/sis_guia_area_registro.php?op=listar_x_id_area", postData)
	      .done(fn)
	      .fail(fnFail);
};

var cargarAreasCombo = function(){
    $.ajax({ 
        url : "../../controlador/sis_guia_consultador_select.php?op=listar_select_area_rango_fechas",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
        	p_fecha_inicio : $txtFechaInicio.val(),
        	p_fecha_fin : $txtFechaFin.val()
        },
        success: function(result){
            new SelectComponente({$select : $txtAreaBuscar, opcion_vacia : false}).render(result.datos);

            listar();
        },
        error: function (request) {
            console.error(request.responseText);
            return;
        },
        cache: true
        }
    );
};

var renderDetalleTabla = function (data) {
	var html = "";

	for (var i = 0; i < data.length; i++){
		var o = data[i];
		html += `<tr>
					<td scope="row" class="text-center">
						<small style="display:none;">${o.numero_guia}</small>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i> OPC.</button>
                            <div class="dropdown-menu">
                              <button data-ng="${o.numero_guia}" data-id="${o.id_area_registro}" class="btnregistrarentrega bg-success btn dropdown-item text-white">Registrar Entrega</button>
                              <button data-id="${o.id_area_registro}" class="btneditar bg-warning btn dropdown-item text-white">Editar Registro</button>
                              <button data-id="${o.id_area_registro}" class="btneliminar bg-danger btn dropdown-item text-white">Eliminar Registro</button>
                            </div>
                        </div>
					</td>
					<td class="text-center"><small>${o.fecha_recepcion}</small></td>
                    <td class="text-center"><small>${o.fecha_entrega}</small></td>
					<td>${o.numero_guia}</td>
					<td>${o.remitente}</td>
					<td>${o.dependencia}</td>
					<td>${o.consignatario}</td>
					<td>${o.destino}</td>
				</tr>`;
	};

	return html;
};

var setearSelects  = function(){
	$txtArea.select2({
            ajax: { 
                url : "../../controlador/sis_guia_consultador_select.php?op=listar_select_area",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_buscar_texto: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            tags: true,
            dropdownParent: $frmRegistro
        });

	$txtDestino.select2({
            ajax: { 
                url : "../../controlador/sis_guia_consultador_select.php?op=listar_select_destino",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_buscar_texto: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            tags: true,
            dropdownParent: $frmRegistro
        });

	$txtRemitente.select2({
            ajax: { 
                url : "../../controlador/sis_guia_consultador_select.php?op=listar_select_remitente",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_buscar_texto: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            tags: true,
            dropdownParent: $frmRegistro
        });

	$txtConsignatario.select2({
            ajax: { 
                url : "../../controlador/sis_guia_consultador_select.php?op=listar_select_consignatario",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_buscar_texto: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            tags: true,
            dropdownParent: $frmRegistro
        });
};

var limpiarModal  =function(limpiadoConModalAbierto = false){
	if (!limpiadoConModalAbierto){
		$txtArea.val(null).trigger('change');
		$txtFechaRecepcion.val("");
	} else{
		$txtFechaRecepcion.focus();
	}

	$txtIdAreaRegistro.val("");
	$txtNumeroGuia.val("");
	$txtDependencia.val("");
	$txtConsignatario.val(null).trigger('change');
	$txtRemitente.val(null).trigger('change');
	$txtDestino.val(null).trigger('change');

    TR_FILA = null;
};

var leer = function(id_area_registro, $tr_fila){

    $.ajax({ 
        url : "../../controlador/sis_guia_area_registro.php?op=leer",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
        	p_id_area_registro : id_area_registro
        },
        success: function(result){
            $mdlRegistro.modal("show");

            var datos = result.datos;
            $txtIdAreaRegistro.val(datos.id_area_registro);
            $txtFechaRecepcion.val(datos.fecha_recepcion);
            $txtNumeroGuia.val(datos.numero_guia);
            $txtDependencia.val(datos.dependencia);

            $txtArea.append(new Option(datos.area,datos.id_area, false, true)).trigger('change');
            $txtConsignatario.append(new Option(datos.consignatario,datos.consignatario, false, true)).trigger('change');
            $txtRemitente.append(new Option(datos.remitente,datos.remitente, false, true)).trigger('change');
            $txtDestino.append(new Option(datos.destino,datos.destino, false, true)).trigger('change');

            TR_FILA = $tr_fila;
        },
        error: function (request) {
            console.error(request.responseText);
            return;
        },
        cache: true
        }
    );
};

var leerFechaEntrega = function(id_area_registro, numero_guia){

    $.ajax({ 
        url : "../../controlador/sis_guia_area_registro.php?op=obtener_imagenes_x_id_registro",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_area_registro : id_area_registro
        },
        success: function(result){
            $mdlRegistroEntrega.modal("show");

            var datos = result.datos;
            $txtIdAreaRegistroEntrega.val(id_area_registro);
            $("#lblnumeroguia").html(`NÚMERO GUÍA: ${numero_guia}`);
            $txtFechaEntrega.val(datos.fecha_entrega);
            $blkFotos.empty();

            for (var i = 0; i < datos.imagenes.length; i++) {
                let o = datos.imagenes[i];
                ARREGLO_FOTOS.push(new ImagenSubirComponente({
                    $root : $blkFotos,
                    id: o.id_registro_imagen,
                    url_imagen: o.url_imagen
                }));
            };
        },
        error: function (request) {
            console.error(request.responseText);
            return;
        },
        cache: true
        }
    );
};
/*
var generarExcel = function(){
	window.open("../../controlador/reporte.pedido.xls.php?p_fi="+$("#txtfechainicio").val()+"&p_ff="+$("#txtfechafin").val(),"_blank");
};
*/

var registrar = function(){
	var editando = $txtIdAreaRegistro.val() != "";
    $.ajax({ 
        url : "../../controlador/sis_guia_area_registro.php?op=registrar",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
        	p_id_area_registro : $txtIdAreaRegistro.val(),
        	p_id_area : $txtArea.val(),
        	p_fecha_recepcion : $txtFechaRecepcion.val(),
        	p_numero_guia : $txtNumeroGuia.val(),
        	p_dependencia : $txtDependencia.val(),
        	p_remitente : $txtRemitente.val(),
        	p_consignatario : $txtConsignatario.val(),
        	p_destino : $txtDestino.val(),
        },
        success: function(result){
        	var datos = result.datos,
        		registro = datos.registro;

        	if (editando){
        		Util.actualizarFilaDataTable(TABLA, renderDetalleTabla, registro, TR_FILA);
        	} else {
        		if (registro.id_area == $txtAreaBuscar.val()){
        			Util.actualizarFilaDataTable(TABLA, renderDetalleTabla, registro);	
        		}
        	}

            if (!$chkVentana.prop("checked")){
        		Util.alert($("#blk-alert-modal"), datos.msj, "success");
            	limpiarModal(true);
            	return;
            }

            $mdlRegistro.modal("hide");
            Util.alert($("#blk-alert"), datos.msj, "success");
        },
        error: function (request) {
        	Util.alert($("#blk-alert-modal"), request.responseText, "danger");
            return;
        },
        cache: true
        }
    );
};

var eliminar = function(id_area_registro, $tr_fila){

    if (!(confirm("¿Desea eliminar este registro?"))){
        return;
    }

    var self = this;

        $.ajax({ 
            url : "../../controlador/sis_guia_area_registro.php?op=eliminar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data : {
                p_id_area_registro : id_area_registro
            },
            success: function(result){
                var datos = result.datos;
                Util.alert($("#blk-alert"), datos.msj, "success");
                if (TABLA){
                    TABLA
                        .row($tr_fila)
                        .remove()
                        .draw();    
                }
            },
            error: function (request) {
                Util.alert($("#blk-alert"), request.responseText, "danger");
                return;
            },
            cache: true
            }
        );
};

var actualizarFechaEntrega = function(fecha){
    $.ajax({ 
        url : "../../controlador/sis_guia_area_registro.php?op=registrar_fecha_entrega",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_area_registro : $txtIdAreaRegistroEntrega.val(),
            p_fecha_entrega: $txtFechaEntrega.val()
        },
        success: function(result){
            var datos = result.datos;
            Util.alert($("#blk-alert-modalentrega"), datos.msj, "success");
        },
        error: function (request) {
            Util.alert($("#blk-alert-modalentrega"), request.responseText, "danger");
            return;
        },
        cache: true
        }
    );
};

var guardandoMasivo = false;
var registrarMasivo = function(){
    var $btnGuardar,
        $mdl = $("#mdl-registromasivo"),
        $blkAlertModal = $mdl.find("#blk-alert-modalmasivo"),
        temporalHTML;
    if (guardandoMasivo){
        return;
    }

    guardandoMasivo =  true;
    $btnGuardar = $mdl.find("#btn-guardarmasivo");
    temporalHTML = $btnGuardar.html();
    $btnGuardar.html(CADENAS.CARGANDO);

      var fn = function(xhr){
        var datos = xhr.datos,
            $blkAlert = $("#blk-alert");

        fnAlways();

        $mdl.modal("hide");
        Util.alert($blkAlert, datos.msj, "success");
        $blkAlert.focus();

        listar();
      };

      var fnFail = function(xhr){
        fnAlways();
        if (xhr.responseJSON && xhr.responseJSON.mensaje){
            Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger"); 
            return;
        }
        
        Util.alert($blkAlertModal, xhr.response, "danger"); 
      };

      var fnAlways = function(){
        guardandoMasivo = false;
        $btnGuardar.html(temporalHTML);
      };

      var datos_frm = new FormData($frmRegistrarMasivo[0]);
       $.ajax({
            url: "../../controlador/sis_guia_area_registro.php?op=registrar_masivo",
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


$(function(){
	init();
});

