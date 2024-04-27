var SelectComponente = function(data){
	this.$select = data.$select ?? null;
	this.opcion_vacia = data.opcion_vacia ?? true;
	this.opcion_preseleccionada = data.opcion_preseleccionada ?? null;

	this.render = function(data){
	    var $html;

	    if (this.opcion_vacia == true){
	    	$html = `<option value="">Seleccionar</option>`;	
	    }
	    
	    for (var i = 0; i < data.length; i++) {
	        let o = data[i];
	        $html += `<option value="${o.id}">${o.descripcion}</option>`;
	    };

	    if (this.$select){
	    	this.$select.html($html);	

	    	if (this.opcion_preseleccionada){
	    		this.$select.val(opcion_preseleccionada);
	    	}
	    }
	    
	};

	return this;
};