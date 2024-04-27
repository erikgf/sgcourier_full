var ImagenSubirComponente = function(data){
	var self = this;
	this.id = data.id ?? "";
	this.id_area_registro = data.id_area_registro ?? "";
	this.url_imagen = data.url_imagen ?? null;

	this.$root = data.$root ?? null;

	this.$el = null;
	this.$inputFile = null;
	this.$btnSubir = null;
	this.$btnEliminar = null;
	this.$imgFoto = null;

	this.archivoImagen = null;
	this.destroyed = false;

	var fnTemplateImagen = function(data){ 
					var str_mostrar_subir = data.mostrar_subir ? "" : "display:none",
						str_mostrar_quitar = data.mostrar_quitar ? "" : "display:none";

					return `<div class="col-sm-4 form-group" data-id="${data.id}">
	                            <div style="position:relative">
	                                <img class="img-thumbnail imgfoto" src="${data.url_imagen}" title="Imagen"/>                      
	                                <div class="lbl-imgetiqueta">${data.etiqueta}</div>
	                            </div>
	                            <div style="position: absolute;right: 12.5px;top:0;">
	                            	<button style="${str_mostrar_subir}" class="btnsubir btn btn-sm btn-success">SUBIR <i class="fa fa-upload"></i></button>
		                        	<button style="${str_mostrar_quitar}" class="btnquitar btn btn-sm btn-danger">QUITAR <i class="fa fa-trash"></i></button>
	                            </div>
			                </div>`
			            };

	this.initRender = function(_data){
		var data_default = {
			id:  "",
			etiqueta : "",
			url_imagen: "../../img/loading_img.gif",
			mostrar_subir: true,
			mostrar_quitar: true
		};

		if (!_data){
			_data = data_default;
		}

		this.$el = $(fnTemplateImagen(_data));

		this.$btnSubir = this.$el.find(".btnsubir");
		this.$btnSubir.on("click", function(e){
			e.preventDefault();
			self.subir();
		});
		this.$btnEliminar = this.$el.find(".btnquitar");
		this.$btnEliminar.on("click", function(e){
			e.preventDefault();
			self.eliminar();
		});
		this.$imgFoto = this.$el.find(".imgfoto");

		if (this.$root){
			this.$root.append(this.$el);
		} else{
			console.error("No se ha inicializado un elemento root para la imagen.");
		}

	};

	this.subir = function(){
		if (!this.archivoImagen){
			console.error("Imagen no valida (Archivo FILE no encontrado).");
			return;
		}

		var fn = function(xhr){
			var datos = xhr.datos;

			self.$btnSubir.hide();
			self.$el.data("id", datos.id);
			self.id = datos.id;

			Util.alert($("#blk-alert-modalentrega"), datos.msj, "success");
		};

		var fnFail = function(xhr){
			var mensaje =  (xhr.responseJSON && xhr.responseJSON.mensaje) ?
								xhr.responseJSON.mensaje :
								xhr.response;

			Util.alert($("#blk-alert-modalentrega"), mensaje, "danger");
		 };
		
		var htmlBtnSubir = this.$btnSubir.html();
		this.$btnSubir.prop("disabled", true);
		this.$btnSubir.html("Cargando...");

		var datos_frm = new FormData();
		       datos_frm.append("p_id_area_registro_imagen", this.id);
		       datos_frm.append("p_id_area_registro", this.id_area_registro);
		       datos_frm.append("p_img", this.archivoImagen);

		     $.ajax({
			    url: "../../controlador/sis_guia_area_registro.php?op=registrar_imagen_entrega",
			    type: "POST",
			    data: datos_frm,
			    delay : 250,
			  	contentType: false,
		        processData: false

			 })
			 	.done(fn)
		     	.fail(fnFail)
		     	.always(function(){
		     		self.$btnSubir.html(htmlBtnSubir);
		     		self.$btnSubir.prop("disabled", false);
		     	});	
	};

	this.eliminar = function(){
		var esEliminacionSinBBDD = this.id == "",
			fnEliminar = function(){
				self.$el.hide("1000", function(){
					self._destroy();
				});
			};
		if (esEliminacionSinBBDD){
			fnEliminar();
			return;
		}

		self.eliminarDesdeBBDD(fnEliminar);
	};

	this.eliminarDesdeBBDD = function(fnCallback){
		if (self.id == ""){
			alert("No se puede eliminar una imagen desde BBDD sin un ID asociado.");
			return;
		}

		if (!confirm("¿Desea quitar esta imagen?")){
			return;
		}

		this.$btnEliminar.prop("disabled", true);
		$.ajax({ 
	        url : "../../controlador/sis_guia_area_registro.php?op=eliminar_imagen_entrega",
	        type: "POST",
	        dataType: 'json',
	        delay: 250,
	        data : {
	        	p_id_area_registro : self.id_area_registro,
	        	p_id_area_registro_imagen : self.id
	        },
	        success: function(result){
	        	var datos = result.datos;
	        	Util.alert($("#blk-alert-modalentrega"), datos.msj, "success");
	            fnCallback();
	        },
	        error: function (request) {
	        	this.$btnEliminar.prop("disabled", false);
	        	Util.alert($("#blk-alert-modalentrega"), request.responseText, "danger");
	            return;
	        },
	        cache: true
	        }
	    );
	};

	var procesarImagenes = function(archivos){
	    if (!archivos.length){
	    	self._destroy();
	        return;
	    }
	    comprimirImagen(archivos[0]);
	};

	var comprimirImagen = function(file) {
	    if (!file) {
	        return;
	    }

	    new Compressor(file, {
	        quality: 0.8,
	        width: 600,
	        success(result_file) {
	            renderImagen(result_file);
	        },
	        error(err) {
	          alert(err.message);
	        },
	      });
	};

	var renderImagen = function(file){
	    if (file){
	        var reader = new FileReader();
		        reader.onload = function(e){
		        	var srcResultFile = e.target.result;
		            var data = {
		            	id: "",
		            	etiqueta: "",
		            	url_imagen : srcResultFile,
		            	mostrar_subir: true,
						mostrar_quitar: true
		            };

	    			self.archivoImagen  = file;
		            self.initRender(data);

		        };
	        reader.readAsDataURL(file);
	    }
	};


	this.init = function(){
		if (this.id == ""){
			//se está genernadao uno nuevo
			this.$inputFile = $('<input type="file" class="iptfotos" name="iptfoto_" accept="image/x-png,image/jpeg"/>');	
			this.$inputFile.on("change", function(e){
				e.preventDefault();
				procesarImagenes(e.target.files);
			});	

			this.$inputFile.click();
			return;
		}

		this.initRender({
			id:  this.id,
			etiqueta : "",
			url_imagen: "../../img/imagenes_sis_guia/"+this.url_imagen,
			mostrar_subir: false,
			mostrar_quitar: true
		});
		return this;
	};

	this._destroy = function(){
		if (this.$inputFile){
			this.$inputFile.off("change");
			this.$inputFile.remove();
		}
		this.$inputFile = null;
		if (this.$btnSubir){
			this.$btnSubir.off("click");
		}
		this.$btnSubir = null;
		if (this.$btnEliminar){
			this.$btnEliminar.off("click");
		}
		this.$btnEliminar = null;
		this.$imgFoto = null;
		this.archivoImagen  = null;

		this.$root = null;
		this.$el = null;


		this.destroyed = true;
	};

	return this.init();
};