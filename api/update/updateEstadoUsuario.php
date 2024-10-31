<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";
require_once __DIR__."/../../updates/actualizaciones.php";

$params=FuncionesExtras::getJson();
$usuario=$params-> idBoleta ?? "";
 $estado=$params->estado ?? ""; 
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        // verificamos que no este  en estado de revisado para poder hacer la actualizacion
        $data=getData::getCalificaciones("id_boleta = '$idBoleta'");
        if ($data && $data[0]['estado_boleta'] != 'Revisado') {

            $updateEstado=Actualizaciones::cambiarEstadoBoleta($idBoleta, $isValidToken['datos']['id_usuario']);
            if ($updateEstado) {
        $data=getData::getCalificaciones("id_boleta = '$idBoleta'");
        $response=["estado_boleta" => $data[0]['estado_boleta'], "verificado" => $data[0]['verificado']];
        

               FuncionesExtras::enviarRespuesta(false,true,"Cambio de estado exitoso", $response);
            } 
            FuncionesExtras::enviarRespuesta(true, true, "ocurrio un error al cambiar el estado de la boleta", []);
           } 
        //    en  caso de que ya este revisado retornamos el mensaje de error
           else{
            FuncionesExtras::enviarRespuesta(true, true, "Esta Boleta ya esta en estado de revisada", []);
           }

        }
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

