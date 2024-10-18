<?php
require_once __DIR__. "/../getData/getData.php";
require_once __DIR__."/../validaciones/validaciones.php";
require_once __DIR__."/../modelo/modelo.php";
require_once __DIR__."/../updates/actualizaciones.php";
$pdo=getDatabaseConnection();
$params=FuncionesExtras::getJson();
$token=$params->token != null ? $params->token : "";
$passwordActual=$params->passwordActual;
$passwordNueva=$params->passwordNueva;
$isValidToken=Validaciones::validarToken($token);

// FuncionesExtras::enviarRespuesta($isValidToken);

if ($token != "" && $isValidToken['valido']) {
    $pdo=getDatabaseConnection();
        try {

            $idUsuario=$isValidToken['datos']['id_usuario'];
            $nuevaPasswordEncriptada=Validaciones::encriptar($passwordNueva);
            $selectPasswordActual=$pdo->query("select clave from usuarios where clave='".Validaciones::encriptar($passwordActual)."' and id_usuario = '$idUsuario'");
            $passwordActualSeleccionada=$selectPasswordActual->fetch(PDO::FETCH_ASSOC);
            if ($passwordActualSeleccionada == false) {
                $respuesta = ["error" => true, "isValidToken" => true, "mensaje" => "verifique la escritura de su contraseña actual", "data" => ""];
                FuncionesExtras::enviarRespuesta($respuesta);
            }else{
            if (Validaciones::desencriptar($passwordActualSeleccionada['clave']) == $passwordNueva) {
                $respuesta = ["error" => true, "isValidToken" => true, "mensaje" => "La contraseña nueva debe de ser diferente a la contraseña actual", "data" => ""];
                FuncionesExtras::enviarRespuesta($respuesta);
            }
            else{
                $updatePassword=Actualizaciones::updatePassword($idUsuario, $nuevaPasswordEncriptada);
                if ($updatePassword) {
                    $respuesta = ["error" => false, "isValidToken" => true, "mensaje" => "Contraseña cambiada correctamente", "data" => ""];
                } else {
                    $respuesta = ["error" => true, "isValidToken" => true, "mensaje" => "ocurrio un error al actualizar la contraseña intentelo", "data" => ""];
                }
            }}

        } catch (Exception $e) {
            $respuesta = ["error" => true, "isValidToken" => true, "mensaje" => "ocurrio un error intentelo mas tarde", "data" => $e->getMessage()];
        }
    
        //aqui termina el if de la validacion del token, caso de que no sea valido 
    } else {
        $respuesta = ["error" => true, "isValidToken" => false, "mensaje" => "Necesita iniciar sesion de nuevo", "data" => ""];
    }
    
    
    FuncionesExtras::enviarRespuesta($respuesta);

