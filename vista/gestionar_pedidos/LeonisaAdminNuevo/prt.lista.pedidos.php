<div class="card">
    <div class="card-body">
        <h5 class="card-title">Lista de <span class="text-success">PEDIDOS</span> </h5>
        <div class="row">
            <div class="col-sm-3 col-md-3 col-lg-2">
                <div class="form-group m-t-10">
                    <label for="txtfechainicio">Fecha Inicio</label>
                    <input type="date" class="form-control" value="<?php echo $haceSieteDias; ?>" id="txtfechainicio" name="txtfechainicio">
                </div>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-2">
                <div class="form-group m-t-10">
                    <label for="txtfechafin">Fecha Fin</label>
                    <input type="date" class="form-control" value="<?php echo $hoy; ?>" id="txtfechafin" name="txtfechafin">
                </div>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <div class="form-group m-t-10">
                    <br>
                    <button class="btn btn-primary btn-block btnbuscar"><i class="fa fa-search"></i> BUSCAR</button>
                </div>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <div class="form-group m-t-10">
                    <br>
                    <button class="btn btn-info btn-block btnnuevo"><i class="fa fa-plus"></i> NUEVO</button>
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
                                <th class="text-center" scope="col">Opc.</th>
                                <th class="text-center" scope="col">ID</th>
                                <th class="text-center" scope="col">Fecha Ingreso</th>
                                <th scope="col" class="text-center">Cantidad</th>
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