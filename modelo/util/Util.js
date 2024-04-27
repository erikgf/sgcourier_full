var Util = {
        api: function (params) {
            return $.ajax({
                url: '../../controlador/controlador',
                data: params
            });
        },
        soloNumeros : function(e) {
            var key = e.keyCode || e.which;
            var tecla = String.fromCharCode(key).toLowerCase();
            var letras = "0123456789";
            var especiales = [8, 35, 36, 37, 39, 46 ,188, 190]; /*permitidos*/

            var tecla_especial = false;
            for (var i = 0; i < especiales.length; i++) {
                if (key == especiales[i]){
                    tecla_especial = true;
                    break;
                }
            };

            if(letras.indexOf(tecla) == -1 && !tecla_especial)
                return false;
            return true;
        },
        soloNumerosEnteros : function(e) {
            var key = e.keyCode || e.which;
            var tecla = String.fromCharCode(key).toLowerCase();
            var letras = "0123456789";
            var especiales = [8, 37, 39, 46 ];

            var tecla_especial = false;
            for(var l in especiales) {
                if(key == l) {
                    tecla_especial = true;
                    break;
                }
            }
            if(letras.indexOf(tecla) == -1 && !tecla_especial)
                return false;
            return true;
        },
        getFechaHoy :  function (estaFormateada) {
            var actualFecha =  this.formatoDateString(new Date());
            if (!estaFormateada){
                return actualFecha;
            } else {
                return formatearFecha(actualFecha);
            }
        },
        formatoDateString: function (d){   
            var month = d.getMonth()+1;
            var day = d.getDate();
            return d.getFullYear() + '-' +
            (month<10 ? '0' : '') + month + '-' +
            (day<10 ? '0' : '') + day; 
        },
        formatoTimeString: function (d){   
            var hours = d.getHours() < 10 ? '0'+d.getHours() : d.getHours();
            var minutes = d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes();
            return hours + ':' + minutes;
        },
        formatoFechaCompleta: function (d){                 
            var day = d.getDate();
            return this.getDia(d.getDay())+ ", " + (day<10 ? '0' : '') + day + " de "+ this.getMes(d.getMonth())+ " del año "+ d.getFullYear();
            
        },
    	formatearFecha : function(dbDate){
            if (dbDate == null){
                return "-";
            }
    		var r = dbDate.split("-");		
    		return r[2]+"/"+r[1]+"/"+r[0];
    	},
        formatearHora: function(dbHora){
            if (dbHora == null){
                return "-";
            }
            var r = dbHora.split(":");	
            var hrs = r[0], mins = r[1], segs = r[2];
            var miem = "AM";
            
            if (hrs >= 12){
                miem = "PM";
                // hrs = hrs - 12 <= 0 ? hrs : hrs-12;
                if (hrs < 10){
                    hrs = '0'+hrs;
                }
            }
       
            return hrs+':'+mins+' '+miem;
	},
        getMes : function (numMes){         
            switch (numMes){
                case 0 : return "Enero";
                case 1 : return "Febrero";
                case 2 : return "Marzo";
                case 3 : return "Abril";
                case 4 : return "Mayo";
                case 5 : return "Junio";
                case 6 : return "Julio";
                case 7 : return "Agosto";
                case 8 : return "Septiembre";
                case 9 : return "Octubre";
                case 10 : return "Noviembre";
                case 11 : return "Diciembre";
                default: return "Error";
            }
        },
        getDia  : function (numDia){         
            switch (numDia){
                case 0 : return "Lunes";
                case 1 : return "Martes";
                case 2 : return "Miércoles";
                case 3 : return "Jueves";
                case 4 : return "Viernes";
                case 5 : return "Sábado";
                case 6 : return "Domingo";                
                default: return "Error";
            }
        },
        getNDiasMes : function (numMes){                   
            switch (parseInt(numMes)){
                case 0:
                case 2:
                case 4:
                case 6:
                case 7:
                case 9:
                case 11:
                    return 31;
                case 1:
                    return 29;
                case 1:
                case 3:
                case 5:
                case 8:
                case 10:
                    return 30;
                default :
                    return -1;
            }
        },
        completarNum:  function(valor, cantidad){
            var tmp = ("000000000000000"+valor);            
            return (tmp).substr(tmp.length  - cantidad,cantidad);
        },
        swal : function(opt,fn){
            var tipo = opt.tipo,
                    texto = opt.texto,
                    cerrar = opt.cerrar,
                    colorBtn, img;
               //     colorBtn = opt.colorBtn,
               //     img = opt.img;
            
            //var fnSi = fn.si, fnNo = fn.no;
            if (tipo == "X"){
                colorBtn = "#d93f1f";
                img = "../../img/otros/eliminar.png";
            } else {
                colorBtn = "#3d9205";
                img = "../../img/otros/pregunta.png";
            }
            
            swal({
                title: "Confirme",
                text: texto,

                showCancelButton: true,
                confirmButtonColor: colorBtn,
                confirmButtonText: 'Si',
                cancelButtonText: "No",
                closeOnConfirm: cerrar,
                closeOnCancel: true,
                imageUrl: img
            },
            function(isConfirm){
                 if (isConfirm){
                    fn();
                 } else{
                   return;
                 }
            });
        },
        noty : function(opt,fn,fnNo){
            //posible: ecto: tipo largo osea pregunta o normalita.
            //tipo: confirmacion_roja,confirmacion_verde (a,s,w,e) normal,succss,warning,error.
            //texto: la pregunta.
            //fn => la funcion.
            switch (opt.tipo){
                case "a":
                    opt.tipo = "alert";
                    break;
                case "s":
                    opt.tipo = "success";
                    break;
                case "w":
                    opt.tipo = "warning";
                    break;
                case "e":
                    opt.tipo = "error";
                    break;
            }
            
           return noty({
                text: "<strong>"+opt.texto+"</strong>",
                layout: "bottomCenter",
                type: opt.tipo,
                modal : true,
                buttons: [
                        {addClass: 'btn btn-success', text: 'SÍ', onClick: function($noty) {
                                        $noty.close();
                                        fn();  
                                }
                        },
                        {addClass: 'btn btn-danger', text: 'NO', onClick: function($noty) {
                                        $noty.close();
                                        if (typeof fnNo == "function"){
                                            fnNo();
                                        }
                                }
                        }
                ]
            });
        },
        notyB : function(texto,tipo,tiempo){
            switch (tipo){
                case "a":
                    tipo = "alert";
                    break;
                case "s":
                    tipo = "success";
                    break;
                case "w":
                    tipo = "warning";
                    break;
                case "e":
                    tipo = "error";
                    break;
            }
            if (tiempo == -1){
                 return noty({text:texto,type:tipo});
            };

            var t = 3000;            
            if (tiempo!=undefined){
                t = tiempo * 1000
            }
            return noty({text:texto,type:tipo,timeout:t});
        }        
};     

