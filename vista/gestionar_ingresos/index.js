var TABLA_ORIGEN, TABLA_DESTINO,
    COL_DEFS = [50, 60, null, 150, 150, 120, 120, 100, 120],
    COL_DEFS_ALIGN = ["C","C",null,null,"C","C","C","C"],
    COLUMNS_DATATABLE = [];
var DATA;
var TR_FILA = null;

let $txtFechaInicio, $txtFechaFin, $btnNuevo, $btnBuscar, $btnGuardar;
let $txtIdIngreso, $txtCliente, $txtAgenciaTransporte, $txtFechaRegistro, $txtCosto, $txtCobrar, $txtOrigen, $txtDestino;
let $tblListadoOrigen, $tblListadoDestino;
let $txtPagadoHastaAhora, $txtPendientePagar, $txtPagado;
let $lblClienteNumeroDocumento, $lblClienteCelular;
let $txtEstado, $tblDetalle;
let $btnAgregarItem, $btnQuitarItems, $btnImportarItems, $btnImprimir;
let $frmRegistro, $mdlRegistro;
let $chkSeleccionarTodos, $chkVentana, $chkImprimirGuardar;
let $lblRegTotales, $lblRegSeleccionados;

var TEMPLATE_LISTA_ORIGEN = null, TEMPLATE_LISTA_DESTINO = null, TEMPLATE_DETALLE = null, TEMPLATE_DETALLE_DESTINO = null;
let ESTADOS =   [   {id:"E", descripcion:"ENVIADO"},
                    {id:"R", descripcion:"RECEPCIONADO"},
                    {id:"N", descripcion:"ENTREGADO"}
                ];
var readjusted = false, readjustedOrigen= false;

let $txtIdIngresoDestino, $txtClienteDestino, $txtAgenciaTransporteDestino, $txtFechaRegistroDestino, $txtCostoDestino, $txtCobrarDestino,
    $txtOrigenDestino, $txtDestinoDestino;
let $txtPagadoHastaAhoraDestino, $txtPendientePagarDestino, $txtPagadoDestino;
let $lblClienteNumeroDocumentoDestino, $lblClienteCelularDestino;
let $txtEstadoDestino, $tblDetalleDestino;
let $frmRegistroDestino, $mdlRegistroDestino;
let $lblRegTotalesDestino;

