
(function (Template7) {
    "use strict";
    window.templater = new function(){
        var cache = {};

        var self = this;

        this.load = function(url)
        {
            return new Promise(function(resolve,reject)
            {
                if(cache[url]){
                    resolve(cache[url]);
                    return true;
                }

                var xhr = new XMLHttpRequest();
                xhr.open('GET', url);
                xhr.onload = function() {
                    if(this.status == 200 && this.response.search('<!DOCTYPE html>') == -1){
                        cache[url] = Template7.compile(this.response);
                        resolve(cache[url]);
                    }else{
                        reject("Template "+url+" not found");
                    }
                };
                xhr.send();
            })
        };

        this.loadMultiple = function(arregloUrl)
        {
          var arregloLoadUrl = [],
              len = arregloUrl.length;

          for (var i = len - 1; i >= 0; i--) {
            arregloLoadUrl.push(self.load(arregloUrl[i]));
          };

          return Promise.all(arregloLoadUrl);
        };

        this.render = function(url, data)
        {
            return self.load(url)
                .then(function(tpl){
                    return tpl(data) ;
                });
        }; 

        this.getCache = function()
        {
            return cache;
        };

    }
})(Template7);

function _getHoy(){
    var d = new Date(),
      anio = d.getYear()+1900,
      mes = d.getMonth()+1,
      dia = d.getDate();

      mes = (mes >= 10)  ? mes : ('0'+mes);
      dia = (dia >= 10)  ? dia : ('0'+dia);

    return anio+"-"+mes+"-"+dia;
};


function _getHora(){
    var d = new Date(),
      hora = d.getHours(),
      min = d.getMinutes(),
      seg = d.getSeconds();

      hora = (hora >= 10)  ? hora : ('0'+hora);
      min = (min >= 10)  ? min : ('0'+min);
      seg = (seg >= 10)  ? seg : ('0'+seg);

    return hora+":"+min+":"+seg;
};

function _formateoFecha(fechaFormateoYanqui){
        var arrTemp;

        if (fechaFormateoYanqui == "" || fechaFormateoYanqui == null){
            return "";
        }

        arrTemp = fechaFormateoYanqui.split("-");
        return arrTemp[2]+"/"+arrTemp[1]+"/"+arrTemp[0];
};

function _preDOM2DOM($contenedor, listaDOM){
    /*Función que recibe un contenedor donde buscar elementos DOM, una lista con sus respectivos nombres de id y los objetos en que se convertirán, la
        lista debe estar en el orden adecuado para que se asigne automáticamente. 
      Devuelve el DOM.*/
    var DOM = {}, preDOM, cadenaFind = "", numeroDOMs = listaDOM.length,
        tmpEntries = [], tmpObjectName = [];

    for (var i = numeroDOMs - 1; i >= 0; i--) {
        tmpEntries = Object.entries(listaDOM[i])
        cadenaFind += (tmpEntries[0][1]+",");
        tmpObjectName[i] = tmpEntries[0][0];
    };

    cadenaFind = cadenaFind.substr(0,cadenaFind.length-1);

    preDOM = $contenedor.find(cadenaFind);

    for (var i = numeroDOMs - 1; i >= 0; i--) {
       DOM[tmpObjectName[i]] = preDOM.eq(i);
    };

    return DOM;
};

function setFX(esMovil){
    var NOMBRE_APP = VARS.NOMBRE_APP;

    if (esMovil){
        /*Alert*/
        alert = function(txtMensaje, fnCallBack){
            navigator.notification.alert(txtMensaje, (typeof fnCallBack == 'function') ? fnCallBack : null, NOMBRE_APP, "OK");
        };
        /*Confirm*/
        confirmar = function(txtMensaje, onConfirm, onRechaz){
           var fnOK = function(index){
                if (typeof onConfirm == 'function'){
                    if (index == 1){
                      onConfirm();
                    } else {
                      if (typeof onRechaz == 'function'){
                        onRechaz();
                      }
                    }
                } else {
                    console.error("Función de confirmación inválida.");
                }
            };
           navigator.notification.confirm(txtMensaje, fnOK, NOMBRE_APP, ["ACEPTAR", "CANCELAR"]);
        };

        getDevice = function(){
            return device.serial+'-'+device.uuid;
        };

        checkConexion = function(){
          var networkState = navigator.connection.type,
              states = {};
            states[Connection.UNKNOWN]  = 'Conexión Desconocida';
            states[Connection.ETHERNET] = 'Conexión Ethernet';
            states[Connection.WIFI]     = 'Conexión WiFi';
            states[Connection.CELL_2G]  = 'Conexión 2G';
            states[Connection.CELL_3G]  = 'Conexión 3G';
            states[Connection.CELL_4G]  = 'Conexión 4G';
            states[Connection.CELL]     = 'Conexión generica';
            states[Connection.NONE]     = 'Sin conexión red';
          return {online: (networkState != Connection.NONE), estados: this.states};
        };

        checkActualizar = function(){
          /*
          var updateUrl = "http://192.168.8.6/server_control_labores/version.app.xml";
          window.AppUpdate.checkAppUpdate(function(e){
            console.log(e);
          }, function(e){
            console.error(e);
          }, updateUrl);
          */    
        };

        exitApp = function(){
          navigator.app.exitApp();
        }

    } else {
        /*Alert*/
        confirmar = function(txtMensaje, onConfirm, onRechaz){
            var rpta = confirm(txtMensaje);
            if (rpta){
                if ((typeof onConfirm == 'function')){
                    onConfirm();
                }else {
                    console.error("Función de confirmación inválida.");
                }
            } else {
              if (typeof onRechaz == 'function'){
                onRechaz();
              }
            }
        };

        getDevice = function(){
            return navigator.userAgent.substr(0,30);
        };

        geoposicionar = function(onSuccess, onError){
           var fnOK = function(posicion){
                    if (typeof onSuccess == 'function'){
                        onSuccess(posicion);
                    } else {
                        console.error("Función de éxito inválida.");
                    }
                },
                fnNotOK = function(error){
                    showError(error);
                    if (typeof onSuccess == 'function'){
                        onSuccess();
                    } else {
                        console.error("Función de error inválida.");
                    }
                },
                showError = function(error){
                    switch(error.code) {
                            case error.PERMISSION_DENIED:
                              alert("User denied the request for Geolocation.");
                              break;
                            case error.POSITION_UNAVAILABLE:
                              alert("Location information is unavailable.");
                              break;
                            case error.TIMEOUT:
                              alert("The request to get user location timed out.");
                              break;
                            case error.UNKNOWN_ERROR:
                              alert("An unknown error occurred.");
                              break;
                    }
                };

            if (navigator.geolocation){
                navigator.geolocation.getCurrentPosition(fnOK, fnNotOK, { enableHighAccuracy: true }); 
            } else {
                alert("No tengo la función de geolocación disponible en este dispositivo.");
            }        
        };

        checkConexion = function(){
          return {online: navigator.onLine, estados: null};
        };

        checkActualizar = function(){
           return;
        };

        back  = function(){
          window.history.back();
        };

        exitApp = function(){
          window.close();
        };
    }
};