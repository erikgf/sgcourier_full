<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");
    $haceSieteDias = date('Y-m-d', strtotime('-7 days'));
    
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
                        <h4 class="page-title">Gestión de <span class="text-success">PEDIDOS</span> LEONISA *NUEVO*</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Pedidos LEONISA</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="card">
                    <ul class="nav nav-tabs" id="tabMain" role="tablist">
                        <li class="nav-item">
                            <a href="#lst-pedidos" class="nav-link active" data-bs-toggle="tab" role="tab">
                                <span class="hidden-sm-up"></span>
                                <span class="hidden-xs-down">Lista Pedidos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#bsc-ordenes" class="nav-link" data-bs-toggle="tab" role="tab" >
                                <span class="hidden-sm-up"></span>
                                <span class="hidden-xs-down">Buscar Órdenes</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane fade show active" id="lst-pedidos" role="tabpanel">
                            <?php include_once './LeonisaAdminNuevo/prt.lista.pedidos.php'; ?>
                        </div>
                        <div class="tab-pane fade show" id="bsc-ordenes" role="tabpanel">
                            <?php include_once './LeonisaAdminNuevo/prt.buscar.orden.php'; ?>
                        </div>
                    </div>
                </div>

            </div>
            <?php include _PARCIALES.'/footer.php'; ?>
        </div>
    </div>

    <?php include_once './LeonisaAdminNuevo/prt.modal.registro.php'; ?>

    <div class="modal fade" id="mdlverdetalle" tabindex="-1" role="dialog" aria-labelledby="lblVerDetalle" style="display: none;"></div>
    <div class="modal fade" id="mdlfotos" tabindex="-1" role="dialog" aria-labelledby="lblVerFotos" style="display: none;"></div>
    
    <?php 
      $arJS = ["jquery","popper","bootstrap","handlebars","perfect-scrollbar","sparkline","app","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>

    <script type="text/javascript" src="../_componentes/FiltrarInput/FiltrarInput.js"></script>
    <script type="text/javascript" src="./LeonisaAdminNuevo/BuscarOrdenes.js"></script>
    <script type="text/javascript" src="./LeonisaAdminNuevo/ListaPedidos.js"></script>
    <script type="text/javascript" src="./LeonisaAdminNuevo/index.js"></script>
</body>

</html>