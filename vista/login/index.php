<?php 
    include_once '../../datos/variables.vista.php';
    include_once _OPERACIONES.'Requerir.php';
    $hoy = date("Y-m-d");
 ?>

<!DOCTYPE html>
<html dir="ltr" lang="es">
<head><meta charset="utf-8">
   <?php 
      include _PARCIALES.'header.base.php'; 
      $arCSS = [];
      echo Requerir::CSS($arCSS);
    ?>
</head>

<body>
    <div id="main-wrapper">
        <?php include _PARCIALES.'preloader.php'; ?>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-info-gradient">
            <div class="auth-box border-top border-secondary">
                <div id="loginform">
                    <div class="text-center p-t-20 p-b-20">
                        <span class="db"><img src="../../img/logo_main.jpg" alt="Logo SG" /></span>
                    </div>
                    <!-- Form -->
                    <form class="form-horizontal m-t-20" id="frmlogin" style="display:none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon0"><i class="ti-tag"></i></span>
                                    </div>
                                    <select name="txttipousuario" id="txttipousuario" class="form-control form-control-lg" aria-label="Tipo Usuario" aria-describedby="basic-addon0" required="">
                                        <option value="4" selected>CLIENTE</option>
                                        <option value="1">ADMINISTRATIVO</option>
                                        <option value="2" >EJECUTIVO</option>
                                        <option value="6" >PODER JUDICIAL</option>
                                        <option value="7" >GERENCIA</option>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input name="txtusuario"  id="txtusuario" type="text" class="form-control form-control-lg" placeholder="Usuario" aria-label="Usuario" aria-describedby="basic-addon1" required="">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input name="txtclave" value="" id="txtclave" type="password" class="form-control form-control-lg" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="basic-addon1" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row p-b-30">
                            <div id="blk-alert" class="col-12">
                            </div>
                        </div>
                        <div class="row border-top">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="p-t-20">
                                        <!--<button class="btn btn-info" id="to-recover" type="button"><i class="fa fa-lock m-r-5"></i> Lost password?</button> -->
                                        <button type="submit" class="btn btn-danger float-right">ACCEDER</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="recoverform">
                    <div class="text-center">
                        <span class="text-white">Enter your e-mail address below and we will send you instructions how to recover a password.</span>
                    </div>
                    <div class="row m-t-20">
                        <!-- Form -->
                        <form class="col-12" action="index.html">
                            <!-- email -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-danger text-white" id="basic-addon1"><i class="ti-email"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-lg" placeholder="Email Address" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                            <!-- pwd -->
                            <div class="row m-t-20 p-t-20 border-top border-secondary">
                                <div class="col-12">
                                    <a class="btn btn-success" href="#" id="to-login" name="action">Back To Login</a>
                                    <button class="btn btn-info float-right" type="button" name="action">Recover</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
      $arJS = ["jquery","popper","bootstrap"];
      echo Requerir::JS($arJS);
    ?>
    <script type="text/javascript" src="index.js"></script>
</body>
</html>