var o;
var app = function(){
	var self = this,
		DOM = {};

	var init = function(){
		setDOM();
		eventos();
		self.xhrObtenerSucursales();
		return self;
	};

	var setDOM = function(){
		DOM.blkCargando = $("#blkcargando");
		DOM.blkError = $("#blkerror");

		DOM.blkData = $("#blkdata");
		DOM.blkProceso = $("#blkproceso");

		DOM.btnBuscar = $("#btnbuscar");
		DOM.txtBuscarCodigoTracking = $("#txtbuscarcodigotracking");
		DOM.txtBuscarMesCodigoTracking = $("#txtbuscarmescodigotracking");
		DOM.txtBuscarAnioCodigoTracking = $("#txtbuscaraniocodigotracking");
		DOM.txtBuscarSucursalCodigoTracking = $("#txtbuscarsucursalcodigotracking");

		$blkAlert = $("#blk-alert");
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
		    txtbuscarmes = DOM.txtBuscarMesCodigoTracking.val(),
		    txtbuscaranio = DOM.txtBuscarAnioCodigoTracking.val(),
		    txtbuscarsucursal = DOM.txtBuscarSucursalCodigoTracking.val(),
			fn  = function(xhr){
				var datos = xhr.datos;
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
			$.post("../../controlador/sis_guia_area_registro.php?op=consultar_x_guia", {
				p_numero_guia : txtbuscar,
				p_numero_mes: txtbuscarmes,
				p_numero_anio: txtbuscaranio,
				p_sucursal: txtbuscarsucursal
			})
		      .done(fn)
		      .fail(function(xhr){
		      	if (xhr.responseJSON && xhr.responseJSON.mensaje){
		      		self.mostrarError(xhr.responseJSON.mensaje);
			  		return;
			  	}
			  	
			  	self.mostrarError(xhr.response);
		      })
		      .always(function(){
		      	buscandoCodigoTracking = false;
		      });
		} catch(e){
			buscandoCodigoTracking = false;
			console.error(e);
		}
 		

	};

	var fnFail = function(xhr){
	  	if (xhr.responseJSON && xhr.responseJSON.mensaje){
	  		Util.alert($blkAlert, xhr.responseJSON.mensaje, "danger");	
	  		return;
	  	}
	  	
	  	Util.alert($blkAlert, xhr.response, "danger");	
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

		DOM.blkData.show(500);
		DOM.blkProceso.show(500);
		
		$("#blknumeroguia").find("span").html(data.numero_guia);	
		$("#blkremitente").find("span").html(data.remitente);
		$("#blkconsignatario").find("span").html(data.consignatario);
		$("#blkdependencia").find("span").html(data.dependencia);
		$("#blkdestino").find("span").html(data.destino);
		$("#blkfecharecepcion").find("span").html(data.fecha_recepcion);
		$("#blkfechaentrega").find("span").html(data.fecha_entrega);

		let fueEntregado = data.fecha_entrega != "";
		$("#blkfechaentrega")[fueEntregado ? "show" : "hide"]();

		self.renderizarEstados(data.imagenes, fueEntregado);
	};

	this.renderizarEstados = function(dataImagenes, fueEntregado){
		var TIEMPO_ENTRE_ESTADO = 390,
			indiceEstados = 1,
			cantidadEstados = 3;

		var fnTemplateImagenes = function(data){
			var $html = ``;

			if (!data.length){
				return "<h4 class='col-sm-12 text-center'>¡Aún no hay imágenes cargadas!</h4>";
			}

			for (var i = 0; i < data.length; i++) {
				var o = data[i];
				$html += `<div class="col-sm-4">
                        	<div class="card card-hover">
	                            <div class="box text-center">
	                                <a href="../../img/imagenes_sis_guia/${o.url_imagen}" target="_blank"><img src="../../img/imagenes_sis_guia/${o.url_imagen}" class="img-fluid"></a>
	                            </div>
	                        </div>
	                    </div>`;
			};
			return $html;
		};

		DOM.blkProceso.show();

		var timer = setInterval(function(){
			if (indiceEstados  > cantidadEstados){
				clearInterval(timer);
				return;
			}

			var $blkEstado = $("#blkestado_"+indiceEstados);
			switch(indiceEstados){
				case 2: 
				if (fueEntregado){
					$blkEstado.find(".lblnombrestado").html("ENTREGADO");
					$blkEstado.find(".font-light").html('<i class="mdi mdi-check"></i>');
					$blkEstado.find(".box").removeClass("bg-info").addClass("bg-success");
				} else {
					$blkEstado.find(".lblnombrestado").html("GESTIONANDO");
					$blkEstado.find(".font-light").html('<i class="mdi mdi-chart-areaspline"></i>');
					$blkEstado.find(".box").removeClass("bg-success").addClass("bg-info");
				}
				break;
				case 3:
				$blkEstado.html(fnTemplateImagenes(dataImagenes));
				break;
			}

			$blkEstado.show(TIEMPO_ENTRE_ESTADO - 50);
			indiceEstados++;
			
		}, TIEMPO_ENTRE_ESTADO);

	};

/*
	var templateEstadoBlock = function(numero_orden, nombre_estado, estado, color){
		var newIcon, newOpacity, newColor, newColorText = "text-white";
		if (estado == "0"){
			newIcon = "mdi mdi-alert-circle";
			newOpacity = "0.5";
			newColor = "secondary";
		} else {
			newOpacity = "1";
			newColor = color;

			if (estado == "1"){
				newIcon = "fa fa-spin mdi mdi-refresh";
			} else {
				newIcon = "mdi mdi-check-circle";
			}
			
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
	*/
	
	
	this.xhrObtenerSucursales = function(){
		var fn  = function(xhr){
				let datos = xhr.datos;
				
				let $html = `<option value="">Ninguno</option>`;
				datos.forEach((o,i)=>{
				    $html += `<option value=${o.id}>${o.text}</option>`;
				});
				
				DOM.txtBuscarSucursalCodigoTracking.html($html);
			};
			
		try{
			$.post("../../controlador/sis_guia_consultador_select.php?op=listar_select_area", {
				p_buscar_texto : ''
			})
		      .done(fn)
		      .fail(function(xhr){
		      	if (xhr.responseJSON && xhr.responseJSON.mensaje){
		      		console.error(xhr.responseJSON.mensaje);
			  		return;
			  	}
		      });
		} catch(e){
			console.error(e);
		}

	};

	return init();
};

$(function(){
	o = new app();
});
