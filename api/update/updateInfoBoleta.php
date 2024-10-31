<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";
require_once __DIR__."/../../updates/actualizaciones.php";

$params=FuncionesExtras::getJson();
$idBoleta=$params-> idBoleta ?? "";
$token=$params->token;
$nombre=$params->nombre ?? "";
$apellidoPaterno=$params->apellidoPaterno ?? "";
$apellidoMaterno=$params->apellidoMaterno ?? "";
$curp= $params->curp ?? "";

$claveCt= $params->claveCt ?? "";
$nombreCt = $params->nombreCt ?? "";
$grupo= $params->grupo ?? "";
$turno = $params->turno ?? "";
$ciclo=$params -> ciclo ?? "";
$nivel = $params->nivel ?? "";
$zona = $params ->zona ?? "";
$idCt=$params ->idCt ?? "";
$localidad = $params->localidad ?? "";
$folio = $params->folio ?? "";
$secundaria = $params->calificacionesSecundaria ?? "";
$calificacionesPrimaria = $params->calificacionesPrimaria ?? [];

$dataPersona=["nombre"=> $nombre, "apellidoPaterno"=> $apellidoPaterno, "apellidoMaterno"=> $apellidoMaterno, "curp"=> $curp];
// $dataSecundaria=["calificaciones"=> $calificacionesSecundaria,"idBoleta" => $idBoleta];
// $dataPrimaria=["calificaciones"=> $calificacionesPrimaria,"idBoleta" => $idBoleta];
$dataCt=["idCt"=>$idCt,"cct"=> $claveCt, "nombreCt"=> $nombreCt, "zona"=> $zona, "nivel"=> $nivel, "localidad"=> $localidad];
$dataBoleta=["folio"=> $folio, "grupo"=> $grupo, "turno"=> $turno, "ciclo"=>$ciclo, "idBoleta"=> $idBoleta];
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
   
        // $updateCalSecundaria=Actualizaciones::UpdateCalificacionesSecundaria($calificacionesSecundaria, $idBoleta);
        FuncionesExtras::enviarRespuesta(false,true,"algox", $secundaria );
//     $updateDatosPersonales=Actualizaciones::updatePersona($dataPersona, $idBoleta);
//     $updateCalPrimaria= Actualizaciones::UpdateCalificacionesPrimaria($calificacionesPrimaria);
//     $updateCct=Actualizaciones::UpdateCt($dataCt);
//     $updateDatosBoleta=Actualizaciones::updateDatosBoleta($dataBoleta);
    
//    if ($updateDatosPersonales && $updateCalPrimaria && $updateCalSecundaria && $updateCct && $updateDatosBoleta) {
//     FuncionesExtras::enviarRespuesta(false, true,'Datos Actualizados correctamente', $updateCalPrimaria);
//    }





        }
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

