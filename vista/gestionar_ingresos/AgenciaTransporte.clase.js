var AgenciaTransporte =  function(){
    let $mdl, $btnGuardar;
    let $txtIdAgenciaTransporte, $txtNombre, $txtCelular;

    this.init = function(){
        this.setDOM();
        this.setEventos();
    };

    this.setDOM = function(){
        $mdl = $("#mdl-registroagenciatransporte");    
        $txtIdAgenciaTransporte = $mdl.find("#txt-idagenciatransporte");
        $txtNombre = $mdl.find("#txt-agenciatransportenombre");
        $txtCelular = $mdl.find("#txt-agenciatransportecelular");

        $btnGuardar = $mdl.find("#btn-agenciatransporteguardar");
    };

    this.setEventos = function(){
        $mdl.on("hide.bs.modal", (e)=>{
            this.limpiar();
        });

        $mdl.on("show.bs.modal", (e)=>{
            setTimeout(function(){
                $txtNombre.focus();
            },600);
        });

        $mdl.on("submit", "form", (e)=>{
            e.preventDefault();
            this.guardar();
        }); 
    };

    this.limpiar= function(){
        $mdl.find("form")[0].reset();
    };

    this.guardar = function(){
        $btnGuardar.prop("disabled", true);
        $.post("../../controlador/sis_agenciatransporte.php?op=registrar", {
                    p_id_agenciatransporte : $txtIdAgenciaTransporte.val(),
                    p_nombre : $txtNombre.val(),
                    p_celular : $txtCelular.val()

              })
              .done(function(xhr){
                var datos = xhr.datos;

                $btnGuardar.prop("disabled", false);
                $mdl.modal("hide");
                if (xhr.estado == 200){
                    let registroNuevo = datos.registro;

                    if (objBuscarComponente){
                        let esEditando = $txtIdAgenciaTransporte.val() != "";
                        console.log(registroNuevo);
                        objBuscarComponente.postGuardar(registroNuevo, esEditando);
                    }
                }
              })
              .fail(function(e){
                console.error(e);
                $btnGuardar.prop("disabled", false);
              });
    };

    this.editar = function($btn, agenciatransporte){
        let id = agenciatransporte.id;
        $mdl.find(".modal-title").html("Editando AgenciaTransporte "+agenciatransporte.descripcion);

        $btn.prop("disabled", true);
        $.post("../../controlador/sis_agenciatransporte.php?op=leer", {
                    p_id_agenciatransporte : id
              })
              .done(function(xhr){
                $btn.prop("disabled", false);
                if (xhr.estado != 200){
                    return;
                }
                let data = xhr.datos;

                $txtIdAgenciaTransporte.val(data.id_agenciatransporte);
                $txtNombre.val(data.nombre);
                $txtCelular.val(data.celular);

                $mdl.modal("show");
              })
              .fail(function(e){
                $btn.prop("disabled", false);
              });
    };

    this.anular = function($btn, repartidor){
        if (!confirm("¿Está seguro que desea anular este registro?")){
            return;
        }

        let id = repartidor.id;
        $btn.prop("disabled", true);
        $.post("../../controlador/sis_agenciatransporte.php?op=anular", {
                    p_id_agenciatransporte : id
              })
              .done(function(xhr){
                $btn.prop("disabled", false);
                if (xhr.estado != 200){
                    return;
                }

                if (objBuscarComponente){
                    objBuscarComponente.postAnular({id: id});
                }

                let data = xhr.datos;
                alert(data.msj);
              })
              .fail(function(e){
                $btn.prop("disabled", false);
              });
    };

    this.nuevo = function($btn){
        $mdl.modal("show");
        $txtIdAgenciaTransporte.val("");
    };

    return this.init();
};