<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Acceso.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");
    $haceSieteDias = date('Y-m-d', strtotime('-7 days'));
    
    Acceso::VALIDAR();

    $id_tipo_usuario = $_SESSION["sesion"]["id_tipo_usuario"] ;
    if ($id_tipo_usuario!= "2" && $id_tipo_usuario != "1" ){
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

    <link rel="stylesheet" type="text/css" href="index.css">
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
                        <h4 class="page-title">Gestión Ingresos</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión Ingresos</li>
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
                                    <div class="offset-4 col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnnuevo" ><i class="fa fa-plus"></i> NUEVO INGRESO</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="blk-alert"></div>


                                <ul class="nav nav-tabs" role="tablist">
                                  <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-origen" role="tab"><span class="hidden-sm-up"></span>
                                      <span class="hidden-xs-down">Origen</span></a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-destino" role="tab"><span class="hidden-sm-up"></span>
                                      <span class="hidden-xs-down">Destino</span></a>
                                  </li>
                                </ul>
                                <div class="tab-content tabcontent-border">
                                  <div class="tab-pane active" id="tab-origen" role="tabpanel">
                                    <div class="p-20">
                                        <table id="tbllistado-origen" style="width:100%" class="table table-sm table-condensed display nowrap">
                                          <thead class="thead-light">
                                            <tr>
                                              <th class="text-center"scope="col">Opc.</th>
                                              <th scope="col">Fecha Registro</th>
                                              <th scope="col">Cliente</th>
                                              <th scope="col">Origen</th>
                                              <th scope="col">Destino</th>
                                              <th scope="col">Costo</th>
                                              <th scope="col">Cobrado</th>
                                              <th scope="col">¿Pagado?</th>
                                              <th scope="col">Estado</th>
                                            </tr>
                                          </thead>
                                          <tbody></tbody>
                                        </table>
                                    </div>
                                  </div>
                                  <div class="tab-pane p-20" id="tab-destino" role="tabpanel">
                                    <div class="p-20">
                                        <table id="tbllistado-destino" style="width:100%" class="table table-sm table-condensed display nowrap">
                                          <thead class="thead-light">
                                            <tr>
                                              <th class="text-center"scope="col">Opc.</th>
                                              <th scope="col">Fecha Registro</th>
                                              <th scope="col">Cliente</th>
                                              <th scope="col">Origen</th>
                                              <th scope="col">Destino</th>
                                              <th scope="col">Costo</th>
                                              <th scope="col">Cobrado</th>
                                              <th scope="col">¿Pagado?</th>
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
                    </div>
                </div>                
            </div>
            <?php include _PARCIALES.'/footer.php'; ?>
        </div>
    </div>

    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="mdl-registrolabel" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registrolabel">Registrar Ingreso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input  id="txt-idingreso"  name="txt-idingreso" type="hidden">
                  <div class="card">
                      <div class="card-header"><b>Cliente</b></div>
                      <div class="card-body" style="padding-top:0px;padding-bottom:0px">
                          <div class="row">
                            <div class="col-sm-5 form-group m-t-10">
                              <label for="txt-cliente">Nombre / Razón Social </label>
                              <input class="form-control"  id="txt-cliente" readonly required name="txt-cliente" type="text" placeholder="Seleccionar cliente...">
                            </div>
                            <div class="col-sm-2 form-group m-t-10">
                                <label for="lbl-clientenumerodocumento">N. Documento</label>
                                <input class="form-control"  readonly id="lbl-clientenumerodocumento" required name="lbl-clientenumerodocumento">
                            </div>
                            <div class="col-sm-2 form-group m-t-10">
                                <label for="lbl-clientecelular">Celular</label>
                                <input class="form-control"  readonly id="lbl-clientecelular" required name="lbl-clientecelular">
                            </div>
                        </div> 
                      </div>
                  </div>

                  <div class="card">
                    <div class="card-header"><b>Datos de Transporte</b></div>
                    <div class="card-body" style="padding-top:0px;padding-bottom:0px">
                        <div class="row">
                            <div class="col-sm-4 form-group m-t-10">
                                <label for="txt-agenciatransporte">Agencia Transporte </label>
                                <input class="form-control"  id="txt-agenciatransporte" readonly required name="txtagenciatransporte" type="text" placeholder="Seleccionar agencia...">
                            </div>
                            <div class="col-sm-3 form-group m-t-10">
                                <label for="txt-origen">Origen (Tu Agencia)</label>{
                                <input type="text" id="txt-origen" name="txt-origen" class="form-control" readonly/>
                            </div>
                            <div class="col-sm-3 form-group m-t-10">
                                <label for="txt-destino">Destino</label>
                                <select class="form-control"  id="txt-destino" required name="txt-destino" style="width: 100%; height:36px;"></select>
                            </div>
                            <div class="col-sm-2 form-group m-t-10">
                                <label for="txt-fecharegistro">Fecha Registro</label>
                                <input type="date" class="form-control" required id="txt-fecharegistro" value="<?php echo $hoy ?>"/>
                            </div>
                        </div> 
                    </div>
                  </div>
                  

                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-12">
                          <label>Detalle de Ingreso</label>
                          <div class="row">
                            <div class="col-sm-8">
                              <p>Reg. Seleccionados: <span id="lbl-regseleccionados">0</span> / Reg. Totales: <span id="lbl-regtotales">0</span> </p>
                            </div>
                            <div class="col-sm-2 text-right">
                              <button id="btn-quitaritems" class="btn btn-danger btn-block" title="Se quitaran los items seleccionados de la LISTA.">QUITAR ITEMS</button>
                            </div>
                            <div class="col-sm-2 text-right">
                              <button id="btn-agregaritem" class="btn btn-primary btn-block">AGREGAR ITEM</button>
                            </div>
                          </div>
                          <hr>
                          <input type="hidden" id="txt-pagadohastaahora" value="0.00">
                          <table id="tbldetalle" class="table table-sm small table-condensed display nowrap table-responsive-md table-mini">
                              <thead class="thead-light">
                                <tr>
                                  <th class="text-center td-opc"scope="col">Opc. <input type="checkbox" id="chk-seleccionartodos"></th>
                                  <th scope="col">Producto</th>
                                  <th scope="col" style="">Descripción Producto</th>
                                  <th class="td-numeroguia" scope="col" >Doc./N. Guía</th>
                                  <th class="td-cantidad" scope="col" style="">Cant.</th>
                                  <th class="td-tipopaquete" scope="col" style="" title="Tipo de Paquete">Tipo</th>
                                  <th class="td-peso" scope="col" style="">Peso(KG)</th>
                                  <th class="td-volumen" scope="col" style="">Volum.(KG)</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th colspan="7" class="font-24 text-right">Costo</th>
                                  <th class="font-24" >S/ <input class="txt-montos" style="max-width:100px" value="0.00" required id="txt-costo"></th>
                                </tr>
                                <tr>
                                  <th colspan="7" class="font-24 text-right">Por Cobrar</th>
                                  <th class="font-24" >S/ <input class="txt-montos" style="max-width:100px" value="0.00" required id="txt-cobrar"></th>
                                </tr>
                                <tr>
                                  <th colspan="7" class="font-18 text-right">Pendiente Pagar</th>
                                  <th class="font-18" >S/ <input class="txt-montos" style="max-width:100px" value="0.00" required readonly id="txt-pendiente"></th>
                                </tr>
                                <tr>
                                  <th colspan="7" class="font-18 text-right">Pagado</th>
                                  <th class="font-18" >S/ <input class="txt-montos" style="max-width:100px" value="0.00" required id="txt-pagado"></th>
                                </tr>
                              </tfoot>
                          </table>
                        </div>
                      </div>

                      <div class="row" id="blk-estado" style="display:none">
                          <div class="col-sm-2">
                              <h5>Estados: </h5>
                              <select class="form-control"  id="txt-estado" name="txt-estado">
                                  <option value="E">ENVIADO</option>
                                  <option value="R">RECEPCIONADO</option>
                                  <option value="N">ENTREGADO</option>
                              </select>
                          </div>
                      </div>
                      <br>
                      <div class="row">
                          <div class="col-sm-12">
                             <a href="./archivos/MANUAL_USUARIO_INGRESOS.docx" download> * Descargar Manual de Ingresos</a><br>
                          </div>
                      </div>
                      
                      <div class="row">
                          <div class="col-sm-12">
                              <div id="blk-alert-modal"></div>
                          </div>
                      </div>
                    </div>
                  </div>


                </div>

                <div class="modal-footer" style="display: flow-root;">
                    <div class="float-left">
                      <button style="display:none" type="button" id="btn-anular" class="btn btn-danger">ANULAR</button>
                      <!-- <button style="display:none" type="button" id="btn-imprimir" class="btn btn-primary">IMPRIMIR</button>-->
                    </div>
                    <div class="float-right">
                      <!-- <input id="chkimprimirguardar" type="checkbox" checked> Imprimir al guardar. -->
                      <input id="chkcerrarventana" type="checkbox" checked> Cerrar ventana al guardar.
                      <button type="submit" id="btn-guardar" class="btn btn-info">GUARDAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="mdl-registrodestino" tabindex="-1" role="dialog" aria-labelledby="mdl-registrodestinolabel" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
            <form id="frm-registrodestino" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registrodestinolabel">Ver Ingreso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input  id="txt-idingresodestino"  name="txt-idingresodestino" type="hidden">
                  <div class="card">
                      <div class="card-header"><b>Cliente</b></div>
                      <div class="card-body" style="padding-top:0px;padding-bottom:0px">
                          <div class="row">
                            <div class="col-sm-5 form-group m-t-10">
                              <label for="txt-clientedestino">Nombre / Razón Social </label>
                              <input class="form-control"  id="txt-clientedestino" readonly required name="txt-clientedestino" type="text" >
                            </div>
                            <div class="col-sm-2 form-group m-t-10">
                                <label for="lbl-clientenumerodocumento">N. Documento</label>
                                <input class="form-control"  readonly id="lbl-clientenumerodocumentodestino" required name="lbl-clientenumerodocumentodestino">
                            </div>
                            <div class="col-sm-2 form-group m-t-10">
                                <label for="lbl-clientecelular">Celular</label>
                                <input class="form-control"  readonly id="lbl-clientecelulardestino" required name="lbl-clientecelulardestino">
                            </div>
                        </div> 
                      </div>
                  </div>

                  <div class="card">
                    <div class="card-header"><b>Datos de Transporte</b></div>
                    <div class="card-body" style="padding-top:0px;padding-bottom:0px">
                        <div class="row">
                            <div class="col-sm-4 form-group m-t-10">
                                <label for="txt-agenciatransportedestino">Agencia Transporte </label>
                                <input class="form-control"  id="txt-agenciatransportedestino" readonly required name="txtagenciatransportedestino" type="text" >
                            </div>
                            <div class="col-sm-3 form-group m-t-10">
                                <label for="txt-origendestino">Origen</label>
                                <input type="text" readonly name="txt-origendestino" id="txt-origendestino" class="form-control"/>
                            </div>
                            <div class="col-sm-3 form-group m-t-10">
                                <label for="txt-destinodestino">Destino (Tu Agencia)</label>
                                <input type="text" readonly name="txt-destinodestino" id="txt-destinodestino" class="form-control"/>
                            </div>
                            <div class="col-sm-2 form-group m-t-10">
                                <label for="txt-fecharegistrodestino">Fecha Registro</label>
                                <input type="date" class="form-control" readonly id="txt-fecharegistrodestino" value=""/>
                            </div>
                        </div> 
                    </div>
                  </div>
                  

                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-12">
                          <label>Detalle de Ingreso</label>
                          <div class="row">
                            <div class="col-sm-8">
                              <p>Reg. Totales: <span id="lbl-regtotalesdestino">0</span> </p>
                            </div>
                          </div>
                          <hr>
                          <input type="hidden" id="txt-pagadohastaahoradestino" value="0.00">
                          <table id="tbldetalledestino" class="table table-sm small table-condensed display nowrap table-responsive-md table-mini">
                              <thead class="thead-light">
                                <tr>
                                  <th scope="col">Producto</th>
                                  <th scope="col" style="">Descripción Producto</th>
                                  <th class="td-numeroguia" scope="col" >Doc./N. Guía</th>
                                  <th class="td-cantidad" scope="col" style="">Cant.</th>
                                  <th class="td-tipopaquete" scope="col" style="" title="Tipo de Paquete">Tipo</th>
                                  <th class="td-peso" scope="col" style="">Peso(KG)</th>
                                  <th class="td-volumen" scope="col" style="">Volum.(KG)</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                <tr>
                                  <th colspan="6" class="font-24 text-right">Costo</th>
                                  <th class="font-24" >S/ <input disabled readonly style="max-width:100px" value="0.00" required id="txt-costodestino"></th>
                                </tr>
                                <tr>
                                  <th colspan="6" class="font-24 text-right">Por Cobrar</th>
                                  <th class="font-24" >S/ <input disabled readonly style="max-width:100px" value="0.00" required id="txt-cobrardestino"></th>
                                </tr>
                                <tr>
                                  <th colspan="6" class="font-18 text-right">Pendiente Pagar</th>
                                  <th class="font-18" >S/ <input disabled readonly style="max-width:100px" value="0.00" required id="txt-pendientedestino"></th>
                                </tr>
                                <tr>
                                  <th colspan="6" class="font-18 text-right">Pagado</th>
                                  <th class="font-18" >S/ <input class="txt-montos"  style="max-width:100px" value="0.00" required id="txt-pagadodestino"></th>
                                </tr>
                              </tfoot>
                          </table>
                        </div>
                      </div>

                      <div class="row">
                          <div class="col-sm-2">
                              <h5>Estados: </h5>
                              <select class="form-control"  id="txt-estadodestino" name="txt-estadodestino">
                                  <option value="E">ENVIADO</option>
                                  <option value="R">RECEPCIONADO</option>
                                  <option value="N">ENTREGADO</option>
                              </select>
                          </div>
                      </div>
                      <br>
        
                      <div class="row">
                          <div class="col-sm-12">
                              <div id="blk-alert-modal-destino"></div>
                          </div>
                      </div>
                    </div>
                  </div>


                </div>

                <div class="modal-footer" style="display: flow-root;">
                    <div class="float-right">
                      <!-- <input id="chkimprimirguardar" type="checkbox" checked> Imprimir al guardar. -->
                      <button type="submit" id="btn-guardardestino" class="btn btn-info">GUARDAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include _COMPONENTES.'/Buscar/buscar.modal.php'; ?>

    <?php include 'cliente.modal.php'; ?>
    <?php include 'agenciatransporte.modal.php'; ?>
    
    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","handlebars","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>

    <script type="text/javascript" src="../_componentes/Select.componente.js"></script>
    <script type="text/javascript" src="../_componentes/Buscar/Buscar.componente.js"></script>
    <script type="text/javascript" src="Cliente.clase.js"></script>
    <script type="text/javascript" src="AgenciaTransporte.clase.js"></script>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>