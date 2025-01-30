<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";
require_once __DIR__."/../../updates/actualizaciones.php";

$params=FuncionesExtras::getJson();
$usuario=Validaciones::limpiarCadena(Validaciones::desencriptar($params->usuario)) ?? "";
 $estado=Validaciones::limpiarCadena($params->estado) ?? ""; 
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        $mensajeEstado=$estado == "inactivo"  ? 'inhabilitado' : 'habilitado';
        $updateEstadoUsuario=Actualizaciones::updateEstadoUsuario($estado, $usuario);
        if ($updateEstadoUsuario) {
            FuncionesExtras::enviarRespuesta(false, true, 'Se ha '.$mensajeEstado.' correctamente el usuario','');
        }
        else{
            FuncionesExtras::enviarRespuesta(true, true, 'No se ha '.$mensajeEstado.' el usuario, intentelo mas tarde','');
        }
        
        }
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

