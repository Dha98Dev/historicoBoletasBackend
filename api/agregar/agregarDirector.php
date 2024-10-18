<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php ";


$params=FuncionesExtras::getJson();
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        $cct=$params->cct != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->cct)) : "";
        $nombreCct=$params->nombreCct != "" ? FuncionesExtras::limpiarCadena(addslashes(strtoupper($params->nombreCct))) : "";
        $nivel=$params->nivel != "" ? FuncionesExtras::limpiarCadena($params->nivel) : "";
        $zonaEscolar=$params->zonaEscolar != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->zonaEscolar)) : "";
        $nombre=$params->nombre != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->nombre)) : "";
        $apellidoPaterno=$params->apellidoPaterno != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->apellidoPaterno)) : "";
        $apellidoMaterno=$params->apellidoMaterno != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->apellidoMaterno)) : "";
        $curp=$params->curp != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->curp)) : "";
        
        $dataPersona=["nombre"=>$nombre, "apellidoP"=>$apellidoPaterno, "apellidoM"=> $apellidoMaterno, "curp" =>$curp];
        
        $dataCentroTrabajo=["cct" => $cct, "nombreCct" => $nombreCct, "nivel" =>$nivel, "zona"=>$zonaEscolar];
        
        $nombreCompleto=$nombre. " ".$apellidoPaterno." ". $apellidoMaterno;
        // validamos si ya se encuentra registrada la persona y si no la insertamos 
        $validarExistePersona=ValidarExistencia::existenciaPersona("",$nombreCompleto);
        $id_persona="";
        $id_ct="";
        if($validarExistePersona !=null){
            $id_persona=$validarExistePersona['id_persona'];
        }else{
            $insertarPersona= Modelo::insertarPersona($dataPersona);
            $id_persona=$insertarPersona;
        }

        // validamos si ya se encuentra el regitro del centro de trabajo y si no lo insertamos

        $validarCt=ValidarExistencia::existenciaCentroTrabajo($cct);
        if ($validarCt != null) {
            $id_ct=$validarCt['id_centro_trabajo'];
        }
        else{
            $insertarCentroTrabajo=Modelo::insertarCentroTrabajo($dataCentroTrabajo);
            $id_ct=$insertarCentroTrabajo;
        }

        // verificamos si se encuentra registrado  el director en el centro de trabajo actual
        $isComplete=false;
        $validarDirectorCt=ValidarExistencia::existenciaDirectorCt($id_ct,$id_persona);
        if ($validarDirectorCt > 0) {
            FuncionesExtras::enviarRespuesta(true,true,"El director ya se encuentra registrado", []);  
        $isComplete=false;

        }
        else{
            $insertarDirector=Modelo::insertarDirectorCct($id_ct,$id_persona);
            $isComplete=true;
        }
        $directores=[];
        if ($isComplete) {
            $directoresCt=getData::getInfoCct($cct);
            for ($i=0; $i <count($directoresCt) ; $i++) { 
                 
                    if ($directoresCt[$i]['id_persona'] != null) {                     
                    $directores[]=["valor" => $directoresCt[$i]['id_director_centro_trabajo'], "nombre" => $directoresCt[$i]['nombre']." ". $directoresCt[$i]['apellido_paterno'] ." " . $directoresCt[$i]['apellido_materno']];

        
                    }
                    
                 }
                 $respuesta=FuncionesExtras::enviarRespuesta(false, true, "Director agregado Correctamente", $directores);
        }
        else{
            $respuesta=FuncionesExtras::enviarRespuesta(true, true, "Error al insertar el director del centro de trabajo", []);
        }
        
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), []);
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

