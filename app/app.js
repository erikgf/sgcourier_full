var $$ =  Dom7,
    t7 = Template7,
    app,
    mainView;

var MAP;
var CLIENTES = null;

var onDeviceReady = function () {   
    document.addEventListener("backbutton", back, false);
    /* ---------------------------------- Local Variables ---------------------------------- */
    var servicio_web = new WebServicio();
        servicio_web.initialize(),
        nombreApp = VARS.NOMBRE_APP,
        DATA_NAV_JSON = localStorage.getItem(VARS.NOMBRE_STORAGE);
        CLIENTES = JSON.parse(localStorage.getItem(VARS.NOMBRE_STORAGE+"_CLIENTES"));
        
        $$("title").html(nombreApp);
        $$(".menu-nombre-app").html(nombreApp);

        if ( DATA_NAV_JSON != null){
          DATA_NAV = JSON.parse(DATA_NAV_JSON); 
        } else {
          DATA_NAV = {
            acceso: false,
            usuario : {dni: '00000000', usuario: 'admin', nombre_usuario: "ADMIN"}
          };
        }

        app = new Framework7({
          root: '#app',
          name: nombreApp,
          id: 'com.sgcouriervisitas.app',
          panel: {
            swipe: 'left',
          },
          theme: 'auto',
          toast: {
            closeTimeout: 5000,
            closeButton: true,
          },
          routes: routes,
          touch : {
            fastClicks: true
          },
          dialog: {
            title: VARS.NOMBRE_APP,
          },
          servicio_web :  servicio_web
        });

      mainView = app.views.create('.view-main');
      if (DATA_NAV.acceso){
        mainView.router.navigate({"name": "mis_asignaciones"});
      } else {
        mainView.router.navigate({"name": "login", "reloadAll" : true});  
      }
};

(function(){
    var app = document.URL.indexOf( 'http://' ) === -1 && document.URL.indexOf( 'https://' ) === -1;
    if ( app ) {
      document.addEventListener("deviceready", onDeviceReady, false);
    } else {
      onDeviceReady();  // Web page
    } 
    setFX(app);
}());

    
var back = function(){
    mainView.router.back();
};


var cerrarSesion = function(){
   DATA_NAV = {
      acceso: false,
      usuario : {dni: '00000000', usuario: 'admin', nombre_usuario: "ADMIN"}
   };

   localStorage.removeItem(VARS.NOMBRE_STORAGE);
   app.panel.close();
   mainView.router.navigate('/login/', {clearPreviousHistory : true, ignoreCache: true});
};