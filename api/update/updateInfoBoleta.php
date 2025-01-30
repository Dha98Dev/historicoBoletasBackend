<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";
require_once __DIR__."/../../updates/actualizaciones.php";

$params=FuncionesExtras::getJson();
$idBoleta= Validaciones::limpiarCadena(Validaciones::desencriptar($params-> idBoleta))  ?? "";
$token=$params->token;
$nombre= Validaciones::limpiarCadena(strtoupper($params->nombre)) ?? "";
$apellidoPaterno=Validaciones::limpiarCadena(strtoupper($params->apellidoPaterno)) ?? "";
$apellidoMaterno=Validaciones::limpiarCadena(strtoupper($params->apellidoMaterno)) ?? "";
$curp= Validaciones::limpiarCadena(strtoupper($params->curp)) ?? "";

$claveCt= Validaciones::limpiarCadena(strtoupper($params->claveCt)) ?? "";
$nombreCt = Validaciones::limpiarCadena(strtoupper($params->nombreCt)) ?? "";
$grupo= Validaciones::limpiarCadena(strtoupper($params->grupo)) ?? "";
$turno = Validaciones::limpiarCadena($params->turno) ?? "";
$ciclo=Validaciones::limpiarCadena($params -> ciclo) ?? "";
$nivel = Validaciones::limpiarCadena($params->nivel) ?? "";
$zona = Validaciones::limpiarCadena($params ->zona) ?? "";
$idCt=Validaciones::limpiarCadena(Validaciones::desencriptar($params ->idCt)) ?? "";
$localidad = Validaciones::limpiarCadena(strtoupper($params->localidad))?? "";
$folio = Validaciones::limpiarCadena($params->folio) ?? "";
$secundaria = $params->calificacionesSecundaria ?? "";
$calificacionesPrimaria = $params->calificacionesPrimaria ?? [];


$dataPersona=["nombre"=> $nombre, "apellidoPaterno"=> $apellidoPaterno, "apellidoMaterno"=> $apellidoMaterno, "curp"=> $curp];
// $dataSecundaria=["calificaciones"=> $calificacionesSecundaria,"idBoleta" => $idBoleta];
// $dataPrimaria=["calificaciones"=> $calificacionesPrimaria,"idBoleta" => $idBoleta];
$dataCt=["idCt"=>$idCt,"cct"=> $claveCt, "nombreCt"=> $nombreCt, "zona"=> $zona, "nivel"=> $nivel, "localidad"=> $localidad];
$dataBoleta=["folio"=> $folio, "grupo"=> $grupo, "turno"=> $turno, "ciclo"=>$ciclo, "idBoleta"=> $idBoleta];
$updateCalPrimaria=false;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
        try 
        {
                $updateDatosBoleta=Actualizaciones::updateDatosBoleta($dataBoleta);
                // FuncionesExtras::enviarRespuesta(true,true, 'folio' , $updateDatosBoleta);


            // FuncionesExtras::enviarRespuesta(false,true,"algox", $secundaria );
   if ($nivel=='PRIMARIA') {
           $updateCalPrimaria= Actualizaciones::UpdateCalificacionesPrimaria($calificacionesPrimaria);

   }
   else{
           $updateCalSecundaria=Actualizaciones::UpdateCalificacionesSecundaria($calificacionesSecundaria, $idBoleta);
   }
    $updateDatosPersonales=Actualizaciones::updatePersona($dataPersona, $idBoleta);
    $updateCct=Actualizaciones::UpdateCt($dataCt);
    
   if (($updateDatosPersonales  && $updateCct && $updateDatosBoleta) && ($updateCalPrimaria || $updateCalSecundaria)) {
    FuncionesExtras::enviarRespuesta(false, true,'Datos Actualizados correctamente', '');
   }





        }
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

