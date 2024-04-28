const BuscarOrdenes = function ({ $parent }){
    this.templates = null;
    this.$el = null;

    this.init = () => {
        this.setDOM();
        this.setEventos();
    }; 

    this.setDOM = () => {
        this.$el = $("#bsc-ordenes");
        this.$tbl = this.$el.find("#tbllistado-ordenes");
        this.$blkFiltrar = this.$el.find("#blk-filtrar-buscar");
        this.$blkAlert = this.$el.find(".blk-alert");
        this.$mdlFotos = $("#mdlfotos");
        this.$mdlVerDetalle = $("#mdlverdetalle");
    };

    this.setEventos = () => {
        this.$el.on("submit", "form", (e) => {
            e.preventDefault();
            this.listar(e.currentTarget);
        });

        this.$tbl.on("click", "tr button.btn-verdetalle", (e) => {
            this.verDetalle(e.currentTarget);
        });
    };
      
    let listando = false;
    this.listar = async ($form) => {
        const $btnBuscar = $(".btnbuscar");
        const temporalHTML = $btnBuscar.html();
        const postData = {
            p_tipo : $form.txtbuscarpor.value,
            p_cadena_buscar : $form.txtbuscar.value
        };

        if (postData.p_cadena_buscar.trim().length <= 0){
            return;
        }

        if (listando === true){
            return;
        }

        listando = true;
        $btnBuscar.html(CADENAS.CARGANDO);
        try {
            
           const { datos : registros} = await $.post("../../controlador/nuevo.pedidos_ordenes.php?op=buscar_ordenes_leonisa", postData);
           this.$tbl.find("tbody").html(this.templates.listaOrdenes(registros));

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

    this.verDetalle = async($btn) => {
        const { id } = $btn.dataset;
        const htmlTemporal = $btn.innerHTML;

        if ($btn.disabled === true) {
            return;
        }

        $btn.innerHTML = CADENAS.CARGANDO;
        $btn.disabled = true;

        try{
            const {datos} = await $.when($.post("../../controlador/nuevo.pedidos_ordenes.php?op=leer_x_id", { p_id_pedido_orden : id }));
            if (datos){
                this.$mdlVerDetalle.html(this.templates.verOrden(datos));
                this.$mdlVerDetalle.modal("show");
            }
        } catch (e) {
            console.error(e);
            Util.alert($(this.$blkAlert),  e.responseJSON?.mensaje || e.responseText  , "danger");
        } finally {
            $btn.innerHTML = htmlTemporal;
            $btn.disabled = false;
        }
    };

    this.verFotos = ({urlsSinFormateo}) => {
        if (urlsSinFormateo == ""){
            return;
        }

        const arregloUrl = urlsSinFormateo.split(",");
        this.$$mdlFotos.html(this.templates.verOrdenFotos(arregloUrl.map((url, i) => {
                                return { url, active : i === 0 ? 'active' : ''};
                            })));
        this.$$mdlFotos.find("#carruselFotos").carousel();        
        this.$$mdlFotos.modal("show");
    };

    this.getTemplates = async (init) => {
        this.templates = {
            listaOrdenes: Handlebars.compile(await $.get("./LeonisaAdminNuevo/lista.ordenes.hbs")),
            verOrdenFotos: Handlebars.compile(await $.get("./LeonisaAdminNuevo/ver.orden.fotos.hbs")),
            verOrden: Handlebars.compile(await $.get("./LeonisaAdminNuevo/ver.orden.hbs")),
        };

        init();
    };

    return this.getTemplates(this.init);
};