var Arr = {
            conseguir :  function(array, propiedadNombre, valorPropiedad) {
            //var prop = "id";
            for (var i = 0, len = array.length; i < len; i++) {        
                if (array[i][propiedadNombre] == valorPropiedad){
                    return array[i];
                }
            }            
            return -1;
            },
            conseguirTodos :  function(array, propiedadNombre, valorPropiedad) {
            //var prop = "id";
            var arrayRet = [];
            for (var i = 0, len = array.length; i < len; i++) {        
                if (array[i][propiedadNombre] == valorPropiedad){
                   arrayRet.push(array[i]);
                }
            }            
            return arrayRet;
            },              
            remover :  function(array, obj, propiedadNombre) {
            //var prop = "id";
            var t_array = $.grep( array, function( n ) {
                        return n[propiedadNombre] !== obj[propiedadNombre];                 
                        //return n > 0;
                  }); 
            return t_array;
            },              
            eliminar :  function(array, objParams) {
            //var prop = "id";
            var propiedadNombre = Object.keys(objParams)[0],
                    valor = objParams[propiedadNombre];            
                var t_array = $.grep( array, function( n ) {
                        return n[propiedadNombre] !== valor;                 
                        //return n > 0;
                  }); 
            return t_array;
            },
            diferencia: function(array1,array2,propiedadNombre){
               var self = this;
               for (var i = 0, lenI = array2.length; i < lenI; i++) {     
                   for (var j = 0, lenJ = array1.length; j < lenJ; j++) {                        
                        if (array2[i][propiedadNombre] === array1[j][propiedadNombre]){
                           array1 = self.remover(array1,array1[j],propiedadNombre);
                           break;     
                        }
                    }
                }     
                return array1;
            },
            union : function(array1,array2,propiedadNombre){
               var ret = array1, bol;
               for (var i = 0, lenI = array2.length; i < lenI; i++) {     
                   bol = true;
                   for (var j = 0, lenJ = array1.length; j < lenJ; j++) {        
                        if (array2[i][propiedadNombre] === array1[j][propiedadNombre]){
                            //tiene el mismo ID.
                           bol = false;                           
                           break;     
                        }
                    }
                   if (bol){
                       ret.push(array2[i]);
                   }                                         
                }  
                return ret; 
            },
            interseccion : function(array1,array2,propiedadNombre){
               var self = this, ret  = [], bol;
               for (var i = 0, lenI = array1.length; i < lenI; i++) {     
                   bol = false;
                   for (var j = 0, lenJ = array2.length; j < lenJ; j++) {        
                        if (array1[i][propiedadNombre] === array2[j][propiedadNombre]){
                            //tiene el mismo ID.
                           array2 = self.remover(array2,array2[j],propiedadNombre);
                           bol = true;                           
                           break;     
                        }
                    }
                   if (bol){
                       ret.push(array1[i]);
                   }                                         
                }  
                return ret; 
            },
            exclusion: function(array1, array2, propiedadNombre){ //obj => {prop_name}
                //Para este "for" usaremos "grep", grep te devuelve un array con objetos que no 
                //cumplen una regla booleana.        
               var self = this;
               return $.grep(array1, function(i)
                {         
                    var o = self.objEnArray(i,array2,propiedadNombre);
                    return !o;
                });
            },
            objEnArray : function (obj,array,propiedadNombre){
                 for (var i = 0, len = array.length; i < len; i++) {    
                        if (array[i][propiedadNombre] === obj[propiedadNombre]){
                           return true;
                        }
                 }
                 return false;
            }
};



