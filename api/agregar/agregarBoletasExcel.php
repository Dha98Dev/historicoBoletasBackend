<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";
require_once __DIR__."/../../getData/validarExistencia.php ";
require_once __DIR__."/../../modelo/delete/delete.php";


$params=FuncionesExtras::getJson();
// variables con las calificaciones 
$calPrimaria=$params->calPrimaria != "" ? $params->calPrimaria: "" ;
$calSecundaria=$params->calSecundaria != "" ? $params->calSecundaria: "" ;
// variables con la información del centro de trabajo
$claveCct=$params->claveCct != "" ? Validaciones::limpiarCadena(strtoupper( $params->claveCct)): "" ;
$cctNombre=$params->nombreCct != "" ? Validaciones::limpiarCadena(strtoupper($params->nombreCct)): "" ;
$cicloId=$params->cicloEscolar != "" ? Validaciones::limpiarCadena($params->cicloEscolar): "" ;
$zonaEscolar=$params->zonaEscolar != "" ? Validaciones::limpiarCadena(strtoupper($params->zonaEscolar)): "" ;
$planEstudioId=$params->planEstudio != "" ? Validaciones::limpiarCadena(strtoupper($params->planEstudio)): "" ;
$nivel=$params->nivelEducativo != "" ? Validaciones::limpiarCadena($params->nivelEducativo): "";
$localidad=$params->localidad != "" ? Validaciones::limpiarCadena(strtoupper($params->localidad)) : "" ;
// informacion de la boleta
$turno= $params->turno != "" ? strtoupper(Validaciones::limpiarCadena($params->turno)):"";
$grupo= $params->grupo != "" ? Validaciones::limpiarCadena(strtoupper($params->grupo)):"";
$folio= $params->folioBoleta != "" ? Validaciones::limpiarCadena(strtoupper($params->folioBoleta)) :"";
$directorCorrespondiente= $params->directorCorrespondiente != "" ? Validaciones::limpiarCadena($params->directorCorrespondiente):"";
// informacion de la persona 
$nombres= $params->nombre != "" ? Validaciones::limpiarCadena(strtoupper($params->nombre)): "" ;
$apellidoPaterno= $params->apellidoPaterno != "" ? Validaciones::limpiarCadena(strtoupper($params->apellidoPaterno)): "" ;
$apellidoMaterno= $params->apellidoMaterno != "" ? Validaciones::limpiarCadena(strtoupper($params->apellidoMaterno)): "" ;
$curp='';


if (isset($params->curp)) {
  $curp= $params->curp != "" ? Validaciones::limpiarCadena(strtoupper($params->curp)) :"";
}

$insertoPersona=false;
$insertoBoleta=false;
$insertoCalificaciones=false;
$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
// esta es la variable del usuario por el  cual fue capturada la informacion
$capturadoPor=$isValidToken['datos']['id_usuario'];


$longitudesAValidar= [["aValidar"=>$claveCct ,"longitud"=>10] ,["aValidar"=>$cctNombre ,"longitud"=>100] ,["aValidar"=>$localidad ,"longitud"=> 100],["aValidar"=> $folio ,"longitud"=> 50], ["aValidar"=>$nombres ,"longitud"=>40] ,[ "aValidar"=>$apellidoPaterno ,"longitud"=>40] , ["aValidar"=>$apellidoMaterno ,"longitud"=>40] , ["aValidar"=>isset($curp) ? $curp : "", "longitud"=>18]];

$validarLongitudes=Validaciones::validarLongitud("",$longitudesAValidar, '2');
if (!$validarLongitudes){FuncionesExtras::enviarRespuesta(true,true,"Algunos de los Datos que envio son demasiado largos, Verifiquelos por favor", ""); die;}

