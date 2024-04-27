var LoginVista = function($el, route, servicioweb, _app) {
    var self = this,
        $txtUsuario,
        $txtClave,
        $preloader,
        $btnIngresar,
        _toasted,
        _preloader = _app.preloader;

    this.initialize = function() {
        //templater.load(route.route.t7.envio).then((function(tpl){ t7Envio = tpl}));        
        self.setDOM();
        self.setEventos();

        self.cargarClientes();
      	return this;
    };

    this.setDOM = function(){
        var $DOM = $el.find("#txt-usuario, #txt-clave, #preloader_1, #btn-ingresar"),
            i = -1;

        $txtUsuario = $DOM.eq(++i);
        $txtClave = $DOM.eq(++i);
        $preloader = $DOM.eq(++i);
        $btnIngresar = $DOM.eq(++i);

        $DOM = null;
    };

    this.removeDOM =function(){
        $txtUsuario = null;
        $txtClave = null;
        $preloader = null;
        $btnIngresar = null;
    };

    this.setEventos = function(){
        $el.on("submit", "form", __clickLogin);
    };

    this.removeEventos = function(){
        $el.off("submit", "form", __clickLogin);
    };

    var toast = function(texto){
        _toasted = _app.toast.show({text: texto});
    };

    var __clickLogin =  function(e){
            e.preventDefault();
            self.iniciarSesion();
        };

    var validarIptSesion = function(){
        var usuario = $txtUsuario.val(),
            clave = $txtClave.val();

        if (usuario == undefined || usuario == ""){
            toast("Debe ingresar un usuario válido");          
            return {r: false};
        }

        if (clave == undefined || clave == ""){
            toast("Debe ingresar una clave válida");          
            return {r: false};
        }

        return {r: true, obj: {usuario: usuario, clave: clave}};
    };

    var preloaderMostrar = function(mostrando){
        $preloader[mostrando ? "show" : "hide"]();
        $btnIngresar[mostrando ? "hide" : "show"]();
    };

    this.iniciarSesion = function(){
        var objValidar = validarIptSesion(),
            fnOK = function(xhr,a,b){
                var data = xhr.data;
                preloaderMostrar(false);
                if (data.estado == 200){
                    var datos = data.datos;

                    DATA_NAV = {
                        acceso: true,
                        usuario : datos.data
                    }

                    localStorage.setItem(VARS.NOMBRE_STORAGE, JSON.stringify(DATA_NAV));
                    mainView.router.navigate({"name": "mis_asignaciones"});
                    return;
                }
                alert(data.mensaje);
            },
            fnError = function(e){
               preloaderMostrar(false);
               alert(e.message);
            };
            
        if (!objValidar.r){
            return;           
        };


        preloaderMostrar(true);
        servicioweb.iniciarSesion({usuario:objValidar.obj.usuario, clave: objValidar.obj.clave}).then(fnOK, fnError);
    };


    this.cargarClientes = function(){
        var fnOK = function(xhr,a,b){
                var data = xhr.data;                
                if (data.estado == 200){
                    var datos = data.datos;

                    CLIENTES = datos;
                    localStorage.setItem(VARS.NOMBRE_STORAGE+"_CLIENTES", JSON.stringify(datos));
                    return;
                }
                alert(data.mensaje);
            },
            fnError = function(e){
               preloaderMostrar(false);
               alert(e.message);
            };
    
        servicioweb.cargarClientes().then(fnOK, fnError);
    };

    this.destroy = function(){
        self.removeEventos();
        self.removeDOM();

        _preloader = null;
        if (_toasted && !_toasted.destroy){
            _toasted.destroy();
        }
        _toasted = null;

        _app = null;
        self = null;
    };

    return this.initialize();
};