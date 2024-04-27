<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

    Acceso::VALIDAR();
    if ($_SESSION["sesion"]["id_tipo_usuario"] != "0" && $_SESSION["sesion"]["id_tipo_usuario"] != "1"){
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
                        <h4 class="page-title">Gestión de Clientes</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Clientes</li>
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
                                <h5 class="card-title">Lista de Clientes</h5>
                                <div class="row">
                                    <div class="offset-10 col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnnuevo" ><i class="fa fa-plus"></i> NUEVO</button>
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
                                                        <th scope="col">Opc. </th>
                                                        <th scope="col">Número Documento</th>
                                                        <th scope="col">razón Social / Nombres</th>
                                                        <th scope="col">Dirección</th>
                                                        <th scope="col">Teléfono</th>
                                                        <th scope="col">Correo</th>
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

   
    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;">
        <div class="modal-dialog modal-lg" role="document ">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group m-t-10">
                                <label for="txtidtipodocumento">Tipo Documento</label>
                                <select id="txtidtipodocumento" name="txtidtipodocumento">
                                    <option value="">Seleccionar</option>
                                    <option value="">Seleccionar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group m-t-10">
                                <label for="txtdiasentregar">Días en Entregar</label>
                                <input value="2" id="txtdiasentregar" name="txtdiasentregar" class="form-control"/>
                            </div>
                        </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtfechaprocesoregistro">Fecha Ingreso</label>
                              <input type="date" class="form-control" name="txtfechaprocesoregistro" id="txtfechaprocesoregistro" value="<?php echo date('Y-m-d'); ?>">
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtdatosregistro">Excel de Datos</label>
                              <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="form-control" id="txtdatosregistro">
                          </div>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                    <button type="submit" class="btn btn-info">GUARDAR</button>
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