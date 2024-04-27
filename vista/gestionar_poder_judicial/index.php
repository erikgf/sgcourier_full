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

 <style type="text/css">
      input[type="file"] {
          display: none;
      }

      input#txtdatosregistro{
         display: block !important;
      }

      .img-thumbnail-upload-ok{
          border: 4px solid #4CAF50 !important;
      }

      .img-thumbnail-upload-fail{
          border: 4px solid #F44336 !important;
      }

      .lbl-imgetiqueta{
          position: absolute;
          bottom: 10px;
          left: 0;
          height: 32px;
          text-align: center;
          width: 100%;
          line-height: 2.5;
          font-weight: bold;
          color:white;
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
                        <h4 class="page-title">Gestión de Poder Judicial</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión de Poder Judicial</li>
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
                                            <label for="txtareabuscar">Área</label>
                                            <select class="form-control" name="txtareabuscar" id="txtareabuscar"></select>
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
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-success btn-block" id="btnregistromasivo" ><i class="fa fa-plus"></i> REGISTRO MASIVO</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnnuevo" ><i class="fa fa-plus"></i> NUEVO REGISTRO</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="blk-alert"></div>
                                <style>
                                    .dataTables_scrollBody{
                                        min-height: 400px;
                                    }
                                </style>
                                <table id="tbllistado" style="width:100%" class="table table-sm table-condensed display nowrap">
                                      <thead class="thead-light">
                                        <tr>
                                          <th class="text-center"scope="col">Opc.</th>
                                          <th scope="col">F. Recepción</th>
                                          <th scope="col">F. Entrega</th>
                                          <th scope="col">N. Guía</th>
                                          <th scope="col">Remitente</th>
                                          <th scope="col">Dependencia</th>
                                          <th scope="col">Consignatario</th>
                                          <th scope="col">Destino</th>
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
            <?php include _PARCIALES.'/footer.php'; ?>
        </div>
    </div>

    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="mdl-registrolabel" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registrolabel">Registrar Guía</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input  id="txtidarearegistro"  name="txtidarearegistro" type="hidden">
                  <div class="row">
                      <div class="col-sm-6 form-group m-t-10">
                          <label for="txtarea">Área</label>
                          <select class="form-control"  id="txtarea" required name="txtarea" style="width: 100%; height:36px;">
                          </select>
                      </div>
                      <div class="col-sm-3 form-group m-t-10">
                        <label for="txtfecharecepcion">Fecha Recepción </label>
                        <input class="form-control"  id="txtfecharecepcion" required name="txtfecharecepcion" type="date">
                      </div>
                      <div class="col-sm-3 form-group m-t-10">
                        <label for="txtnumeroguia">Número Guía </label>
                        <input class="form-control"  id="txtnumeroguia" required name="txtnumeroguia" type="text">
                      </div>
                  </div> 
                  <div class="row">
                      <div class="col-sm-6 form-group m-t-10">
                          <label for="txtremitente">Remitente</label>
                          <select class="form-control"  id="txtremitente" required name="txtremitente" style="width: 100%; height:36px;">
                          </select>
                      </div>
                      <div class="col-sm-6 form-group m-t-10">
                          <label for="txtdependencia">Dependencia</label>
                          <input type="text" class="form-control"  id="txtdependencia" required name="txtdependencia"/>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-7 form-group m-t-10">
                          <label for="txtconsignatario">Consignatario</label>
                          <select class="form-control"  id="txtconsignatario" required name="txtconsignatario" style="width: 100%; height:36px;">
                          </select>
                      </div>
                      <div class="col-sm-5 form-group m-t-10">
                          <label for="txtdestino">Destino</label>
                          <select class="form-control"  id="txtdestino" required name="txtdestino" style="width: 100%; height:36px;">
                          </select>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-sm-12">
                          <div id="blk-alert-modal"></div>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <input id="chkcerrarventana" type="checkbox" checked> Cerrar ventana al guardar.
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                    <button type="submit" id="btn-guardar" class="btn btn-info">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="mdl-registroentrega" tabindex="-1" role="dialog" aria-labelledby="mdl-registroentregalabel" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <form id="frm-registroentrega" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registroentregalabel">Registrar Entrega | <span id="lblnumeroguia"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                      <div class="col-sm-12">
                          <div id="blk-alert-modalentrega"></div>
                      </div>
                  </div>
                  <input  id="txtidarearegistroentrega"  name="txtidarearegistroentrega" type="hidden">
                  <div class="row">
                      <div class="col-sm-3 form-group m-t-10">
                        <label for="txtfechaentrega">Fecha Entrega </label>
                        <input class="form-control"  id="txtfechaentrega" type="date">
                      </div>
                  </div> 

                  <h6>Fotos a subir
                    <div class="float-right">
                      <button class="btn btn-primary btn-sm" id="btnnuevafoto">NUEVA FOTO</button>
                    </div>
                  </h6>
                  <br>
                  <div class="row" id="blk-fotos"></div>
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="mdl-registromasivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;">
        <div class="modal-dialog modal-lg" role="document ">
            <form id="frm-registromasivo" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar Datos Masivos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                      <div class="col-sm-4">
                          <div class="form-group m-t-10">
                              <label for="txtdatosregistro">Excel de Datos</label>
                              <input type="file" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="form-control" name="txtdatosregistro" id="txtdatosregistro">
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sn-12">
                          <div id="blk-alert-modalmasivo"></div>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                    <button type="submit" id="btn-guardarmasivo" class="btn btn-info">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>

    <script type="text/javascript" src="../../libs/compressor/compressor.min.js"></script>
    <script type="text/javascript" src="../_componentes/ImagenSubir.componente.js"></script>
    <script type="text/javascript" src="../_componentes/Select.componente.js"></script>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>