const FiltrarInput = function (initData){
    this.$ = null;
    this.isMounted = false;
    this.template = null;
    this.$tbody = null;
    this.$rows = [];

    this.compileTemplate = () => {
        const html = `<div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon2">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" placeholder="Filtrar..."  style="border-color: #0755a3;">
                    </div>`;

        return Handlebars.compile(html);
    };

    const init = ({$id, data}) => {
        this.template = this.compileTemplate();
        this.$ = $($id);

        if (data){
            this.$tbody = $(data.$tbody);
        }

        this.render();

        return this;
    };

    const setDOM = () => {
        this.$input = this.$.find("input[type=text]");

        if (this.isMounted){
            return;
        }

        this.isMounted = true;
        setEventos();
    };

    const setEventos = ()=>{
        this.$input.on("change", e => {
            this.onBuscar(e.currentTarget.value);
        });

        this.$input.on("keypress", e => {
            if (e.keyCode == 13){
                this.onBuscar(e.currentTarget.value);
            }
        });

        this.$input.on("keyup", e => {
            this.onBuscar(e.currentTarget.value);
        });
    };

    this.onBuscar = (cadena) => {
        const cadenaFormeateada = cadena.replace(/ +/g, ' ').toLowerCase();
        this.$rows = this.$tbody.find("tr");
        this.$rows.show().filter(function(e) {
            const text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(cadenaFormeateada);
        }).hide();
    };

    this.render = () => {
        this.$.html(this.template());
        setDOM();
        setEventos();
    };

    return init(initData);
};