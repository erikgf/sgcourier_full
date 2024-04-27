<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

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
                        <h4 class="page-title">Consulta por Código Tracking</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Consultas</li>
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
                            <div class="card-body" id="blkmain">
                                <div id="blk-alert">                                   
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group m-t-10">
                                            <label for="txtbuscarcodigotracking">Código Tracking</label>
                                            <input placeholder="Ingresar código de TRACKING" maxlength="30" id="txtbuscarcodigotracking" name="txtbuscarcodigotracking" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnbuscar" ><i class="fa fa-search"></i> BUSCAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>

                <div class="card">
                    <div class="row">
                        <div class="col-sm-8 offset-sm-2">
                            <div class="card">
                                <div class="card-body" id="blkcargando" style="display:none;">
                                    <h4>Cargando... <i class="fa fa-spin fa-spinner"></i></h4>
                                </div>
                                <div class="card-body text-center" id="blkerror" style="display:none;">
                                    <h4>
                                        <i class="fa mdi mdi-alert-circle font-weight-bold"
                                        style="font-size: 84px;"></i>
                                        <br>
                                        <span class="lblerror">¡Pedido no encontrado!</span>
                                    </h4>
                                </div>
                                <ul class="list-style-none" style="display:none;" id="blkdata">
                                    <li class="no-block  p-2" id="blkcodigoremito">
                                        <div>
                                            <a href="#" class="m-b-0 font-medium p-0">Código Remito: </a>
                                            <span ></span>
                                        </div>
                                    </li>
                                    <li class="no-block p-2">
                                        <div class="row">
                                            <div class="col-md-9 col-sm-12" id="blkcliente">
                                                <a href="#" class="m-b-0 font-medium p-0">Cliente: </a>
                                                <span ></span>
                                            </div>
                                            <div class="col-md-3 col-sm-12" id="blkfecha">
                                                <a href="#" class="m-b-0 font-medium p-0">Fecha: </a>
                                                <span ></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="no-block p-2">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12" id="blkdestinatario">
                                                <a href="#" class="m-b-0 font-medium p-0">Destinatario: </a>
                                                <span ></span>
                                            </div>
                                            <div class="col-md-6 col-sm-12" id="blkdireccion">
                                                <a href="#" class="m-b-0 font-medium p-0">Dirección: </a>
                                                <span ></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="no-block  p-2" id="blknumeropaquetes">
                                        <div>
                                            <a href="#" class="m-b-0 font-medium p-0">Número Paquetes: <span ></span> </a>
                                            
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="row" id="blkproceso"  style="display:none;">
                                 <!--
                                <div class="col-sm-4" class="blkestado_1">
                                    <div class="card card-hover">
                                        <div class="box bg-dark text-center">
                                            <h1 class="font-light text-white"><i class="mdi mdi-check"></i></h1>
                                            <h6 class="lblnombrestado text-white">EN RUTA</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4" class="blkestado_2">
                                    <div class="card card-hover">
                                        <div class="box bg-info text-center">
                                            <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                                            <h6 class="lblnombrestado text-white">GESTIONADO</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4" class="blkestado_3">
                                    <div class="card card-hover">
                                        <div class="box bg-danger text-center">
                                            <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                                            <h6 class="lblnombrestado text-white">MOTIVADO</h6>
                                        </div>
                                    </div>
                                </div>
                                -->
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

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2","charts"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>