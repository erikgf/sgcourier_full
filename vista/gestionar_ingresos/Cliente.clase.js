var Cliente =  function(){
    let $mdl, $btnGuardar;
    let $txtIdCliente, $txtNombres, $txtCelular, $txtNumeroDocumento;

    this.init = function(){
        this.setDOM();
        this.setEventos();
    };

    this.setDOM = function(){
        $mdl = $("#mdl-registrocliente");    
        $txtIdCliente = $mdl.find("#txt-idcliente");
        $txtNombres = $mdl.find("#txt-clientenombres");
        $txtNumeroDocumento = $mdl.find("#txt-clientenumerodocumento");
        $txtCelular = $mdl.find("#txt-clientecelular");

        $btnGuardar = $mdl.find("#btn-clienteguardar");
    };

    this.setEventos = function(){
        $mdl.on("hide.bs.modal", (e)=>{
            this.limpiar();
        });

        $mdl.on("show.bs.modal", (e)=>{
            setTimeout(function(){
                $txtNombres.focus();
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
        $.post("../../controlador/sis_cliente.php?op=registrar", {
                    p_id_cliente : $txtIdCliente.val(),
                    p_numero_documento : $txtNumeroDocumento.val(),
                    p_nombres : $txtNombres.val(),
                    p_celular : $txtCelular.val()

              })
              .done(function(xhr){
                var datos = xhr.datos;

                $btnGuardar.prop("disabled", false);
                $mdl.modal("hide");
                if (xhr.estado == 200){
                    let registroNuevo = datos.registro;

                    if (objBuscarComponente){
                        let esEditando = $txtIdCliente.val() != "";
                        objBuscarComponente.postGuardar({
                            id: registroNuevo.id,
                            descripcion: registroNuevo.descripcion,
                            celular: registroNuevo.celular,
                            numero_documento: registroNuevo.numero_documento
                        }, esEditando);
                    }
                }
              })
              .fail(function(e){
                console.error(e);
                $btnGuardar.prop("disabled", false);
              });
    };

    this.editar = function($btn, cliente){
        let id = cliente.id;
        $mdl.find(".modal-title").html("Editando Cliente "+cliente.descripcion);

        $btn.prop("disabled", true);
        $.post("../../controlador/sis_cliente.php?op=leer", {
                    p_id_cliente : id
              })
              .done(function(xhr){
                $btn.prop("disabled", false);
                if (xhr.estado != 200){
                    return;
                }
                let data = xhr.datos;

                $txtIdCliente.val(data.id_cliente);
                $txtNumeroDocumento.val(data.numero_documento);
                $txtNombres.val(data.nombres);
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
        $.post("../../controlador/sis_cliente.php?op=anular", {
                    p_id_cliente : id
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
        $txtIdCliente.val("");
    };

    return this.init();
};