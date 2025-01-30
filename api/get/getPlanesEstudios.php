<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
$params=FuncionesExtras::getJson();
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
    
           $planesEstudio=getData::getPlanesEstudios();
           $planesEstudio=FuncionesExtras::encriptarIdentificadores(['valor'], $planesEstudio );

    
            FuncionesExtras::enviarRespuesta(false,true,"Listado de planes de    estudio ", $planesEstudio);

    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

