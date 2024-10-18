<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php ";


$params=FuncionesExtras::getJson();
// variables con las calificaciones 
$calPrimaria=$params->calPrimaria != "" ? $params->calPrimaria: "" ;
$calSecundaria=$params->calSecundaria != "" ? $params->calSecundaria: "" ;
// variables con la información del centro de trabajo
$claveCct=$params->claveCct != "" ? FuncionesExtras::limpiarCadena(strtoupper( $params->claveCct)): "" ;
$cctNombre=$params->nombreCct != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->nombreCct)): "" ;
$cicloId=$params->cicloEscolar != "" ? FuncionesExtras::limpiarCadena($params->cicloEscolar): "" ;
$zonaEscolar=$params->zonaEscolar != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->zonaEscolar)): "" ;
$planEstudioId=$params->planEstudio != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->planEstudio)): "" ;
$nivel=$params->nivelEducativo != "" ? FuncionesExtras::limpiarCadena($params->nivelEducativo): "";
$localidad=$params->localidad != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->localidad)) : "" ;
// informacion de la boleta
$turno= $params->turno != "" ? FuncionesExtras::limpiarCadena($params->turno):"";
$grupo= $params->grupo != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->grupo)):"";
$folio= $params->folioBoleta != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->folioBoleta)) :"";
$directorCorrespondiente= $params->directorCorrespondiente != "" ? FuncionesExtras::limpiarCadena($params->directorCorrespondiente):"";
// informacion de la persona 
$nombres= $params->nombre != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->nombre)): "" ;
$apellidoPaterno= $params->apellidoPaterno != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->apellidoPaterno)): "" ;
$apellidoMaterno= $params->apellidoMaterno != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->apellidoMaterno)): "" ;
$curp= $params->curp != "" ? FuncionesExtras::limpiarCadena(strtoupper($params->curp)) :"";

$dataPersona=["nombre"=>$nombres,"apellidoP"=>$apellidoPaterno,"apellidoM"=>$apellidoMaterno,"curp"=>$curp ];
$dataCt=["cct"=>$claveCct,"nombreCct"=>$cctNombre,"zona"=>$zonaEscolar,"nivel"=>$nivel, "localidad"=>$localidad];
$dataBoleta=[];

$respuesta="";
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
// esta es la variable del usuario por el  cual fue capturada la informacion
$capturadoPor=$isValidToken['datos']['id_usuario'];

if ($token != "" && $isValidToken['valido']) {
    try 
    {
      $validarCentroTrabajo=ValidarExistencia::existenciaCentroTrabajo($claveCct);
      $idCct=0;
      $idPersona=null;
      $idBoleta=0;
      $completo=false;
    //   si existe el centro de trabajo obtenemos su id
      if($validarCentroTrabajo != null){
        $idCct=$validarCentroTrabajo['id_centro_trabajo'];
      }
    //   si no existe insertamos el centro de trabajo para obtener su id
    else{
        $insertarCt=Modelo::insertarCentroTrabajo($dataCt);
        $idCct=$insertarCt;
    }

    // verificamos si la curp es diferente de vacia y si  ya esta registrada la persona 
    if ($curp != "") {
        $validarPersona=ValidarExistencia::existenciaPersona($curp, "");
        if ($validarPersona) {
            $idPersona=$validarPersona['id_persona'];
        }
    }else{
        $nombreCompleto=$nombres. " ".$apellidoPaterno." ". $apellidoMaterno;
        $validarPersona=ValidarExistencia::existenciaPersona("",$nombreCompleto);
       if ($validarPersona != null) {
        $idPersona=$validarPersona['id_persona'];
       }
    }
    
    
    // si la variable idPersona aun sigue vacia es por que la persona no existe y procedemos a insertarla
    if ($idPersona == null) {
        $insertarPersona=Modelo::insertarPersona($dataPersona);
        $idPersona=$insertarPersona;
    }
    
    $dataBoleta=["idPersona"=>$idPersona,"idNivel"=>$nivel,"idPlan"=>$planEstudioId,"idCct"=>$idCct,"idCiclo"=>$cicloId, "folio"=>$folio, "grupo"=>$grupo, "idTurno"=>$turno , "directorCorrespondiente"=>$directorCorrespondiente, "capturador" => $capturadoPor];
    
    // verificamos que la persona no tenga otra boleta cargada que pertenesca al mismo nivel
    $numeroBoletas=ValidarExistencia::existenciaBoletaPersona($idPersona,$nivel);
    if ($numeroBoletas == 0) {
        $insertBoleta=Modelo::insertarBoleta($dataBoleta);
        $idBoleta=$insertBoleta;
        // FuncionesExtras::enviarRespuesta(false,true,"Boleta agregada Correctamente", $insertBoleta);
    }
    else{
    FuncionesExtras::enviarRespuesta(true,true,"La persona que registro ya tiene una boleta registrada en el nivel solicitado ", "");
        
    }
    // insertamos las calificaciones en caso de que el nivel sea de primaria
    if ($nivel == 1) {
        $insertarCalificaciones=Modelo::insertarCalificacionesPrimaria($calPrimaria,$idBoleta);

            FuncionesExtras::enviarRespuesta(false,true,"Boleta agregada Correctamente", "");
        
    }
    elseif ($nivel==2) {
        $insertarCalificaciones=Modelo::insertarCalificacionesSecundaria($calSecundaria,$idBoleta);
        FuncionesExtras::enviarRespuesta(false,true,"boleta agregada correctamente ", $insertBoleta);
        
    }

    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}