var Inputter = {
        find : function($el, $classList, text){
            var blocks = [].slice.call($el.getElementsByClassName($classList));
                for (var i = blocks.length - 1; i >= 0; i--) {
                    blocks[i].innerHTML = text;
                };
            blocks = null;
        },
        activarError : function($input, msjError){
            var $formGroup = $input.parentElement;
            $formGroup.classList.add("has-error");
            this.find($formGroup, "help-block", msjError);
            $formGroup = null;
        },
        quitarError : function($formGroup){
            $formGroup.classList.remove("has-error");
            this.find($formGroup, "help-block", "");
            $formGroup = null;
        }
    };

var Alert = function($el){
    var self = this,
        TIEMPO_DURACION = 5,
        lastTipoAlert = "",
        ID_TIMER = -1,
        mostrandose =  false,
        destroyed = false;

    this.init = function(){
        return this;
    };

    var _tipoAlert = function(numeroTipoAlert){
        switch(numeroTipoAlert){
            case 0:
            return "callout-danger";
            case 1:
            return "callout-success";
            case 2:
            return "callout-warning";
            case 3:
            return "callout-info";
            default:
            return "";
        }
    };

    this.mostrar = function(textoHTML, tipoAlert,  tiempoDuracion){
        var strAlert = _tipoAlert(tipoAlert);

        $el.innerHTML = textoHTML;

        if (mostrandose == true){
            if (ID_TIMER > 0) clearTimeout(ID_TIMER);
        } else {
            $el.classList.remove("hide");
            mostrandose = true;
        }

        if (lastTipoAlert != ""){
            $el.classList.remove(lastTipoAlert);
        } else {
            $el.classList.forEach(function(elNombre, i, classList){
                if (elNombre.indexOf("callout-") > 0){
                    classList.remove(elNombre);
                }
            }); 
        }
        
        $el.classList.add(strAlert);
        lastTipoAlert = strAlert;
        $el.focus();

        ID_TIMER = setTimeout(function(){
            mostrandose = false;
            $el.innerHTML = "";
            $el.classList.remove(lastTipoAlert);
            lastTipoAlert = "";
            $el.classList.add("hide");
            ID_TIMER = -1;
            
        }, ((tiempoDuracion || TIEMPO_DURACION) * 1000));

        strAlert = null;
    };

    this.destroy = function() {
        if (mostrandose == true){
            if (ID_TIMER > 0) clearTimeout(ID_TIMER);
        }
        ID_TIMER = -1;
        $el = null;
        destroyed = true;
    };

    return this.init();
};


function Timeout(fn, interval, scope, args) {
    scope = scope || window;
    var self = this;
    var wrap = function(){
        self.clear();
        fn.apply(scope, args || arguments);
    }
    this.id = setTimeout(wrap, interval);
}
Timeout.prototype.id = null
Timeout.prototype.cleared = false;
Timeout.prototype.clear = function () {
    clearTimeout(this.id);
    this.cleared = true;
    this.id = null;
};
 
 var Class = function(methods) {   
    var klass = function() {    
        this.initialize.apply(this, arguments);          
    };  
    
    for (var property in methods) { 
       klass.prototype[property] = methods[property];
    }
          
    if (!klass.prototype.initialize) klass.prototype.initialize = function(){};      
    
    return klass;    
};