<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php";

$params=FuncionesExtras::getJson();
$materia=$params->materia != "" ? Validaciones::limpiarCadena(strtoupper($params->materia)): "" ;
$respuesta="";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);

$aValidar= Validaciones::validarLongitud(35, $materia, '1');
if (!$aValidar) {FuncionesExtras::enviarRespuesta(true,true,"Algunos de los Datos que envio son demasiado largos, Verifiquelos por favor", ""); die;}

if ($token != "" && $isValidToken['valido']) {
    try 
    {
        // verificamos que todos los parametros vengan llenos en este caso es el de la materia
        if ($materia == "" ) {
            FuncionesExtras::enviarRespuesta(true,true,"El nombre de la Materia a Agregar es Requerido", "");
        }
        else{  
            $existeMateria=ValidarExistencia::existenciaMateria($materia);
            if (!$existeMateria) {
                $nuevaMateria=Modelo::insertarMateria($materia);
                if ($nuevaMateria !=  false) {
                    $materiaAgregada=["valor"=> $nuevaMateria.'-'.$materia,"nombre"=>$materia];
                    FuncionesExtras::enviarRespuesta(false,true,"Materia Agregada correctamente", $materiaAgregada);    
                }
            } 
            else{
                FuncionesExtras::enviarRespuesta(true,true,"La Materia ya Existe", "");
            }
        }
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}
