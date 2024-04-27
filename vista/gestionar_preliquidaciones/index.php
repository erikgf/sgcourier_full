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
                        <h4 class="page-title">Gestión Preliquidaciones</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestión Preliquidaciones</li>
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
                                            <button class="btn btn-info btn-block" id="btnnuevo" ><i class="fa fa-plus"></i> NUEVO REGISTRO</button>
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
        max-width: 40px;
      }
      
      .td-estado{
        width: 75px;
        max-width: 75px;
      }

      .td-numeroguia{
        width: 80px;
        max-width: 80px;
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

      .val_selected{
        height: 25px;
        vertical-align: middle;
        width: 25px;
      }
      
    </style>

    <div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="mdl-registrolabel" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
            <form id="frm-registro" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registrolabel">Registrar Preliquidación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input  id="txtidpreliquidacion"  name="txtidpreliquidacion" type="hidden">
                  <div class="row">
                      <div class="col-sm-6 form-group m-t-10">
                        <label for="txtrepartidor">Repartidor </label>
                        <input class="form-control"  id="txtrepartidor" readonly required name="txtrepartidor" type="text" placeholder="Seleccionar repartidor...">
                      </div>
                      <div class="col-sm-2 form-group m-t-10">
                          <label for="txttipovehiculo">Tipo Vehiculo</label>
                          <select class="form-control"  id="txttipovehiculo" required name="txttipovehiculo" style="width: 100%; height:36px;">
                              <option value="1">MOTORIZADO</option>
                              <option value="2">CAMIÓN</option>
                              <option value="3">AUTO PARTICULAR</option>
                              <option value="4">AUTO TERCIARIZADO</option>
                          </select>
                      </div>
                      <div class="col-sm-2 form-group m-t-10" style="display:none;">
                          <label for="txtestado">Estado</label>
                          <input type="text" class="form-control" readonly required id="txtestado"/>
                      </div>
                      <div class="col-sm-2 form-group m-t-10">
                          <label for="txtfecharegistro">Fecha Registro</label>
                          <input type="date" class="form-control" required id="txtfecharegistro" value="<?php echo $hoy ?>"/>
                      </div>
                  </div> 

                  <div class="row">
                    <div class="col-sm-12">
                      <label>Detalle de Preliquidación</label>
                      <div class="row">
                        <div class="col-sm-4">
                          <p>Reg. Seleccionados: <span id="lbl-regseleccionados">0</span> / Reg. Totales: <span id="lbl-regtotales">0</span> </p>
                        </div>
                        <div clasS="col-sm-2 group-input-sm">
                            <select class="form-control"  id="txtestadocambiar" title="Cambiar Estado a registros seleccionados.">
                                <option value="">Seleccionar...</option>
                                <option value="P">PENDIENTE</option>
                                <option value="R">RUTA</option>
                                <option value="E">ENTREGADO</option>
                                <option value="A">PAGADO</option>
                            </select>
                        </div>
                        <div class="col-sm-2 text-right">
                          <button id="btn-quitaritems" class="btn btn-danger btn-block" title="Se quitaran los items seleccionados de la LISTA.">QUITAR ITEMS</button>
                        </div>
                        <div class="col-sm-2 text-right">
                          <button id="btn-importaritems" class="btn btn-success btn-block">IMPORTAR EXCEL</button>
                        </div>
                        <div class="col-sm-2 text-right">
                          <button id="btn-agregaritem" class="btn btn-primary btn-block">AGREGAR ITEM</button>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3 text-right">
                          <button disabled id="btn-importarexcelmasivo" class="btn btn-success btn-block" title="Se debe seleccionar un excel MASIVO para filtrar usando sus código de REMITO.">IMPORTAR EXCEL MASIVO</button>
                        </div>
                      </div>
                      <hr>
                      <table id="tbldetalle" class="table table-sm small table-condensed display nowrap table-responsive-md table-mini">
                          <thead class="thead-light">
                            <tr>
                              <th class="text-center td-opc"scope="col">Opc. <input type="checkbox" id="chk-seleccionartodos"></th>
                              <th class="td-estado" scope="col">Estado</th>
                              <th scope="col">Cliente</th>
                              <th scope="col">Cliente Interno</th>
                              <th class="td-numeroguia" scope="col" >Doc./N. Guía</th>
                              <th class="td-cantidad" scope="col" style="">Cant.</th>
                              <th class="td-tipopaquete" scope="col" style="" title="Tipo de Paquete">Tipo</th>
                              <th class="td-peso" scope="col" style="">Peso(KG)</th>
                              <th class="td-volumen" scope="col" style="">Volum.(KG)</th>
                              <th class="td-direccion" scope="col" style="">Dirección</th>
                              <th class="td-ciudad" scope="col" style="">Zona/Ciudad</th>
                              <th class="td-costounitario"  scope="col" style="">Costo U.(S/)</th>
                              <th class="td-costosubtotal" scope="col" style="">Subtotal(S/)</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr class="font-16 text-right">
                              <th colspan="12">Costo Extra</th>
                              <th><input style="max-width:100px" value="0.00" required id="txt-costoglobal"></th>
                            </tr>
                            <tr>
                              <th colspan="8">
                                  <div class="group-input-sm">
                                    <label>Observaciones</label>
                                    <textarea class="form-control" id="txt-observaciones"></textarea>  
                                </div>
                              </th>
                              <th colspan="4" class="font-24 text-right">Total</th>
                              <th class="font-24" >S/ <span id="txt-costoentrega">0.00</span></th>
                            </tr>
                          </tfoot>
                      </table>
                    </div>
                  </div>
    
                <div class="row">
                      <div class="col-sm-12">
                         <a href="./archivos/FORMATO_IMPORTACION_PRELIQUIDACIONES.xlsx" download> * Descargar Formato Preliquidaciones</a><br> 
                         <a href="./archivos/MANUAL_USUARIO_PRELIQUIDACIONES.docx" download> * Descargar Manual Preliquidaciones</a><br>
                         <!-- <a href="./archivos/MANUAL_USUARIO_PRELIQUIDACIONES_MASIVOS.docx" download> * Descargar Manual Preliquidaciones - EXCEL MASIVOS</a><br> -->
                      </div>
                  </div>
                  
                  <div class="row">
                      <div class="col-sm-12">
                          <div id="blk-alert-modal"></div>
                      </div>
                  </div>
                </div>
                <div class="modal-footer" style="display: flow-root;">
                    <div class="float-left">
                      <button style="display:none" type="button" id="btn-anular" class="btn btn-danger">ANULAR</button>
                      <button style="display:none" type="button" id="btn-imprimir" class="btn btn-primary">IMPRIMIR</button>
                    </div>
                    <div class="float-right">
                      <input id="chkimprimirguardar" type="checkbox" checked> Imprimir al guardar.
                      <input id="chkcerrarventana" type="checkbox" checked> Cerrar ventana al guardar.
                      <button type="submit" id="btn-guardar" class="btn btn-info">GUARDAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include _COMPONENTES.'/Buscar/buscar.modal.php'; ?>

    <div class="modal fade" id="mdl-registrorepartidor" tabindex="-1" role="dialog" aria-labelledby="mdl-registrorepartidorlabel" style="display: none;">
        <div class="modal-dialog" role="document">
            <form id="frm-registrorepartidor" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registrorepartidorlabel">Registrar Repartidor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input id="txt-idrepartidor"  name="txt-idrepartidor" type="hidden">
                  <div class="row">
                      <div class="col-sm-4 col-xs-12 form-group m-t-10">
                          <label for="txt-repartidornumerodocumento">Número Documento</label>
                          <input type="text" class="form-control" required name="txt-repartidornumerodocumento" id="txt-repartidornumerodocumento" placeholder="DNI, RUC, CE..."/>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-12 form-group m-t-10">
                          <label for="txt-repartidorrazonsocial">Nombres y Apellidos / Razón Social</label>
                          <input type="text" class="form-control" required name="txt-repartidorrazonsocial" id="txt-repartidorrazonsocial"/>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4 form-group m-t-10">
                          <label for="txt-repartidorcelular">Celular / Teléfono</label>
                          <input type="text" class="form-control" name="txt-repartidorcelular" id="txt-repartidorcelular"/>
                      </div>
                      <div class="col-sm-4 form-group m-t-10">
                        <label for="txt-repartidorcostoentrega">Costo Entrega Unit.</label>
                        <input class="form-control"  id="txt-repartidorcostoentrega" step="0.001" required required name="txt-repartidorcostoentrega" type="number"/>
                      </div>
                  </div>
                <div class="modal-footer" style="flow-root">
                    <div class="float-right">
                      <button type="submit" id="btn-repartidorguardar" class="btn btn-info">GUARDAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="modal fade" id="mdl-filtrarpedidos" tabindex="-1" role="dialog" aria-labelledby="mdl-filtrarpedidoslabel" style="display: none;">
        <div class="modal-dialog" role="document">
            <form id="frm-filtrarpedidos" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-filtrarpedidoslabel">Filtrar Pedidos ARCHIVO: <span>-</span>  </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                      <div class="col-sm-4 col-xs-12 form-group m-t-10">
                          <label for="txt-filtrarpedidosnumero">Número Doc./Guía/Remito</label>
                          <input type="text" class="form-control" required name="txt-filtrarpedidosnumero" id="txt-filtrarpedidosnumero" placeholder="Usar pistola lectora..."/>
                      </div>
                      <div class="col-sm-3 col-xs-12 form-group m-t-10">
                          <label for="txt-filtrarpedidosdigitos">Cant. Dígitos</label>
                          <input type="text" class="form-control" required name="txt-filtrarpedidosdigitos" id="txt-filtrarpedidosdigitos" title="Este número permite detectar la cantidad de dígitos que tiene un código de remito/guía en al momento de usar la lectora automática" placeholder="Ingresar dígitos..."/>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-12 form-group m-t-10">
                          <label for="txt-filtrarpedidosfiltrados">Pedidos Filtrados</label>
                          <textarea rows="4" class="form-control" required name="txt-filtrarpedidosfiltrados" id="txt-filtrarpedidosfiltrados"></textarea>
                      </div>
                  </div>
                </div>
                <div class="modal-footer" style="flow-root">
                    <div class="float-right">
                      <button type="submit" id="btn-filtrarpedidos" class="btn btn-info">CARGAR REGISTROS</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","handlebars","datatables","select2"];
      echo Requerir::JS($arJS);
    ?>

    <script type="text/javascript" src="../_componentes/Select.componente.js"></script>
    <script type="text/javascript" src="../_componentes/Buscar/Buscar.componente.js"></script>
    <script type="text/javascript" src="Repartidor.clase.js"></script>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>