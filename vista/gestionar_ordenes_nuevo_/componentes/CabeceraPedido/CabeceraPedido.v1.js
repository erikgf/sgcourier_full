const CabeceraPedido = function (initData){
    this.$ = null;
    this.isMounted = false;
    this.template = null;

    this.compileTemplate = () => {
        const html = `<h5 class="card-title">Órdenes de Pedido: <small>ID: {{id_pedido_log}}</small></h5>
                        <div class="row">
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group m-t-10">
                                    <label>Fecha Ingreso</label>
                                    <p>{{fecha_ingreso}}</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group m-t-10">
                                <label>Cantidad: <b>{{cantidad}}</b></label>
                                <br>
                                {{#ordenes_estado}}
                                    <button class="btn btn-sm btn-{{estado_color}}">{{nombre_estado}} <span class="badge badge-pill badge-{{estado_color}}">{{cantidad}}</span></button> 
                                {{/ordenes_estado}}
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12 text-left">
                                <label>Avances: </label>
                                <div id="pie-ordenesestado" class="pie" style="height:90px;"></div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                {{#if puedo_generar_entregas}}
                                    <div class="form-group m-t-10">
                                        <br>
                                        <button type="button" class="btn btn-success btn-block" id="btn-excel-entregas">
                                        <i class="fa fa-file-excel"></i> GENERAR ENTREGAS
                                        </button>
                                    </div>
                                {{/if}}
                            </div>
                        </div>`;

        return Handlebars.compile(html);
    };

    const init = ({$id, data}) => {
        this.template = this.compileTemplate();
        this.$ = $($id);

        if (data){
            const {id, id_cliente_especifico} = data;
            this.obtenerDatos({id, id_cliente_especifico});
        }

        return this;
    };

    this.obtenerDatos = function({id}) {
        const postData = {
              p_id : id,
        };

        const fn = (xhr) => {
            const data = xhr.datos;

            this.render({...data, 
                puedo_generar_entregas: true,
                ordenes_estado : data.estados.map(e => {
                    return {
                        nombre_estado: e.descripcion,
                        estado_color: e.estado_color_rotulo,
                        cantidad: e.cantidad
                    }
                }),
                series : data.estados.map(e => {
                    return {
                        rotulo: e.descripcion,
                        cantidad: e.cantidad,
                        color: e.estado_color
                    }
                })});
        };
        
        const fnFail = (xhr) => {
            console.error({error: xhr.responseJSON.mensaje})
            Util.alert(this.$, xhr.responseJSON.mensaje , "error");
        };
    
        const fnAlways = () => {
        };

        this.$.html(CADENAS.CARGANDO);
        $.post("../../controlador/nuevo.pedidos.php?op=leer_cabecera", postData)
            .done(fn)
            .fail(fnFail)
            .always(fnAlways);
    };

    const setDOM = () => {
        this.$pie = this.$.find("#pie-ordenesestado");

        if (this.isMounted){
            return;
        }

        this.isMounted = true;
        setEventos();
    };

    const setEventos = ()=>{
        this.$.on("click", "#btn-excel-entregas", (e)=>{
            e.preventDefault();
            this.generarExcelEntregas();
        });
    };
    
    const renderGrafico = (SERIES) => {
        var data = [];
        for( var i = 0; i< SERIES.length; i++){	
            var _serie = SERIES[i];
            data[i] = { label: _serie.rotulo, data: parseInt(_serie.cantidad), color: _serie.color};
        };
        
        $.plot(this.$pie, data,{
            series: {
                pie: {
                    show: true,
                    radius: 3/4,
                    label: {
                        show: true,
                        radius: 3/4,
                        formatter: function(label, series){
                            return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">('+Math.round(series.percent)+'%)</div>';
                        },
                        background: {
                            opacity: 0.5,
                            color: '#000'
                        }
                    },
                    innerRadius: 0.25
                }
            }
        });	
    
        this.$pie.find(".legend").hide();
    };
    
    this.generarExcelEntregas = () => {
    	window.open("../../controlador/reporte.nuevos.pedidos.leonisa.xls.php?p_id="+_ID,"_blank");
    };

    this.render = (data) => {
        this.$.html(this.template(data));
        setDOM();

        setTimeout(function(){
            renderGrafico(data.series);	
        },330);
    };

    return init(initData);
};