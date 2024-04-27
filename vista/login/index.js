var init = function(){
	$('[data-toggle="tooltip"]').tooltip();
    $(".preloader").fadeOut(function(){
    	$("#frmlogin").show(500);
    });
    // ============================================================== 
    // Login and Recover Password 
    // ============================================================== 
    
    $frmLogin = $("#frmlogin");
  	eventos();
   //obtenerDatos();
};

var eventos = function(){
	$('#to-recover').on("click", function() {
        $frmLogin.slideUp();
        $("#recoverform").fadeIn();
    });
    $('#to-login').click(function(){
        $("#recoverform").hide();
        $frmLogin.fadeIn();
    });

    $frmLogin.on("submit", function(e){
    	e.preventDefault();
    	iniciarSesion();
    });
};

var iniciarSesion = function(){
  var tipo_usuario = $("#txttipousuario").val(),
  	  usuario = $("#txtusuario").val(),
      clave = $("#txtclave").val();

  var postData = {
  	p_tipousuario : tipo_usuario,
    p_usuario : usuario,
    p_clave : clave
  };

  var fn = function(xhr){
      var datos = xhr.datos;
	    location.href = "../"+datos.url_go;
  };

  var fnFail = function(xhr){
  	Util.alert($("#blk-alert"), xhr.responseJSON.mensaje, "danger");
    $("#txtclave").val("");
  };

  $.post("../../controlador/sesion.validar.web.php", postData)
      .done(fn)
      .fail(fnFail);
};

$(function(){
	init();
});