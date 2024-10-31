<?php
require_once __DIR__. "./../../getData/getDataGraficas.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";

$params=FuncionesExtras::getJson();
$token=$params->token ?? "";
$year=$params->year ?? "";
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
        $year = date('Y');
        // este es el avance semanal del rendimiento de la captura
      $avanceSemanal=GetDataGraficasAvance::avancePorSemana($year);

    //   este es el avance mensual del rendimiento de la captura
      $avanceMensual=GetDataGraficasAvance::avancePorMes($year);
      
    //   esta es la informacion del rendimiento de las personas que estan capturando la informacion
        $rendimientoCapturistas=GetDataGraficasAvance::DesempeñoCapturadores($year);

        $avanceEstadosBoletas=GetDataGraficasAvance::avanceDeVerificacion($year);

    $totalBoletasRegistradas=GetDataGraficasAvance::contarBoletas();

      $data=["avanceSemanal" => $avanceSemanal, "avanceMensual"=>$avanceMensual,"avanceCapturistas"=>$rendimientoCapturistas, "estadosDeBoleta"=>$avanceEstadosBoletas, "totalBoletasRegistradas"=>$totalBoletasRegistradas];
      FuncionesExtras::enviarRespuesta(false, true, "informacion del avance", $data);
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}

// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}
