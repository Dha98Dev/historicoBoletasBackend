<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";
require_once __DIR__."/../../updates/actualizaciones.php";

$params=FuncionesExtras::getJson();
$token=$params->token;
$localidad= Validaciones::limpiarCadena(strtoupper($params->localidad)) ?? "";
$municipio= Validaciones::limpiarCadena(strtoupper($params->municipio)) ?? "";
$domicilio= Validaciones::limpiarCadena(strtoupper($params->domicilio)) ?? "";
$idBoleta= Validaciones::limpiarCadena(Validaciones::desencriptar($params->idBoleta)) ?? "";
$telefono = Validaciones::limpiarCadena($params->telefono) ?? "";



$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        $data=['domicilio'=> $domicilio , 'localidad'=> $localidad ,'telefono'=> $telefono ,'municipio'=> $municipio , 'boleta'=> $idBoleta] ;
        $updateDomicilio=Actualizaciones::updateDomicilio($data);
        if ($updateDomicilio) {
            FuncionesExtras::enviarRespuesta(false,true,"Informacion agregada Correctamente correctamente", "");
        }
        else{
            FuncionesExtras::enviarRespuesta(true,true,"Ocurrio un error al agregar la informacion complementaria", "");
        }
        
        

        }
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

