const ListaPedidos = function ({ $parent }){
    this.templates = null;
    this.$el = null;

    let loadedEstados = false;

    this.init = () => {
        this.setDOM();
        this.setEventos();
        this.listarEstados();
    }; 

    this.setDOM = () => {
        this.$el = $("#lst-pedidos");
        this.$tbl = this.$el.find("#tbllistado");
        this.$mdl = this.$el.find("#mdl-registro");
        this.$blkAlert = this.$el.find("#blk-alert");
        this.$frmRegistro = this.$el.find("#frm-registro");
    };

    this.setEventos = () => {
        this.$el.on("click", ".btnnuevo", function(){
            e.preventDefault();
            $mdl.modal("show");
        });
    
        this.$mdl.on("hidden.bs.modal", function(){
            $frmRegistro[0].reset();
        });
    
        this.$frmRegistro.on("submit", function(e){
            e.preventDefault();
            //this.guardar();
        });
    
        this.$el.on("click", ".btnbuscar", () => {
            this.listar();
        });

        this.$tbl.on("click", "tr button.btn-eliminar", (e) => {
            const $btn = $(e.currentTarget);
            this.eliminar($btn);
        })
    };

    let guardando = false;
    this.guardar = () => {

    };
      
    let listando = false;
    this.listar = async () => {
        const $btnBuscar = $(".btnbuscar");
        const temporalHTML = $btnBuscar.html();
        const postData = {
            p_fechainicio : $("#txtfechainicio").val(),
            p_fechafin : $("#txtfechafin").val()
        };

        if (loadedEstados === false){
            return;
        }

        if (listando === true){
            return;
        }

        listando = true;
        $btnBuscar.html(CADENAS.CARGANDO);
        try {
            
           const { datos : registros} = await $.post("../../controlador/nuevo.pedidos.php?op=listar_leonisa", postData);

           this.$tbl.find("tbody").html(
                this.templates.listaPedidos(
                    registros.map(registro => {
                        const arregloCantidades = registro.cantidades.split(",").map(itemCantidad => {
                            const [ id, cantidad ] = itemCantidad.split("|");
                            const cantidadEntero = parseInt(cantidad);
                            const porcentaje = parseFloat(cantidadEntero / registro.cantidad ) * 100;
                            const considerarPorcentajeAltoRojo = id === 'E'; //E  = ENTREGADO
                            const colorPorcentaje = $parent?.colorPorcentaje(porcentaje, considerarPorcentajeAltoRojo);  
                            return {
                                id , 
                                cantidad: cantidadEntero, 
                                porcentaje : porcentaje.toFixed(2),
                                colorPorcentaje
                            }
                        });
                        return {
                            ...registro, 
                            cantidades : arregloCantidades
                        };
                    })
                )
           );

        } catch (error) {
            console.error(error)
            if (error?.responseJSON?.mensaje){
                Util.alert($("#blk-alert"), error.responseJSON.mensaje, "danger");
            }
            
        } finally {
            listando = false;
            $btnBuscar.html(temporalHTML);
        }
    };

    let listandoEstados = false;
    this.listarEstados = async () => {
        if ( listandoEstados == true){
            return;
        }

        listandoEstados = true;

        try {
            const { datos } = await $.post("../../controlador/estado.orden.php?op=obtener");
            this.$tbl.find("thead tr").append(this.templates.listaEstados(datos));
            loadedEstados = true;
            this.listar();
        } catch (error) {
            console.error(error);
            if (error?.responseJSON?.mensaje){
                Util.alert($("#blk-alert"), error?.responseJSON?.mensaje, "danger");
            }
        } finally {
            listandoEstados = false;
        }
    };

    let eliminando = false;
    this.eliminar = async ($btn) => {
        if (!confirm("¿Está seguro de realizar esta acción?")){
            return;
        }

        const id_pedido = $btn.data("id");

        if (id_pedido == null){
            return;
        }

        if (eliminando){
            return;
        }

        const $blkAlert = $("#blk-alert");
        eliminando =  true;
        const temporalHTML = $btn.html();
        $btn.html(CADENAS.CARGANDO);

        const postData = {
            id_pedido : id_pedido
        };

        try {
            
            const { datos } = await $.post("../../controlador/nuevo.pedidos.php?op=eliminar", postData);
            Util.alert($("#blk-alert"), datos?.msj, "success");	
            this.listar();
            
        } catch (error) {
            if (error.responseJSON && error.responseJSON.mensaje){
                Util.alert($blkAlert, error.responseJSON.mensaje, "danger");	
                return;
            }
            Util.alert($blkAlert, error.response, "danger");	
        } finally {
            eliminando = false;
            $btn.html(temporalHTML);
        }
    };

    this.getTemplates = async (init) => {
        this.templates = {
            listaPedidos: Handlebars.compile(await $.get("./LeonisaAdminNuevo/lista.pedidos.hbs")),
            listaEstados: Handlebars.compile(await $.get("./LeonisaAdminNuevo/lista.estados.hbs"))
        };

        init();
    };

    return this.getTemplates(this.init);
};