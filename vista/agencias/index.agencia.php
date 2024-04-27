<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

    Acceso::VALIDAR();
    $id_tipo_usuario = $_SESSION["sesion"]["id_tipo_usuario"];
    if ($id_tipo_usuario != "0" && $id_tipo_usuario != "1" && $id_tipo_usuario != "7"){
      header("Location: ../login");
      exit;
    }
 ?>
<!DOCTYPE html>
<html dir="ltr" lang="es">

<head><meta charset="utf-8">
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
                        <h4 class="page-title">Gestión de Agencias</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Agencias</li>
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
                    <div class="col-10">
                        <div class="card">
                            <div class="card-header" style="display:none" id="lblcargando">
                                <h4>Cargando... <i class="fa fa-spin fa-spinner"></i></h4>
                            </div>
                            <div class="card-body" id="blkmain" >
                                <h5 class="card-title">Lista de Agencias</h5>
                                <div class="row">
                                    <div class="offset-md-10 col-md-2 col-sm-12">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnnuevo" ><i class="fa fa-plus"></i> NUEVO</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="blk-alert" tabindex="-1">                                   
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                          <div class="table-responsive">
                                              <table id="tbllistado" class="table table-sm table-condensed">
                                                    <thead class="thead-light">
                                                      <tr>
                                                        <th scope="col">Opc. </th>
                                                        <th scope="col">Nombre Agencia</th>
                                                        <th scope="col">Distrito</th>
                                                        <th scope="col">Provincia</th>
                                                        <th scope="col">Departamento</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>                                                   
                                                    </tbody>
                                              </table>
                                            </div>                          
                                        </div>
                                    </div>
                                </div>
                            
                                
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <?php include _PARCIALES.'/footer.php'; ?>
        </div>
    </div>
   
    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="lblModalTitulo" style="display: none;">
        <div class="modal-dialog modal-lg" role="document ">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header bg-app">
                    <h5 class="modal-title" id="lblModalTitulo">Nuevo Registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">       
                        <div class="col-sm-12">
                            <div class="form-group m-t-10">
                                <label for="txtdescripcion">Nombre de Agencia (*)</label>
                                <input type="text" id="txtdescripcion" name="txtdescripcion" required class="form-control"/>
                            </div>
                        </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtubigeo">Departamento</label>
                              <select  style="width: 100%; height:36px;" required class="form-control" name="txtdepartamentos" required id="txt-departamentos"></select>
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtubigeo">Provincia</label>
                              <select  style="width: 100%; height:36px;" class="form-control" name="txtprovincias"  id="txt-provincias"></select>
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtubigeo">Distrito</label>
                              <select  style="width: 100%; height:36px;" class="form-control" name="txtdistritos"  id="txt-distritos"></select>
                          </div>
                      </div>
                  </div>
                    <div class="row">
                        <div class="col-sn-12">
                            <div id="blk-alert-modal"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                    <button type="submit"  id="btnguardar"  class="btn btn-info">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>

    <script type="text/javascript" src="index.1.js"></script>
</body>

</html>