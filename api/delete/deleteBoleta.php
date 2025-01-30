<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../modelo/delete/delete.php";
$params=FuncionesExtras::getJson();
$idPersona = $params->idPersona;
$idBoleta= $params->idBoleta;

try {
    $deleteCalificacionesPrimaria = delete::deleteCalificacionesPrimariaBoleta($idBoleta);
    $deleteBoleta = delete::deleteBoleta($idBoleta);
    $deletePersona= delete::deletePersona($idPersona);

if($deletePersona){
    FuncionesExtras::enviarRespuesta(false,true,"Perosona eliminada correctamente",'');
}else{
    FuncionesExtras::enviarRespuesta(true, true,"Ocurrio un error al eliminar la persona","");
}
} catch (\Throwable $th) {
    FuncionesExtras::enviarRespuesta(true, true,"Ocurrio un error al eliminar la persona","");
}