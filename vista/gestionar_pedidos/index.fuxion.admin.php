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
                        <h4 class="page-title">Gestión de Pedidos FUXION</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Pedidos FUXION</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Lista de Pedidos </h5>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group m-t-10">
                                            <label for="txtfechainicio">Fecha Inicio</label>
                                            <input type="date" class="form-control" value="<?php echo $haceSieteDias; ?>" id="txtfechainicio" name="txtfechainicio">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group m-t-10">
                                            <label for="txtfechafin">Fecha Fin</label>
                                            <input type="date" class="form-control" value="<?php echo $hoy; ?>" id="txtfechafin" name="txtfechafin">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-primary btn-block" id="btnbuscar"><i class="fa fa-search"></i> BUSCAR</button>
                                        </div>
                                    </div>
                                    <div class="offset-2 col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnnuevo" ><i class="fa fa-plus"></i> NUEVO</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="blk-alert"></div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <table id="tbllistado" class="table table-sm table-condensed">
                                                  <thead class="thead-light">
                                                    <tr>
                                                      <th class="text-center"scope="col">Opc.</th>
                                                      <th class="text-center" scope="col">ID</th>
                                                      <th class="text-center" scope="col">Fecha Ingreso</th>
                                                      <th scope="col">Cliente</th>
                                                      <th scope="col" class="text-center">N°. Órdenes</th>
                                                      <th scope="col" class="text-center bg-dark text-white">No Asignado</th>
                                                      <th scope="col" class="text-center bg-info text-white">Gestionando</th>
                                                      <th scope="col" class="text-center bg-success text-white">Entregadas</th>
                                                      <th scope="col" class="text-center bg-danger text-white">Motivadas</th>
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
            <?php include _PARCIALES.'/footer.php'; ?>
        </div>
    </div>

    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;">
        <div class="modal-dialog modal-lg" role="document ">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar Pedido del Día</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                        <div class="col-sm-7">
                            <div class="form-group m-t-10">
                                <label for="txtclienteregistro">Cliente</label>
                                <select class="form-control"  id="txtclienteregistro" required name="txtclienteregistro">
                                    <option value="<?php echo GlobalVariables::$ID_FUXION_SAC; ?>" selected>[20604632448] FUXION PERU SAC  </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group m-t-10">
                                <label for="txtdiasruta">Días en Ruta</label>
                                <input value="3" id="txtdiasruta" name="txtdiasruta"  required class="form-control"/>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group m-t-10">
                                <label for="txtdiasentregar">Días en Entregar</label>
                                <input value="2" id="txtdiasentregar" name="txtdiasentregar"  required class="form-control"/>
                            </div>
                        </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group m-t-10">
                              <label for="txtfechaprocesoregistro">Fecha Ingreso</label>
                              <input type="date" class="form-control" name="txtfechaprocesoregistro" id="txtfechaprocesoregistro" required value="<?php echo date('Y-m-d'); ?>">
                          </div>
                      </div>
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtdatosregistro">Excel de Datos</label>
                              <input type="file" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="form-control" name="txtdatosregistro" id="txtdatosregistro">
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
                    <button type="submit" id="btn-guardar" class="btn btn-info">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript" src="index.fuxion.admin.js"></script>
</body>

</html>