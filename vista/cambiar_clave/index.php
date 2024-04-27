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
                        <h4 class="page-title">Cambiar Clave</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cambiar Clave</li>
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
                                <div id="blk-alert"></div>
                                <p>Tu clave está <b>encriptada</b> es personal y secreta, en caso de pérdida; consultar al administrador del sistema para un reseteo de clave.</p>
                                <form class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtantiguaclave">Anterior Clave</label>
                                            <input type="password" maxlength="30" id="txtantiguaclave" name="txtantiguaclave" class="form-control"/>
                                            <small class="float-right ver-clave"> <span class="fa fa-eye"></span>  Ver</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtnuevaclave">Nueva Clave</label>
                                            <input type="password" maxlength="30" id="txtnuevaclave" name="txtnuevaclave" class="form-control"/>
                                            <small class="float-right ver-clave"> <span class="fa fa-eye"></span>  Ver</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btncambiarclave" ><i class="fa fa-refresh"></i> CAMBIAR CLAVE</button>
                                        </div>
                                    </div>
                                </form>
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
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>