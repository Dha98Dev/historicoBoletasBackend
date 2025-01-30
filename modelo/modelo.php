<?php
require __DIR__ . '/conexion.php';
require __DIR__ . '/../funcionesExtras/funcionesExtras.php';

class Modelo
{
    static public function insertarMateria($materia){
        $pdo=Conexion::getDatabaseConnection();
        $insertarMateria= $pdo->query("insert into materias(nombre_materia) values('$materia') RETURNING  id_materia");
        if ($insertarMateria) {
        return $insertarMateria->fetch(PDO::FETCH_ASSOC)['id_materia'];
        }
        $pdo=null;
        return false;
    }
    
    static public function insertarPersona($persona){
        $pdo=Conexion::getDatabaseConnection();
        $hoy=date('Y-m-d');
        $insertarPersona= $pdo->query("insert into personas(nombre,apellido_paterno, apellido_materno, curp, fecha_registro) values('". $persona['nombre'] ."', '". $persona['apellidoP'] ."', '". $persona['apellidoM'] ."', '". $persona['curp'] ."', '$hoy') returning id_persona ");
        if($insertarPersona) {
            $id_persona = $insertarPersona->fetch(PDO::FETCH_ASSOC)['id_persona'];
            $pdo=null;
            return $id_persona;
        }
        $pdo=null;
        return 0;

    }

    static public function insertarPlanEstudio($planEstudio, $inicio, $fin){
        $pdo=Conexion::getDatabaseConnection();
        $camposAInsertar='';
         $valores="('$planEstudio', '$inicio', '$fin')";
        if ($inicio != null && $fin != null) {
            $camposAInsertar="(nombre_plan_estudio, periodo_inicio, periodo_fin)";
            $valores="('$planEstudio', '$inicio', '$fin')";
        }else{
            $camposAInsertar="(nombre_plan_estudio)";
            $valores="('$planEstudio')";
        }

        $insertarPlanEstudio=$pdo->query("insert into planes_estudios $camposAInsertar values $valores returning id_plan_estudio");

        if ($insertarPlanEstudio) {
            $id_plan_estudio = $insertarPlanEstudio->fetch(PDO::FETCH_ASSOC)['id_plan_estudio'];
            $pdo=null;
            return $id_plan_estudio;
        }
        $pdo=null;
        return 0;
    }

    static public function insertarCentroTrabajo($centroTrabajo) {
        $pdo = Conexion::getDatabaseConnection();
    
        // Preparar la consulta SQL con placeholders
        $sql = "INSERT INTO centros_trabajos (clave_centro_trabajo, nombre_cct, zona_escolar, nivel_escolar_id, localidad)
                VALUES (:cct, :nombreCct, :zona, :nivel, :localidad)
                RETURNING id_centro_trabajo";
    
        // Preparar la declaración (statement)
        $stmt = $pdo->prepare($sql);
    
        // Ejecutar la declaración con los valores pasados en un array asociativo
        $stmt->execute([
            ':cct' => $centroTrabajo['cct'],
            ':nombreCct' => $centroTrabajo['nombreCct'],
            ':zona' => $centroTrabajo['zona'],
            ':nivel' => $centroTrabajo['nivel'],
            ':localidad' => $centroTrabajo['localidad']
        ]);
    
        // Obtener el id_centro_trabajo retornado
        $id_centro_trabajo = $stmt->fetch(PDO::FETCH_ASSOC)['id_centro_trabajo'];
    
        return $id_centro_trabajo;
    }
    
    static public function insertarBoleta($dataBoleta){
        $insertAgregacion="";
        $valorAgregacion="";
        if ($dataBoleta['directorCorrespondiente']) {
            $insertAgregacion=", autorizo_director";
            $valorAgregacion=" , '".$dataBoleta['directorCorrespondiente']."' ";
        }
        $pdo=Conexion::getDatabaseConnection();
        $insertBoleta = $pdo->query("insert into boletas (persona_id, nivel_id, plan_estudio_id, centro_trabajo_id, ciclo_escolar_id, folio, grupo, fecha_registro, turno_id ".$insertAgregacion.", capturado_por, estado_id, promedio) values ('".$dataBoleta['idPersona']."', '".$dataBoleta['idNivel']."', '".$dataBoleta['idPlan']."', '".$dataBoleta['idCct']."', '".$dataBoleta['idCiclo']."', '".$dataBoleta['folio']."', '".$dataBoleta['grupo']."', NOW(), '".$dataBoleta['idTurno']."' ".$valorAgregacion." , '".$dataBoleta['capturador']."', '3', ".$dataBoleta['promedio'].") returning id_boleta");

 if ($insertBoleta) {
    $id_boleta = $insertBoleta->fetch(PDO::FETCH_ASSOC)['id_boleta'];
    return $id_boleta;
 }
return null;
    }



