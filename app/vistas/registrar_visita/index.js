var RegistrarVisitaVista = function($el, _route, servicioweb, _app) {
    var self = this,
        _ID,
        _IDCLIENTE,
        /*DOM*/
        $blkCabecera,
        $blkMotivaciones,
        $blkRecepcion,
        //$btnSubirFotosCamara,
        //$btnSubirFotosGaleria,
        $btnSeleccionado = null,
        $preloader,
        $btnGuardar,
        $tmpInput = null,
        _toasted,
        t7 = {};

    var GUARDANDO = false,  
        IMAGENES = [];

    this.initialize = function() {
        var _t7 = _route.route.t7, 
            arregloT7 = [_t7.$preloader, _t7.cabecera],
            i = arregloT7.length;
        
        templater.loadMultiple(arregloT7).then(function(res){
            t7.$preloader = res[--i];
            t7.cabecera = res[--i];
            
            self.setDOM();
            self.setEventos();

            _ID = _route.query.id;
            var datosPedido  =_route.query.data;
            if (datosPedido == undefined || datosPedido == null || datosPedido == ""){
                self.obtenerDatos();    
                return;
            }
            
            self.renderPedido(JSON.parse(datosPedido));
            seleccionarTipoEnvio($el.find("#btn-aceptado")[0]);
        });

        return this;
    };

    this.setDOM = function(){
        var $DOM = $el.find("#blk-cabecera, #blk-motivaciones, #blk-recepciondestinatario, #txt-esreceptordestinatario,"
                             +"#blk-recepcion, #txt-documentoreceptor, #txt-receptor, #txt-observaciones,"
                             +"#preloader_2"),
            i = 0;

        $blkCabecera = $DOM.eq(i++);
        $blkMotivaciones = $DOM.eq(i++);
        $blkRecepcionaDestinatario = $DOM.eq(i++);
        $chkRecepcionaDestinatario = $DOM.eq(i++);
        $blkRecepcion = $DOM.eq(i++);
        $txtNumeroDocumentoDestinatario = $DOM.eq(i++);
        $txtNombresDestinario =  $DOM.eq(i++);
        $txtObservaciones =  $DOM.eq(i++);
        $preloader = $DOM.eq(i++);

        //$btnSubirFotosCamara = $DOM.eq(i++);
        //$btnSubirFotosGaleria = $DOM.eq(i++);

        $btnGuardar = $el.find("#btn-guardar");
        $btnSeleccionado = $el.find("#btn-aceptado")[0];
        $DOM = null;
    };

    this.removeDOM = function(){
        $blkCabecera = null;
        $blkMotivaciones = null;
        $chkRecepcionaDestinatario = null;
        $blkRecepcion = null;

        $txtNumeroDocumentoDestinatario = null;
        $txtNombresDestinario = null;
        $txtObservaciones = null;

        $tmpInput = null;
        $preloader = null;
        $btnGuardar = null;
    };

    this.setEventos = function(){
       $el.on("click", ".tipo-envio", __clickTipoEnvio);
       $el.on("change", "#txt-esreceptordestinatario", __changeReceptor);
       $el.on("change", ".iptfotos", __changeFoto);
       $el.on("dblclick", ".img-cargada", __eliminarFoto);
       $el.on("click", "#btn-guardar", __guardar);

       $$("#btn-subirfotosgaleria").on("click", __clickSubirFoto);
       $$("#btn-subirfotoscamara").on("click", __clickActivarCamara);
    };

    this.removeEventos = function(){
        $el.off("click", ".tipo-envio", __clickTipoEnvio);
        $el.off("change", "#txt-esreceptordestinatario", __changeReceptor);
    
        $el.off("change", ".iptfotos", __changeFoto);
        $el.off("dblclick", ".img-cargada", __eliminarFoto);
        $el.off("click", "#btn-guardar", __guardar);

        $$("#btn-subirfotosgaleria").off("click", __clickSubirFoto);
        $$("#btn-subirfotoscamara").off("click", __clickActivarCamara);
    };

    var indiceFoto = 0;
    var __clickTipoEnvio = function(e){
            e.preventDefault();
            seleccionarTipoEnvio(this);
        },
        __changeReceptor = function(){
            if (!this.checked){
                $blkRecepcion.show();
                $el.find("#txt-documentoreceptor").prop("required",true);
                $el.find("#txt-receptor").prop("required",true);
            } else {
                $blkRecepcion.hide();
                $el.find("#txt-documentoreceptor").val("").prop("required",false);
                $el.find("#txt-receptor").val("").prop("required",false);
            }
        },
        __clickSubirFoto = function(e){
            e.preventDefault();

            if ($tmpInput != null){
                $tmpInput.remove();
                $tmpInput = null;
            }

            $tmpInput = $$('<input type="file" class="iptfotos" id="iptfoto_'+indiceFoto+'" name="iptfoto_'+indiceFoto+'" accept="image/x-png,image/jpeg"/>');
            $el.find("#blk-inputfiles").append($tmpInput);
            $tmpInput.click();
        },
        __changeFoto = function(e){
            e.preventDefault();
            $tmpInput = null;
            compressImagen(e.target.files[0]);
        },
        __eliminarFoto = function(e){
            e.preventDefault();
            var indice = this.dataset.i;
            if (indice){
                $el.find("#iptfoto_"+indice).remove();
                this.remove();
            }
        },
        __guardar = function(e){
            e.preventDefault();
            var objValidarFormulario;

            if (GUARDANDO == true){
                return;
            }

            objValidarFormulario = validar();

            if (objValidarFormulario.r == false){
                return;
            }

            var fnOK = function(xhr){
                var data = xhr.data,
                    datos;

                GUARDANDO = false;
                preloaderMostrar(false);
                if (data.estado == 200){
                   datos = data.datos;
                   toast(datos.msj);
                   back();
                   _app.routes[1]._page_.volverAListar();
                   return;
                }
              },
              fnError = function(e){
                   GUARDANDO = false;
                   preloaderMostrar(false);
                   alert(e.message);
                   if (e.status == 401){
                        setTimeout(function(){
                            cerrarSesion();
                        }, 300)
                        return;
                   }
              };

            confirmar("¿Confirmar registro?", function(){
                GUARDANDO =  true;
                preloaderMostrar(true);
                servicioweb.guardarVisita(objValidarFormulario.obj).then(fnOK, fnError);
            });
        };

    var __clickActivarCamara = function(e){
        var  opts = {
            quality: 80,
            destinationType: Camera.DestinationType.FILE_URI,
            sourceType: Camera.PictureSourceType.CAMERA,
            mediaType: Camera.MediaType.PICTURE,
            encodingType: Camera.EncodingType.JPEG,
            cameraDirection: Camera.Direction.BACK,
            targetWidth: 600,
            targetHeight: 800
        };

        var fnSuccess = function(imageURI){
                window.resolveLocalFileSystemURL(imageURI, resolveOnSuccess, resOnError);
                /*document.getElementById('msg').textContent = imgURI;
                    document.getElementById('photo').src = imgURI;*/
            },
            fnError = function(error){
                console.error(error);
            };

        var resOnError = function (error) {
            alert("resOnError - " + error.code);
        };
        
        var resolveOnSuccess = function(entry) {
            entry.file(function (file) {

                var reader = new FileReader();
                reader.onloadend = function(evt){
                    var imgBlob = new Blob([evt.target.result], { type: file.type });
                    imgBlob.name = file.name;
                    compressImagen(imgBlob);
                }
              
                reader.onerror = function(e) {
                    console.log('Failed file read: ' + e.toString());
                    reject(e);
                };

                reader.readAsArrayBuffer(file);
            }, function () { alert('fail on trying to read the file.') });
        };

        navigator.camera.getPicture(fnSuccess, fnError, opts);
    };

    var seleccionarTipoEnvio = function($btn){
        var CLASS_NAME_OPACITY= "_opacitylow",
            motivado = false,
            key = $btn.dataset.key;

        if (key == "G"){
            $blkRecepcionaDestinatario.hide();
            $blkRecepcion.hide();
            $blkMotivaciones.hide();
        } else {
            $blkRecepcionaDestinatario.show();
            if (key == "M"){
                motivado = true;
                $blkRecepcionaDestinatario.hide();
                $blkRecepcion.hide();
                $blkMotivaciones.show();
            } else {
                $blkMotivaciones.hide();
            }
        }
        
        if ($btnSeleccionado == null){
            $btnSeleccionado = $btn;
            $btn.classList.remove(CLASS_NAME_OPACITY);
            return;
        }

        if ($btnSeleccionado.dataset.key == $btn.dataset.key){
            $btnSeleccionado = null;
            $blkRecepcionaDestinatario.hide();
            $blkRecepcion.hide();
            $btn.classList.add(CLASS_NAME_OPACITY);
            if (motivado){
                $blkMotivaciones.hide();
            }
            return;
        }

        $btnSeleccionado.classList.add(CLASS_NAME_OPACITY);
        $btn.classList.remove(CLASS_NAME_OPACITY);
        $btnSeleccionado = $btn;
    };

    var validar = function(){
        var tipoVisita = ($btnSeleccionado != null ) ? $btnSeleccionado.dataset.key : "",
            esReceptorDestinatario = $chkRecepcionaDestinatario[0].checked,
            numeroDocumentoDestinatario = $txtNumeroDocumentoDestinatario.val(),
            nombreDestinatario = $txtNombresDestinario.val(),
            observaciones = $txtObservaciones.val(),
            $motivaciones = [].slice.call($el.find(".txt-tipopago:checked")),
            imagenes = [],
            $imagenes = IMAGENES;


        if (tipoVisita == ""){
            toast("Debe seleccionar un tipo de visita");
            return {r: false};
        }

        if (tipoVisita != "G"){
            if (tipoVisita == "M"){
                if ($motivaciones.length <= 0){
                    toast("En una visita motivada debe haber por lo menos 1 motivación seleccionada.");
                    return {r: false};       
                }
            }

            if (tipoVisita == "E"){
               if (!esReceptorDestinatario){
                    if (numeroDocumentoDestinatario == ""){
                        toast("Se debe ingreso el número documento de receptor.");
                        return {r: false};
                    }

                    if (nombreDestinatario == ""){
                        toast("Se debe ingreso el nombre de receptor.");
                        return {r: false};
                    }
                }  
            }
            
        }

        var motivaciones = [], imagenes = [];
        for (var i = $motivaciones.length - 1; i >= 0; i--) {
            motivaciones.push($motivaciones[i].value);
        };

        for (var i = $imagenes.length - 1; i >= 0; i--) {
            var tmpImagen = $imagenes[i];
            if (tmpImagen != null){
                imagenes.push(tmpImagen);    
            }
        }; 
        
        return {r: true, obj: { 
                                idPedidoOrden : _ID,
                                tipoVisita: tipoVisita, 
                                esReceptorDestinatario: esReceptorDestinatario,
                                numeroDocumentoDestinatario: numeroDocumentoDestinatario,
                                nombreDestinatario: nombreDestinatario,
                                observaciones: observaciones,
                                motivaciones: motivaciones,
                                imagenes : imagenes
                            }};


    };

    this.getData = function(){
        return data;
    };

    this.setData = function(_data){
        data = _data;
    };

    var renderImagen = function(file){
        if (file){
            var $img = $$('<img id="imgfoto_'+indiceFoto+'" data-i="'+indiceFoto+'" class="img-cargada"/>');
            var reader = new FileReader();
               reader.onload = function(e){
                var base64 = e.target.result;
                $img.attr('src', base64);
                //var dataUrl = 'data:'+file.type+';base64,'+base64;
                $el.find("#blk-fotos").append($img);
                //file.base64 = base64;
                IMAGENES[indiceFoto] = file;
                indiceFoto++; 
            };
            reader.readAsDataURL(file);
        }

        _app.sheet.close('.my-sheet');
    };

    
    var b64toBlob = function (b64Data, contentType){
        var sliceSize = 512;
        var byteCharacters = atob(b64Data);
        var byteArrays = [];
      
        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
          var slice = byteCharacters.slice(offset, offset + sliceSize);
          const byteNumbers = new Array(slice.length);
          for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
          }
      
          var byteArray = new Uint8Array(byteNumbers);
          byteArrays.push(byteArray);
        }
      
        var blob = new Blob(byteArrays, {type: contentType});
        return blob;
      };

    var preloaderMostrar = function(mostrando){
        if (mostrando){
            $el.find("._placeholder").html(t7.$preloader());
            $el.find(".block").hide();
        } else {
            $el.find("._placeholder").empty();
            $el.find(".block").show();
        }
    };

    var compressImagen = function(file) {
        if (!file) {
            return;
        }

        new Compressor(file, {
            quality: 0.8,
            width: 600,
            success(result) {
                renderImagen(result);
            },
            error(err) {
              alert(err.message);
            },
          });
    };

    this.obtenerDatos = function(){
         var fnOK = function(xhr){
            var data = xhr.data,
                datos;
            if (data.estado == 200){
                datos = data.datos;
                self.renderPedido(datos);
            }
          },
          fnError = function(e){
           alert(e.message);
          };
        servicioweb.cargarPedido({id: _ID}).then(fnOK, fnError);
    };

    var toast = function(texto){
        _toasted = _app.toast.show({text: texto});
    };

    this.renderPedido = function(datos){
        self.setData(datos);
        $blkCabecera.html(t7.cabecera(datos));
    };

    var preloaderMostrar = function(mostrando){
        $preloader[mostrando ? "show" : "hide"]();
        $btnGuardar[mostrando ? "hide" : "show"]();
    };

    this.destroy = function(){
        if (_toasted && !_toasted.destroy){
            _toasted.destroy();
        }
        _toasted = null;

        this.removeEventos();
        this.removeDOM();
        self = null;
    };

    return this.initialize();
};