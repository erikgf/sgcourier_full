var MisAsignacionesVista = function($el, _route, servicioweb, _app) {
    var self = this,
        /*DOM*/
        $lstAsignaciones,
        t7 = {};
    // Loading flag
    var allowInfinite = true,
        itemsPerLoad = 15,
        maxItems = 99,
        lastItemIndex = 0;

    var paginaActual = 0;
    var ITEMS = [];
    var $txtCodigoCliente;
    var key_storage_cliente = "_idclientebuscar";

    var _toasted;
        /*
        $mapa,    
        $ver_mapa,  
        $finpagina,
        _markers, 
        _google,
        GOOGLE_MAPS_APIKEY = "AIzaSyAxMsuT_-ZvPMuG69IfvROqFOUJIu6Wl5o";
    */
    this.initialize = function() {
        var _t7 = _route.route.t7, 
            arregloT7 = [_t7.$preloader, _t7.lst_asignaciones, _t7.cbo_clientes],
            i = arregloT7.length;

        templater.loadMultiple(arregloT7).then(function(res){
            t7.$preloader = res[--i];
            t7.lst_asignaciones = res[--i];
            t7.cbo_clientes = res[--i];

            self.setDOM();
            self.setEventos();
            if (CLIENTES){
                self.obtenerDatos();    
            }
            
        });
      	return this;
    };

    this.setDOM = function(){
        var $DOM = $el.find("#txt-cliente, #txt-codigoremito, #lst-asignaciones"),
            i = 0;

        $txtCodigoCliente = $DOM.eq(i++);
        $txtCodigoRemito = $DOM.eq(i++);
        $lstAsignaciones = $DOM.eq(i++);

        $txtCodigoCliente.html(t7.cbo_clientes(CLIENTES));

        VARS.NOMBRE_STORAGE+"_idclientebuscar";
        var idClienteStorage = localStorage.getItem(VARS.NOMBRE_STORAGE+key_storage_cliente);
        if (idClienteStorage == null){
            idClienteStorage = VARS.ID_LEONISA;
        }
        $txtCodigoCliente.val(idClienteStorage);
        idClienteStorage = null;
        
        $DOM = null;
    };

    this.removeDOM = function(){
        $lstAsignaciones = null;
        $txtCodigoCliente = null;
    };

    this.setEventos = function(){
        // Attach 'infinite' event handler
        $el.on('infinite','.infinite-scroll-content', function () {
          self.cargarPedidos(0);
        });

        $el.on("click",".item-link", function(e){
            e.preventDefault();
            self.irPedidoOrden(this.dataset.id, null);
        });

        $el.on("click","#btn-refrescar", function(e){
            e.preventDefault();
            self.volverAListar();
        });

        $el.on("click","#btn-buscar", function(e){
            e.preventDefault();
            //self.buscarRemito();
            self.buscarRemitoBarcode(this);
        });

        $el.on("change","#txt-codigoremito", function(e){
            e.preventDefault();
            self.buscarRemito(this.value);
        });

        $el.on("change","#txt-cliente", function(e){
            e.preventDefault();
            localStorage.setItem(VARS.NOMBRE_STORAGE+key_storage_cliente, this.value);
            self.volverAListar();
        });
    };

    this.removeEventos = function(){
        $el.off("infinite", '.infinite-scroll-content');
        $el.off("click", '.item-link');
        $el.off("click", '#btn-refrescar');
        $el.off("click", "#btn-buscar");
        $el.off("change","#txt-codigoremito");
        $el.off("change", "#txt-cliente");
    };

    this.cargarPedidos = function(primeraCarga){
      // Exit, if loading in progress
     

      if (!allowInfinite) return;
      // Set loading flag
      allowInfinite = false; 

      var fnOK = function(xhr){
            var data = xhr.data,
                datos, registros;

            if (data.estado == 200){
               var datos = data.datos;
               registros = datos.registros;
                if (primeraCarga == 1){
                    maxItems = datos.max_items;

                    if (registros.length <= maxItems){
                        $el.find('.infinite-scroll-preloader').remove();
                    }
                }
                self.renderPedidos(registros);
                paginaActual++;

                ITEMS.push(registros);
            }
          },
          fnError = function(e){
           $el.find('.infinite-scroll-preloader').remove();
           alert(e.message);
           if (e.status == 401){
                setTimeout(function(){
                  //  cerrarSesion();
                }, 300)
                return;
           }
          };

      idCliente = $txtCodigoCliente.val();

      if (idCliente == "" || idCliente === undefined){
        return;
      }
      servicioweb.cargarPedidosPendientes({paginaActual:paginaActual, itemsPerLoad: itemsPerLoad, primeraCarga:  primeraCarga, idCliente: idCliente}).then(fnOK, fnError);
    };


    this.renderPedidos = function(lista_pedidos){
        allowInfinite = true;
        if (lastItemIndex >= maxItems) {
          // Nothing more to load, detach infinite scroll events to prevent unnecessary loadings
          _app.infiniteScroll.destroy('.infinite-scroll-content');
          // Remove preloader
          $el.find('.infinite-scroll-preloader').remove();

          if (lastItemIndex == 0){
            $lstAsignaciones.append(t7.lst_asignaciones([]));            
          }
          return;
        }

        $lstAsignaciones.append(t7.lst_asignaciones(lista_pedidos));
        lastItemIndex = $lstAsignaciones.find("li").length;
    };

    this.removeDOM = function(){
        if ($lstAsignaciones){
            $lstAsignaciones = null;
        }
    };

    this.removeEventos = function(){
        $el.off('infinite','.infinite-scroll-content');
        $el.off("click", ".item-link");
    };

    this.getItems = function(){
        return ITEMS;
    };

    this.setItems = function(_ITEMS){
        ITEMS = _ITEMS;
    };

    this.obtenerDatos = function(){
        self.cargarPedidos(1);
    };

    this.renderDatos = function(datos){
        self.setData(datos);
        $lstAsignaciones.html(t7.lst_asignaciones(datos));
    };

    this.irPedidoOrden  = function(idPedidoOrden, dataPedidoOrden){
        if (idPedidoOrden == ""){
            return;
        }

        var objNavy = {"name": "registrar_visita",  query : {id: idPedidoOrden, data : (dataPedidoOrden == null) ? dataPedidoOrden : (JSON.stringify(dataPedidoOrden))}};
        mainView.router.navigate(objNavy);
    };

    this.volverAListar = function(){
        allowInfinite = true;
        maxItems = 99;
        lastItemIndex = 0;
        paginaActual = 0;
        ITEMS = [];
        $lstAsignaciones.parent().append('<div class="preloader infinite-scroll-preloader"></div>');
        $lstAsignaciones.empty();

        this.obtenerDatos();
    };


    this.buscarRemito = function(codigoRemito){
        codigoRemito = (codigoRemito == undefined ? $txtCodigoRemito.val() : codigoRemito);
        
        var fnOK = function(xhr){
                var data = xhr.data,
                    datos;
                   
                _app.toast.close();
                if (data.estado == 200){
                   var datos = data.datos;

                   if (datos == ""){
                        toast("Código no encontrado");
                        return;
                   }


                   self.irPedidoOrden(datos.id_pedido_orden, datos);
                }
              },
            fnError = function(e){
                _app.toast.close();
               alert(e.message);
               if (e.status == 401){
                    setTimeout(function(){
                        cerrarSesion();
                    }, 300)
                    return;
               }
            };

      toast("Consultando...");
      servicioweb.buscarOrdenRemito({codigoRemito: parseInt(codigoRemito), idCliente : $txtCodigoCliente.val()}).then(fnOK, fnError); 
    };

    this.buscarRemitoBarcode = function($btn){
        $btn.classList.add("parpadea");
        setTimeout(function(){
            $btn.classList.remove("parpadea");
        },2500);

        cordova.plugins.barcodeScanner.scan(
            function (result) {
                if (result.cancelled){
                    toast("Lector barras cancelado");
                    return;
                }

                self.buscarRemito(result.text);
            },
            function (error) {
                alert("Scanning failed: " + error);
            },
            {
                orientation : "portrait" // Android only (portrait|landscape), default unset so it rotates with the device
            }
         );
    };

    var toast = function(texto){
        _toasted = _app.toast.show({text: texto});
    };

    this.destroy = function(){
        this.removeDOM();
        this.removeEventos();

        if (_toasted && !_toasted.destroy){
            _toasted.destroy();
        }
        _toasted = null;

        self = null;
    };

    return this.initialize();
};