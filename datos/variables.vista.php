<?php 

session_name("courier_demo");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

DEFINE("_PARCIALES", "../_parciales/");
DEFINE("_COMPONENTES", "../_componentes/");
DEFINE("_OPERACIONES", "../_operaciones/");
DEFINE("MODO_PRODUCCION", "0");
