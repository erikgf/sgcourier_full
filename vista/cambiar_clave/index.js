var CambiarClave = function() {
    var $txtAntiguaClave,
        $txtNuevaClave,
        $btnCambiarClave,
        $blkAlert,
        $frm;

    this.setDOM = function(){
        $blkAlert  = $("#blk-alert");
        $txtAntiguaClave = $("#txtantiguaclave");
        $txtNuevaClave = $("#txtnuevaclave");
        $btnCambiarClave = $("#btncambiarclave");

        $frm  = $("form");
    };  
    
    this.setEventos = function(){

        $(".ver-clave").on("mouseover", function(e){
            var $this = $(this);
            $this.parent().find("input").attr("type", "text");
        });

        $(".ver-clave").on("mouseout", function(e){
            var $this = $(this);
            $this.parent().find("input").attr("type", "password");
        });

        $btnCambiarClave.on("click", function(e){
            e.preventDefault();
            cambiarClave();
        });
    };

    var cambiarClave = function(){
        if ($txtNuevaClave.val().length < 6){
            Util.alert($blkAlert, "Tu nueva clave debe tener al menos 6 caracteres.", "danger");
            return;
        }

        $.ajax({ 
            url: "../../controlador/usuarios.php?op=cambiar_clave",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_clave_anterior : $txtAntiguaClave.val(),
               p_clave_nueva : $txtNuevaClave.val()
            },
            success: function(xhr){
                $frm[0].reset();
                Util.alert($blkAlert, xhr.datos.msj, "success");
            },
            error: function (request) {
                $frm[0].reset();
                Util.alert($blkAlert, request.responseJSON.mensaje, "danger");
            },
            cache: true
        });
    };


    this.setDOM();
    this.setEventos();
    return this;
};

$(document).ready(function(){
    objCambiarClave = new CambiarClave(); 
});