<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

    Acceso::VALIDAR();

    $id_tipo_usuario = $_SESSION["sesion"]["id_tipo_usuario"];

    if ($id_tipo_usuario != "0" && $id_tipo_usuario != "1"){
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
                        <h4 class="page-title">Gestión de Usuarios</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Usuarios</li>
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
                            <div class="card-header" style="display:none" id="lblcargando">
                                <h4>Cargando... <i class="fa fa-spin fa-spinner"></i></h4>
                            </div>
                            <div class="card-body" id="blkmain" >
                                <h5 class="card-title">Lista de Usuarios</h5>
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
                                                        <th scope="col">Número Documento</th>
                                                        <th scope="col">Nombres Apellidos</th>
                                                        <th scope="col">Tipo Usuario</th>
                                                        <th scope="col">Celular</th>
                                                        <th scope="col">Estado</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>                                                   
                                                    </tbody>
                                              </table>
                                          </div
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

    <style>
        .select2-selection--multiple{
            overflow: hidden !important;
            height: auto !important;
        }
    </style>
   
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
                        <div class="col-sm-3">
                            <div class="form-group m-t-10">
                                <label for="txtnumerodocumento">Número Documento</label>
                                <input type="text" id="txtnumerodocumento" name="txtnumerodocumento" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group m-t-10">
                                <label for="txtnombres">Nombres (*)</label>
                                <input type="text" id="txtnombres" required name="txtnombres" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group m-t-10">
                                <label for="txtapellidos">Apellidos (*)</label>
                                <input type="text" id="txtapellidos" required name="txtapellidos" class="form-control"/>
                            </div>
                        </div>
                  </div>
                  <div class="row">
                        <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txttipousuario">Tipo Usuario (*)</label>
                              <select  class="form-control" name="txttipousuario" required id="txttipousuario">
                                <option value="">Seleccionar</option>
                                <option value="1">ADMINISTRADOR</option>
                                <option value="2">EJECUTIVO</option>
                                <option value="99">EJECUTIVO + REPARTIDOR</option>
                                <option value="3">REPARTIDOR</option>
                                <option value="6">ADM. PODER JUDICIAL</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtcelular">Teléfono/Celular</label>
                              <input type="tel"  class="form-control" name="txtcelular" id="txtcelular">
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtcorreo">Correo</label>
                              <input type="email"  class="form-control" name="txtcorreo" id="txtcorreo">
                          </div>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                          <div class="form-group m-t-10">
                              <label for="txtagencia">Agencia</label>
                              <select class="select2 form-control" name="txtagencia" multiple  id="txtagencia" style="width: 100%;min-height:36px">
                                <option selected value="">Ninguna</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtestadoacceso">Estado Actividad / Acceso (*)</label>
                              <select  class="form-control" name="txtestadoacceso" required id="txtestadoacceso">
                                <option selected value="A">ACTIVO</option>
                                <option value="I">INACTIVO</option>
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtnombreusuario">Nombre Usuario (*)</label>
                              <input type="text"  class="form-control" required name="txtnombreusuario" id="txtnombreusuario">
                          </div>
                      </div>
                      <div class="col-sm-3" id="blkclave">
                          <div class="form-group m-t-10">
                              <label for="txtclave">Clave (*)</label>
                              <input type="password"  class="form-control" required name="txtclave" id="txtclave">
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

    <div class="modal fade" id="mdl-cambiarclave" tabindex="-1" role="dialog" aria-labelledby="lblLabelClave" style="display: none;">
        <div class="modal-dialog modal-lg" role="document ">
            <form id="frm-cambiarclave" class="modal-content">
                <div class="modal-header bg-app">
                    <h5 class="modal-title" id="lblLabelClave">Cambiar Clave</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">       
                        <div class="col-sm-9">
                            <div class="form-group m-t-10">
                                <label for="lblusuario">Usuario</label>
                                <input type="text" readonly id="lblusuario" class="form-control"/>
                            </div>
                        </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtnuevaclave">Nueva Clave</label>
                              <input type="tel"  class="form-control" name="txtnuevaclave" required id="txtnuevaclave">
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