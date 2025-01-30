<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";

$params=FuncionesExtras::getJson();
$cctSeleccionado=$params->cctSeleccionado != null ? Validaciones::limpiarCadena($params->cctSeleccionado) : "";
$respuesta="";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        // verificamos que todos los parametros vengan llenos en este caso es el de la materia
           $cctSeleccionado=Validaciones::limpiarCadena(strtoupper($cctSeleccionado));
           $infoCct=getData::getInfoCct($cctSeleccionado);
           $infoCct=FuncionesExtras::encriptarIdentificadores(['id_nivel','id_centro_trabajo', 'id_persona'], $infoCct);
           //    separamos la informacion especifica del centro de trabajo y la informacion de los directores
           $directores=[];
           $centroTrabajo=[];
           $data=["centroTrabajo"=>"","directores"=>""];
           if ($infoCct) {
               for ($i=0; $i <count($infoCct) ; $i++) { 
                   $centroTrabajo=[
                       "idCentroTrabajo"=> $infoCct[$i]['id_centro_trabajo'],  "claveCct"=>$infoCct[$i]['clave_centro_trabajo'],"nombreCt"=>$infoCct[$i]['nombre_cct'], "zonaEscolar"=>$infoCct[$i]['zona_escolar'], "id_nivel"=>$infoCct[$i]['id_nivel'], "nivel" => $infoCct[$i]['nivel'], "localidad" => $infoCct[$i]['localidad']];
                    
                       if ($infoCct[$i]['id_persona'] != null) {
                        $curp=$infoCct[$i]['curp'] != null ? $infoCct[$i]['curp'] : "";                       
                       $directores[]=["id_persona" => $infoCct[$i]['id_director_centro_trabajo'], "nombre" => $infoCct[$i]['nombre'],"apellidoPaterno" => $infoCct[$i]['apellido_paterno'],"apellidoMaterno" => $infoCct[$i]['apellido_materno'], "curp" => $curp];

                       }
                       
                    }
                    $data=["centroTrabajo"=>$centroTrabajo,"directores"=>$directores];
                }                          
                   FuncionesExtras::enviarRespuesta(false,true,"Listado de ciclos escolares", $data);

    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}




