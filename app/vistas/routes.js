var onPageBeforeRemove = function(e, page){
  page.route.route._page_.destroy();
  page.route.route._page_ = {};
};

var routes = [
  {
    path: '/login/',
    name: 'login',
    t7 : {},
    templateUrl: "vistas/login/index.html",
    on : {
      pageInit:  function  (e, page) {
        page.route.route._page_ = new LoginVista(page.$el, page.route, page.app.params.servicio_web, page.app);
      },
      pageBeforeRemove: onPageBeforeRemove
    }
  },
  {
    path: '/mis_asignaciones/',
    name: 'mis_asignaciones',
    t7 : {
      $preloader: "vistas/parciales/preloader.tpl",
      lst_asignaciones: "vistas/mis_asignaciones/lstasignaciones.tpl",
      cbo_clientes: "vistas/mis_asignaciones/cboclientes.tpl"
    },
    templateUrl: "vistas/mis_asignaciones/index.html",
    on : {
      pageInit:  function  (e, page) {
        page.route.route._page_ = new MisAsignacionesVista(page.$el, page.route, page.app.params.servicio_web, page.app);
      },
      pageBeforeRemove: onPageBeforeRemove
    }
  },
  {
    path: '/mis_completados/',
    name: 'mis_completados',
    t7 : {
      $preloader: "vistas/parciales/preloader.tpl",
      lst_asignaciones: "vistas/mis_completados/lstcompletados.tpl",
      cbo_clientes: "vistas/mis_completados/cboclientes.tpl"
    },
    templateUrl: "vistas/mis_completados/index.html",
    on : {
      pageInit:  function  (e, page) {
        page.route.route._page_ = new MisCompletadosVista(page.$el, page.route, page.app.params.servicio_web, page.app);
      },
      pageBeforeRemove: onPageBeforeRemove
    }
  },
  {
    path: '/registrar_visita/',
    name: 'registrar_visita',
    t7 : {
      $preloader: "vistas/parciales/preloader.tpl",
      cabecera: "vistas/registrar_visita/tplcabecera.tpl"
    },
    templateUrl: "vistas/registrar_visita/index.html",
    on : {
      pageInit:  function  (e, page) {
        page.route.route._page_ = new RegistrarVisitaVista(page.$el, page.route, page.app.params.servicio_web, page.app);
        //page.route.route._page_ = new TrackingVista(page.$el, page.route, page.app.params.servicio_web, page.app);
      },
      pageBeforeRemove: onPageBeforeRemove
    }
  }
];
