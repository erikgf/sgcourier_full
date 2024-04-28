<div class="card">
    <div class="card-body">
        <h5 class="card-title">Buscar Órdenes</h5>
        <div class="blk-alert"></div>
        <form class="row">
            <div class="col-sm-3 col-md-4 col-lg-2">
                <div class="form-group m-t-10">
                    <label for="txtbuscarpor">Tipo Búsqueda: </label>
                    <select name="txtbuscarpor" required class="form-control">
                        <option value="dni" >DNI</option>
                        <option value="codigo_guia" selected>Código Guía</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3">
                <div class="form-group m-t-10">
                    <label for="txtbuscar">Buscar: </label>
                    <input type="text" required class="form-control" name="txtbuscar" placeholder="Buscar...">
                </div>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <div class="form-group m-t-10">
                    <br>
                    <button type="submit" class="btn btn-primary btn-block btnbuscar"><i class="fa fa-search"></i> BUSCAR</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="table-responsive">
                        <table id="tbllistado-ordenes" class="table table-sm table-condensed">
                            <thead >
                                <tr>
                                    <th scope="col" style="width: 150px;">N. Guía</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Repartidor</th>
                                    <th scope="col">Destinatario</th>
                                    <th scope="col">Ciudad/Distrito</th>
                                    <th scope="col">Provincia</th>
                                    <th scope="col">Departamento</th>
                                </tr>
                            </thead>
                            <tbody class="small"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>