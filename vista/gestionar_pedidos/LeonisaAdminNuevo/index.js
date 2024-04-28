const App = function () {
    this.objListaPedidos = null;
    this.objBuscarOrdenes = null;

    this.init = () => {
        this.objListaPedidos = new ListaPedidos({$parent: this});
        this.objBuscarOrdenes = new BuscarOrdenes({$parent: this});
        setEventos();    
    };

    const setEventos  = ()=>{
        $("a[role=tab]").toArray().forEach(function (triggerEl, index) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            /*
            if (triggerEl.classList.contains("active")){
                initTab(triggerEl.dataset.id);
            }
            */

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
                //initTab(index);
            });
        });
    };


    this.colorPorcentaje = function(valorPorcentual, mejorElAlto){
        let RESPUESTAS_COLOR;
        if (mejorElAlto){
            RESPUESTAS_COLOR = {
                bajo: "danger",
                medio : "warning",
                alto: "success"
            };
        } else {
            RESPUESTAS_COLOR = {
                bajo : "success",
                medio : "warning",
                alto : "danger"
            };
        }
    
        if (valorPorcentual < 40.00){
            return RESPUESTAS_COLOR.bajo;
        }
    
        if (valorPorcentual < 70.00){
            return RESPUESTAS_COLOR.medio;
        }
    
        return RESPUESTAS_COLOR.alto;
    };

    return this.init();
};

$(function(){
    new App();
});
