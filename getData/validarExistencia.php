<?php
class ValidarExistencia{
    static public function existenciaPersona($curp, $nombre){
       $pdo=Conexion::getDatabaseConnection();
       if ($curp == "") {
        $clausulaWhere="CONCAT(nombre,' ', apellido_paterno,' ', apellido_materno) = '$nombre'";
       }else{
        $clausulaWhere="curp = '$curp' ";
       }
       $existePersona= $pdo->query("select * from personas where $clausulaWhere"); 
       if($existePersona->rowCount() > 0){
           return $existePersona->fetch(PDO::FETCH_ASSOC);

       }
       return null;
    }

    static public function existenciaCentroTrabajo($cct){
        $pdo=Conexion::getDatabaseConnection();
        $existeCt= $pdo->query("select * from centros_trabajos where clave_centro_trabajo='$cct'");
        if($existeCt->rowCount() > 0){
            return $existeCt->fetch(PDO::FETCH_ASSOC);
        }
        else{
            return null;
        }
    }

    static public function existenciaDirectorCt($cct, $persona){
        $pdo=Conexion::getDatabaseConnection();
        $existeDirector= $pdo->query("select count(id_director_centro_trabajo) as existe from directores_centro_trabajo dct
        join personas p on dct.persona_id=p.id_persona
        join centros_trabajos ct on dct.centro_trabajo_id=ct.id_centro_trabajo
        where persona_id='$persona' and centro_trabajo_id='$cct'");
        $existe=$existeDirector->fetch(PDO::FETCH_ASSOC);
        return $existe['existe'];
    }
    static public function existenciaMateria($materia){
        $pdo=Conexion::getDatabaseConnection();
        $existeMaterias= $pdo->query("select * from materias where nombre_materia = '$materia'");
        if($existeMaterias->rowCount() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    static public function existenciaBoletaPersona($idPersona, $nivel){
        $pdo=Conexion::getDatabaseConnection();
        $existeBoleta=$pdo->query("select count(id_boleta) as  num_boletas from boletas where persona_id ='$idPersona' and nivel_id='$nivel'");
        $existe=$existeBoleta->fetch(PDO::FETCH_ASSOC);
        return $existe['num_boletas'];
    }

    static public function existenciaUsuario($usuario){
        $pdo=Conexion::getDatabaseConnection();
        $existeUsuario= $pdo->query("select * from usuarios where usuario='$usuario'");
        if($existeUsuario->rowCount() > 0){
            return $existeUsuario->fetch(PDO::FETCH_ASSOC);
        }
        else{
            return null;
        }
    }

// metodo para obtener la informacion de una boleta en especifico para saber si ya existe 
    static public function existenciaBoleta($folio){
        $pdo=Conexion::getDatabaseConnection();
        $existeBoleta=$pdo->prepare("select * from boletas where folio= :folio");
        $existeBoleta->bindValue(':folio', $folio);
        $existeBoleta->execute();
        if($existeBoleta->fetch(PDO::FETCH_ASSOC)){
            return true;
        }
        return false;

    }
// metodo creado inicialmente para poder obtener el id del nivel
    static public function existenciaNivel($nivel){
        $pdo=Conexion::getDatabaseConnection();
        $existeNivel= $pdo->prepare("select * from  niveles where nivel= :nivel");
        $existeNivel->bindValue(':nivel', $nivel);
        $existeNivel->execute();
        if($existeNivel->rowCount() > 0){
            return $existeNivel->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    static public function existenciaCiclo($ciclo){
        $pdo=Conexion::getDatabaseConnection();
        $existenciaCiclo=$pdo->prepare("select * from ciclos_escolares where ciclo=:ciclo");+
        $existenciaCiclo->bindValue(':ciclo', $ciclo);
        $existenciaCiclo->execute();
        if($existenciaCiclo->rowCount() > 0){
            return  $existenciaCiclo=$existenciaCiclo->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    static public function existenciaPlanEstudio($planEstudio){
        $pdo=Conexion::getDatabaseConnection();
        $existenciaPlanEstudio=$pdo->prepare("select * from planes_estudios where nombre_plan_estudio=:planEstudio");
        $existenciaPlanEstudio->bindValue(':planEstudio', $planEstudio);
        $existenciaPlanEstudio->execute();
        if($existenciaPlanEstudio->rowCount() > 0){
            return $existenciaPlanEstudio=$existenciaPlanEstudio->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    static public function existenciaTurno($turno){
        $pdo=Conexion::getDatabaseConnection();
        $existenciaTurno=$pdo->prepare("select * from turnos  where turno = :turno");
        $existenciaTurno->bindValue(':turno', $turno);
        $existenciaTurno->execute();
        if($existenciaTurno->rowCount() > 0){
            return $existenciaTurno=$existenciaTurno->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }


}
?>