<?php

require_once '../util/jwt/vendor/autoload.php';
require_once '../util/jwt/auth.php';

function getDataToken($token){
    try {
        if ( Auth::Check($token) ){
          return Auth::GetData($token);
        }
    } catch (Exception $e) {
        throw $e;
    }
}