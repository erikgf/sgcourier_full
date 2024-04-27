var WebServicio = function() {
    var URL = VARS.SERVER+"/controlador/",
        TIMEOUT = 30000;

    this.initialize = function(){
        return this;
    };

    var promiseAuth = function(dataPromise){
      if (dataPromise && dataPromise.data){
        if (DATA_NAV.acceso){
          dataPromise.data.app_user_key = DATA_NAV.usuario.id_usuario;
        }
      }
      return app.request.promise(dataPromise);
    };

    this.iniciarSesion = function(objSesion) {
       return app.request.promise({
                  url: URL+"sesion.validar.app.php",
                  data: {
                    p_usuario: objSesion.usuario, 
                    p_clave: objSesion.clave
                  },
                  method: "POST",
                  dataType: "json",
                  timeout : TIMEOUT
                });
    };


    this.cargarClientes = function() {
       return app.request.promise({
                  url: URL+"clientes.php?op=listar_clientes_app",
                  data: {},
                  method: "POST",
                  dataType: "json",
                  timeout : TIMEOUT
                });
    };
    

    this.cargarPedidosPendientes = function(objConsulta) {
       return promiseAuth({
                  url: URL+"pedidos_ordenes.php?op=listar_pendientes_app",
                  data: {
                    p_pagina_actual: objConsulta.paginaActual, 
                    p_items_per_load: objConsulta.itemsPerLoad,
                    p_primera_carga : objConsulta.primeraCarga,
                    p_id_cliente : objConsulta.idCliente
                  },
                  method: "POST",
                  dataType: "json",
                  timeout : TIMEOUT
                });
    };

    this.cargarPedidosCompletados = function(objConsulta) {
       return promiseAuth({
                  url: URL+"pedidos_ordenes.php?op=listar_completados_app",
                  data: {
                    p_pagina_actual: objConsulta.paginaActual, 
                    p_items_per_load: objConsulta.itemsPerLoad,
                    p_primera_carga : objConsulta.primeraCarga,
                    p_id_cliente : objConsulta.idCliente
                  },
                  method: "POST",
                  dataType: "json",
                  timeout : TIMEOUT
                });
    };

    this.cargarPedido = function(objConsulta) {
       return promiseAuth({
                  url: URL+"pedidos_ordenes.php?op=leer_x_orden_id_app",
                  data: {
                    p_id_pedido_orden: objConsulta.id
                  },
                  method: "POST",
                  dataType: "json",
                  timeout : TIMEOUT
                });
    };

    this.guardarVisita = function(objRegistro) {
       var datos_frm = new FormData(),
           segundosExtra = 5000, timeout = 0;

       datos_frm.append("p_id_pedido_orden", objRegistro.idPedidoOrden);
       datos_frm.append("p_tipo_visita", objRegistro.tipoVisita);
       datos_frm.append("p_es_receptor_destinatario", objRegistro.esReceptorDestinatario);
       datos_frm.append("p_numero_documento_receptor", objRegistro.numeroDocumentoDestinatario);
       datos_frm.append("p_nombre_receptor", objRegistro.nombreDestinatario);
       datos_frm.append("p_observaciones", objRegistro.observaciones);
       datos_frm.append("p_motivaciones", JSON.stringify(objRegistro.motivaciones));

       for (var i = 0; i < objRegistro.imagenes.length; i++) {
          var imagenFile = objRegistro.imagenes[i];
          datos_frm.append("p_img_"+i, imagenFile);
          timeout = timeout + (timeout * (segundosExtra * i));
       };

       datos_frm.append("p_cantidad_imagenes", objRegistro.imagenes.length);
              
       timeout = timeout + TIMEOUT;
       return promiseAuth({
                  url: URL+"pedidos_ordenes.php?op=registrar_visita_app",
                  cache: false, 
                  crossDomain: true, 
                  contentType: 'multipart/form-data',
                  data: datos_frm,
                  processData: true,
                  method: "POST",
                  dataType: 'json',
                  timeout : timeout
                });
    };

    this.buscarOrdenRemito = function(objConsulta) {
       return promiseAuth({
                  url: URL+"pedidos_ordenes.php?op=buscar_codigo_remito",
                  //url: URL+"pedidos_ordenes.php?op=buscar_codigo_remito_app",
                  //alike: url: URL+"pedidos_ordenes.php?op=leer_x_orden_id_app",
                  data: {
                    p_codigo_remito: objConsulta.codigoRemito,
                    p_id_cliente : objConsulta.idCliente
                  },
                  method: "POST",
                  dataType: "json",
                  timeout : TIMEOUT
                });
    };

   
};