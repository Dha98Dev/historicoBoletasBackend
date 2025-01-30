<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";

$params=FuncionesExtras::getJson();
$idPlanEstudio=$params->idPlanEstudio != "" ? Validaciones::limpiarCadena($params->idPlanEstudio) : "";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if (isset($params->nombrePlanEstudio)) {
    $nombrePlanEstudio=$params->nombrePlanEstudio;
}
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        // verificamos que todos los parametros vengan llenos en este caso es el de la materia
        if(isset($nombrePlanEstudio)){
            $materias=getData::getMateriasPlan("",$nombrePlanEstudio);
        $materias=FuncionesExtras::encriptarIdentificadores(['valor'], $materias);
        }
        else if ($idPlanEstudio == "" ) {
           $materias=getData::getMaterias();
        $materias=FuncionesExtras::encriptarIdentificadores(['valor'], $materias);
        }
        else{   
           $materias=getData::getMateriasPlan(Validaciones::desencriptar($idPlanEstudio), "");
        $materias=FuncionesExtras::encriptarIdentificadores(['valor', 'id_plan_estudio'], $materias);

        }

        FuncionesExtras::enviarRespuesta(false,true,"listado de materias", $materias);
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}
