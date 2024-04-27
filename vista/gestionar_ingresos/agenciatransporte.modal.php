<div class="modal fade" id="mdl-registroagenciatransporte" tabindex="-1" role="dialog" aria-labelledby="mdl-registroagenciatransportelabel" style="display: none;">
        <div class="modal-dialog" role="document">
            <form id="frm-registroagenciatransporte" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdl-registroagenciatransportelabel">Registrar Agencia Transporte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true ">×</span>
                    </button>
                </div>
                <div class="modal-body">
                  <input id="txt-idagenciatransporte"  name="txt-idagenciatransporte" type="hidden">
                  <div class="row">
                      <div class="col-sm-12 form-group m-t-10">
                          <label for="txt-agenciatransportenombre">Nombre</label>
                          <input type="text" class="form-control" required name="txt-agenciatransportenombre" id="txt-agenciatransportenombre"/>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4 form-group m-t-10">
                          <label for="txt-agenciatransportecelular">Celular / Teléfono</label>
                          <input type="text" class="form-control" name="txt-agenciatransportecelular" id="txt-agenciatransportecelular"/>
                      </div>
                  </div>
                </div>
                <div class="modal-footer" style="flow-root">
                    <div class="float-right">
                      <button type="submit" id="btn-agenciatransporteguardar" class="btn btn-info">GUARDAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>