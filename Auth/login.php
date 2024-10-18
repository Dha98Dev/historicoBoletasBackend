<?php
require_once __DIR__. "/../getData/getData.php";
require_once __DIR__."/../validaciones/validaciones.php";
require_once __DIR__."/../modelo/modelo.php";

$pdo=Conexion::getDatabaseConnection();
$params=FuncionesExtras::getJson();
$usuario=$params->usuario;
$password=$params->password;
$password=Validaciones::encriptar($password);
try {
      $sql= $pdo->prepare("select usuario,id_usuario,tipo_usuario_id as fk_tipo_usuario, estado   from usuarios inner join estados on usuarios.estado_id = estados.id_estado where usuario=? and clave =? and estado= 'activo'");
    $sql->bindParam(1,$usuario);
    $sql->bindParam(2,$password);
    $sql->execute();

    $datos=$sql->fetch(PDO::FETCH_ASSOC); 
    if ($sql &&  !empty($datos)) {
        $tipoUsuario=$datos['fk_tipo_usuario'] != "" ? $datos['fk_tipo_usuario'] : "";
        // $datos["vigencia"]=1724949099;
        $datos["vigencia"]=(time()+604800);
       
         if($tipoUsuario == 1 || $tipoUsuario == 2 || $tipoUsuario == 3){
            $token=Validaciones::generarToken($datos);
            $datos["token"]=$token;
            $insertarToken= $pdo->query("update usuarios set token = '$token' where id_usuario= '".$datos['id_usuario']."' ");
    FuncionesExtras::enviarRespuesta(false,true,"Sesion iniciada Correctamente",$datos);
            
        }
    }
    else{
    FuncionesExtras::enviarRespuesta(true,false,"el usuario o la contraseña es incorrecto","");

    }


} catch (Exception $e) {
    FuncionesExtras::enviarRespuesta(true,false,"el usuario o la contraseña es incorrecto","");

}
