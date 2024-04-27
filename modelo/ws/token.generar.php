<?php
require_once "../util/jwt/vendor/autoload.php";
require '../util/jwt/auth.php';

function generarToken($data = null, $duracionToken = 3600)
{
    return Auth::SignIn($data, $duracionToken);
}
