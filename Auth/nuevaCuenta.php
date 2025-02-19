<?php

require_once __DIR__."/../validaciones/validaciones.php";
require_once __DIR__."/../modelo/modelo.php";
include __DIR__."/../funcionesExtras/cors.php";
require_once __DIR__."/../getData/validarExistencia.php ";


$params=FuncionesExtras::getJson();
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        $nombre=$params->nombre != "" ? Validaciones::limpiarCadena(strtoupper($params->nombre)) : "";
        $apellidoPaterno=$params->apellidoP != "" ? Validaciones::limpiarCadena(strtoupper($params->apellidoP)) : "";
        $apellidoMaterno=$params->apellidoM != "" ? Validaciones::limpiarCadena(strtoupper($params->apellidoM)) : "";
        $curp=$params->curp != "" ? Validaciones::limpiarCadena(strtoupper($params->curp)) : "";
        $usuario=$params->usuario != "" ? $params->usuario : "";
        $password = $params ->password != "" ? base64_decode($params->password) : "" ;
        $t_usuario = Validaciones::limpiarCadena($params->t_usuario) ?? "";
        // vericamos si la persona ya se encuentra registrada 
        $idPersona='';
        $existePersona= ValidarExistencia::existenciaPersona($curp, '');
        if ($existePersona != null) { 
            $idPersona=$existePersona['id_persona'];
        }else{
            $dataPersona=[ "nombre"=>$nombre,"apellidoP" => $apellidoPaterno, "apellidoM" => $apellidoMaterno, "curp" => $curp];
            $idPersona=Modelo::insertarPersona($dataPersona);
        }
        
        // verificamos si el usuario ya se encuentra registrado
        $validarUsuario= ValidarExistencia::existenciaUsuario($usuario);
        if ($validarUsuario != null) {
            FuncionesExtras::enviarRespuesta(true,true,"El usuario ya se encuentra registrado", []);  
        }else{
            $insertarUsuario=["t_usuario" => $t_usuario,"usuario"=>$usuario, "clave"=>Validaciones::encriptar($password), "id_persona"=>$idPersona];
            $insertarUsuario=Modelo::insertarUsuario($insertarUsuario);
            if ($insertarUsuario) {
                FuncionesExtras::enviarRespuesta(false,true,"Usuario Agregado Correctamente","");
            }
        }


    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), []);
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

