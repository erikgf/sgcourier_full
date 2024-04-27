
<div class="row">
    <div class="col-sm-8">
        <h4 class="subtitle">Uso código de barras</h4>
        <div class="row">
            <div class="col-sm-3" >
                <div class="form-group m-t-10">
                    <label for="txtdigitos">Dígitos Cod. Remito:</label>
                    <input type="number" class="form-control"  id="txtdigitos" value="9" title="Dígitos que conforman el código remito a procesar">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group m-t-10">
                    <label for="txtcodigoremitobuscar">Código Remito</label>
                    <div class="input-group">
                        <input type="text" id="txtcodigoremitobuscar" class="form-control" placeholder="Ingresar código...">
                        <div class="input-group-append">
                            <span class="input-group-text" id="txtcodigoremitobuscarzona">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h4 class="subtitle">Uso Masivo</h4>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group m-t-10">
                    <label for="txtcodigoremitobuscarmasivo">Código Remito(s)</label>
                    <div class="input-group">
                        <textarea   id="txtcodigoremitobuscarmasivo" rows="4" class="form-control" placeholder="Ingresar código..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div>
            <label for="txtcolaborador">Colaborador a Asignar</label>
            <select class="control-form" id="txtcolaborador" name="txtcolaborador" style="width: 100%; height:36px;">
            </select>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <br>
                    <button type="button" class="btn btn-info btn-block" id="btnasignar">
                        <i class="fa fa-user-plus"></i> ASIGNAR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="blk-alert">                                   
</div>
<div id="blk-multialert">                                   
</div>
<div class="row">
    <div class="col-sm-2">
        <div class="form-group m-t-10">
            <label for="txtciudad">Ciudad / Agencia</label>
            <select class="select2 form-control"  id="txtciudad" name="txtciudad" style="width: 100%; height:36px;">
            </select>
        </div>
    </div>
    <div class="col-sm-3">
            <br>
            <button type="button" class="btn btn-success btn-block" id="btnactualizar" title="Volver a listar los registros, en caso se haya registrado varias asignaciones rápidas.">
            <i class="fa fa-refresh"></i> ACTUALIZAR LISTADO
            </button>
    </div>
</div>