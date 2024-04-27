<style type="text/css">
    .tbl-buscar tbody tr:hover{
        opacity: .9;
        background: #e6e8eb;
    }

    .tr-seleccionado, .tr-seleccionado:hover{
        background-color: #c3daff !important;
    }
</style>
<div class="modal fade" id="mdl-buscarcomponente" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true ">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-md-9">
                                <div class="input-group">
                                    <div class="input-group-append">
                                      <span class="input-group-text h-100"><i class="fa fa-search"></i></span>
                                    </div>
                                    <input type="search" class="txt-buscar form-control" placeholder="Buscar..."/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn-block btn btn-info btn-buscarnuevo">NUEVO</button>
                            </div>
                        </div>
                    </div>
                    <div class="body table-responsive" style="max-height:400px">
                        <table class="tbl-buscar table small table-sm table-condensed">
                            <thead>
                                <tr>
                                    <th class="text-center" width="75px" style="max-width:75px">ID</th>
                                    <th>DESCRIPCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-buscareditar">EDITAR</button>
                <button type="button" class="btn btn-danger btn-buscaranular">ANULAR</button>
                <button type="button" class="btn btn-info btn-buscarseleccionar">SELECCIONAR</button>
            </div>
        </form>
    </div>
</div>
