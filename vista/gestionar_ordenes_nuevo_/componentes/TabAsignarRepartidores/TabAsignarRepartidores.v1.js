const TabAsignarRepartidores = function (initData){
    const { objCabeceraPedido } = initData?.app;
    this.$ = null;
    this.isMounted = false;
    this.template = null;

    this.id = null;
    this._data = null;

    this.objFiltroPorUbigeoService = null;

    this.compileTemplate = () => {
        const ordenesHTML = `{{#registros}}
                                <tr data-indice="{{@index}}">
                                    <th class="text-center text-white bg-{{estado_color_rotulo}}" scope="row">
                                        {{#if_ numero_visitas '>' 0}}
                                        <span class="badge badge-pill badge-dark" title="Número de Visitas">{{numero_visitas}}</span> 
                                        {{/if_}}
                                        {{codigo_guia}}
                                    </th>
                                    <td class="text-center">{{numero_documento_destinatario}}</td>
                                    <td>{{destinatario}}</td>
                                    <td>{{direccion}}</td>
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

        Handlebars.registerPartial('ordenesAsignarPartial', ordenesHTML);

        const mainHTML = `  <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4 class="subtitle">Asignación de Colaboradores a Entregas en Zona de Reparto</h4>
                                            <div class="row">
                                                <div class="col-sm-12 col-md-7">
                                                    <div class="form-group m-t-10">
                                                        <label for="txtcodigoremitobuscarmasivo">Código Remito(s)/Guías</label>
                                                        <div class="input-group">
                                                            <textarea id="txtcodigoremitobuscarmasivo" rows="4" class="form-control" placeholder="Ingresar código..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-5">
                                                    <div class="form-group m-t-10">
                                                        <label for="txtcolaborador">Colaborador Por Asignar</label>
                                                        <select class="control-form" id="txtcolaborador" name="txtcolaborador" style="width: 100%; height:36px;">
                                                            {{> selectPartial datos=colaboradores}}
                                                        </select>
                                                        <div id="txtcolaborador-error"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group text-left col-xs-12 col-md-6">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input form-check-input-provincia" id="chk-reenvio">
                                                                <label class="form-check-label w-100 mb-0 todo-label" for="chk-reenvio">
                                                                    <span class="todo-desc">
                                                                       MARCAR COMO <span class="label rounded-pill badge-cyan">REENVÍO</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group text-right col-xs-12 col-md-6">
                                                            <button type="button" class="btn btn-info" id="btnasignar" title="Permite asignar a un colaborador a la lista de órdenes colocadas.">
                                                                <i class="fa fa-user-plus"></i> ASIGNAR COLABORADOR
                                                            </button>
                                                            <button type="button" class="btn btn-cyan hide" id="btnreenvio" title="Permite marcar como reenvío a la lista de órdenes colocadas.">
                                                                <i class="fa fa-share"></i> ASIGNAR REENVIO
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="blk-alert"></div>
                                    <div id="blk-multialert"></div>
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
                                        <!--
                                        <div class="col-md-2 col-sm-12">
                                            <br>
                                            <button type="button" class="btn btn-success" id="btnactualizar" title="Volver a listar los registros, en caso se haya registrado varias asignaciones rápidas.">
                                                <i class="fa fa-recycle"></i> VOLVER A LISTAR
                                            </button>
                                        </div>
                                        -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                          <div class="table-responsive">
                                              <table id="tbllistado" class="table table-sm table-condensed">
                                                    <thead >
                                                        <tr>
                                                        <td scope="col">N. Guía <small style="cursor:pointer" title="Copiar Códigos de Toda la columna" class="text-cyan btn-copiarcolumnas">Copiar <i class="fa fa-copy"></i> </small> </td>
                                                        <td scope="col">ID/Documento </td>
                                                        <td scope="col">Destinatario</td>
                                                        <td scope="col">Dirección</td>
                                                        <td scope="col">Ciudad/Distrito</td>
                                                        <td scope="col">Provincia</td>
                                                        <td scope="col">Departamento</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbllistadotbody" class="small">
                                                        {{> ordenesAsignarPartial registros=ordenes}}                                                   
                                                    </tbody>
                                                    <tfooter>
                                                        <tr>
                                                            <td>Registros: <span id="lblcantidadregistros">0</span></td>
                                                        </tr>
                                                    </tfooter>
                                              </table>
                                         </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

        return Handlebars.compile(mainHTML);
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
        });

        this.obtenerColaboradores();
        this.obtenerOrdenes({id : this.id});

        return this;
    };

    const setDOM = () => {
        [this.$cboColaborador, this.$cboColaboradorError, this.$chkReenvio] = this.$.find("#txtcolaborador, #txtcolaborador-error, #chk-reenvio");
        [this.$cboDepartamento, this.$cboProvincia, this.$cboDistrito] = this.$.find("#txtdepartamento, #txtprovincia, #txtdistrito");
        [this.$tblListadoBody] = this.$.find("#tbllistadotbody");
        [this.$btnCopiarColumnas] = this.$.find(".btn-copiarcolumnas");
        [this.$btnAsignar, this.$btnReenvio] = this.$.find("#btnasignar, #btnreenvio");

        if (this.isMounted){
            return;
        }

        this.isMounted = true;
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
        
        this.$.on("change", "#txtcodigoremitobuscarmasivo", (event) => {
            const { target } = event;
            const itemsText = target.value;
            const arregloItemText = itemsText.split("\n");
            
            
            target.value = arregloItemText.map( item => {
                if (item.length > 10){
                    return item.substr(2, item.length);
                }
                return item;
            }).join("\n");
        });

        this.$.on("click", ".btn-copiarcolumnas", (event) => {
            event.preventDefault();
            if (this._data.ordenes.length <= 0){
                return;
            }
            const [$txtCodigoMasivo] = this.$.find("#txtcodigoremitobuscarmasivo");
            $txtCodigoMasivo.focus();
            $txtCodigoMasivo.value = this._data.ordenes.map(orden => orden.codigo_guia).join("\n");
        });

        this.$.on("click", "#btnasignar", (event) => {
            event.preventDefault();
            this.asignarMasivo({id: this.id});
        });

        this.$.on("click", "#btnreenvio", (event) => {
            event.preventDefault();
            this.asignarMasivo({id: this.id, esReenvio: '1'});
        });

        this.$.on("change", "#chk-reenvio", (event)=>{
            const checkeado = event.currentTarget.checked;
            this.$btnAsignar.disabled = checkeado;
            this.$cboColaborador.disabled = checkeado;
            this.$cboColaboradorError.innerHTML = "";
            this.$btnAsignar.classList[checkeado ? "add" : "remove"]("hide");
            this.$btnReenvio.classList[checkeado ? "remove" : "add"]("hide");
        });
    };

    this.obtenerColaboradores = async () => {
        this.$cboColaborador.innerHTML = `<option selected value="">Cargando...</option>`;
        this.$cboColaborador.disabled = true;

        try{
            const {datos} = await $.when($.post("../../controlador/colaboradores.php?op=obtener_repartidores"));
            if (datos){
                const colaboradores = {datos: {rotulo: "Seleccionar colaborador", registros: datos}};

                this.$cboColaborador.innerHTML = Handlebars.partials.selectPartial(colaboradores);
                $(this.$cboColaborador).select2();

                this._data = {...this._data, colaboradores};
            }
        } catch (e) {
            console.error(e);
            Util.alert($(this.$cboColaboradorError),  e.responseJSON?.mensaje || e.responseText  , "danger");
        } finally {
            this.$cboColaborador.disabled = false;
        }
    };

    this.obtenerOrdenes = async ({id}) => {
        this.$tblListadoBody.innerHTML = `${CADENAS.CARGANDO} Cargando...`;
        try{
            const {datos} = await $.when($.post("../../controlador/nuevo.pedidos.php?op=leer_ordenes_nivel_dos", { p_id : id }));
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
                    }
                };

                this.$cboDepartamento.innerHTML = Handlebars.partials.selectPartial({datos: this._data.departamentos});
                renderOrdenes([]);
            }
        } catch (e) {
            console.error(e);
            Util.alert($( this.$tblListadoBody).$,  e.responseJSON?.mensaje || e.responseText  , "danger");
        }
    };
    
    this.asignarMasivo = async function({id, esReenvio = '0'}){
        const $btnProcesarAccion = esReenvio == '0' ? this.$btnAsignar : this.$btnReenvio;
        if ($btnProcesarAccion.disabled){
            return;
        }

        const [$txtCodigoMasivo, $blkAlert, $blkMultiAlert] = this.$.find("#txtcodigoremitobuscarmasivo, #blk-alert, #blk-multialert");
        const registrosAsignar = $txtCodigoMasivo.value.trim().replace(/\r\n|\r|\n/g," ").split(" ");
        const idColaboradorAsignar = this.$cboColaborador.value;
        
        if (esReenvio === '0' && idColaboradorAsignar == ""){
            Util.alert($blkAlert,"Seleccionar colaborador a asignar", "danger");
            return;   
        }
        
        const cantidadRegistrosAsignar = registrosAsignar.length;

        if (!cantidadRegistrosAsignar || registrosAsignar[0] === ""){
            Util.alert($blkAlert,"No se ha encontrado CODIGOS válidos en el formulario.", "danger");
            return;
        }
    
        const $localAlert = $(`<div id="blk-alert">
                                    <div class="alert alert-warning" role="alert">Asignando: ${cantidadRegistrosAsignar} registros...</div>
                                </div>`);
        $blkMultiAlert.append($localAlert);

        $btnProcesarAccion.disabled = true;

        try{
            const postData = {
                p_idpedido : id,
                p_idcolaborador : idColaboradorAsignar,
                p_reenvio: esReenvio,
                p_pedidoordenes : JSON.stringify(registrosAsignar)
            };
            const {datos} = await $.when($.post("../../controlador/nuevo.pedidos_ordenes.php?op=asignar_masivo_codigo", postData));
            if (datos){
                const { msj, estado } = datos;

                this._data.registrosTotales = this._data.registrosTotales.map(registro => {
                    const index = registrosAsignar.findIndex(registroAsignado => {
                        return registroAsignado === registro.codigo_guia;
                    });

                    if (index < 0){
                        return registro;
                    }

                    const reemplazar = registro.codigo_guia === registrosAsignar[index];
                    if (reemplazar){
                        registrosAsignar.splice(index, 1);
                        return {
                            ...registro,
                            estado_actual : estado.estado_actual,
                            estado_color : estado.estado_color,
                            estado_color_rotulo : estado.estado_color_rotulo,
                        }
                    }

                    return registro;
                });

                renderOrdenes(filtrarOrdenes(this.$cboDepartamento.value, this.$cboProvincia.value, this.$cboDistrito.value));
    
                $blkMultiAlert.innerHTML = `<div class="alert alert-success" role="alert">${msj}</div>`;
                setTimeout(function(){
                    $blkMultiAlert.innerHTML = "";
                }, 5000);
        
                $txtCodigoMasivo.value = "";
                objCabeceraPedido.obtenerDatos({id: this.id});
            }
        } catch (e) {
            console.error(e);
            $blkMultiAlert.innerHTML = `<div class="alert alert-danger" role="alert">${e.responseJSON?.mensaje || e.responseText}</div>`;
            setTimeout(function(){
                $blkMultiAlert.innerHTML = "";
            }, 5000);
        } finally {
            $btnProcesarAccion.disabled = false;
        }    
    };

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

    const renderOrdenes = (ordenesSeleccionadas) => {
        this._data.ordenes = ordenesSeleccionadas;
        this.$tblListadoBody.innerHTML = Handlebars.partials.ordenesAsignarPartial({registros: ordenesSeleccionadas});
        this.$.find("#lblcantidadregistros").html(ordenesSeleccionadas.length);
    };

    this.render = (data) => {
        this.$.html(this.template(data));
        this._data = data;
        setDOM();
    };

    return init(initData);
};