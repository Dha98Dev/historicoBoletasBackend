<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";

$params=FuncionesExtras::getJson();
$materias=$params->materias != "" ? Validaciones::limpiarCadena($params->materias) : "" ;
$idPlanEstudio = $params->idPlanEstudio != ""  ? Validaciones::limpiarCadena($params->idPlanEstudio) : "" ;
// $idNivel= $params->idNivel != "" ? $params->id : "";

$respuesta="";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        $completo=false;
        for ($i=0; $i <count($materias)  ; $i++) { 
           $asignarMateria=Modelo::asignarMaterias($idPlanEstudio,$materias[$i]);
           if ($asignarMateria) {
            $completo=true;
           }
        }
    
    if ($completo) {
        FuncionesExtras::enviarRespuesta(false,true,"Materias Agregadas Corectamente","");
    }
    FuncionesExtras::enviarRespuesta(true,true,"Ocurrio un error al asignar las materias","");
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}
