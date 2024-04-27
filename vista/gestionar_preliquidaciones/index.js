var TABLA,
	COL_DEFS = [70, 90, 100, null, 150, 110, 95, 85],
	COL_DEFS_ALIGN = ["C","C",null,null,null,null,null,null],
	COLUMNS_DATATABLE = [];
var DATA;
var TR_FILA = null;


let $txtFechaInicio, $txtFechaFin, $btnNuevo, $btnBuscar, $btnGuardar;
let $txtIdPrequilidacion, $txtRepartidor, $txtFechaRegistro, $txtTipoVehiculo;
let $txtEstado, $tblDetalle;
let $txtEstadoCambiar, $btnAgregarItem, $btnQuitarItems, $btnImportarItems;
let $frmRegistro, $mdlRegistro;
let $chkSeleccionarTodos, $chkVentana, $chkImprimirGuardar;
let $lblRegTotales, $lblRegSeleccionados;
let $txtObservaciones, $txtCostoGlobal;
let $btnImportarExcelMasivo;

let TEMPLATE_LISTA = null, TEMPLATE_DETALLE = null;
let ESTADOS =   [   {id:"P", descripcion:"PENDIENTE"},
                    {id:"R", descripcion:"RUTA"},
                    {id:"E", descripcion:"ENTREGADO"},
                    {id:"A", descripcion:"PAGADO"},
                    {id:"C", descripcion:"MOTIVADO"}
                ];

var init = function(){
    this.getTemplates()
        .then((tpl0, tpl1)=>{
            TEMPLATE_DETALLE = Handlebars.compile(tpl0[0]);
            TEMPLATE_LISTA = Handlebars.compile(tpl1[0]);
            setDOM();
            setEventos();
            listar();
        })
        .fail(error=>console.error(error));

    return this;
};

var setDOM = function(){
    $txtFechaInicio = $("#txtfechainicio");
    $txtFechaFin = $("#txtfechafin");
    $btnNuevo = $("#btnnuevo");
    $btnBuscar = $("#btnbuscar");
    $mdlRegistro = $("#mdl-registro");
    $tblListado = $("#tbllistado");
    $btnImprimir = $("#btn-imprimir");

    $txtIdPrequilidacion = $("#txtidpreliquidacion");
    $txtRepartidor = $("#txtrepartidor");
    //$txtAgencia = $("#txtagencia");
    $txtFechaRegistro = $("#txtfecharegistro");
    $txtTipoVehiculo = $("#txttipovehiculo");
    $txtEstado = $("#txtestado");

    $txtEstadoCambiar = $("#txtestadocambiar");

    $lblRegTotales = $("#lbl-regtotales");
    $lblRegSeleccionados = $("#lbl-regseleccionados");
    $tblDetalle = $("#tbldetalle");
    $btnAgregarItem = $("#btn-agregaritem");
    $btnQuitarItems = $("#btn-quitaritems");
    $btnImportarItems = $("#btn-importaritems");

    $chkSeleccionarTodos = $("#chk-seleccionartodos");
    $chkVentana = $("#chkcerrarventana");
    $chkImprimirGuardar = $("#chkimprimirguardar");
    
    $txtObservaciones = $("#txt-observaciones");
    $txtCostoGlobal = $("#txt-costoglobal");
    
    $btnImportarExcelMasivo = $("#btn-importarexcelmasivo");
};