    static public function insertarDirectorCct($cct, $persona, $cicloEscolarId){
        $pdo=Conexion::getDatabaseConnection();
        $insertarDirectorCct=$pdo->query("insert into directores_centro_trabajo (centro_trabajo_id, persona_id, ciclo_escolar_id)
        values ('$cct','$persona', '$cicloEscolarId') returning id_director_centro_trabajo");
        if ($insertarDirectorCct) {
        return true;
        }
        return false;
    }
    static public function insertarCalificacionesPrimaria($calificaciones, $idBoleta, $tipo){
        $pdo=Conexion::getDatabaseConnection();
        $completo=false;
   if($tipo==1){
    foreach ($calificaciones as $calificacion) {
        // Acceder a las propiedades del objeto
        $materiaId = Validaciones::desencriptar($calificacion->id_materia);
        $calificacionValor = $calificacion->calificacion;

        // Realizar la inserción
        $insertarCalificacion = $pdo->query("INSERT INTO calificaciones_primaria (catalogo_materia_plan_id, boleta_id, calificacion)
        VALUES ('".$materiaId."', '".$idBoleta."', '".$calificacionValor."') RETURNING id_calificacion_primaria");
        $completo = $insertarCalificacion->rowCount() > 0; // Verificamos si se insertó alguna fila
    }
   }
   else if($tipo == 2){
    for ($i=0; $i <count($calificaciones) ; $i++) { 
        $insertarCalificacion=$pdo->query("INSERT INTO calificaciones_primaria (catalogo_materia_plan_id, boleta_id, calificacion)
        VALUES ('".$calificaciones[$i]['id_materia']."', '".$idBoleta."', '".$calificaciones[$i]['calificacion']."') RETURNING id_calificacion_primaria");
    }
   }

        return $completo ? true : false;
    }
    static public function insertarCalificacionesSecundaria($calificaciones, $idBoleta){
        $pdo=Conexion::getDatabaseConnection();
        $insertarCalificaciones = $pdo->query(
            "INSERT INTO calificaciones_secundaria (calificacion_primero, calificacion_segundo, calificacion_tercero, promedio_final, boleta_id) 
            VALUES ('".$calificaciones->Primero."', '".$calificaciones->Segundo."', '".$calificaciones->Tercero."', '".$calificaciones->calificacionFinal."', '".$idBoleta."')"
        );
        if ($insertarCalificaciones) {
            return true;
        }
        return false;
    }

    static public function asignarMaterias($idPlanEstudio,$idMateria){
        $pdo=Conexion::getDatabaseConnection();
        $asignarMaterias=$pdo->query("insert into catalogo_materias_plan_estudio (plan_estudio_id, materia_id) values ('$idPlanEstudio','$idMateria')");
        if ($asignarMaterias) {
            return true;
        }
        return false;
    }
    static public function insertarUsuario($dataUser) {
        try {
            $pdo = Conexion::getDatabaseConnection();
            
            $sql = "INSERT INTO usuarios (usuario, clave, estado_id, persona_id, tipo_usuario_id) 
                    VALUES (:usuario, :clave, :estado_id, :persona_id, :tipo_usuario_id)";
            
            $stmt = $pdo->prepare($sql);
            
            // Asociamos los valores de manera segura con bindValue
            $stmt->bindValue(':usuario', $dataUser['usuario']);
            $stmt->bindValue(':clave', $dataUser['clave']);
            $stmt->bindValue(':estado_id', 1); // Se establece '1' directamente en bindValue
            $stmt->bindValue(':persona_id', $dataUser['id_persona']);
            $stmt->bindValue(':tipo_usuario_id', $dataUser['t_usuario']);
            
            // Ejecutamos la consulta
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    
}

