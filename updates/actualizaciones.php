<?php
require_once __DIR__ . '/../modelo/conexion.php';
class Actualizaciones
{
   static public function cambiarEstadoBoleta($boleta, $verificador){
    $pdo=Conexion::getDatabaseConnection();
    $updateEstado = $pdo->prepare("UPDATE boletas SET estado_id = (SELECT id_estado FROM estados WHERE estado = :estado), verificada_por = :verificador WHERE id_boleta = :boleta");

$estado = 'Revisado';
$updateEstado->bindParam(':estado', $estado);
$updateEstado->bindParam(':verificador', $verificador);
$updateEstado->bindParam(':boleta', $boleta);

$updateEstado->execute();

if ($updateEstado->rowCount()) {
    return true;
}

return false;
   }
   static public function updateDomicilio($dom){
    $pdo = Conexion::getDatabaseConnection();

    // Consulta preparada con placeholders para prevenir inyección SQL
    $updateDomicilio = $pdo->prepare("
        UPDATE personas p
        SET domicilio_particular = :domicilio,
            municipio = :municipio,
            localidad = :localidad,
            telefono = :telefono
        FROM boletas b
        WHERE p.id_persona = (
            SELECT b.persona_id 
            FROM boletas b
            WHERE b.id_boleta = :boleta
        )
    ");

    // Vinculación de parámetros
    $updateDomicilio->bindParam(':domicilio', $dom['domicilio'], PDO::PARAM_STR);
    $updateDomicilio->bindParam(':municipio', $dom['municipio'], PDO::PARAM_STR);
    $updateDomicilio->bindParam(':localidad', $dom['localidad'], PDO::PARAM_STR);
    $updateDomicilio->bindParam(':telefono', $dom['telefono'], PDO::PARAM_STR);
    $updateDomicilio->bindParam(':boleta', $dom['boleta'], PDO::PARAM_INT);

    // Ejecutar la consulta
    $updateDomicilio->execute();

    // Verificar si se actualizó alguna fila
    if ($updateDomicilio->rowCount()) {
        return true;
    }

    return false;
}
// Función para actualizar persona
static public function updatePersona($dataPersona, $idBoleta) {
    $pdo = Conexion::getDatabaseConnection();
    $updateBoletaPersona = $pdo->prepare("
        UPDATE personas 
        SET nombre = :nombre,
            apellido_paterno = :apellido_paterno,
            apellido_materno = :apellido_materno,
            curp = :curp
        WHERE id_persona = (
            SELECT persona_id 
            FROM boletas 
            WHERE id_boleta = :id_boleta
        )
    ");
    // Vinculación de parámetros
    $updateBoletaPersona->bindParam(':nombre', $dataPersona['nombre'], PDO::PARAM_STR);
    $updateBoletaPersona->bindParam(':apellido_paterno', $dataPersona['apellidoPaterno'], PDO::PARAM_STR);
    $updateBoletaPersona->bindParam(':apellido_materno', $dataPersona['apellidoMaterno'], PDO::PARAM_STR);
    $updateBoletaPersona->bindParam(':curp', $dataPersona['curp'], PDO::PARAM_STR);
    $updateBoletaPersona->bindParam(':id_boleta', $idBoleta, PDO::PARAM_INT);
    $updateBoletaPersona->execute();
    if ($updateBoletaPersona) {
        return true;
    }
    return false;
}

// Función para actualizar calificaciones de secundaria
static public function UpdateCalificacionesSecundaria($cal, $idBoleta) {
    $pdo = Conexion::getDatabaseConnection();
    $updated=true;
    if ($cal[0]->Primero != 0) {
        $updateCalificaciones = $pdo->prepare("
            UPDATE calificaciones_secundaria 
            SET calificacion_primero = :primero,
                calificacion_segundo = :segundo,
                calificacion_tercero = :tercero,
                promedio_final = :promedio_final
            WHERE boleta_id = :boleta_id
        ");
        // Vinculación de parámetros
        $updateCalificaciones->bindParam(':primero', $cal->Primero, PDO::PARAM_STR);
        $updateCalificaciones->bindParam(':segundo', $cal->Segundo, PDO::PARAM_STR);
        $updateCalificaciones->bindParam(':tercero', $cal->Tercero, PDO::PARAM_STR);
        $updateCalificaciones->bindParam(':promedio_final', $cal->calificacionFinal, PDO::PARAM_STR);
        $updateCalificaciones->bindParam(':boleta_id', $idBoleta, PDO::PARAM_INT);
        $updateCalificaciones->execute();
        $updated= $updateCalificaciones ? true : false;
    }
    return $updated;
}

// Función para actualizar calificaciones de primaria
static public function UpdateCalificacionesPrimaria($cal) {
    $pdo = Conexion::getDatabaseConnection();
    $updated=true;
    foreach ($cal as $c) {
        if (count($cal) > 0) {
            $updateCalificaciones = $pdo->query("
                UPDATE calificaciones_primaria 
                SET calificacion =$c->calificacion 
                WHERE id_calificacion_primaria= $c->id_calificacion_primaria returning calificacion");

                    $updated= $updateCalificaciones  ? true : false;
        }
    }
    return $updated;
}

// Función para actualizar el centro de trabajo
static public function UpdateCt($ct) {
    $pdo = Conexion::getDatabaseConnection();
    $updateCt = $pdo->prepare("
        UPDATE centros_trabajos 
        SET clave_centro_trabajo = :clave_centro_trabajo,
            nombre_cct = :nombre_cct,
            zona_escolar = :zona_escolar,
            nivel_escolar_id = (
                SELECT id_nivel 
                FROM niveles 
                WHERE nivel = :nivel
                ),
                localidad = :localidad
        WHERE id_centro_trabajo = :idCt
    ");
    // Vinculación de parámetros
    $updateCt->bindParam(':clave_centro_trabajo', $ct['cct'], PDO::PARAM_STR);
    $updateCt->bindParam(':nombre_cct', $ct['nombreCt'], PDO::PARAM_STR);
    $updateCt->bindParam(':zona_escolar', $ct['zona'], PDO::PARAM_STR);
    $updateCt->bindParam(':nivel', $ct['nivel'], PDO::PARAM_STR);
    $updateCt->bindParam(':idCt', $ct['idCt'], PDO::PARAM_INT);
    $updateCt->bindParam(':localidad', $ct['localidad'], PDO::PARAM_STR);
    $updateCt->execute();
if ($updateCt) {
    return true;
}
return false;
}

static public function updateDatosBoleta($dataBoleta){
    $pdo=Conexion::getDatabaseConnection();
    $updateBoleta=$pdo->prepare("update boletas set folio=':folio',ciclo_escolar_id=(select id_ciclo from ciclos_escolares where ciclo=':ciclo') , turno_id=(select id_turno from turnos where turno=':turno'), grupo=':grupo' where id_boleta=':id_boleta'");

    $updateBoleta->bindParam('folio',$dataBoleta['folio'], PDO::PARAM_STR);
    $updateBoleta->bindParam(':ciclo',$dataBoleta['ciclo']);
    $updateBoleta->bindParam(':turno',$dataBoleta['turno']);
    $updateBoleta->bindParam(':grupo',$dataBoleta['grupo']);
    $updateBoleta->bindParam(':id_boleta',$dataBoleta['idBoleta']);
    $updateBoleta->execute();
    if($updateBoleta){
        return true;
    }
    return false;

}

static public function updateEstadoUsuario($estado, $usuario){
    $pdo=Conexion::getDatabaseConnection();
    $updateEstadoUsuario= $pdo ->query("update usuarios set estado_id=(select id_estado from estados where estado='$estado') where id_usuario='$usuario'");
    if($updateEstadoUsuario){
        return true;
    }
    return false;
}

}
