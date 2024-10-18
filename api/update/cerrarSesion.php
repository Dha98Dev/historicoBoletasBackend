<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";
require_once __DIR__."/../../updates/actualizaciones.php";

$params=FuncionesExtras::getJson();
$materia=$params->materia != "" ? $params->materia: "" ;
$respuesta="";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
      $eliminarToken=Validaciones::limpiarToken($token);
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
$respuesta = ["error" => true, "isValidToken" => false, "mensaje" => "Necesita iniciar sesion de nuevo", "data" => ""];
FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

