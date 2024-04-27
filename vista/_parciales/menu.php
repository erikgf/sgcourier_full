<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
               <li class="sidebar-item">
                     <h4 class="sidebar-link text-white hide-menu" style="white-space: normal;opacity: 1;">Bienvenido, <?php echo $_SESSION["sesion"]["nombre_usuario"]; ?></h4>
                </li>
                <hr>
                <?php 
                $id_tipo_usuario = $_SESSION["sesion"]["id_tipo_usuario"];
                
                switch ($id_tipo_usuario) {
                    case '0':
                    case '1':
                        include _PARCIALES.'menu.admin.php';
                        break;
                    case '2':
                        include _PARCIALES.'menu.agencia.php';
                        break;
                    case '4':
                        include _PARCIALES.'menu.cliente.php';
                        break;
                    case '6':
                        include _PARCIALES.'menu.poderjudicial.php';
                        break;
                    case '7':
                        include _PARCIALES.'menu.gerencia.php';
                        break;
                    default:
                        include _PARCIALES.'menu.default.php';
                        break;
                }
                ?>
                <hr>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark bg-secondary" onclick="cerrarSesion();" href="javascript:;" aria-expanded="false">
                        <i class="mdi mdi-arrow-left-box"></i><span class="hide-menu"> Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>