var setEventos = function() {
    $txtRepartidor.on("click", function(e){
        e.preventDefault();
        objBuscarComponente.render({
            title:"Buscar Repartidor...",
            getData: cargarRepartidores(),
            onSeleccionarItem: function(o){ 
                if (o){
                    let costoEntrega = o.costo_entrega;
                    $txtRepartidor.val(o.descripcion);
                    $txtRepartidor.data("id", o.id);
                    $txtRepartidor.data("costo_entrega", costoEntrega);

                    $(".val_costounitario").val(costoEntrega);
                    $tblDetalle.find("tbody tr").each(function(i, tr){
                        const $tr = $(tr);
                        const cantidad = $($tr).find(".val_cantidad").val();
                        const subtotal = cantidad * costoEntrega;
                        $tr.find(".val_subtotal").val(parseFloat(subtotal).toFixed(2));
                    });

                    calcularTotal();
                }
            },
            onEditarItem: function($btn, o){
                objRepartidor.editar($btn, o);
            },
            onNuevoItem: function() {
                objRepartidor.nuevo();
            },
            onAnularItem: function($btn, o){
                objRepartidor.anular($btn, o);
            }
        });
    });

    $btnBuscar.on("click", function(e){
        e.preventDefault();
        listar();
    });

    $btnNuevo.on("click", function(e){
        e.preventDefault();
        $mdlRegistro.modal("show");
        $mdlRegistro.find(".modal-title").html("Registrar Preliquidaci贸n");
    });

    $mdlRegistro.on("submit","form", function(e){
        e.preventDefault();
        registrar();
    });

    $btnImprimir.on("click", function(e){
        e.preventDefault();
        imprimirPreliquidacionPDF($txtIdPrequilidacion.val());
    });

    $(".modal").on('hidden.bs.modal', function (e) { //LINEA DE CODIGO PARA CORREGIR MULTIPLES MODALS
        if (e.currentTarget.id == "mdl-registro"){
            $('body').removeClass('modal-open');
        } else {
            $('body').addClass('modal-open');    
        }
    });

    $mdlRegistro.on("hide.bs.modal", function(e){
        limpiarModal();
    });

    $btnAgregarItem.on("click", (e)=>{
        e.preventDefault();
        agregarItem();
    });

    $btnQuitarItems.on("click", (e)=>{
        e.preventDefault();
        removerItemsSeleccionados();
    });

    $btnImportarItems.on("click", (e)=>{
        e.preventDefault();
        importarItemsExcel();
    });

    $txtEstadoCambiar.on("change", (e)=>{
        e.preventDefault();
        cambiarEstadoSeleccionados(e.currentTarget.value);
    });

    $chkSeleccionarTodos.on("change", (e)=>{
        e.preventDefault();
        setearValorTodosLosCheck(e.currentTarget.checked);
    });

    $tblListado.on("click", ".btn-editar", (e)=>{
        e.preventDefault();
        let $tr = $(e.currentTarget).parents("tr");
        leer($tr.data("id"), $tr);
    });

    $tblListado.on("click", ".btn-eliminar", (e)=>{
        e.preventDefault();
        let $tr = $(e.currentTarget).parents("tr");
        eliminar($tr.data("id"), $tr);
    });

    $tblDetalle.on("change", ".val_selected", (e) =>{
        e.preventDefault();
        var esSeleccionado = e.currentTarget.checked;
        if (!esSeleccionado){
            $chkSeleccionarTodos.prop("checked", false);
        }

        $lblRegSeleccionados.html(parseInt($lblRegSeleccionados.html()) + (esSeleccionado ? 1 : -1));
    });
    
    $tblDetalle.on("change", ".val_estado", (e) =>{
        e.preventDefault();
        let $estado = e.currentTarget;
        let dataset = $estado.dataset;
        let estado = $estado.value;
        let descripcion_motivacion = "";

        if (estado == "C"){ //motivado
            descripcion_motivacion = prompt("Ingrese una descripcion breve sobre la motivaci贸n del entregable.");
            if (descripcion_motivacion == null || descripcion_motivacion.length <= 0){
                $estado.value = dataset.previous == "" ? "P" : dataset.previous;
                $estado.dataset.desc = "";
                $estado.title = "";
                return;
            }

            $estado.dataset.desc = descripcion_motivacion;
            $estado.title =  "Motivo: "+descripcion_motivacion;descripcion_motivacion;
            $estado.dataset.previous = estado;
            return;
        }

        $estado.dataset.desc = "";
        $estado.title = "";
        $estado.dataset.previous = estado;
    });

    $tblDetalle.on("click", ".btn-quitar", (e)=>{
        e.preventDefault();
        quitarItem($(e.currentTarget).parents("tr"));
    });

    $tblDetalle.on("change", ".val_cantidad", (e)=>{
        e.preventDefault();
        let $input = $(e.currentTarget);
        if ($input.val() === "0" || $input.val() === ""){
            $input.val("1");
        }
        let $tr = $input.parents("tr");
        let costoUnitario = $tr.find(".val_costounitario").val();
        let cantidad = $input.val();
        reajustarFila($tr, costoUnitario, cantidad);
    });

    $tblDetalle.on("change", ".val_costounitario", (e)=>{
        e.preventDefault();
        let $input = $(e.currentTarget);
        let $tr = $input.parents("tr");
        let costoUnitario = $input.val();
        let cantidad = $tr.find(".val_cantidad").val();

        $input.val(parseFloat(costoUnitario).toFixed(2))
        reajustarFila($tr, costoUnitario, cantidad);
    });
    
    $txtCostoGlobal.on("change", (e)=>{
        let valorCosto = e.currentTarget.value;
        if (valorCosto == ''){
            valorCosto = 0.00;
        }
        
        e.currentTarget.value = parseFloat(valorCosto).toFixed(2);
        calcularTotal();
    });
    
    
    $btnImportarExcelMasivo.on("click", (e)=>{
        importarExcelMasivo(); 
    });
    
    $("#txt-filtrarpedidosnumero").on("keyup", (e)=>{
        let $txt = e.currentTarget;
        e.preventDefault();
        if ($txt.value.length >= $("#txt-filtrarpedidosdigitos").val()){
            agregarItemFiltradoLista($txt.value);
            $txt.value = "";
            return;
        }
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

var reajustarFila = function($tr, costoUnitario, cantidad){
    $tr.find(".val_subtotal").val(parseFloat(costoUnitario * cantidad).toFixed(2));
    calcularTotal();
};


var calcularTotal = function(){
    let total = 0;
    $tblDetalle.find("tbody .val_subtotal").each(function(i, $input){
        total = total + parseFloat($input.value);  
    });
    
    let costoGlobal = parseFloat($txtCostoGlobal.val());
    total = total + costoGlobal;

    $("#txt-costoentrega").html(parseFloat(total).toFixed(2));
};

var initDT = function(registros) {
	if (TABLA){
		TABLA.destroy();
	}
	$tblListado.find("tbody").html(TEMPLATE_LISTA(registros));

    TABLA = $tblListado.DataTable({
        "responsive": true,
        "order": [[ 1, "desc" ]],
    	"ordering":true,
        "pageLength": 50,
        "columns": COLUMNS_DATATABLE,
        "scrollX": true
    	//language: { url : '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'}
    });
};

var listar = function() {
	var postData = {
	  	p_fecha_inicio : $txtFechaInicio.val(),
	    p_fecha_fin : $txtFechaFin.val()
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
	  $.post("../../controlador/sis_preliquidacion.php?op=listar_x_agencia", postData)
	      .done(fn)
	      .fail(fnFail);
};

var cargarRepartidores = function(){
   return $.ajax({ 
        url : "../../controlador/sis_repartidor.php?op=obtener",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {}
    });
};

var setearSelects  = function(){
    let $frmRegistro = $mdlRegistro.find("form");

	$txtAgencia.select2({
            ajax: { 
                url : "../../controlador/agencias.php?op=buscar_select",
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
            dropdownParent: $frmRegistro
        });
};

var limpiarModal  =function(limpiadoConModalAbierto = false){
	if (!limpiadoConModalAbierto){
		$txtRepartidor.val("").data("id","");
	} else{
		$txtRepartidor.focus();
	}

	$txtIdPrequilidacion.val("");
	$txtRepartidor.val("").data("id","");
    $txtTipoVehiculo.val("1");
    $txtEstado.parents(".form-group").hide();
    $txtObservaciones.val("");
    $txtCostoGlobal.val("0.00");

    renderVariosItems([]);

    $("#txt-costoentrega").html("0.00");
    $btnImprimir.hide();
    TR_FILA = null;
};

var leer = function(id_preliquidacion, $tr_fila){
    $.ajax({ 
        url : "../../controlador/sis_preliquidacion.php?op=leer",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
        	p_id_preliquidacion : id_preliquidacion
        },
        success: function(result){
            $mdlRegistro.modal("show");
            $mdlRegistro.find(".modal-title").html("Editando Preliquidacion: Cod. "+id_preliquidacion);

            let datos = result.datos;
            $txtIdPrequilidacion.val(datos.id_preliquidacion);
            $txtFechaRegistro.val(datos.fecha_registro);
            $txtRepartidor.val(datos.repartidor_descripcion).data("id", datos.id_repartidor).data("costo_entrega", datos.costo_entrega);
            $txtTipoVehiculo.val(datos.id_tipo_vehiculo);

            renderVariosItems(datos.registros_detalle);

            $txtEstado.parents(".form-group").show();
            $txtEstado.val(datos.estado_descripcion);

            $txtCostoGlobal.val(datos.costo_global);
            $("#txt-costoentrega").html((parseFloat(datos.costo_entrega_total) + parseFloat(datos.costo_global)).toFixed(2));
            
            $txtObservaciones.val(datos.observaciones);
            $btnImprimir.show();
            
            $chkImprimirGuardar.prop("checked", false);

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

var imprimirPreliquidacionPDF = function(id_preliquidacion){
	window.open("../../imprimir/preliquidacion.pdf.php?p_id="+id_preliquidacion,"_blank");
};

var registrar = function(){
	let editando = $txtIdPrequilidacion.val() != "";

    let repartidor = $txtRepartidor.data("id"),
        tipo_vehiculo = $txtTipoVehiculo.val(),
        fecha_registro = $txtFechaRegistro.val();
    let observaciones = $txtObservaciones.val(),
        costo_global = $txtCostoGlobal.val();

    let registrosDetalle = [];
    $tblDetalle.find("tbody tr").each(function(i, tr){
        const $tr = $(tr);
        const $estado = $tr.find(".val_estado");
        registrosDetalle.push({
            estado : $estado.val(),
            descripcion_cliente: $tr.find(".val_cliente").val(),
            cliente_interno: $tr.find(".val_clienteinterno").val() ?? "PARTICULAR",
            documento_guia: $tr.find(".val_numeroguia").val(),
            cantidad: $tr.find(".val_cantidad").val(),
            peso: $tr.find(".val_peso").val() ?? "",
            volumen: $tr.find(".val_volumen").val() ?? "",
            tipo_paquete: $tr.find(".val_tipopaquete").val(),
            direccion_entrega: $tr.find(".val_direccionentrega").val(),
            lugar_entrega: $tr.find(".val_lugarentrega").val(),
            costo_unitario: $tr.find(".val_costounitario").val(),
            subtotal : $tr.find(".val_subtotal").val(),
            descripcion_motivacion : $estado.data("desc") ?? ""
        });
    });

    $.ajax({ 
        url : "../../controlador/sis_preliquidacion.php?op=registrar",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
        	p_id_preliquidacion : $txtIdPrequilidacion.val(),
        	p_id_tipo_vehiculo : tipo_vehiculo,
            p_id_repartidor : repartidor,
        	p_fecha_registro : fecha_registro,
        	p_registros_detalle: JSON.stringify(registrosDetalle),
        	p_costo_global: costo_global,
        	p_observaciones: observaciones
        },
        success: function(result){
        	var datos = result.datos,
        		registro = datos.registro;

        	if (editando){
        		Util.actualizarFilaDataTable(TABLA, TEMPLATE_LISTA, registro, TR_FILA);
        	} else {
        		Util.actualizarFilaDataTable(TABLA, TEMPLATE_LISTA, registro);	
        	}

            if (!editando && $chkImprimirGuardar.prop("checked")){
                imprimirPreliquidacionPDF(registro.id);
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

var eliminar = function(id_preliquidacion, $tr_fila){
    if (!(confirm("驴Desea eliminar este registro?"))){
        return;
    }

    var self = this;
    $.ajax({ 
        url : "../../controlador/sis_preliquidacion.php?op=eliminar",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_preliquidacion : id_preliquidacion
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

let agregarItem = function(){
    let costoEntrega = $txtRepartidor.data("costo_entrega");
    if (costoEntrega == undefined || costoEntrega == ""){
        costoEntrega = 0.00;
    }

    costoEntrega = parseFloat(costoEntrega).toFixed(2);

    let $trNuevo = $(TEMPLATE_DETALLE({
        estados: ESTADOS,
        registros:  [
                        {   cliente: '', 
                            cliente_interno: 'PARTICULAR',
                            documento_guia: '', 
                            cantidad : '1',
                            tipo_paquete: '1',
                            direccion_entrega:'',
                            lugar_entrega: '',
                            costo_unitario: costoEntrega,
                            subtotal: costoEntrega,
                            bloqueado : '0'
                        }
                    ]
        }));

    $tblDetalle.append($trNuevo);

    setTimeout(function(){
        $trNuevo.find(".val_cliente").focus();
    }, 400);

    calcularTotal();
    $lblRegTotales.html(parseInt($lblRegTotales.html()) + 1);
};

let renderVariosItems = function(registros_detalle, agregarAlFinal = false){
    $tblDetalle.find("tbody")[agregarAlFinal ? "append" : "html"](TEMPLATE_DETALLE({estados: ESTADOS, registros: registros_detalle}));
    contarFilas();
};

let quitarItem = function($tr){
    $lblRegTotales.html(parseInt($lblRegTotales.html()) - 1);
    if ($tr.find(".val_selected")[0].checked){
        $lblRegSeleccionados.html(parseInt($lblRegSeleccionados.html()) - 1);
    }

    $tr.remove();
    calcularTotal();
};

let removerItemsSeleccionados = function(){
    let $listaTR = $tblDetalle.find(".val_selected:checked").parents("tr");
    $listaTR.remove();


    contarFilas();
    $lblRegSeleccionados.html("0");
    calcularTotal();
};

let setearValorTodosLosCheck = function(isChecked){
    $tblDetalle.find(".val_selected:not(:disabled)").prop("checked", isChecked);
    contarFilasSeleccionadas();
};

let contarFilasSeleccionadas = function(){
    let cantidadSeleccionadas = $tblDetalle.find(".val_selected:checked").length;
    $lblRegSeleccionados.html(cantidadSeleccionadas);
};

let contarFilas = function(){
    let cantidad = $tblDetalle.find("tbody tr").length;
    $lblRegTotales.html(cantidad);
};

let cambiarEstadoSeleccionados = function(valorEstado){
    let $listaTR = $tblDetalle.find(".val_selected:checked").parents("tr");
    $listaTR.find(".val_estado").val(valorEstado);

    $tblDetalle.find(".val_selected:checked").prop("checked", false);
    $lblRegSeleccionados.html("0");
};

let importarItemsExcel = function(){
    let $div = $(`<input type="file">`);
    $div.click();
    $div.on("change", function(e){
        var formData = new FormData($("form")[0]);
        formData.append('excel_importar', e.currentTarget.files[0]); 
        $btnImportarItems.prop("disabled", true);
        try{
            $.ajax({
                url: "../../controlador/sis_preliquidacion.php?op=importar_excel",
                type: "POST",
                dataType: "json",
                data: formData,
                contentType: false,
                processData: false
             })
                .done(function(xhr){
                    if (xhr.estado === 200){
                        var datos = xhr.datos;
                        renderVariosItems(datos, true);
                        calcularTotal();
                    }
                })
                .fail(function(request) {
                    Util.alert($("#blk-alert-modal"), request.responseText, "danger");
                    return;
                })
                .always(function(e){
                    $btnImportarItems.prop("disabled", false);
                })
        } catch(e){
            $btnImportarItems.prop("disabled", true);
            console.error(e);
        }
        $div.off();
        $div.remove();

        $div = null;
    });
};


let OBJ_ARCHIVOS_CARGADOS_EXCEL_TEMPORAL = null;
let OBJ_ARCHIVOS_FILTRADOS = [];

let importarExcelMasivo = function(){
    if (OBJ_ARCHIVOS_CARGADOS_EXCEL_TEMPORAL){
        if (!confirm("¿Desea seguir utilizando el archivo previamente cargado?")){
            pedirExcelMasivoPrecarga();
            return;
        }
        limpiarModalParaTrabajar();
    }
};

let pedirExcelMasivoPrecarga = function(){
    let $div = $(`<input type="file">`);
    $div.click();
    $div.on("change", function(e){
        var formData = new FormData($("form")[0]);
        formData.append('excel_importar', e.currentTarget.files[0]); 
        $btnImportarExcelMasivo.prop("disabled", true);
        try{
            $.ajax({
                url: "../../controlador/sis_preliquidacion.php?op=importar_excel_masivo",
                type: "POST",
                dataType: "json",
                data: formData,
                contentType: false,
                processData: false
             })
                .done(function(xhr){
                    if (xhr.estado === 200){
                        var datos = xhr.datos;
                        OBJ_ARCHIVOS_CARGADOS_EXCEL_TEMPORAL = {
                            nombre_archivo: datos.nombre_archivo,
                            items: datos.registros
                        };
                        
                        $("#mdl-filtrarpedidoslabel").find("span").html(datos.nombre_archivo);
                        limpiarModalParaTrabajar();
                    }
                })
                .fail(function(request) {
                    Util.alert($("#blk-alert-modal"), request.responseText, "danger");
                    return;
                })
                .always(function(e){
                    $btnImportarItems.prop("disabled", false);
                })
        } catch(e){
            $btnImportarExcelMasivo.prop("disabled", true);
            console.error(e);
        }
        $div.off();
        $div.remove();

        $div = null;
    });
};

let limpiarModalParaTrabajar = function(){
    $("#txt-filtrarpedidosnumero").val("");
    $("#txt-filtrarpedidosfiltrados").val("");
};

let agregarItemFiltradoLista = function(txtItemFiltrado){
    let $txtFiltrarPedidosFiltrados = $("#txt-filtrarpedidosfiltrados");
    let itemBuscado;
    
    if (txtItemFiltrado.length > 0){
        let items = OBJ_ARCHIVOS_CARGADOS_EXCEL_TEMPORAL.items;
        itemBuscado = items.find((e)=>{
            return e.key == txtItemFiltrado;
        });
    }
    
    if (itemBuscado){
        OBJ_ARCHIVOS_FILTRADOS.push(itemBuscado);
        
        if ($txtFiltrarPedidosFiltrados.val().length <= 0){
            $txtFiltrarPedidosFiltrados.val(txtItemFiltrado);
        } else {
            $txtFiltrarPedidosFiltrados.val($txtFiltrarPedidosFiltrados.val()+","+txtItemFiltrado);
        }
    }
    
};

this.getTemplates = function(){
    return $.when($.get("lista.preliquidaciones.detalle.hbs", {cache:false}), 
                    $.get("lista.preliquidaciones.hbs", {cache:false}));
};

$(function(){
    objRepartidor = new Repartidor();
    objBuscarComponente = new BuscarComponente({
                            $id: "#mdl-buscarcomponente", 
                            RUTA_TEMPLATE: "../_componentes/Buscar/buscar.componente.lista.hbs"
                        });
    init();
});

