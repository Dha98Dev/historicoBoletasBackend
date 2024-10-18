<?php
require_once __DIR__. "/../getData/getData.php";
require_once __DIR__."/../validaciones/validaciones.php";
require_once __DIR__."/../modelo/modelo.php";

$params=FuncionesExtras::getJson();
$token = $params->token;

try {
    $arreglo = Validaciones::validarToken($token);

    if ($arreglo['valido']) {
        FuncionesExtras::enviarRespuesta(false,true,$arreglo['mensaje'], $arreglo['datos']);
    } else {
        FuncionesExtras::enviarRespuesta(true,false,$arreglo['mensaje'], "");
        
    }
} catch (Exception $e) {
    $respuesta = ["error" => true, "mensaje" => "ocurrio algun error", "token" => "", "data" =>  $e->getMessage()];
}
