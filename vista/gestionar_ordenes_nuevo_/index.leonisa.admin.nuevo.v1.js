var app;
$(function(){
	app = {
		init : function (id) {
			this.ID = id;
			this.objCabeceraPedido = new CabeceraPedido({
				$id: "#cmp-cabecerapedido", 
				data : {
					id: _ID, 
					id_cliente_especifico: _KEYLEONISA
				}
			});

			this.objTabProcesos = new TabsProcesos({
				$id: "#cmp-tabsapp",
				data : {
					id: _ID
				},
				app: this
			});
		},
	};

	app.init(_ID);
}());