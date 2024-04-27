const TabMonitoreoRepartidores = function (initData){
    const { objCabeceraPedido } = initData?.app;
    this.$ = null;
    this.isMounted = false;
    this.template = null;

    this.id = null;
    this._data = null;
    this.objFiltroPorUbigeoService = null;

    this.compileTemplate = () => {
        const ordenesHTML = `{{#registros}}
                                <tr data-indice="{{id_pedido_orden}}">
                                    <th class="text-center text-white bg-{{estado_color_rotulo}}" scope="row">
                                        <button class="btn btn-xs btn-default btn-verdetalle" title="Ver Detalle" data-id={{id_pedido_orden}}>
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <span class="badge badge-pill badge-dark" title="Número de Visitas">{{numero_visitas}}</span> 
                                        {{codigo_guia}}
                                    </th>
                                    <td>{{repartidor_asignado}}</td>
                                    <td>{{numero_documento_destinatario}} - {{destinatario}}</td>
                                    <td>{{distrito}}</td>
                                    <td>{{provincia}}</td>
                                    <td>{{departamento}}</td>
                                </tr>
                             {{/registros}}
                             {{^registros}}
                                <tr>
                                    <td colspan="100">
                                        <div class="font-20 text-center">
                                            No hay registros que mostrar <i class="fa fa-file-code"></i>
                                        </div>
                                    </td>
                                </tr>
                             {{/registros}}
                             `;

        Handlebars.registerPartial('ordenesMonitoreoPartial', ordenesHTML);

        const resumenOrdenesHTML = `<tr>
                                        <td colspan="99"><b>Registros Totales:</b> {{registrosTotales}} | <b>Repartidores:</b> {{registrosRepartidor}} |  <b>Entregados:</b> {{registrosEntregados}} | <b>Motivados:</b> {{registrosMotivados}}</td>
                                    </tr>
                                    `;

        Handlebars.registerPartial('resumenOrdenesMonitoreoPartial', resumenOrdenesHTML);

        const mainHTML = `  <div class="card">
                                <div class="card-body">
                                    <div id="blk-alert"></div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group m-t-10">
                                                <label for="txtdepartamento">Filtrar Por Departamento</label>
                                                <select class="select2 form-control"  id="txtdepartamento" name="txtdepartamento" style="width: 100%; height:36px;">
                                                    {{> selectPartial datos=departamentos}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group m-t-10">
                                                <label for="txtprovincia">Filtrar Por Provincia</label>
                                                <select class="select2 form-control"  id="txtprovincia" name="txtprovincia" style="width: 100%; height:36px;">
                                                    {{> selectPartial datos=provincias}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group m-t-10">
                                                <label for="txtdistrito">Filtrar Por Ciudad / Distrito</label>
                                                <select class="select2 form-control"  id="txtdistrito" name="txtdistrito" style="width: 100%; height:36px;">
                                                    {{> selectPartial datos=distritos}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <button type="button" class="btn btn-success" id="btn-refrescar">
                                                <i class="fa fa-recycle"></i> REFRESCAR
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                          <div class="table-responsive">
                                              <table id="tbllistado" class="table table-sm table-condensed">
                                                    <thead >
                                                        <tr>
                                                            <td scope="col" style="width: 140px;">N. Guía</td>
                                                            <td scope="col">Repartidor</td>
                                                            <td scope="col">Destinatario</td>
                                                            <td scope="col">Ciudad/Distrito</td>
                                                            <td scope="col">Provincia</td>
                                                            <td scope="col">Departamento</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbllistadotbody" class="small">
                                                        {{> ordenesMonitoreoPartial registros=ordenes}}                                                   
                                                    </tbody>
                                                    <tfoot id="tbllistadotfooter">
                                                        {{> resumenOrdenesMonitoreoPartial resumenOrdenes}}
                                                    </tfoot>
                                              </table>
                                         </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

        const verDetalleModalHTML = `<div class="modal-dialog modal-lg" role="document">
                                        <form class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Viendo Pedido / Órden Código: {{codigo_guia}}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true ">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <div class="form-group m-t-10">
                                                            <label>Repartidor</label>
                                                            <p>{{repartidor_asignado}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group m-t-10">
                                                            <label>Fecha Hora Asignado</label>
                                                            <p>{{fecha_hora_asignado}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div ckass="p-1" style="border-top:1px solid; color: lightgray"></div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group m-t-10">
                                                            <label for="txtnumerodocumento">Núm. Documento</label>
                                                            <p id="txtnumerodocumento">{{numero_documento_destinatario}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-7">
                                                        <div class="form-group m-t-10">
                                                            <label for="txtdestinatario">Destinatario</label>
                                                            <p id="txtdestinatario">{{destinatario}}</p>
                                                        </div>
                                                    </div>
                                                        <div class="col-sm-2">
                                                        <div class="form-group m-t-10">
                                                            <label for="txtestado">Estado</label>
                                                            <span id="txtestado" class="badge badge-{{estado_color_rotulo}}">{{estado}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group m-t-10">
                                                            <label>Dirección</label>
                                                            <p>{{direccion}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group m-t-10">
                                                            <label>Barrio / Urbanización</label>
                                                            <p>{{referencia}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group m-t-10">
                                                            <label>Ciudad / Distrito</label>
                                                            <p>{{distrito}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group m-t-10">
                                                            <label>Provincia</label>
                                                            <p>{{provincia}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group m-t-10">
                                                            <label>Departamento</label>
                                                            <p>{{departamento}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group m-t-10">
                                                            <label for="txttelefono">Celular / Teléfono</label>
                                                            <p id="txttelefono">{{celular}} / {{telefono}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <h4>Visitas</h4>
                                                        <div class="table-responsive">
                                                            <small>
                                                                <table lass="table table-condensed">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th scope="col" style="width:75px" class="text-center">N°</th>
                                                                            <th scope="col" style="width:125px" class="text-center">Fecha atención</th>
                                                                            <th scope="col" style="width:200px">Receptor</th>
                                                                            <th scope="col">Motivaciones</th>
                                                                            <th scope="col">Observaciones</th>
                                                                            <th scope="col" style="width:75px" class="text-center">Fotos</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tbdvisitas">
                                                                        {{#visitas}}
                                                                            <tr>
                                                                                <th scope="row" class="text-center">{{numero_visita}}</th>
                                                                                <td class="text-center">{{fecha}}<br>{{hora}}</td>
                                                                                <td>{{numero_documento_receptor}} - {{nombres_receptor}}</td>
                                                                                <td>{{motivaciones}}</td>
                                                                                <td>{{observaciones}}</td>
                                                                                <td class="text-center">
                                                                                        <button data-urls="{{urls}}" 
                                                                                            {{#if_ urls '==' ''}}disabled{{/if_}}
                                                                                            type="button" 
                                                                                            class="btn btn-sm btn-primary btn-verfotos" 
                                                                                            title="Ver Fotos">
                                                                                            <i class="fa fa-image"></i>
                                                                                        </button>
                                                                                </td>
                                                                            </tr>
                                                                        {{/visitas}}
                                                                        {{^visitas}}
                                                                            <tr><td  class='text-center' scope='row' colspan='6'>No hay visitas aún.</td></tr>
                                                                        {{/visitas}}
                                                                    </tbody>
                                                                </table>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                                            </div>
                                        </form>
                                    </div>`;

        const verFotosModalHTML = `<div class="modal-dialog modal-md" role="document">
                                        <form class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Viendo Fotos</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true ">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="carruselFotos" class="carousel slide" data-ride="carousel">
                                                            <ol class="carousel-indicators">
                                                                {{#.}}
                                                                    <li data-target="#carruselFotos" data-slide-to="{{@index}}" {{active}}></li>
                                                                {{/.}}
                                                            </ol>
                                                            <div class="carousel-inner">
                                                                {{#.}}
                                                                    <div class="carousel-item {{active}}">
                                                                       <img class="d-block w-100" src="../../img/imagenes_visitas_nuevo/{{url}}" alt="Foto {{@index}}">
                                                                    </div>
                                                                {{/.}}
                                                            </div>
                                                            <a class="carousel-control-prev" href="#carruselFotos" role="button" data-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Previo</span>
                                                            </a>
                                                            <a class="carousel-control-next" href="#carruselFotos" role="button" data-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Siguiente</span>
                                                            </a>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                                            </div>
                                        </form>
                                    </div>`;
                                
        return {
            main: Handlebars.compile(mainHTML),
            verDetalleModal: Handlebars.compile(verDetalleModalHTML),
            verFotosModal : Handlebars.compile(verFotosModalHTML)
        };
    };

    const init = ({$id, data}) => {
        this.template = this.compileTemplate();
        this.$ = $($id);
        this.id = data?.id;
        this.objFiltroPorUbigeoService = new FiltroPorUbigeoService();

        this.render({
            ordenes : [],
            registrosTotales : [],
            distritos: { rotulo : "Seleccionar distrito", registros: []},
            provincias: { rotulo : "Seleccionar provincia", registros: []},
            departamentos: { rotulo : "Seleccionar departamento", registros: []},
            resumenOrdenes: {
                registrosTotales: 0,
                registrosRepartidor: 0,
                registrosEntregados: 0,
                registrosMotivados: 0
            }
        });

        this.obtenerOrdenes({id : this.id});

        return this;
    };

    const setDOM = () => {
        [this.$blkAlert] = this.$.find("#blk-alert");
        [this.$cboDepartamento, this.$cboProvincia, this.$cboDistrito] = this.$.find("#txtdepartamento, #txtprovincia, #txtdistrito");
        [this.$tblListadoBody, this.$tblListadoFooter] = this.$.find("#tbllistadotbody, #tbllistadotfooter");
        [this.$btnRefrescar] = this.$.find("#btn-refrescar");

        if (this.isMounted){
            return;
        }

        this.isMounted = true;

        this.$$mdlVerdetalle = $("#mdlverdetalle");
        this.$$mdlFotos = $("#mdlfotos");

        setEventos();
    };

    const setEventos  = ()=>{
        this.$.on("change", "#txtdepartamento", (event) => {
            const { target } = event;
            const departamento = target.value;
            filtrarProvincias(departamento);
            this.$cboProvincia.innerHTML = Handlebars.partials.selectPartial({datos: this._data.provincias});
            this.$cboDistrito.innerHTML = Handlebars.partials.selectPartial({datos: this._data.distritos});
            renderOrdenes(filtrarOrdenes(departamento));
        });

        this.$.on("change", "#txtprovincia", (event) => {
            const { target } = event;
            const departamento = this.$cboDepartamento.value;
            const provincia = target.value;
            filtarDistritos(departamento, provincia);
            this.$cboDistrito.innerHTML = Handlebars.partials.selectPartial({datos: this._data.distritos});
            renderOrdenes(filtrarOrdenes(departamento,provincia));
        });

        this.$.on("change", "#txtdistrito", (event) => {
            const { target } = event;
            const departamento = this.$cboDepartamento.value;
            const provincia = this.$cboProvincia.value;
            const distrito = target.value;
            renderOrdenes(filtrarOrdenes(departamento,provincia, distrito));
        });

        this.$.on("click", ".btn-verdetalle", (event) => {
            event.preventDefault();
            const { currentTarget } = event;
            this.obtenerDetalle(currentTarget);
        });

        this.$.on("click", "#btn-refrescar", (event) => {
            event.preventDefault();
            this.obtenerOrdenes({id: this.id});
            objCabeceraPedido.obtenerDatos({id: this.id});
        });


        this.$$mdlVerdetalle.on("click", ".btn-verfotos", (event) => {
            event.preventDefault();
            const { currentTarget } = event;
            this.verFotos({urlsSinFormateo: currentTarget.dataset.urls});
        }); 

        this.$$mdlFotos.on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
        });

    };

    this.obtenerOrdenes = async ({id}) => {
        if (this.$btnRefrescar.disabled === true){
            return;
        }

        this.$btnRefrescar.disabled = true;
        this.$tblListadoBody.innerHTML = `${CADENAS.CARGANDO} Cargando...`;
        try{
            const {datos} = await $.when($.post("../../controlador/nuevo.pedidos.php?op=leer_ordenes_nivel_tres", { p_id : id }));
            if (datos){
                const { registros : registrosTotales, departamentos : departamentosTotales, provincias : provinciasTotales, distritos : distritosTotales} = datos;

                this._data = {
                    ...this._data,
                    registrosTotales,
                    departamentosTotales,
                    provinciasTotales,
                    distritosTotales,
                    departamentos : {
                        rotulo : "Seleccionar departamento",
                        registros: departamentosTotales.map( dep => {
                            return {
                                id: dep.departamento, 
                                descripcion: dep.departamento
                            };
                        })
                    },
                    distritos: { rotulo : "Seleccionar distrito", registros: []},
                    provincias: { rotulo : "Seleccionar provincia", registros: []},
                };

                this.$cboDepartamento.innerHTML = Handlebars.partials.selectPartial({datos: this._data.departamentos});
                this.$cboProvincia.innerHTML = Handlebars.partials.selectPartial({datos: this._data.provincias});
                this.$cboDistrito.innerHTML = Handlebars.partials.selectPartial({datos: this._data.distritos});
                renderOrdenes([]);
            }
        } catch (e) {
            console.error(e);
            Util.alert($( this.$tblListadoBody).$,  e.responseJSON?.mensaje || e.responseText  , "danger");
        } finally {
            this.$btnRefrescar.disabled = false;
        }
    };

    this.obtenerDetalle = async($btn) => {
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
                renderDetalle(datos);
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
        this.$$mdlFotos.html(this.template.verFotosModal(arregloUrl.map((url, i) => {
                                return { url, active : i === 0 ? 'active' : ''};
                            })));
        this.$$mdlFotos.find("#carruselFotos").carousel();        
        this.$$mdlFotos.modal("show");
    }
    
    const filtrarProvincias = (departamento = "") => {
        const {provinciasTotales} = this._data;
        const { distritos, provincias } = this.objFiltroPorUbigeoService.filtrarProvincias({
            departamento,
            provinciasTotales
        });
        
        this._data.distritos = {
            ...this._data.distritos,
            registros : distritos
        };

        this._data.provincias = {
            ...this._data.provincias,
            registros : provincias
        };
    };

    const filtarDistritos = (departamento = "", provincia = "") => {
        const {distritosTotales} = this._data;
        const { distritos } = this.objFiltroPorUbigeoService.filtarDistritos({
            distritosTotales, 
            departamento, 
            provincia
        });

        this._data.distritos = {
            ...this._data.distritos,
            registros : distritos
        };
    };

    const filtrarOrdenes = (departamento = "", provincia = "", distrito = "") => {
        const { registrosTotales } = this._data;
        return this.objFiltroPorUbigeoService.filtrarOrdenes({
            registrosTotales, 
            departamento,
            provincia,
            distrito
        });
    };

    const obtenerResumenOrdenes = (ordenes) => {
        const registrosRepartidor = ordenes.reduce((n, o) => o.estado_actual === 'R' ? n+1 : n, 0);
        const registrosEntregados = ordenes.reduce((n, o) => o.estado_actual === 'E' ? n+1 : n, 0);
        const registrosMotivados = ordenes.reduce((n, o) => o.estado_actual === 'M' ? n+1 : n, 0);
        const registrosTotales = registrosRepartidor + registrosEntregados + registrosMotivados;
        
        return {
            registrosTotales,
            registrosRepartidor,
            registrosEntregados,
            registrosMotivados
        };
    };

    const renderOrdenes = (ordenesSeleccionadas) => {
        this._data.ordenes = ordenesSeleccionadas;
        this._data.resumenOrdenes = obtenerResumenOrdenes(ordenesSeleccionadas);

        this.$tblListadoBody.innerHTML = Handlebars.partials.ordenesMonitoreoPartial({registros: ordenesSeleccionadas});
        this.$tblListadoFooter.innerHTML = Handlebars.partials.resumenOrdenesMonitoreoPartial(this._data.resumenOrdenes);
    };

    const renderDetalle = (data) => {
        this.$$mdlVerdetalle.html(this.template.verDetalleModal(data));
        this.$$mdlVerdetalle.modal("show");
    };

    this.render = (data) => {
        this.$.html(this.template.main(data));
        this._data = data;
        setDOM();
    };

    return init(initData);
};