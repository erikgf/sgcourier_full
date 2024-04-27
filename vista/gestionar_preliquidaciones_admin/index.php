<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");
    $haceSieteDias = date('Y-m-d', strtotime('-7 days'));
    
    Acceso::VALIDAR();

     $id_tipo_usuario = $_SESSION["sesion"]["id_tipo_usuario"] ;
    if ($id_tipo_usuario!= "7" && $id_tipo_usuario!= "1"){
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

 <style type="text/css">
      input[type="file"] {
          display: none;
      }

      @media (min-width: 1080px){
        .modal-xl {
          max-width: 1200px;
        }
      }
      
  </style>

<body>
    <?php include _PARCIALES.'preloader.php'; ?>
    <div id="main-wrapper" data-sidebartype="full" class="mini-sidebar">
        <?php include _PARCIALES.'header.php' ?>
        <?php include _PARCIALES.'menu.php' ?>
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Reportes Preliquidaciones</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Reportes Preliquidaciones</li>
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
                                <h5 class="card-title">Lista de Registros </h5>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtagenciabuscar">Agencia</label>
                                            <select class="form-control" name="txtagenciabuscar" id="txtagenciabuscar"></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtfechainicio">Fecha Inicio</label>
                                            <input type="date" class="form-control" value="<?php echo $haceSieteDias; ?>" id="txtfechainicio" name="txtfechainicio">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtfechafin">Fecha Fin</label>
                                            <input type="date" class="form-control" value="<?php echo $hoy; ?>" id="txtfechafin" name="txtfechafin">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-primary btn-block" id="btnbuscar"><i class="fa fa-list"></i> LISTAR</button>
                                        </div>
                                    </div>
                                    <div class="offset-2 col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-success btn-block" id="btnexcel" ><i class="fa fa-file-excel"></i> EXPORTAR EXCEL</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="blk-alert"></div>
                                <table id="tbllistado" style="width:100%" class="table table-sm table-condensed display nowrap">
                                      <thead class="thead-light">
                                        <tr>
                                          <th class="text-center"scope="col">Opc.</th>
                                          <th scope="col">ID</th>
                                          <th scope="col">F. Registro</th>
                                          <th scope="col">Repartidor</th>
                                          <th scope="col">Agencia</th>
                                          <th scope="col">Responsable</th>
                                          <th scope="col">T. Vehículo</th>
                                          <th scope="col">Costo Total</th>
                                          <th scope="col">Estado</th>
                                        </tr>
                                      </thead>
                                      <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <?php include _PARCIALES.'/footer.php'; ?>
        </div>
    </div>

    <style type="text/css">
      .td-opc{
        max-width: 30px;
      }
      
      .td-estado{
        width: 75px;
        max-width: 75px;
      }

      .td-numeroguia{
        width: 100px;
        max-width: 100px;
      }
      .td-cantidad{
        width: 50px;
        max-width: 50px;
      }
      .td-peso{
        width: 60px;
        max-width: 60px;
      }
      .td-volumen{
        width: 60px;
        max-width: 60px;
      }

      .td-tipopaquete{
        width: 100px;
        max-width: 100px;
      }
      .td-direccion{
        width: 150px;
        max-width: 150px;
      }

      .td-ciudad{
        width: 150px;
        max-width: 150px;
      }
     
      .td-costounitario{
        width: 75px;
        max-width: 75px;
      }
      .td-costosubtotal{
        width: 75px;
        max-width: 75px;
      }


      .table-mini tbody tr td{  
        padding: 2.5px;
      }

      #tbldetalle input:focus, #tbldetalle select:focus {
        border-width: 2.5px;
        outline: none;
        border-color: #112b52;
      }
    </style>

    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="mdl-registrolabel" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registrolabel">Ver Preliquidación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div id="blk-detalle"></div>
                  <div class="row">
                      <div class="col-sm-12">
                          <div id="blk-alert-modal"></div>
                      </div>
                  </div>
                </div>
                <div class="modal-footer" style="display: flow-root;">
                    <div class="float-left">
                      <button  type="button" id="btn-imprimir" class="btn btn-primary">IMPRIMIR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","handlebars","app","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>

    <script type="text/javascript" src="index.js"></script>
</body>

</html>