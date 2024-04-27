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
                        <h4 class="page-title">Gestión de Pedidos</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Pedidos</li>
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

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript" src="index.agencia_v1.js"></script>
</body>

</html>