var CADENAS = {
	CARGANDO : '<i class="fa fa-spin fa-spinner"></i>',
	ERROR : '<i class="fa fa-cancel"></i>',
	OK : '<i class="fa fa-check"></i>',
};

var Util = {
	alert : function($blk, mensaje, tipoMensaje, tiempoDuracionSegundos = 4){
		var $tmpHtml = $(`<div class="alert alert-`+tipoMensaje+`" role="alert">`+mensaje+`</div>`);
		$blk.html($tmpHtml);
		setTimeout(function() {
			if ($tmpHtml){
				$tmpHtml.remove();
			}
		}, tiempoDuracionSegundos * 1000);
	},
	actualizarFilaDataTable : function(objDataTable, fnTemplate, dataRegistro, $trFila = null){
	     var arr = [].slice.call($(fnTemplate([dataRegistro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });
    
	   let temp;
	    if (objDataTable){
	        if ($trFila){ 
	            temp= objDataTable
	                .row($trFila)
	                .data(dataNuevaFila);
	        } else {
	        	temp = objDataTable.row.add(dataNuevaFila);
	        	$(temp.node()).attr("data-id", dataRegistro.id);
	        }
	       
	       temp.draw(false); 
	    }
	}
};
var cerrarSesion = function(){
	var fn = function(){
		//window.location.reload();
		window.location.href = "../login/";
	};

	$.post("../../controlador/sesion.cerrar.web.php")
		.always(fn);
};

if (window.Handlebars){
    Handlebars.registerHelper('if_', function (v1, operator, v2, options) {
        switch (operator) {
            case '==':
                return (v1 == v2) ? options.fn(this) : options.inverse(this);
            case '===':
                return (v1 === v2) ? options.fn(this) : options.inverse(this);
            case '!=':
                return (v1 != v2) ? options.fn(this) : options.inverse(this);
            case '!==':
                return (v1 !== v2) ? options.fn(this) : options.inverse(this);
            case '<':
                return (v1 < v2) ? options.fn(this) : options.inverse(this);
            case '<=':
                return (v1 <= v2) ? options.fn(this) : options.inverse(this);
            case '>':
                return (v1 > v2) ? options.fn(this) : options.inverse(this);
            case '>=':
                return (v1 >= v2) ? options.fn(this) : options.inverse(this);
            case '&&':
                return (v1 && v2) ? options.fn(this) : options.inverse(this);
            case '||':
                return (v1 || v2) ? options.fn(this) : options.inverse(this);
            default:
                return options.inverse(this);
        }
    });
    
    Handlebars.registerHelper('round', function (numero, decimales) {
    	return Math.round10(numero, decimales * -1).toFixed(2);
    }); 
    
    Handlebars.registerHelper('indexer', function (i) {
		return parseInt(i) + 1;
	});
}

