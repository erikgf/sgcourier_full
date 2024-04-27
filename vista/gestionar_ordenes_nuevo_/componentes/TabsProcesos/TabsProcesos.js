const TabsProcesos = function (initData){
    const app = initData.app;
    this.$ = null;
    this.isMounted = false;
    this.template = null;

    this.id = null;

    const TABS = [
        { tabId: "tabFaseUno", active: "active", tabName: "Pedidos en Ruta / Zona de Reparto", tab: TabDespacharRuta, objTab: null},
        { tabId: "tabFaseDos", active: "", tabName: "Asignación de Repartidores / Zona Reenvío", tab : TabAsignarRepartidores, objTab: null},
        { tabId: "tabFaseTres", active: "", tabName: "Monitoreo de Entregados / Motivados", tab : TabMonitoreoRepartidores, objTab: null},
    ];

    this.compileTemplate = () => {
        const selectHTML =  `   {{#datos}}
                                    <option value="" selected>{{rotulo}}</option>
                                    {{#registros}}
                                    <option value="{{id}}">{{descripcion}}</option>
                                    {{/registros}}
                                {{/datos}}
                            `;

        Handlebars.registerPartial('selectPartial', selectHTML);

        const html = `  <div class="card">
                            <ul class="nav nav-tabs" id="tabMain" role="tablist">
                                {{#tabs}}
                                    <li class="nav-item">
                                        <a href="#{{tabId}}" class="nav-link {{active}}" data-bs-toggle="tab" role="tab" data-id="{{@index}}" >
                                            <span class="hidden-sm-up"></span>
                                            <span class="hidden-xs-down">{{tabName}}</span>
                                        </a>
                                    </li>
                                {{/tabs}}
                            </ul>
                            <div class="tab-content tabcontent-border">
                                {{#tabs}}
                                    <div class="tab-pane fade show {{active}}" id="{{tabId}}" role="tabpanel">
                                        <div class="p-20">{{tabName}}</div>
                                    </div>
                                {{/tabs}}
                            </div>
                        </div>`;

        return Handlebars.compile(html);
    };

    const init = ({$id, data}) => {
        this.template = this.compileTemplate();
        this.$ = $($id);
        this.id = data?.id;
        this.render({tabs: TABS});

        return this;
    };

    const setDOM = () => {
        if (this.isMounted){
            return;
        }
        
        this.isMounted = true;
        setEventos();
    };

    const setEventos  = ()=>{
        this.$.find("a[role=tab]").toArray().forEach(function (triggerEl, index) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            if (triggerEl.classList.contains("active")){
                initTab(triggerEl.dataset.id);
            }

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
                initTab(index);
            });
        });
    };

    const initTab = (index) => {
        const Tab =  TABS[index];
        if (Tab.objTab === null){
            TABS[index].objTab = new Tab.tab({$id: `#${Tab.tabId}`, data : {id: this.id}, app, tabs: TABS});
        }
    };

    this.render = (data) => {
        this.$.html(this.template(data));
        setDOM();
    };

    return init(initData);
};