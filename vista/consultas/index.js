var o;
var app = function(){
	var self = this,
		DOM = {};

	var init = function(){
		setDOM();
		eventos();
		return self;
	};

	var setDOM = function(){
		DOM.blkCargando = $("#blkcargando");
		DOM.blkError = $("#blkerror");

		DOM.blkData = $("#blkdata");
		DOM.blkProceso = $("#blkproceso");

		DOM.btnBuscar = $("#btnbuscar");
		DOM.txtBuscarCodigoTracking = $("#txtbuscarcodigotracking");
	};

	this.getDOM = function(){
		return DOM;
	};

	var eventos = function(){
		DOM.btnBuscar.on("click", function(e){
			e.preventDefault();
			self.buscarCodigoTracking();
		});
	};

	this.buscarCodigoTracking = function(){
		self.xhrBuscarCodigoTracking();
	};

	var buscandoCodigoTracking = false;
	this.xhrBuscarCodigoTracking = function(){
		var txtbuscar = DOM.txtBuscarCodigoTracking.val(),
			fn  = function(xhr){
				var datos = xhr.datos;
				buscandoCodigoTracking = false;
				if (datos == null){
					self.mostrarError("¡Búsqueda sin resultados!");
					return;
				}
				self.mostrarDataEncontrada(datos);
			};

		if (buscandoCodigoTracking){
			return;
		}

		self.mostrarCargando();
		buscandoCodigoTracking = true;

		try{
			$.post("../../controlador/pedidos_ordenes_externo.php?op=buscar_codigo_tracking_web", {
				p_buscar_texto : txtbuscar
			})
		      .done(fn)
		      .fail(function(xhr){
		        buscandoCodigoTracking = false;
		      	if (xhr.responseJSON && xhr.responseJSON.mensaje){
		      		self.mostrarError(xhr.responseJSON.mensaje);
			  		return;
			  	}
			  	
			  	self.mostrarError(xhr.response);
		      });
		} catch(e){
			buscandoCodigoTracking = false;
			console.error(e);
		}

	};

	var fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($blkAlertModal, xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($blkAlertModal, xhr.response, "danger");	
	};

	this.mostrarError = function(strError){
		DOM.blkCargando.hide(500);
		DOM.blkError.find(".lblerror").html(strError);
		DOM.blkError.show( "slow", function() {
		   DOM.blkError.find("h4").addClass("text-danger");
		});

		DOM.blkData.hide();
		DOM.blkProceso.hide();
	};

	this.mostrarCargando = function(){
		DOM.blkCargando.show();
		DOM.blkError.hide();
		DOM.blkData.hide();
		DOM.blkProceso.hide();
	};

	this.mostrarDataEncontrada = function(data){
		DOM.blkCargando.hide();
		DOM.blkError.hide();

		DOM.blkData.show();
		DOM.blkProceso.show();

		$("#blkcliente").find("span").html(data.cliente);
		$("#blkfecha").find("span").html(data.fecha_ingreso);
		$("#blkcodigoremito").find("span").html(data.codigo_remito);	
		$("#blkdestinatario").find("span").html(data.destinatario);
		$("#blkdireccion").find("span").html(data.direccion+". "+data.ubigeo);
		$("#blknumeropaquetes").find("span").html(data.numero_paquetes);

		self.renderizarEstados(data.estados);
	};

	this.renderizarEstados = function(dataEstados){
		var TIEMPO_ENTRE_ESTADO = 200,
			fnRenderEstado = function(numero_orden, nombre_estado, estado, color, dataExtra){
				var $blk = $(templateEstadoBlock(numero_orden, nombre_estado, estado, color));
				DOM.blkProceso.append($blk);
				$blk.show(300);
			};

		var cantidadEstados = dataEstados.length,
			indiceEstados = 0;

		DOM.blkProceso.empty().show();

		var objEstado = dataEstados[indiceEstados];
		fnRenderEstado(indiceEstados, objEstado.nombre_estado, objEstado.estado, objEstado.color);
		indiceEstados++;

		var timer = setInterval(function(){
			var objEstado = dataEstados[indiceEstados];
			fnRenderEstado(indiceEstados, objEstado.nombre_estado, objEstado.estado, objEstado.color);
			indiceEstados++;
			if (indiceEstados  >= cantidadEstados){
				clearInterval(timer);
				return;
			}
		}, TIEMPO_ENTRE_ESTADO);

	};

	var templateEstadoBlock = function(numero_orden, nombre_estado, estado, color){
		var newIcon, newOpacity, newColor, newColorText = "text-white";
		if (estado == "0"){
			newIcon = "mdi mdi-alert-circle";
			newOpacity = "0.5";
			newColor = "secondary";
		} else {
			newOpacity = "1";
			newColor = color;
            newIcon = "mdi mdi-check-circle";
		}

		return `<div class="col-sm-4" class="blkestado_`+numero_orden+`" style="display:none">
                    <div class="card card-hover" style="opacity:`+newOpacity+`">
                        <div class="box bg-`+newColor+` text-center `+newColorText+`">
                            <h1 class="font-light"><i class="`+newIcon+`"></i></h1>
                            <h6 class="lblnombrestado">`+nombre_estado+`</h6>
                        </div>
                    </div>
                </div>`;
	};

	return init();
};

$(function(){
	o = new app();
});
