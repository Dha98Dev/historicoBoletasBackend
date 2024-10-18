<?php
require_once __DIR__.'/../modelo/conexion.php';
class  GetInfoRecuperarPassword{
    static public function getUsuario($usuario){
    $pdo=getDatabaseConnection();
    $existe=$pdo->query("select * from  usuarios where usuario='$usuario'");
    $datosUsuario=$existe->fetch(PDO::FETCH_ASSOC);
    if($datosUsuario){
        return $datosUsuario;
    }else{
        return false;
    }
    }

    static public function getCodigoGuardado($usuario){
        $pdo=getDatabaseConnection();
        $codigoGuardado=$pdo->query("select  codigo_verificacion from usuarios where usuario='$usuario'");
        $codigo=$codigoGuardado->fetch(PDO::FETCH_ASSOC);
        return $codigo;
    }
}