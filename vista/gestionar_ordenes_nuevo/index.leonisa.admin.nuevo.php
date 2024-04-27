<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

    Acceso::VALIDAR();
 ?>
<!DOCTYPE html>
<html dir="ltr" lang="es">

<head>
    <?php 
      include _PARCIALES.'header.base.php'; 
      $arCSS = ["select2","datatables"];
      echo Requerir::CSS($arCSS);
    ?>

    <style>
        .rounded-pill{
            border-radius: 50rem!important;
        }

        .float-end{
            float: right!important;
        }

        .absolute-end{
            position: absolute;
            right: 0px;
            top: 0px;
        }
    </style>
</head>

<body>
    <?php include _PARCIALES.'preloader.php'; ?>
    <div id="main-wrapper" data-sidebartype="full">
        <?php include _PARCIALES.'header.php' ?>
        <?php include _PARCIALES.'menu.php' ?>
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Gestión de Órdenes LEONISA</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Órdenes LEONISA</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!--
                            <div class="card-header" id="lblcargando">
                                <h4>Cargando... <i class="fa fa-spin fa-spinner"></i></h4>
                            </div>
                            -->
                            <div class="card-body" id="blkmain">
                                <div id="cmp-cabecerapedido"></div>
                                <div id="cmp-tabsapp"></div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php include _PARCIALES.'/footer.php'; ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->

    <div class="modal fade" id="mdlverdetalle" tabindex="-1" role="dialog" aria-labelledby="lblVerDetalle" style="display: none;"></div>

    <div class="modal fade" id="mdlfotos" tabindex="-1" role="dialog" aria-labelledby="lblVerFotos" style="display: none;"></div>

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2","charts", "handlebars"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript">
        const _ID = <?php echo isset($_GET["p_id"]) ? "'".$_GET["p_id"]."'" : "null"; ?>;
        const _KEYLEONISA = <?php echo GlobalVariables::$ID_LEONISA; ?>;
    </script>

    <script type="text/javascript" src="services/FiltroPorUbigeoService.js"></script>
    
    <script type="text/javascript" src="componentes/FiltrarInput/FiltrarInput.js"></script>
    <script type="text/javascript" src="componentes/CabeceraPedido/CabeceraPedido.js"></script>
    <script type="text/javascript" src="componentes/TabDespacharRuta/TabDespacharRuta.js"></script>
    <script type="text/javascript" src="componentes/TabAsignarRepartidores/TabAsignarRepartidores.js"></script>
    <script type="text/javascript" src="componentes/TabMonitoreoRepartidores/TabMonitoreoRepartidores.js"></script>

    <script type="text/javascript" src="componentes/TabsProcesos/TabsProcesos.js"></script>
    <script type="text/javascript" src="index.leonisa.admin.nuevo.js"></script>
</body>

</html>