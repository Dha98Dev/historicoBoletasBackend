<?php
require_once __DIR__. "./../../getData/getData.php";
require_once __DIR__."/../../validaciones/validaciones.php";
require_once __DIR__."/../../modelo/modelo.php";
include __DIR__."/../../funcionesExtras/cors.php";

$params=FuncionesExtras::getJson();
$cct = Validaciones::limpiarCadena($params->cct) ?? "";
$ciclo = Validaciones::limpiarCadena(Validaciones::desencriptar($params->idCiclo)) ?? "";
$curp = Validaciones::limpiarCadena($params->curp) ?? "";
$folio = Validaciones::limpiarCadena($params->folio) ?? "";
$nombre = Validaciones::limpiarCadena($params->nombre) ?? "";
$numeroFiltro=Validaciones::limpiarCadena($params->numeroFiltro) ?? "";
$localidad=Validaciones::limpiarCadena($params->localidad) ?? "";
$estadoFiltro= Validaciones::limpiarCadena($params->estado )?? "" ;
$boleta=Validaciones::limpiarCadena(Validaciones::desencriptar($params->boleta)) ?? "";

$clausulaWhere="";


if ($estado =! "") {
    $clausulaWhere="estado = '$estadoFiltro'";
}


if ($numeroFiltro == '1') {
  $clausulaWhere="p.curp ='$curp'";
}
else if($numeroFiltro == '2') {
  $clausulaWhere="id_ciclo='$ciclo' and clave_centro_trabajo='$cct'";
}
else if ($numeroFiltro == '3') {
$clausulaWhere="folio = '$folio' ";
}
else if($numeroFiltro == '4'){
  $clausulaWhere="CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) LIKE '%$nombre%' and clave_centro_trabajo = '$cct'";
}
else if($numeroFiltro == '5'){
    $clausulaWhere="CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) LIKE '%$nombre%' ";
  }
else if($numeroFiltro == '6'){
    $clausulaWhere="ct.localidad LIKE '%$localidad%'";
}else if($numeroFiltro == '7'){
    $clausulaWhere="
     id_boleta='$boleta'";
}

$token=$params->token;
$isValidToken = Validaciones::validarToken($token);
if ($token != "" && $isValidToken['valido']) {
    try 
    {
      $data=getData::getCalificaciones($clausulaWhere);
      if ($data) {

        $data=FuncionesExtras::encriptarIdentificadores(['id_boleta', 'id_ciclo','id_centro_trabajo', 'id_calificacion_primaria','id_calificacion_secundaria'], $data);

        $calificacionesPrimaria = [];
        $calificacionesSecundaria = [];
        
        $personas = [];

        foreach ($data as $item) {
            // Buscamos si ya existe una persona con el mismo id_boleta
            $found = false;
            foreach ($personas as &$persona) {
                if ($persona['id_boleta'] === $item['id_boleta']) {
                    // Si la persona ya existe, agregamos las calificaciones
                    if ($item['nivel'] === 'PRIMARIA') {
                        $persona['calificacionesPrimaria'][] = [
                            'nombre_materia' => $item['nombre_materia'],
                            'calificacion' => $item['calificacion'],
                            'id_calificacion_primaria' => $item['id_calificacion_primaria']
                        ];
                    } elseif ($item['nivel'] === 'SECUNDARIA') {
                        $persona['calificacionesSecundaria'] = [
                            'Primero' => $item['calificacion_primero'] ?: null,
                            'Segundo' => $item['calificacion_segundo'] ?: null,
                            'Tercero' => $item['calificacion_tercero'] ?: null,
                            'calificacionFinal' => $item['promedio_final'] ?: null
                        ];
                    }
                    $found = true;
                    break;
                }
            }
            
            // Si no se encontró a la persona, la agregamos al arreglo
            if (!$found) {
                $nuevaPersona = [
                    'id_boleta' => $item['id_boleta'],
                    'nombre' => $item['nombre'],
                    'apellido_paterno' => $item['apellido_paterno'],
                    'apellido_materno' => $item['apellido_materno'],
                    'curp'=>$item['curp'],
                    'capturado_por' => $item['capturado_por'],
                    'nivel' => $item['nivel'],
                    'plan_estudio' => $item['nombre_plan_estudio'],
                    'ciclo' => $item['ciclo'],
                    'clave_centro_trabajo' => $item['clave_centro_trabajo'],
                    'nombre_cct' => html_entity_decode($item['nombre_cct']),
                    'folio' => $item['folio'],
                    'grupo' => $item['grupo'],
                    'turno' => $item['turno'],
                    'id_ct'=>$item['id_centro_trabajo'],
                    'fecha_registro_boleta' => $item['fecha_registro_boleta'],
                    'verificado'=> $item['verificado'],
                    'localidad' => $item['localidad'],
                    'localidad_dom'=>$item['localidad_dom'],
                    'municipio_dom'=>$item['municipio_dom'],
                    'domicilio_particular'=>$item['domicilio_particular'],
                    'telefono'=>$item['telefono'],
                    'zona' => $item['zona_escolar'],
                    'estado_boleta' => $item['estado_boleta'],
                    'director_ct'=> $item['director_ct'],
                    'boletaSolicitudServicio' =>FuncionesExtras::codificarUrl($item['id_boleta']),
                    'calificacionesPrimaria' => [],
                    'calificacionesSecundaria' => []
                ];
        
                // Agregamos las calificaciones según el nivel
                if ($item['nivel'] === 'PRIMARIA') {
                    $nuevaPersona['calificacionesPrimaria'][] = [
                        'nombre_materia' => $item['nombre_materia'],
                        'calificacion' => $item['calificacion'],
                        'id_calificacion_primaria' => $item['id_calificacion_primaria']
                    ];
                } elseif ($item['nivel'] === 'SECUNDARIA') {
                    $nuevaPersona['calificacionesSecundaria'] = [
                        'Primero' => $item['calificacion_primero'] ?: null,
                        'Segundo' => $item['calificacion_segundo'] ?: null,
                        'Tercero' => $item['calificacion_tercero'] ?: null,
                        'calificacionFinal' => $item['promedio_final'] ?: null
                    ];
                }
        
                // Agregamos la nueva persona al arreglo
                $personas[] = $nuevaPersona;
            }
        }
        
        
        FuncionesExtras::enviarRespuesta(false,true,"resultado de la consulta que realizo",$personas);
      }
      
      else{
      FuncionesExtras::enviarRespuesta(true,true,"no hay registros aun ",'');

      }
    } 
catch (Exception $e) {
FuncionesExtras::enviarRespuesta(true,true,$e->getMessage(), "");
}
 
// //aqui termina el if de la validacion del token, caso de que no sea valido 
} else {
    FuncionesExtras::enviarRespuesta(true,false,"Necesita iniciar sesion de nuevo", "");
}



