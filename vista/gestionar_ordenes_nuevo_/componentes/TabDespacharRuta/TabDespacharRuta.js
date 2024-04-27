const TabDespacharRuta = function (initData){
    const { objCabeceraPedido } = initData?.app;
    const parentTabs = initData?.tabs;
    this.$ = null;
    this.isMounted = false;
    this.template = null;

    this.id = null;
    this._data = null;

    this.$button = null;
    this.$estadoCambiar = null;

    this._ESTADOS = [];

    this.compileTemplate = () => {
        const html = `  <div class="card">
                            <div class="card-body">
                                <div class="blk-alert"></div>
                                <h4 class="card-title">
                                    Listado Por Departamento
                                    <div class="absolute-end small">
                                        <label for="cbo-estado">Cambiar Seleccionados: </label>
                                        <select id="cbo-estado">
                                            {{#estados}}
                                                <option value="{{id}}">{{descripcion}}</option>
                                            {{/estados}}
                                        </select>
                                        <button class="btn btn-sm btn-primary btn-guardar" disabled>GUARDAR</button>
                                    </div>
                                </h4>
                                <div class="todo-widget scrollable pl-5 pr-5">
                                    <ul class="todo-list list-group mb-0">
                                    {{#registros}}
                                        <li class="list-group-item todo-item">
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input form-check-input-departamento"
                                                    id="customCheck_{{@index}}"
                                                    value = "{{descripcion}}"
                                                    />
                                                <label
                                                    class="form-check-label w-100 mb-0 todo-label"
                                                    for="customCheck_{{@index}}">
                                                    <span class="todo-desc">
                                                        {{descripcion}} ({{cantidad}})
                                                    </span>
                                                </label>
                                            </div>
                                            <ul class="todo-list list-group mb-0"  style="padding-left:48px;font-size:.9em">
                                            {{#subregistros}}
                                                <li class="list-group-item todo-item">
                                                    <div class="form-check">
                                                        <input
                                                            type="checkbox"
                                                            class="form-check-input form-check-input-provincia"
                                                            id="{{../descripcion}}_{{descripcion}}"
                                                            value = "{{descripcion}}"
                                                            />
                                                        <label
                                                            class="form-check-label w-100 mb-0 todo-label"
                                                            for="customCheck_{{@index}}">
                                                            <span class="todo-desc">
                                                                {{descripcion}} ({{cantidad}})
                                                            </span>
                                                            <span class="label rounded-pill badge-{{estado_color}} float-end">
                                                                {{estado}}
                                                            </span
                                                            >
                                                        </label>
                                                    </div>
                                                </li>
                                            {{/subregistros}}
                                            </ul>
                                        </li>
                                    {{/registros}}
                                    </ul>
                                </div>
                            </div>
                        </div>`;

        return Handlebars.compile(html);
    };

    const init = ({$id, data}) => {
        this.template = this.compileTemplate();
        this.$ = $($id);
        this.id = data?.id;

        this.obtenerEstados();
        this.obtenerDatos({id : this.id});

        return this;
    };

    const setDOM = () => {
        [this.$button] = this.$.find(".btn-guardar");
        [this.$estadoCambiar] = this.$.find("#cbo-estado");

        if (this.isMounted){
            return;
        }

        this.isMounted = true;
        setEventos();
    };

    const setEventos  = ()=>{
        
        this.$.on("change","input.form-check-input", (e) => {
            const cantidadInputsChecked = this.$.find(".form-check-input:checked").length;
            this.$button.disabled = cantidadInputsChecked <= 0;
        });

        this.$.on("change","input.form-check-input-departamento", (e) => {
            const $target = $(e.currentTarget);
            const isChecked = $target.prop("checked");

            $target.parent().parent().find(".form-check-input-provincia").prop("checked", isChecked);
        });

        this.$.on("click", "button.btn-guardar",  (e) => {
            e.preventDefault();

            const provincias = this.$.find(".form-check-input-provincia:checked").toArray().map(input => input.id);
            const estado =  this.$estadoCambiar.value;

            if (provincias.length <= 0){
                alert("No se está enviando provincias.");
                return;
            }

            if ( typeof  estado !== 'string'){
                alert("Estado a cambiar no válido.")
                return;
            }

            this.guardarDatos({
                p_idpedido: this.id,
                p_provincias : JSON.stringify(provincias),
                p_estado: estado
            });
        });

    };

    const htmlMensajeOKGuardado = (data) => {
        let $html = `<p>Cambios realizados! </p>`;
        data.forEach((o, i) => {
            $html += `${o.keyDep} - ${o.keyProv}: ${o.cantidad_actualizados}/${o.cantidad} registros correctos.  </br>`;
        })

        return $html;
    };

    this.obtenerEstados = async () => {
        this.$.html(CADENAS.CARGANDO);

        try{
            const {datos} = await $.when($.post("../../controlador/estado.orden.php?op=obtener"));
            if (datos){
                this._ESTADOS = datos.filter(estado => {
                                    return estado.numero_orden <= 2
                                }).map(estado => {
                                    return {
                                        id: estado.id_estado_orden,
                                        descripcion: estado.descripcion
                                    }
                                });
            }
        } catch (e) {
            console.error(e);
            Util.alert(this.$, e.responseJSON?.mensaje || e.responseText  , "danger");
        }
    };

    this.obtenerDatos = async ({id}) => {
        this.$.html(CADENAS.CARGANDO);

        try{
            const {datos} = await $.when($.post("../../controlador/nuevo.pedidos.php?op=leer_ordenes_nivel_uno", { p_id : id }));
            if (datos){
                this.render({registros: datos});
            }
        } catch (e) {
            console.error(e);
            Util.alert(this.$, e.responseJSON?.mensaje || e.responseText  , "danger");
        }
    };

    this.guardarDatos = async ({p_idpedido, p_provincias, p_estado}) => {
        if (this.$button.disabled){
            return;
        }

        const buttonHTML = this.$button.innerHTML;
        this.$button.disabled = true;
        this.$button.innerHTML = CADENAS.CARGANDO;

        try{
            const postData = {
                p_idpedido,
                p_provincias,
                p_estado
            };
            const {datos} = await $.when($.post("../../controlador/nuevo.pedidos_ordenes.php?op=asignar_estados_x_provincia", postData));
            if (datos){
                const { registros, estado } = datos;
                const nuevosRegistros = this._data.registros.map(registro => {
                    const deboActualizar = registros.findIndex(d => {
                        return d.keyDep === registro.descripcion
                    }) >= 0;

                    if (deboActualizar){
                        const oldSubRegistros = [...registro.subregistros];
                        const subRegistros = oldSubRegistros.map(subRegistro => {
                            const deboActualizar = registros.findIndex(d => {
                                return d.keyProv === subRegistro.descripcion
                            }) >= 0;

                            if (deboActualizar){
                                return {
                                    ...subRegistro, 
                                    estado_actual: estado.id_estado_orden,
                                    estado: estado.descripcion,
                                    estado_color: estado.estado_color
                                };
                            }

                            return subRegistro;
                        });

                        return {...registro, subregistros: subRegistros};
                    }

                    return registro;
                })

                this.render({registros: nuevosRegistros});
                Util.alert(this.$.find(".blk-alert"), htmlMensajeOKGuardado(registros), "success", 10);

                objCabeceraPedido.obtenerDatos({id: this.id});

                //Accedo a la tab posicion [1] y actualizo.
                if (parentTabs[1]?.objTab){
                    parentTabs[1].objTab.obtenerOrdenes({id: this.id});
                }
            }
        } catch (e) {
            console.error(e);
            this.$button.disabled = false;
            Util.alert(this.$.find(".blk-alert"), e.responseJSON?.mensaje || e.responseText , "danger");
        } finally {
            this.$button.innerHTML = buttonHTML;
        }
    };

    this.render = (data) => {
        this.$.html(this.template({...data, estados: this._ESTADOS}));
        this._data = data;
        setDOM();
    };

    return init(initData);
};