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
                        <h4 class="page-title">Gestión de Órdenes PRONABEC</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Órdenes PRONABEC</li>
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
                            <div class="card-header" id="lblcargando">
                                <h4>Cargando... <i class="fa fa-spin fa-spinner"></i></h4>
                            </div>
                            <div class="card-body" id="blkmain" style="display:none">
                                <h5 class="card-title">Órdenes de Pedido: <small>ID: <span id="lblpedido"></span></small></h5>
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="form-group m-t-10">
                                            <label for="lblcliente">Cliente</label>
                                            <p id="lblcliente"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group m-t-10">
                                            <label for="lbldireccion">Dirección</label>
                                            <p id="lbldireccion"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="lblcelular">Celular</label>
                                            <p id="lblcelular"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="lblfechaingreso">Fecha Ingreso</label>
                                            <p id="lblfechaingreso"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                      <div class="form-group m-t-10">
                                        <label>Cantidad: <b id="lblcantidad"></b>  <a href="javascript:;" onclick="listar(this,'')">Mostrar Todos</a> </label>
                                        <br>
                                        <button onclick="listar(this,'N')" class="btn btn-sm btn-dark">NO ASIGNADO <span id="lblcantidadnoasignado" class="badge badge-pill badge-dark"></span></button> 
                                        <button onclick="listar(this,'G')" class="btn btn-sm btn-info">GESTIONANDO <span id="lblcantidadgestionando" class="badge badge-pill badge-info"></span></button> 
                                        <button onclick="listar(this,'E')" class="btn btn-sm btn-success">ENTREGADO <span id="lblcantidadentregados" class="badge badge-pill badge-success"></span></button> 
                                        <button onclick="listar(this,'M')" class="btn btn-sm btn-danger">MOTIVADO <span id="lblcantidadmotivados" class="badge badge-pill badge-danger"></span></button> 
                                      </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="pie" style="height: 150px;"></div>
                                    </div>
                                </div>
                                <?php include_once 'blk.asignaciones.php'; ?> 
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                          <div class="table-responsive">
                                            <small>
                                              <table id="tbllistado" class="table table-sm table-condensed">
                                                    <thead class="thead-light">
                                                      <tr>
                                                        <th scope="col">N.Orden </th>
                                                        <th scope="col">F. Atención</th>
                                                        <th scope="col">Colaborador Asignado</th>
                                                        <th scope="col">Observación</th>
                                                        <th scope="col">Destinatario</th>
                                                        <th scope="col">Ubigeo </th>
                                                        <th scope="col">Dirección</th>
                                                        <th scope="col">SIGEDO </th>
                                                        <th scope="col">Oficina</th>
                                                        <th scope="col">Orden</th>
                                                        <th scope="col">Correlativo</th>
                                                        <th scope="col">Núm. Paquetes</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>                                                   
                                                    </tbody>
                                              </table>
                                            </small>
                                          </div
                                        </div>
                                    </div>
                                </div>
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

    <div class="modal fade" id="mdlverdetalle" tabindex="-1" role="dialog" aria-labelledby="lblVerDetalle" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lblVerDetalle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="mdlfotos" tabindex="-1" role="dialog" aria-labelledby="lblVerFotos" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lblVerFotos"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                     <div class="row">
                        <div class="col-12">
                            <div id="carruselFotos" class="carousel slide" data-ride="carousel">
                              <ol class="carousel-indicators">
                                <li data-target="#carruselFotos" data-slide-to="0" class="active"></li>
                                <li data-target="#carruselFotos" data-slide-to="1"></li>
                                <li data-target="#carruselFotos" data-slide-to="2"></li>
                              </ol>
                              <div class="carousel-inner">
                                <div class="carousel-item active">
                                  <img class="d-block w-100" src="../../img/users/5.jpg" alt="1 slide">
                                  <img class="d-block w-100" src="../../img/users/6.jpg" alt="1 slide">
                                  <img class="d-block w-100" src="../../img/users/7.jpg" alt="1 slide">
                                </div>
                              </div>
                              <a class="carousel-control-prev" href="#carruselFotos" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previo</span>
                              </a>
                              <a class="carousel-control-next" href="#carruselFotos" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Siguiente</span>
                              </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                </div>
            </form>
        </div>
    </div>

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2","charts"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript">
        var _ID = <?php echo isset($_GET["p_id"]) ? "'".$_GET["p_id"]."'" : "null"; ?>;
        var _KEY = <?php echo GlobalVariables::$ID_PRONABEC; ?>;
    </script>

    <script type="text/javascript" src="blk.asignaciones.js"></script>
    <script type="text/javascript" src="index.pronabec.admin.js"></script>
</body>

</html>