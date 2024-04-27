<div class="modal fade" id="mdl-registrocliente" tabindex="-1" role="dialog" aria-labelledby="mdl-registroclientelabel" style="display: none;">
        <div class="modal-dialog" role="document">
            <form id="frm-registrocliente" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registroclientelabel">Registrar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input id="txt-idcliente"  name="txt-idcliente" type="hidden">
                  <div class="row">
                      <div class="col-sm-12 form-group m-t-10">
                          <label for="txt-clientenombres">Nombres / Razón Social</label>
                          <input type="text" class="form-control" required name="txt-clientenombres" id="txt-clientenombres"/>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4 col-xs-12 form-group m-t-10">
                          <label for="txt-clientenumerodocumento">Número Documento</label>
                          <input type="text" class="form-control" required name="txt-clientenumerodocumento" id="txt-clientenumerodocumento" placeholder="DNI, RUC, CE..."/>
                      </div>
                      <div class="col-sm-4 form-group m-t-10">
                          <label for="txt-clientecelular">Celular / Teléfono</label>
                          <input type="text" class="form-control" name="txt-clientecelular" id="txt-clientecelular"/>
                      </div>
                  </div>
                </div>
                <div class="modal-footer" style="flow-root">
                    <div class="float-right">
                      <button type="submit" id="btn-clienteguardar" class="btn btn-info">GUARDAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>