var init = function(){
    this.getTemplates()
        .then((tpl0, tpl1, tpl2, tpl3)=>{
            TEMPLATE_DETALLE = Handlebars.compile(tpl0[0]);
            TEMPLATE_DETALLE_DESTINO = Handlebars.compile(tpl1[0]);
            TEMPLATE_LISTA_ORIGEN = Handlebars.compile(tpl2[0]);
            TEMPLATE_LISTA_DESTINO = Handlebars.compile(tpl3[0]);
            setDOM();
            setEventos();
            setearSelects();
            setearAgencia();
            
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
    $tblListadoOrigen = $("#tbllistado-origen");
    $tblListadoDestino = $("#tbllistado-destino");
    $btnImprimir = $("#btn-imprimir");

    $txtIdIngreso = $("#txt-idingreso");
    $txtCliente = $("#txt-cliente");
    $lblClienteNumeroDocumento = $("#lbl-clientenumerodocumento");
    $lblClienteCelular = $("#lbl-clientecelular");

    $txtAgenciaTransporte = $("#txt-agenciatransporte");
    $txtOrigen = $("#txt-origen");
    $txtDestino = $("#txt-destino");
    $txtFechaRegistro = $("#txt-fecharegistro");
    $txtCosto = $("#txt-costo");
    $txtCobrar = $("#txt-cobrar");

    $txtPagadoHastaAhora = $("#txt-pagadohastaahora");
    $txtPendientePagar = $("#txt-pendiente");
    $txtPagado = $("#txt-pagado");
    $txtEstado = $("#txt-estado");
    $blkEstado = $("#blk-estado");

    $lblRegTotales = $("#lbl-regtotales");
    $lblRegSeleccionados = $("#lbl-regseleccionados");
    $tblDetalle = $("#tbldetalle");
    $btnAgregarItem = $("#btn-agregaritem");
    $btnQuitarItems = $("#btn-quitaritems");
    $btnImportarItems = $("#btn-importaritems");

    $chkSeleccionarTodos = $("#chk-seleccionartodos");
    $chkVentana = $("#chkcerrarventana");
    $chkImprimirGuardar = $("#chkimprimirguardar");

    //---
    $txtIdIngresoDestino = $("#txt-idingresodestino");
    $txtClienteDestino = $("#txt-clientedestino");
    $lblClienteNumeroDocumentoDestino = $("#lbl-clientenumerodocumentodestino");
    $lblClienteCelularDestino = $("#lbl-clientecelulardestino");

    $txtAgenciaTransporteDestino = $("#txt-agenciatransportedestino");
    $txtOrigenDestino = $("#txt-origendestino");
    $txtDestinoDestino = $("#txt-destinodestino");
    $txtFechaRegistroDestino = $("#txt-fecharegistrodestino");
    $txtCostoDestino = $("#txt-costodestino");
    $txtCobrarDestino = $("#txt-cobrardestino");

    $txtPagadoHastaAhoraDestino = $("#txt-pagadohastaahoradestino");
    $txtPendientePagarDestino = $("#txt-pendientedestino");
    $txtPagadoDestino = $("#txt-pagadodestino");
    $txtEstadoDestino = $("#txt-estadodestino");

    $frmRegistroDestino = $("#lbl-registrodestino");
    $mdlRegistroDestino = $("#mdl-registrodestino");

    $lblRegTotalesDestino = $("#lbl-regtotalesdestino");
    $tblDetalleDestino = $("#tbldetalledestino");

};

var setEventos = function() {
    $txtCliente.on("click", function(e){
        e.preventDefault();
        objBuscarComponente.render({
            title:"Buscar Cliente...",
            getData: cargarClientes(),
            onSeleccionarItem: function(o){ 
                if (o){
                    $txtCliente.val(o.nombres);
                    $txtCliente.data("id", o.id);

                    if (o.id === ""){
                        $lblClienteCelular.val("");
                        $lblClienteNumeroDocumento.val("");
                    } else {
                        $lblClienteCelular.val(o.celular);
                        $lblClienteNumeroDocumento.val(o.numero_documento);
                    }
                    
                }
            },
            onEditarItem: function($btn, o){
                objCliente.editar($btn, o);
            },
            onNuevoItem: function() {
                objCliente.nuevo();
            },
            onAnularItem: function($btn, o){
                objCliente.anular($btn, o);
            }
        });
    });

    $txtAgenciaTransporte.on("click", function(e){
        e.preventDefault();
        objBuscarComponente.render({
            title:"Buscar Agencia Transporte...",
            getData: cargarAgenciaTransportes(),
            onSeleccionarItem: function(o){ 
                console.log(o);
                if (o){
                    $txtAgenciaTransporte.val(o.descripcion);
                    $txtAgenciaTransporte.data("id", o.id);
                }
            },
            onEditarItem: function($btn, o){
                objAgenciaTransporte.editar($btn, o);
            },
            onNuevoItem: function() {
                objAgenciaTransporte.nuevo();
            },
            onAnularItem: function($btn, o){
                objAgenciaTransporte.anular($btn, o);
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
        $mdlRegistro.find(".modal-title").html("Registrar Ingreso");
        $txtPagadoHastaAhora.val("0.00");
        $blkEstado.hide();
    });

    $mdlRegistro.on("submit","form", function(e){
        e.preventDefault();
        registrar();
    });
    /*
    $btnImprimir.on("click", function(e){
        e.preventDefault();
        imprimirPreliquidacionPDF($txtIdPrequilidacion.val());
    });
    */

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

    $chkSeleccionarTodos.on("change", (e)=>{
        e.preventDefault();
        setearValorTodosLosCheck(e.currentTarget.checked);
    });

    $tblDetalle.on("change", ".val_selected", (e) =>{
        e.preventDefault();
        var esSeleccionado = e.currentTarget.checked;
        if (!esSeleccionado){
            $chkSeleccionarTodos.prop("checked", false);
        }

        $lblRegSeleccionados.html(parseInt($lblRegSeleccionados.html()) + (esSeleccionado ? 1 : -1));
    });

    /*
    $btnImportarItems.on("click", (e)=>{
        e.preventDefault();
        importarItemsExcel();
    });

    $txtEstadoCambiar.on("change", (e)=>{
        e.preventDefault();
        cambiarEstadoSeleccionados(e.currentTarget.value);
    });
    */
    

    $tblListadoOrigen.on("click", ".btn-editar", (e)=>{
        e.preventDefault();
        let $tr = $(e.currentTarget).parents("tr");
        leer($tr.data("id"), $tr);
    });

    $tblListadoOrigen.on("click", ".btn-eliminar", (e)=>{
        e.preventDefault();
        let $tr = $(e.currentTarget).parents("tr");
        eliminar($tr.data("id"), $tr);
    });

    $tblListadoDestino.on("click", ".btn-ver", (e)=>{
        e.preventDefault();
        let $tr = $(e.currentTarget).parents("tr");
        leerDestino($tr.data("id"), $tr);
    });

    $(".txt-montos").on("change", function(e){
        if (this.value < 0){
            this.value = "0.00";
        }
    })

    $txtCobrar.on("change", function(e){
        $txtPendientePagar.val(parseFloat($txtCobrar.val() - $txtPagadoHastaAhora.val()).toFixed(2));
    });

    $txtPagado.on("change", function(e){
        if ($txtPagado.val() > $txtPendientePagar.val()){
            $txtPagado.val($txtPendientePagar.val());
            return;
        }
    });

    $txtPagadoDestino.on("change", function(e){
        if ($txtPagadoDestino.val() > $txtPendientePagarDestino.val()){
            $txtPagadoDestino.val($txtPendientePagarDestino.val());
            return;
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        if (e.target.hash === '#tab-destino' && readjusted == false) {
            readjusted = true;
            TABLA_DESTINO.columns.adjust().responsive.recalc();
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        if (e.target.hash === '#tab-origen' && readjustedOrigen == false) {
            readjustedOrigen = true;
            TABLA_ORIGEN.columns.adjust().responsive.recalc();
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

var initDT = function(tabla_name, template_name, $tbListado, registros) {
    if (window[tabla_name]){
        window[tabla_name].destroy();
    }

    $tbListado.find("tbody").html(window[template_name](registros));
    window[tabla_name] = $tbListado.DataTable({
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

          readjusted = false;
          readjustedOrigen = false;

          initDT("TABLA_ORIGEN", "TEMPLATE_LISTA_ORIGEN", $tblListadoOrigen, datos.origen);
          initDT("TABLA_DESTINO", "TEMPLATE_LISTA_DESTINO", $tblListadoDestino, datos.destino);
      };

      var fnFail = function(xhr){
        $btnBuscar.prop("disabled", false);
        Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
      };


      $btnBuscar.prop("disabled", true);
      $.post("../../controlador/sis_ingreso.php?op=listar_x_agencia", postData)
          .done(fn)
          .fail(fnFail);
};

var cargarClientes = function(){
   return $.ajax({ 
        url : "../../controlador/sis_cliente.php?op=obtener",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {}
    });
};

var cargarAgenciaTransportes = function(){
   return $.ajax({ 
        url : "../../controlador/sis_agenciatransporte.php?op=obtener",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {}
    });
};

var setearSelects  = function(){
    let $frmRegistro = $mdlRegistro.find("form");

    $txtDestino.select2({
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

var setearAgencia = function(){
    $.ajax({ 
        url : "../../controlador/agencias.php?op=get_agencia_usuario",
        type: "POST",
        dataType: 'json',
        delay: 250,
        success: function(result){
            $txtOrigen.val(result.datos.descripcion);
        },
        error: function (request) {
            console.error(request.responseText);
            return;
        },
        cache: true
        }
    );
};

var limpiarModal  =function(limpiadoConModalAbierto = false){
    if (!limpiadoConModalAbierto){
        $txtCliente.val("").data("id","");
        $txtAgenciaTransporte.val("").data("id","");
    } else{
        $txtCliente.focus();
    }

    $txtIdIngreso.val("");
    $txtCliente.val("").data("id","");
    $lblClienteCelular.val("");
    $lblClienteNumeroDocumento.val("");
    $txtAgenciaTransporte.val("").data("id","");
    $blkEstado.hide();
    $txtEstado.val("E");
    $txtCosto.val("0.00");
    $txtCobrar.val("0.00");
    $txtPagadoHastaAhora.val("0.00");
    $txtPendientePagar.val("0.00");
    $txtPagado.val("0.00");

    $txtDestino.val("").trigger("change");
    renderVariosItems([]);

    //$btnImprimir.hide();
    TR_FILA = null;
};

var leer = function(id_ingreso, $tr_fila){
    $.ajax({ 
        url : "../../controlador/sis_ingreso.php?op=leer",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_ingreso : id_ingreso
        },
        success: function(result){
            $mdlRegistro.modal("show");
            $mdlRegistro.find(".modal-title").html("Editando Ingreso: Cod. "+id_ingreso);

            let datos = result.datos;
            $txtIdIngreso.val(datos.id_ingreso);
            $txtFechaRegistro.val(datos.fecha_registro);
            $txtCliente.val(datos.cliente_descripcion).data("id", datos.id_cliente);
            $lblClienteCelular.val(datos.cliente_celular);
            $lblClienteNumeroDocumento.val(datos.cliente_numero_documento);
            $txtCosto.val(datos.costo);
            $txtCobrar.val(datos.cobrar);
            $txtPagadoHastaAhora.val(datos.total_pagado);
            $txtPendientePagar.val(parseFloat(datos.cobrar - datos.total_pagado).toFixed(2));
            $txtPagado.val("0.00");

            let destino = {
                text : "",
                id : ""
            };

            if (datos.id_destino != null){
                destino = {
                    text: datos.destino_descripcion,
                    id: datos.id_destino
                };
            };

            $txtOril
            $txtDestino.append(new Option(destino.text, destino.id, false, false)).trigger('change');

            $txtAgenciaTransporte.val(datos.agencia_transporte_descripcion).data("id", datos.id_agencia_transporte);
            renderVariosItems(datos.registros_detalle);

            $blkEstado.show();
            $txtEstado.val(datos.estado);

            $btnImprimir.show();
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


var leerDestino = function(id_ingreso, $tr_fila){
    $.ajax({ 
        url : "../../controlador/sis_ingreso.php?op=leer",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_ingreso : id_ingreso
        },
        success: function(result){
            $mdlRegistroDestino.modal("show");
            $mdlRegistroDestino.find(".modal-title").html("Ver Ingreso: Cod. "+id_ingreso);

            let datos = result.datos;
            $txtIdIngresoDestino.val(datos.id_ingreso);
            $txtFechaRegistroDestino.val(datos.fecha_registro);
            $txtClienteDestino.val(datos.cliente_descripcion);
            $lblClienteCelularDestino.val(datos.cliente_celular);
            $lblClienteNumeroDocumentoDestino.val(datos.cliente_numero_documento);
            $txtCostoDestino.val(datos.costo);
            $txtCobrarDestino.val(datos.cobrar);
            $txtPagadoHastaAhoraDestino.val(datos.total_pagado);
            $txtPendientePagarDestino.val(parseFloat(datos.cobrar - datos.total_pagado).toFixed(2));
            $txtPagadoDestino.val("0.00");

            $txtOrigenDestino.val(datos.origen_descripcion);
            $txtDestinoDestino.val(datos.destino_descripcion);

            $txtAgenciaTransporteDestino.val(datos.agencia_transporte_descripcion);
            renderVariosItemsDestino(datos.registros_detalle);

            $txtEstadoDestino.val(datos.estado);

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

/*
var imprimirPreliquidacionPDF = function(id_ingreso){
    window.open("../../imprimir/ingreso.pdf.php?p_id="+id_ingreso,"_blank");
};
*/

const registrar = function(){
    let editando = $txtIdIngreso.val() != "";

    let id_cliente = $txtCliente.data("id"),
        id_agencia_transporte = $txtAgenciaTransporte.data("id"),
        id_destino = $txtDestino.val(),
        fecha_registro = $txtFechaRegistro.val();

    let costo = $txtCosto.val(),
        cobrar = $txtCobrar.val(),
        estado = $txtEstado.val(),
        pagado = $txtPagado.val();

    let registrosDetalle = [];
    $tblDetalle.find("tbody tr").each(function(i, tr){
        const $tr = $(tr);
        const $estado = $tr.find(".val_estado");
        registrosDetalle.push({
            producto: $tr.find(".val_producto").val(),
            producto_descripcion: $tr.find(".val_productodescripcion").val(),
            documento_guia: $tr.find(".val_numeroguia").val(),
            cantidad: $tr.find(".val_cantidad").val(),
            tipo_paquete: $tr.find(".val_tipopaquete").val(),
            peso: $tr.find(".val_peso").val() ?? "",
            volumen: $tr.find(".val_volumen").val() ?? ""
        });
    });

    $.ajax({ 
        url : "../../controlador/sis_ingreso.php?op=registrar",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_ingreso : $txtIdIngreso.val(),
            p_id_cliente : id_cliente,
            p_id_agencia_transporte: id_agencia_transporte,
            p_id_destino : id_destino,
            p_fecha_registro : fecha_registro,
            p_costo : costo,
            p_cobrar : cobrar,
            p_pagado : pagado,
            p_estado : estado,
            p_registros_detalle: JSON.stringify(registrosDetalle)
        },
        success: function(result){
            var datos = result.datos,
                registro = datos.registro;

            if (editando){
                Util.actualizarFilaDataTable(TABLA_ORIGEN, TEMPLATE_LISTA_ORIGEN, registro, TR_FILA);
            } else {
                Util.actualizarFilaDataTable(TABLA_ORIGEN, TEMPLATE_LISTA_ORIGEN, registro);  
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

const registrarDestino = function(){ //solo estados / pagos
    let editando = $txtIdIngresoDestino.val() != "";

    let estado = $txtEstado.val(),
        pagado = $txtPagado.val();

    $.ajax({ 
        url : "../../controlador/sis_ingreso.php?op=registrar_destino",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_ingreso : $txtIdIngresoDestino.val(),
            p_pagado : pagado,
            p_estado : estado
        },
        success: function(result){
            var datos = result.datos,
                registro = datos.registro;

            Util.actualizarFilaDataTable(TABLA_DESTINO, TEMPLATE_LISTA_DESTINO, registro, TR_FILA);
            $mdlRegistroDestino.modal("hide");
            Util.alert($("#blk-alert"), datos.msj, "success");
        },
        error: function (request) {
            Util.alert($("#blk-alert-modal-destino"), request.responseText, "danger");
            return;
        },
        cache: true
        }
    );
};


var eliminar = function(id_ingreso, $tr_fila){
    if (!(confirm("¿Desea eliminar este registro?"))){
        return;
    }

    var self = this;
    $.ajax({ 
        url : "../../controlador/sis_ingreso.php?op=eliminar",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
            p_id_ingreso : id_ingreso
        },
        success: function(result){
            var datos = result.datos;
            Util.alert($("#blk-alert"), datos.msj, "success");
            if (TABLA_ORIGEN){
                TABLA_ORIGEN
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
    let $trNuevo = $(TEMPLATE_DETALLE({
        registros:  [
                        {   
                            producto: '',
                            producto_descripcion: '',
                            documento_guia: '', 
                            cantidad : '1',
                            tipo_paquete: '1',
                            bloqueado : '0'
                        }
                    ]
        }));

    $tblDetalle.append($trNuevo);

    setTimeout(function(){
        $trNuevo.find(".val_producto").focus();
    }, 400);

    $lblRegTotales.html(parseInt($lblRegTotales.html()) + 1);
};

let renderVariosItems = function(registros_detalle, agregarAlFinal = false){
    $tblDetalle.find("tbody")[agregarAlFinal ? "append" : "html"](TEMPLATE_DETALLE({registros: registros_detalle}));
    contarFilas();
};


let renderVariosItemsDestino = function(registros_detalle, agregarAlFinal = false){
    $tblDetalleDestino.find("tbody")[agregarAlFinal ? "append" : "html"](TEMPLATE_DETALLE_DESTINO({registros: registros_detalle}));
    contarFilasDestino();
};

let quitarItem = function($tr){
    $lblRegTotales.html(parseInt($lblRegTotales.html()) - 1);
    if ($tr.find(".val_selected")[0].checked){
        $lblRegSeleccionados.html(parseInt($lblRegSeleccionados.html()) - 1);
    }

    $tr.remove();
};

let removerItemsSeleccionados = function(){
    let $listaTR = $tblDetalle.find(".val_selected:checked").parents("tr");
    $listaTR.remove();

    contarFilas();
    $lblRegSeleccionados.html("0");
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
    $lblRegTotalesDestino.html(cantidad);
};

let contarFilasDestino = function(){
    let cantidad = $tblDetalleDestino.find("tbody tr").length;
    $lblRegTotalesDestino.html(cantidad);
};

let renderEstados = function(){

};

let OBJ_ARCHIVOS_CARGADOS_EXCEL_TEMPORAL = null;
let OBJ_ARCHIVOS_FILTRADOS = [];

this.getTemplates = function(){
    return $.when($.get("lista.ingresos.detalle.hbs", {cache:false}), 
                    $.get("lista.ingresos.detalle.destino.hbs", {cache:false}),
                    $.get("lista.ingresos.origen.hbs", {cache:false}),
                    $.get("lista.ingresos.destino.hbs", {cache:false}));
};

$(function(){
    objCliente = new Cliente();
    objAgenciaTransporte = new AgenciaTransporte();
    objBuscarComponente = new BuscarComponente({
                            $id: "#mdl-buscarcomponente", 
                            RUTA_TEMPLATE: "../_componentes/Buscar/buscar.componente.lista.hbs"
                        });
    init();
});

