var MisCompletadosVista = function($el, _route, servicioweb, _app) {
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
        var $DOM = $el.find("#txt-cliente, #lst-asignaciones"),
            i = 0;

        $txtCodigoCliente = $DOM.eq(i++);
        $lstAsignaciones = $DOM.eq(i++);

        $txtCodigoCliente.html(t7.cbo_clientes(CLIENTES));

        VARS.NOMBRE_STORAGE+"_idclientebuscar";
        var idClienteStorage = localStorage.getItem(VARS.NOMBRE_STORAGE+key_storage_cliente);
        if (idClienteStorage == null){
            idClienteStorage = VARS.ID_FUXION_SAC;
        }
        $txtCodigoCliente.val(idClienteStorage);
        idClienteStorage = null;

        $DOM = null;
    };

   this.removeDOM = function(){
        if ($lstAsignaciones){
            $lstAsignaciones = null;
        }
        $txtCodigoCliente = null;
    };

    this.setEventos = function(){
        // Attach 'infinite' event handler
        $el.on('infinite','.infinite-scroll-content', function () {
          self.cargarPedidos(0);
        });

        $el.on("change","#txt-cliente", function(e){
            e.preventDefault();
            localStorage.setItem(VARS.NOMBRE_STORAGE+key_storage_cliente, this.value);
            self.volverAListar();
        });
    };

    this.removeEventos = function(){
        $el.off("infinite", '.infinite-scroll-content');
        $el.off("change", '#txt-cliente');
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
                datos = data.datos,
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
                        cerrarSesion();
                    }, 300)
                    return;
               }
          };

      var idCliente = $txtCodigoCliente.val();
      servicioweb.cargarPedidosCompletados({paginaActual:paginaActual, itemsPerLoad: itemsPerLoad, primeraCarga:  primeraCarga, idCliente: idCliente}).then(fnOK, fnError);
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

    this.destroy = function(){
        this.removeDOM();
        this.removeEventos();
        self = null;
    };

    return this.initialize();
};