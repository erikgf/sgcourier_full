
var STR_LOCALSTORAGE = "_sgcourier_digitoscodigoremito",
    DEFAULT_DIGITOS = "9";

var TIMER;
var $blkMultiAlert;
var INICIADO_ASIGNACIONES 
var $txtCodigoRemitoBuscarMasivo;

var asignacionesInit = function(){
    var digitos = localStorage.getItem(STR_LOCALSTORAGE);
    if (digitos === "undefined" || digitos == null){
        digitos = DEFAULT_DIGITOS;
    }
        
    $txtDigitos.val(digitos);
    
    $txtCodigoRemitoBuscar.on("keyup", function(e){
		var valor = this.value;
		if (valor.length >= $txtDigitos.val()){
			asignar();
		}
	});
	
	$txtDigitos.on("change", function(e){
	    e.preventDefault();
	    var valor = this.value;
	    if (valor.length){
	        localStorage.setItem(STR_LOCALSTORAGE, valor);   
	    }
	});
	
	$txtCiudad.on("change", function(e){
		filtrar(this.value);
	});

	$("#btnactualizar").on("click" ,function(e){
		e.preventDefault();
		$("#btnactualizar").prop("disabled", true);
		obtenerDatos();
	 });
	 

	if (!$btnAsignar){
		console.error("No existe el BOTÓN asignar.");
	} else{
		$btnAsignar.on("click", function(e){
			e.preventDefault();
			asignarMasivo();
		});
	}
	
    $blkMultiAlert = $("#blk-multialert");
    
};

var asignar = function(){
    /*
	var $trs = [].slice.call($("#tbllistado").find("tbody tr")),
		codigoRemitoBuscar = $txtCodigoRemitoBuscar.val();
    */
    var idColaborador = $txtColaborador.val();
    if (idColaborador == ""){
        Util.alert($blkAlert,"Seleccionar colaborador a asignar", "danger");
        $txtCodigoRemitoBuscar.val("");
        return;   
    }
    
    var codigoRemitoBuscar = $txtCodigoRemitoBuscar.val();
    var registros = DATA.registros;
	var found = false;
	
	for (var i = registros.length - 1; i >= 0; i--) {
		var o = registros[i];
		if ((o.estado_actual == "G" || o.estado_actual == "N") && o.codigo_remito == codigoRemitoBuscar.trim()){
		    $txtCodigoRemitoBuscarZona.html(HTML_OK);
		    guardarAsignacionIndividual(idColaborador, o, i);
		    found = true;
			break; 
		}
	};
	

	if (!found){
		$txtCodigoRemitoBuscarZona.html(HTML_ERROR);
	}
	
	if (TIMER){
		clearTimeout(TIMER);
	}

	TIMER = setTimeout(function(){
		$txtCodigoRemitoBuscarZona.html(HTML_SEARCH);
	},1400);	

	$txtCodigoRemitoBuscar.val("");
};

var arMultiAlert = [];
var guardarAsignacionIndividual = function(idUsuarioAsignar, objOrden, indice){
    	var arregloOrdenesAsignados = [objOrden.id_pedido_orden];
    	var $localAlert = $('<div id="blk-alert"><div class="alert alert-warning" role="alert">Asignando: '+objOrden.codigo_remito+'...</div></div>');
        $blkMultiAlert.append($localAlert);
        arMultiAlert[indice] = $localAlert;
    	var fnOK = function(xhr){
    	  	var data = xhr.datos;
    	  	DATA.registros[indice].estado_actual = "G";
    	  	var $localAlert = arMultiAlert[indice];
    	  	$localAlert.html('<div class="alert alert-success" role="alert">Asignado: '+objOrden.codigo_remito+'...</div>');
    	  	setTimeout(function(){
    	  	    $localAlert.remove();
    	  	    $localAlert = null;
    	  	},3000);
    	};
    
    	var fnFail = function(error){
    	    var $localAlert = arMultiAlert[indice];
    	    $localAlert.html('<div class="alert alert-danger" role="alert">Asignado: '+xhr.responseJSON.mensaje+'...</div>');
    	  	setTimeout(function(){
    	  	    $localAlert.remove();
    	  	    $localAlert = null;
    	  	},3000);
    	};
    
    	var postData = {
    		p_idpedido : _ID,
    		p_idcolaborador : idUsuarioAsignar,
    		p_pedidoordenes : JSON.stringify(arregloOrdenesAsignados)
    	};
    	
    	$.post("../../controlador/pedidos_ordenes.php?op=asignar", postData)
    	      .done(fnOK)
    	      .fail(fnFail);
    };
    
var renderSelect = function (data_usuarios, rotulo) {
	var html = "<option value=''>"+rotulo+"</option>";

	for (var i = 0; i < data_usuarios.length; i++){
		var o = data_usuarios[i];
		html += `<option value="`+o.id+`">`+o.nombres_apellidos+`</option>`;
	};
	
	return html;
};

