<div class="modal fade" id="mdl-registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;">
    <div class="modal-dialog modal-lg" role="document ">
        <form id="frm-registro" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar <span class="text-success">PEDIDOS</span>  del Día</h5>
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
                                <option value="62" selected>[20301409151] LEONISA - LEO ANDES S.A.</option>
                            </select>
                        </div>
                    </div>
                    <!--
                    <div class="col-sm-2">
                        <div class="form-group m-t-10">
                            <label for="txtdiasruta">Días en Ruta</label>
                            <input value="3" id="txtdiasruta" name="txtdiasruta"  required class="form-control"/>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group m-t-10">
                            <label for="txtdiasentregar">Días Entrega</label>
                            <input value="2" id="txtdiasentregar" name="txtdiasentregar"  required class="form-control"/>
                        </div>
                    </div>
                    -->
                </div>
                <div class="row">
                <input type="hidden" name="txttipopedido" value="1">
                    <div class="col-sm-3">
                        <div class="form-group m-t-10">
                            <label for="txtfechaprocesoregistro">Fecha Registro</label>
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
                    <div class="col-sm-12">
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