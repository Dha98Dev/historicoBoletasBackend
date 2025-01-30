<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";

$params=FuncionesExtras::getJson();
$nombrePlan=$params->nombrePlan != "" ? Validaciones::limpiarCadena($params->nombrePlan): " " ;
$inicio=$params->periodoInicio != "" ? $params->periodoInicio: null ;
$fin= $params->periodoInicio != "" ? $params->periodoFin: null ;
$respuesta="";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        // verificamos que todos los parametros vengan llenos en este caso es el de la materia
        if ($nombrePlan == "" ) {
            FuncionesExtras::enviarRespuesta(true,true,"El nombre de la Materia a Agregar es Requerido", "");
        }
        else{   
            $insertarPlan=Modelo::insertarPlanEstudio($nombrePlan,$inicio,$fin);
            if ($insertarPlan) {
                $planesEstudios=getData::getPlanesEstudios();
                FuncionesExtras::enviarRespuesta(false,true,"Plan de Estudio Agregado correctamente", $planesEstudios);    
            }
        }
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}
 