var renderSelectCiudades = function (data_ciudades) {
	var html = "<option value=''>Todas</option>";

	for (var i = 0; i < data_ciudades.length; i++){
		var o = data_ciudades[i];
		html += `<option value="`+o.ciudad+`">`+o.ciudad+`</option>`;
	};
	
	return html;
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

var asignarMasivo = function(){
    var idColaboradorAsignar = $txtColaborador.val();
    if (idColaboradorAsignar == ""){
        Util.alert($blkAlert,"Seleccionar colaborador a asignar", "danger");
        return;   
    }
    
    var codigoRemitoBuscar = $txtCodigoRemitoBuscarMasivo.val().trim().replace(/\r\n|\r|\n/g," ");
	var registrosAsignar = codigoRemitoBuscar.split(" ");
	var cantidadRegistrosAsignar = registrosAsignar.length;
	if (!cantidadRegistrosAsignar){
		alert("No se ha encontrado CODIGOS DE RÉMITO válidos en este PEDIDO.");
		return;
	}

	var $localAlert = $('<div id="blk-alert"><div class="alert alert-warning" role="alert">Asignando: '+cantidadRegistrosAsignar+' registros...</div></div>');
	$blkMultiAlert.append($localAlert);
	//arMultiAlert[999] = $localAlert;
	var fnOK = function(xhr){
		var data = xhr.datos;
		$("#btnactualizar").click();

		$localAlert.html('<div class="alert alert-success" role="alert">Asignados: '+data.cantidad_asignados+' registros...<br> Actualizando datos.</div>');
		setTimeout(function(){
			$localAlert.remove();
			$localAlert = null;
		},3000);

		$txtCodigoRemitoBuscarMasivo.val("");
	};

	var fnFail = function(error){
		$localAlert.html('<div class="alert alert-danger" role="alert">Asignado: '+xhr.responseJSON.mensaje+'...</div>');
		setTimeout(function(){
			$localAlert.remove();
			$localAlert = null;
		},3000);
	};

	var postData = {
		p_idpedido : _ID,
		p_idcolaborador : idColaboradorAsignar,
		p_pedidoordenes : JSON.stringify(registrosAsignar)
	};
	
	$.post("../../controlador/pedidos_ordenes.php?op=asignar_masivo_codigo_remito", postData)
			.done(fnOK)
			.fail(fnFail);

};

var obtenerDatos = function() {
	var postData = {
	  	p_id : _ID,
	  	p_id_cliente_especifico: ID_CLIENTE_ESPECIFICO
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
		$("#lblfecha").html(data.fecha_ingreso);

		$("#lblcliente").html("["+data.numero_documento+"] "+data.razon_social);
		$("#lbldireccion").html(data.direccion);
		$("#lblcelular").html(data.celular);

		$("#lblcantidadnoasignado").html(cantidades.noasignados);
		$("#lblcantidadgestionando").html(cantidades.gestionando);
		$("#lblcantidadentregados").html(cantidades.entregados);
		$("#lblcantidadmotivados").html(cantidades.motivados);

		$("#lblcantidad").html(parseInt(cantidades.noasignados) + parseInt(cantidades.gestionando) + parseInt(cantidades.entregados) + parseInt(cantidades.motivados));
            
            
		DATA.registros = data.registros;
		setTimeout(function(){
			renderGrafico(series);	
		},330);
		
		if (data.usuarios_asignar){
            $txtColaborador.html(renderSelect(data.usuarios_asignar, "Ninguno")).select2();
		}
		
		if (data.ciudades){
		    $txtCiudad.html(renderSelectCiudades(data.ciudades)).select2();
		}

		fnAlways();
	  };
	
	var fnFail = function(xhr){
	  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
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

var renderGrafico = function(SERIES){
	var data = [];
	for( var i = 0; i< SERIES.length; i++){	
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
		p_id_cliente_especifico: ID_CLIENTE_ESPECIFICO,
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

var cargando = false;
var listar = function($btn, estado){
	var postData = {
	  	p_id : _ID,
	  	p_id_cliente_especifico: ID_CLIENTE_ESPECIFICO,
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

$(function(){
	$(".select2").select2();

	$tbl = $("#tbllistado");
	$blkAlert = $("#blk-alert");
    $txtDigitos = $("#txtdigitos");
	$btnAsignar = $("#btnasignar");
	$txtCodigoRemitoBuscar = $("#txtcodigoremitobuscar");
	$txtCodigoRemitoBuscarMasivo = $("#txtcodigoremitobuscarmasivo");
	$txtColaborador = $("#txtcolaborador");
	$txtCiudad  = $("#txtciudad");
	$txtCodigoRemitoBuscarZona  = $("#txtcodigoremitobuscarzona");

	$mdlVerdetalle =$("#mdlverdetalle")
	$mdlFotos = $("#mdlfotos");
    eventos();

    if (_ID == null){
    	alert("No se ha encontrado pedido registrado.");
    	return;
    }

    obtenerDatos();
	asignacionesInit();
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