<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");

 ?>
<!DOCTYPE html>
<html dir="ltr" lang="es">

<head>
    <?php 
      include _PARCIALES.'header.base.php'; 
      $arCSS = [];
      echo Requerir::CSS($arCSS);
    ?>
</head>

<body>
    <?php include _PARCIALES.'preloader.php'; ?>
    <div id="main-wrapper" data-sidebartype="full">
        <?php include _PARCIALES.'header.php' ?>
        
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav" class="p-t-30">
                       <li class="sidebar-item">
                             <h4 class="sidebar-link text-white hide-menu" style="white-space: normal;opacity: 1;">¡Bienvenido!</h4>
                        </li>
                        <hr>
                        <?php 
                            include _PARCIALES.'menu.consultas.poderjudicial.php';
                        ?>
                        <hr>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Consulta por Número de Guía</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Consultas Poder Judicial</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" id="blkmain">
                                <div id="blk-alert">                                   
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtbuscarcodigotracking">Número de Guía</label>
                                            <input placeholder="Ingresar número de GUÍA" maxlength="30" id="txtbuscarcodigotracking" name="txtbuscarcodigotracking" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtbuscarmescodigotracking">Mes Ref.(Opcional)</label>
                                            <select id="txtbuscarmescodigotracking" name="txtbuscarmescodigotracking" class="form-control"/>
                                                <option value="" selected>Ninguno</option>  
                                                <option value="1">ENERO</option>  
                                                <option value="2">FEBRERO</option>  
                                                <option value="3">MARZO</option> 
                                                <option value="4">ABRIL</option> 
                                                <option value="5">MAYO</option> 
                                                <option value="6">JUNIO</option>  
                                                <option value="7">JULIO</option>  
                                                <option value="8">AGOSTO</option>  
                                                <option value="9">SETIEMBRE</option>  
                                                <option value="10">OCTUBRE</option>  
                                                <option value="11">NOVIEMBRE</option>  
                                                <option value="12">DICIEMBRE</option>  
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtbuscaraniocodigotracking">Año Ref.(Opcional)</label>
                                            <select id="txtbuscaraniocodigotracking" name="txtbuscaraniocodigotracking" class="form-control"/>
                                                <option value="" selected>Ninguno</option>  
                                                <option value="2020">2020</option>  
                                                <option value="2021">2021</option>  
                                                <option value="2022">2022</option> 
                                                <option value="2023">2023</option> 
                                                <option value="2024">2024</option> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <label for="txtbuscarsucursalcodigotracking">Sucursal(Opcional)</label>
                                            <select id="txtbuscarsucursalcodigotracking" name="txtbuscarsucursalcodigotracking" class="form-control"/>
                                                <option value="" selected>Ninguno</option>  
                                               
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group m-t-10">
                                            <br>
                                            <button class="btn btn-info btn-block" id="btnbuscar" ><i class="fa fa-search"></i> BUSCAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>

                <div class="card">
                    <div class="row">
                        <div class="col-sm-8 offset-sm-2">
                            <div class="card">
                                <div class="card-body" id="blkcargando" style="display:none;">
                                    <h4>Cargando... <i class="fa fa-spin fa-spinner"></i></h4>
                                </div>
                                <div class="card-body text-center" id="blkerror" style="display:none;">
                                    <h4>
                                        <i class="fa mdi mdi-alert-circle font-weight-bold"
                                        style="font-size: 84px;"></i>
                                        <br>
                                        <span class="lblerror">Registro no encontrado!</span>
                                    </h4>
                                </div>
                                <ul class="list-style-none" style="display:none;" id="blkdata">
                                    <li class="no-block  p-2" id="blknumeroguia">
                                        <div>
                                            <a href="#" class="m-b-0 font-medium p-0">Número de Guía: </a>
                                            <span ></span>
                                        </div>
                                    </li>
                                    <li class="no-block p-2">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12" id="blkremitente">
                                                <a href="#" class="m-b-0 font-medium p-0">Remitente: </a>
                                                <span ></span>
                                            </div>
                                            <div class="col-md-6 col-sm-12" id="blkdependencia">
                                                <a href="#" class="m-b-0 font-medium p-0">Dependencia: </a>
                                                <span ></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="no-block p-2">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12" id="blkconsignatario">
                                                <a href="#" class="m-b-0 font-medium p-0">Consignatario: </a>
                                                <span ></span>
                                            </div>
                                            <div class="col-md-6 col-sm-12" id="blkdestino">
                                                <a href="#" class="m-b-0 font-medium p-0">Destino: </a>
                                                <span ></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="no-block  p-2" >
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12" id="blkfecharecepcion">
                                                <a href="#" class="m-b-0 font-medium p-0">Fecha de Recepción: </a>
                                                <span ></span>
                                            </div>
                                            <div class="col-md-6 col-sm-12" id="blkfechaentrega" style="display:none">
                                                <a href="#" class="m-b-0 font-medium p-0">Fecha de Entrega: </a>
                                                <span ></span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div id="blkproceso" style="display:none;">
                                <div class="row">
                                    <div class="col-sm-6" id="blkestado_1">
                                        <div class="card card-hover">
                                            <div class="box bg-success text-center">
                                                <h1 style="font-size: 88px;" class="font-light text-white"><i class="mdi mdi-check"></i></h1>
                                                <h6 class="lblnombrestado text-white">RECEPCIONADO</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" id="blkestado_2">
                                        <div class="card card-hover">
                                            <div class="box bg-info text-center">
                                                <h1 style="font-size: 88px;" class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                                                <h6 class="lblnombrestado text-white">GESTIONANDO</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5>Imágenes cargadas al sistema: </h5>
                                <div class="row" id="blkestado_3">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php include _PARCIALES.'/footer.php'; ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->

    <?php 
      $arJS = ["jquery","popper","bootstrap","perfect-scrollbar","sparkline","app","charts"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript" src="index1.js"></script>
</body>

</html>