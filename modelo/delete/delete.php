<?php
require_once __DIR__ . '/../conexion.php';

class Delete {
    static public  function deletePersona($idPersona){
        if ($idPersona !='') {
            $pdo=Conexion::getDatabaseConnection();
            $deletePersona = $pdo -> prepare('DELETE FROM personas where id_persona = :persona_id');
            $deletePersona->bindParam('persona_id',$idPersona);
            $deletePersona->execute();
            if($deletePersona->rowCount()){
                return true;
            }
            return false;
        }
    }

    
    static public function deleteCalificacionesPrimariaBoleta($boletaID){
if ($boletaID != "") {
    $pdo=Conexion::getDatabaseConnection();
    $deleteCalificacionesBoleta=$pdo->prepare('DELETE FROM calificaciones_primaria where boleta_id = :boleta_id');
    $deleteCalificacionesBoleta->bindParam('boleta_id',$boletaID);
    $deleteCalificacionesBoleta->execute();
    if($deleteCalificacionesBoleta->rowCount()){
        return true;
    }
    return false;
}
    }  


    static public function deleteBoleta($idBoleta){
if ($idBoleta != "") {
    $pdo=Conexion::getDatabaseConnection();
    $deleteBoleta = $pdo->prepare('DELETE FROM BOLETAS WHERE ID_BOLETA = :idBoleta');
    $deleteBoleta->bindParam('idBoleta',$idBoleta);
    $deleteBoleta->execute();
    if($deleteBoleta->rowCount()){
        return true;
    }
    return false;
}
    }
}

?>