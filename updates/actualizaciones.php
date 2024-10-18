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
}
