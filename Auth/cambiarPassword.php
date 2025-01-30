<?php
require_once __DIR__. "/../getData/getData.php";
require_once __DIR__."/../validaciones/validaciones.php";
require_once __DIR__."/../modelo/modelo.php";
require_once __DIR__."/../updates/actualizaciones.php";
$pdo=Conexion::getDatabaseConnection();
$params=FuncionesExtras::getJson();
$token=$params->token != null ? $params->token : "";
$passwordActual=base64_decode($params->passwordActual) ?? "";
$passwordNueva=base64_decode($params->passwordNueva) ?? "";
$isValidToken=Validaciones::validarToken($token);

// FuncionesExtras::enviarRespuesta($isValidToken);

if ($token != "" && $isValidToken['valido']) {
    $pdo=Conexion::getDatabaseConnection();
        try {

            $idUsuario=$isValidToken['datos']['id_usuario'];
            $nuevaPasswordEncriptada=Validaciones::encriptar($passwordNueva);
            $selectPasswordActual=$pdo->query("select clave from usuarios where clave='".Validaciones::encriptar($passwordActual)."' and id_usuario = '$idUsuario'");
            $passwordActualSeleccionada=$selectPasswordActual->fetch(PDO::FETCH_ASSOC);
            if ($passwordActualSeleccionada == false) {
                FuncionesExtras::enviarRespuesta(true,true,'Su contraseña actual es incorrecta', '');
            }else{
            if (Validaciones::desencriptar($passwordActualSeleccionada['clave']) == $passwordNueva) {
                FuncionesExtras::enviarRespuesta(true, true, 'su nueva contraseña debe de ser diferente a la contraseña actual', '');
            }
            else{
                $updatePassword=Actualizaciones::updatePassword($idUsuario, $nuevaPasswordEncriptada);
                if ($updatePassword) {
                    FuncionesExtras::enviarRespuesta(false, true, 'La contraseña de cambio correctamente', '');
                } else {
                    FuncionesExtras::enviarRespuesta(true, true, 'Ocurrrio un error al cambiar la contrseña ','');
                }
            }}

        } catch (Exception $e) {
            FuncionesExtras::enviarRespuesta(true, true, 'Ocurrio un error intentelo mas tarde', $e->getMessage());
        }
    
        //aqui termina el if de la validacion del token, caso de que no sea valido 
    } else {
        FuncionesExtras::enviarRespuesta(true,false,'Necesita iniciar Sesion de Nuevo','');
    }
