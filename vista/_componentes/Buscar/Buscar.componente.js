var SELECTED_ITEM;

const BuscarComponente = function({$id, title, RUTA_TEMPLATE}){
    //const RUTA_TEMPLATE = "../_componentes/Buscar/Buscar.componente.lista.hbs";
    let templateLista;
    let $modal, $txtTitle, $txtBuscar, $tblBuscar,  $btnSeleccionar, $btnNuevo, $btnEditar, $btnAnular;
    let localData = [];
    let digitosParaBuscar = 3;
    let ACTIVE_SEARCHING = false;
    let STR_CLASS_TRSELECCIONADO = "tr-seleccionado";

    SELECTED_ITEM = null;

    let fnOnSeleccionarItem = function(e){
        console.log(e);
    };

    let fnOnNuevoItem = function(e){
        console.log(e);
    };

    let fnOnEditarItem = function(e){
        console.log(e);
    };
    let fnOnAnularItem = function(e){
        console.log(e);
    };

    this.init = function(){
        this.getTemplates()
            .then((tpl)=>{
                $modal = $($id);
                if (title){
                    $txtTitle.html(title);    
                }
                
                templateLista = Handlebars.compile(tpl);
                this.setDOM();
                this.setEventos();
            })
            .fail(error=>console.error(error.responseText));
        return this;
    };

    this.setDOM = function(){
        $txtTitle = $modal.find(".modal-title");
        $txtBuscar = $modal.find(".txt-buscar");
        $tblBuscar = $modal.find(".tbl-buscar");

        $btnNuevo = $modal.find(".btn-buscarnuevo");
        $btnEditar = $modal.find(".btn-buscareditar");
        $btnAnular = $modal.find(".btn-buscaranular");
        $btnSeleccionar = $modal.find(".btn-buscarseleccionar");
    };

    this.setEventos = function(){
        $tblBuscar.on("click","tbody tr:not(.tr-void)", function(e){
            e.preventDefault();
            preseleccionarItem($(this));
        });

        $txtBuscar.on("keyup", (e)=>{
            this.search($txtBuscar.val());
        });
        /*
        $tblBuscar.on("dblclick","tbody tr:not(.tr-void)", function(e){
            e.preventDefault();
            console.log(SELECTED_ITEM);

            if (SELECTED_ITEM){
                seleccionarItem(SELECTED_ITEM);
            }  else {

                seleccionarItem({id: "",descripcion:""});
            }
        });
        */

        $btnSeleccionar.on("click", (e)=>{
            e.preventDefault();
            if (SELECTED_ITEM){
                seleccionarItem(SELECTED_ITEM);
            }  else {
                seleccionarItem({id: "",descripcion:""});
            }
        });

        $btnNuevo.on("click", (e)=>{
            fnOnNuevoItem();
        });

        $btnEditar.on("click", (e)=>{
            if (!SELECTED_ITEM){
                return;
            }

            let $btn = $(e.currentTarget);
            fnOnEditarItem($btn, SELECTED_ITEM);
        });

        $btnAnular.on("click", (e)=>{
            if (!SELECTED_ITEM){
                return;
            }

            let $btn = $(e.currentTarget);
            fnOnAnularItem($btn, {id: SELECTED_ITEM.id, descripcion: SELECTED_ITEM.descripcion});
        });
    };

    this.render = ({title, data, getData, onSeleccionarItem, onNuevoItem, onEditarItem, onAnularItem})=>{
        if (title){
            $txtTitle.html(title);    
        }

        SELECTED_ITEM = null;

        if (!data){
            $tblBuscar.find("tbody").html("Cargando...");
            $.when(getData)
                .done((xhr)=>{
                    if (xhr.estado == 200){
                        localData = xhr.datos;
                        this.renderList(localData);

                        setTimeout(function(){
                            $txtBuscar.focus();
                        },600);
                    }
                })
                .fail((e)=>{
                    $tblBuscar.find("tbody").html("Error al consultar...");
                    console.error(e);
                });
        } else{
            localData = data;
            this.renderList(data);
        }
        
        $txtBuscar.val("");
        this.setOnSeleccionarItem(onSeleccionarItem);

        if (onNuevoItem){
            this.setOnNuevoItem(onNuevoItem);
        }

        if (onEditarItem){
            this.setOnEditarItem(onEditarItem);
        }

        if (onAnularItem){
            this.setOnAnularItem(onAnularItem);
        }

        $modal.modal("show");

    };

    this.renderList = (data)=>{
        $tblBuscar.find("tbody").html(templateLista(data));
    };

    this.search = function(cadenaBusqueda){
        var resultadoBusqueda = this.filterData(localData);
        this.renderList(resultadoBusqueda);
    };


    this.filterData = function(items){
        var filterString = $txtBuscar.val().toUpperCase();
        if (filterString.length === 0 ){
          return items;
        }

        return items.filter(function(item){
          return  item.descripcion.indexOf(filterString) !== -1;
        });
    };

    this.setOnSeleccionarItem = function(onSeleccionarItem){
        fnOnSeleccionarItem = onSeleccionarItem;
    };

    this.setOnNuevoItem = function(onNuevoItem){
        fnOnNuevoItem = onNuevoItem;
    };

    this.setOnEditarItem = function(onEditarItem){
        fnOnEditarItem = onEditarItem;
    };

    this.setOnAnularItem = function(onAnularItem){
        fnOnAnularItem = onAnularItem;
    };

    let preseleccionarItem = function($tr){
        $tblBuscar.find("tbody tr."+STR_CLASS_TRSELECCIONADO).removeClass(STR_CLASS_TRSELECCIONADO);
        $tr.addClass(STR_CLASS_TRSELECCIONADO);

        SELECTED_ITEM  = localData.find((e)=>e.id == $tr.data("id"));
    };

    let seleccionarItem = function(objSeleccionado){
        $modal.modal("hide");
        fnOnSeleccionarItem(objSeleccionado);
    };

    this.postGuardar = function(objSeleccionado, esEditando){
        let $tbody = $tblBuscar.find("tbody");
        var estaVacio = $tbody.find("tr:not(.tr-void)").length == 0,
            strNuevoTr = templateLista([{id: objSeleccionado.id, descripcion: objSeleccionado.descripcion, seleccionado: '1'}]);

        if (esEditando){
            let $tr = $tbody.find('tr[data-id="'+id+'"]');
            $tr.data("descripcion", objSeleccionado.descripcion);
            $tr.find("td").eq(1).html(objSeleccionado.descripcion);

            localData = localData.map(o => o.id !== id ? o : objSeleccionado);
        } else {
            if (estaVacio){
                $tbody.html(strNuevoTr);
            } else {
                $tbody.find("tr."+STR_CLASS_TRSELECCIONADO).removeClass(STR_CLASS_TRSELECCIONADO);
                $tbody.prepend(strNuevoTr);
            }     
        }

        SELECTED_ITEM = objSeleccionado;
    };

    this.postAnular = function({id}){
        let $tr = $tblBuscar.find('tbody tr[data-id="'+id+'"]');
        $tr.remove();

        localData = localData.filter(o => o.id != id);
    };

    this.getTemplates = function(){
        return $.when($.get(RUTA_TEMPLATE, {cache:false}));
    };

    return this.init();
};
