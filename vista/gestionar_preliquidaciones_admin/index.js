var TABLA,
	COL_DEFS = [60, 90, 100, null, 160, 150, 110, 95, 85],
	COL_DEFS_ALIGN = ["C","C",null,null,null,null,null,null,null],
	COLUMNS_DATATABLE = [];

let $txtFechaInicio, $txtFechaFin, $btnNuevo, $btnBuscar, $btnExcel, $btnGuardar, $blkDetalle;
let $frmRegistro, $mdlRegistro;

let DEFAULT_AGENCIA =  {descripcion : "TODAS", id: "*"};
let TEMPLATE_LISTA = null, TEMPLATE_DETALLE = null;

var init = function(){
    this.getTemplates()
        .then((tpl0, tpl1)=>{
            TEMPLATE_DETALLE = Handlebars.compile(tpl0[0]);
            TEMPLATE_LISTA = Handlebars.compile(tpl1[0]);
            setDOM();
            setEventos();
            setearSelects();
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
    $btnExcel = $("#btnexcel");
    $txtAgenciaBuscar = $("#txtagenciabuscar");
    $mdlRegistro = $("#mdl-registro");
    $tblListado = $("#tbllistado");
    $btnImprimir = $("#btn-imprimir");

    $blkDetalle = $("#blk-detalle");
};

var setEventos = function() {
    $btnBuscar.on("click", function(e){
        e.preventDefault();
        listar();
    });

    $btnExcel.on("click", function(e){
        e.preventDefault();
        exportarExcel()
    });
    
    $btnImprimir.on("click", function(e){
        e.preventDefault();
        imprimirPreliquidacionPDF($("#txtidpreliquidacion").val());
    });

    $tblListado.on("click", ".btn-ver", (e)=>{
        e.preventDefault();
        let $tr = $(e.currentTarget).parents("tr");
        verDetalle($tr.data("id"));
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
	$tblListado.find("tbody").html(TEMPLATE_LISTA(registros));

    TABLA = $tblListado.DataTable({
        "responsive": true,
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
	    p_fecha_fin : $txtFechaFin.val(),
        p_id_agencia : $txtAgenciaBuscar.val() == "" ? "*" : $txtAgenciaBuscar.val()
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
	  $.post("../../controlador/sis_preliquidacion.php?op=listar", postData)
	      .done(fn)
	      .fail(fnFail);
};


var setearSelects  = function(){
    let $frmRegistro = $mdlRegistro.find("form");
	$txtAgenciaBuscar.select2({
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
                    response.datos.push(DEFAULT_AGENCIA);
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar"
        });

    $txtAgenciaBuscar.append(new Option(DEFAULT_AGENCIA.descripcion, DEFAULT_AGENCIA.id, false, false));
};

var verDetalle = function(id_preliquidacion){
    $.ajax({ 
        url : "../../controlador/sis_preliquidacion.php?op=obtener_imprimir",
        type: "POST",
        dataType: 'json',
        delay: 250,
        data : {
        	p_id_preliquidacion : id_preliquidacion
        },
        success: function(result){
            $mdlRegistro.modal("show");

            let datos = result.datos;
            $mdlRegistro.find(".modal-title").html("Ver Detalle Preliquidación: Cod. "+datos.codigo);
            renderDetalle(datos);
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

var exportarExcel = function(){
  var strUrl = "../../controlador/reporte.preliquidaciones.xls.php?p_id="+$txtAgenciaBuscar.val()+"&p_fi="+$txtFechaInicio.val()+"&p_ff="+$txtFechaFin.val(); 
  window.open(strUrl,'_blank'); 
};

let renderDetalle = function(registros_detalle){
    $blkDetalle.html(TEMPLATE_DETALLE(registros_detalle));
};

this.getTemplates = function(){
    return $.when($.get("lista.preliquidaciones.detalle.hbs", {cache:false}), 
                    $.get("lista.preliquidaciones.hbs", {cache:false}));
};

$(function(){
    init();
});

