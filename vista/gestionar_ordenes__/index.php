<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

    Acceso::VALIDAR();
    if ($_SESSION["sesion"]["id_tipo_usuario"] != "4"){
        header("Location: ../login");
        exit;
    }
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
                        <h4 class="page-title">Gestión de Órdenes</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Órdenes</li>
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
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="lblfechaingreso">Fecha Ingreso</label>
                                            <p id="lblfechaingreso"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                      <div class="form-group m-t-10">
                                        <label>Cantidad: <b id="lblcantidad"></b>  <a href="javascript:;" onclick="listar(this,'')">Mostrar Todos</a> </label>
                                        <br>
                                        <button onclick="listar(this,'N')" class="btn btn-sm btn-dark">EN RUTA <span id="lblcantidadnoasignado" class="badge badge-pill badge-dark"></span></button> 
                                        <button onclick="listar(this,'G')" class="btn btn-sm btn-info">GESTIONANDO <span id="lblcantidadgestionando" class="badge badge-pill badge-info"></span></button> 
                                        <button onclick="listar(this,'E')" class="btn btn-sm btn-success">ENTREGADO <span id="lblcantidadentregados" class="badge badge-pill badge-success"></span></button> 
                                        <button onclick="listar(this,'M')" class="btn btn-sm btn-danger">MOTIVADO <span id="lblcantidadmotivados" class="badge badge-pill badge-danger"></span></button> 
                                      </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="pie" style="height: 150px;"></div>
                                    </div>
                                    <div class="offset-1 col-sm-2">
                                      <div class="form-group m-t-10">
                                          <br>
                                          <button onclick="generarExcel()" type="button" class="btn btn-success btn-block"  id="btn-excel">
                                            <i class="fa fa-file-excel"></i> GENERAR EXCEL
                                          </button>
                                      </div>
                                    </div>
                                </div>
                                <div id="blk-alert">                                   
                                </div>
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
                                                        <th scope="col">Observación</th>
                                                        <th scope="col">F. Courier </th>
                                                        <th scope="col">ID/Documento </th>
                                                        <th scope="col">Destinatario</th>
                                                        <th scope="col">Dirección 1</th>
                                                        <th scope="col">Dirección 2</th>
                                                        <th scope="col">Referencia</th>
                                                        <th scope="col">Ciudad</th>
                                                        <th scope="col">Dpto. </th>
                                                        <th scope="col">Celular</th>
                                                        <th scope="col">Núm. Paquetes </th>
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
                                <span class="sr-only">Previous</span>
                              </a>
                              <a class="carousel-control-next" href="#carruselFotos" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
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
    </script>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>