if ($token != "" && $isValidToken['valido']) {
  try 
  {
    $idCct=null;
    $idPersona=null;
    $idBoleta=null;
    $idNivel=null;
    $idCiclo=null;
    $idPlanEstudio=null;
    $completo=false;
    
    // hacemos las validaciones correspondientes
    
    
    // validaciones 
    
    
    
    if ($curp == "") {
    $nombre=$nombres. " ".$apellidoPaterno." ". $apellidoMaterno;
    $existePersona =ValidarExistencia::existenciaPersona("",$nombre);
    }
    else{
      $existePersona =ValidarExistencia::existenciaPersona($curp, "") ;
    }
    $validarCentroTrabajo=ValidarExistencia::existenciaCentroTrabajo($claveCct);
    $validarExistenciaBoleta= ValidarExistencia::existenciaBoleta($folio);
    $validarNivelEscolar=ValidarExistencia::existenciaNivel($nivel);
    $validarCicloEscolar=ValidarExistencia::existenciaCiclo($cicloId);
    $validarPlanEstudio=ValidarExistencia::existenciaPlanEstudio($planEstudioId);
    $existenciaTurno=ValidarExistencia::existenciaTurno($turno);
    $existeBoletaByFolio=ValidarExistencia::existenciaBoleta($folio);

    // validamos el tamaño de la curp que no sea mayor que 18 digitos
    if (strlen($curp) > 18) {
      FuncionesExtras::enviarRespuesta(true,true,'La CURP no puede tener mas de 18 caracteres','');
    }
    
    
    if($validarNivelEscolar != false && $validarCicloEscolar != false && $validarPlanEstudio != false && $existenciaTurno !=false && !$existeBoletaByFolio){
      $idNivel=$validarNivelEscolar['id_nivel'];
      $idCiclo=$validarCicloEscolar['id_ciclo'];
      $idPlanEstudio=$validarPlanEstudio['id_plan_estudio'];
      $idTurno=$existenciaTurno['id_turno'];
      
          $dataPersona=["nombre"=>$nombres,"apellidoP"=>$apellidoPaterno,"apellidoM"=>$apellidoMaterno,"curp"=>$curp ];
          $dataCt=["cct"=>$claveCct,"nombreCct"=>$cctNombre,"zona"=>$zonaEscolar,"nivel"=>$idNivel, "localidad"=>$localidad];
          $dataBoleta=[];
      
  
      //   si existe el centro de trabajo obtenemos su id
      if($validarCentroTrabajo == null){
        $insertarCt=Modelo::insertarCentroTrabajo($dataCt);
          $idCct=$insertarCt;
        }else{
          $idCct=$validarCentroTrabajo['id_centro_trabajo'];
          // FuncionesExtras::enviarRespuesta(false,true,'',$idCct);
        }
    }
    else{
      FuncionesExtras::enviarRespuesta(true,true,'boleta con folio: ' . $folio . ' con datos invalidos o ya se encuentra registrada','');
    }
    
    
      
      // verificamos si la curp es diferente de vacia y si  ya esta registrada la persona 
      if ($existePersona != null) {
              $idPersona=$existePersona['id_persona'];    
      }
      // si la variable idPersona aun sigue vacia es por que la persona no existe y procedemos a insertarla
    else {
        $insertarPersona=Modelo::insertarPersona($dataPersona);
        $insertoPersona=true;
        $idPersona=$insertarPersona;
    }

    
    
    
    
    
    
    $dataBoleta=["idPersona"=>$idPersona,"idNivel"=>$idNivel,"idPlan"=>$idPlanEstudio,"idCct"=>$idCct,"idCiclo"=>$idCiclo, "folio"=>$folio, "grupo"=>$grupo, "idTurno"=>$idTurno , "directorCorrespondiente"=>$directorCorrespondiente, "capturador" => $capturadoPor];
    
    // verificamos que la persona no tenga otra boleta cargada que pertenesca al mismo nivel
    
    // FuncionesExtras::enviarRespuesta(true,true,'aqui llego','');

    $numeroBoletas=ValidarExistencia::existenciaBoletaPersona($idPersona,$idNivel);
    if ($numeroBoletas == 0 && !$existeBoletaByFolio && $insertoPersona) {
        $insertBoleta=Modelo::insertarBoleta($dataBoleta);
        $idBoleta=$insertBoleta;

        // FuncionesExtras::enviarRespuesta(false,true,"Boleta agregada Correctamente", $insertBoleta);
      }
      else{
        // delete::deletePersona($idPersona);
        FuncionesExtras::enviarRespuesta(true,true,"boleta con folio: ".$folio.' con datos invalidos o ya se encuentra registrada' , '');
      }


      //  ---------------------------------- aqui van las calificaciones de primaria ------------------------------


      // insertamos las calificaciones en caso de que el nivel sea de primaria
      if ($idNivel == 1) {
        // obtenemos los id de las materias y generamos el arreglo para pasarlo
        $materias=array();
        for ($i=0; $i <count($calPrimaria) ; $i++) { 
          $conAcentos=array("Á", "É", "Í", "Ó", "Ú", "ñ","á", "é", "í", "ó", "ú");
          $sinAcentos=array("A", "E", "I", "O", "U", "Ñ", "A", "E", "I", "O", "U");
          $materia=strtoupper($calPrimaria[$i]->materia);
          $materiaSinAcentos = str_replace($conAcentos,$sinAcentos, $materia);
          $materiaSinGuion= str_replace('_'," ", $materiaSinAcentos);
          $materia=getData::getIdMateria($materiaSinGuion, $idPlanEstudio);
          $materias[]=["nombreMateria" => $materia['nombre_materia'] ,"id_materia" => $materia['id_catalogo_materia_plan'], "calificacion"=>$calPrimaria[$i]->calificacion];
          // $materias[]=$materiaSinGuion;
        }        
        // FuncionesExtras::enviarRespuesta(false, true,'',$materias);
        // FuncionesExtras::enviarRespuesta(true,true,'',$materias);
          
        $agregarCalificacionesPrimaria=Modelo::insertarCalificacionesPrimaria($materias, $idBoleta,2);
        
        
        // $insertarCalificaciones=Modelo::insertarCalificacionesPrimaria($calPrimaria,$idBoleta);
        FuncionesExtras::enviarRespuesta(false,true,"Boleta con folio: $folio  agregada Correctamente", "");
    }
    elseif ($idNivel==2) {
        $insertarCalificaciones=Modelo::insertarCalificacionesSecundaria($calSecundaria,$idBoleta);
        FuncionesExtras::enviarRespuesta(false,true,"boleta con folio: $folio agregada correctamente ", '');
        
    }

    } 
catch (Exception $e) {
  // si ocurre un error 
  
  FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
  FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}

// if ($insertoPersona) {
// // eliminamos las calificaciones del id de la persona que se inserto
// if ($idBoleta != "" || $idBoleta != null) {
//   delete::deleteCalificacionesPrimariaBoleta($idBoleta);
//   delete::deleteBoleta($idBoleta);
//   delete::deletePersona($idPersona);
// }
// }