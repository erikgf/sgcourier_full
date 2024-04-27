var Repartidor =  function(){
    let $mdl, $btnGuardar;
    let $txtIdRepartidor, $txtRazonSocial, $txtCelular, $txtNumeroDocumento, $txtCostoEntregaUnitario;

    this.init = function(){

        this.setDOM();
        this.setEventos();
    };

    this.setDOM = function(){
        $mdl = $("#mdl-registrorepartidor");    
        $txtIdRepartidor = $mdl.find("#txt-idrepartidor");
        $txtNumeroDocumento = $mdl.find("#txt-repartidornumerodocumento");
        $txtRazonSocial = $mdl.find("#txt-repartidorrazonsocial");
        $txtCelular = $mdl.find("#txt-repartidorcelular");
        $txtCostoEntrega = $mdl.find("#txt-repartidorcostoentrega");

        $btnGuardar = $mdl.find("#btn-repartidorguardar");
    };

    this.setEventos = function(){
        $mdl.on("hide.bs.modal", (e)=>{
            this.limpiar();
        });

        $mdl.on("show.bs.modal", (e)=>{
            setTimeout(function(){
                $txtNumeroDocumento.focus();
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
        $.post("../../controlador/sis_repartidor.php?op=registrar", {
                    p_id_repartidor : $txtIdRepartidor.val(),
                    p_numero_documento : $txtNumeroDocumento.val(),
                    p_razon_social : $txtRazonSocial.val(),
                    p_celular : $txtCelular.val(),
                    p_costo_entrega : $txtCostoEntrega.val()

              })
              .done(function(xhr){
                var datos = xhr.datos;

                $btnGuardar.prop("disabled", false);
                $mdl.modal("hide");
                if (xhr.estado == 200){
                    let registroNuevo = datos.registro;

                    if (objBuscarComponente){
                        let esEditando = $txtIdRepartidor.val() != "";
                        objBuscarComponente.postGuardar({
                            id: registroNuevo.id,
                            descripcion: registroNuevo.descripcion,
                            costo_entrega: registroNuevo.costo_entrega
                        }, esEditando);
                    }
                }
              })
              .fail(function(e){
                console.error(e);
                $btnGuardar.prop("disabled", false);
              });
    };

    this.editar = function($btn, repartidor){
        let id = repartidor.id;
        $mdl.find(".modal-title").html("Editando Repartidor "+repartidor.descripcion);

        $btn.prop("disabled", true);
        $.post("../../controlador/sis_repartidor.php?op=leer", {
                    p_id_repartidor : id
              })
              .done(function(xhr){
                $btn.prop("disabled", false);
                if (xhr.estado != 200){
                    return;
                }
                let data = xhr.datos;

                $txtIdRepartidor.val(data.id_repartidor);
                $txtNumeroDocumento.val(data.numero_documento);
                $txtRazonSocial.val(data.razon_social);
                $txtCelular.val(data.celular);
                $txtCostoEntrega.val(data.costo_entrega);

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
        $.post("../../controlador/sis_repartidor.php?op=anular", {
                    p_id_repartidor : id
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
        $txtIdRepartidor.val("");
    };

    return this.init();
};