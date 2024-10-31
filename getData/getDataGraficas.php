<?php
class GetDataGraficasAvance
{
    static public function avancePorSemana($year)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getAvanceSemanal = $pdo->query("SELECT 
  CONCAT('Semana numero ', TO_CHAR(fecha_registro, 'IW')) AS periodo,
  COUNT(id_boleta) AS total_boletas
FROM 
  boletas
WHERE 
  TO_CHAR(fecha_registro, 'IYYY') = '2024'
GROUP BY 
  TO_CHAR(fecha_registro, 'IW')
ORDER BY 
  TO_CHAR(fecha_registro, 'IW')::integer ASC;");

           $avanceSemanal= FuncionesExtras::GenerarArrayAssoc($getAvanceSemanal);
            return $avanceSemanal;
    }



    static public function avancePorMes($year)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getAvanceMensual = $pdo->query("SELECT TO_CHAR(fecha_registro, 'YYYY-MM') AS periodo, COUNT(id_boleta) AS total_boletas
        FROM boletas
        WHERE SUBSTRING(fecha_registro::text, 1, 4) = '$year'
        GROUP BY TO_CHAR(fecha_registro, 'YYYY-MM')
        ORDER BY periodo ASC;");
        $avanceMensual=FuncionesExtras::GenerarArrayAssoc($getAvanceMensual);
        return $avanceMensual;
    }



    static public function avancePorYear($year)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getAvanceYear = $pdo->query("SELECT TO_CHAR(fecha_registro, 'YYYY') AS periodo, COUNT(id_boleta) AS total_boletas
        FROM boletas
         WHERE SUBSTRING(b.fecha_registro::text, 1, 4) = '$year'
        GROUP BY TO_CHAR(fecha_registro, 'YYYY')
        ORDER BY periodo ASC;");
        $avanceYear=FuncionesExtras::GenerarArrayAssoc($getAvanceYear);
        return $avanceYear;
    }



    static public function DesempeñoCapturadores($year)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getAvanceCapturadores = $pdo->query("SELECT count(*) as total_boletas, concat(nombre, ' ', apellido_paterno, ' ', apellido_materno) as capturador 
        from boletas b
        join usuarios u on b.capturado_por=u.id_usuario
        join personas p on u.persona_id=p.id_persona
        WHERE SUBSTRING(b.fecha_registro::text, 1, 4) = '$year'
        group by capturador");
        $avanceCapturadores=FuncionesExtras::GenerarArrayAssoc($getAvanceCapturadores);
        return $avanceCapturadores;
    }



    static public function avanceDeVerificacion($year)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getAvanceRevision = $pdo->query("SELECT COUNT(id_boleta) AS total_boletas, estado
        FROM boletas b
        JOIN estados e ON b.estado_id = e.id_estado
        WHERE SUBSTRING(fecha_registro::text, 1, 4) = '$year'
        GROUP BY estado;");
        $avanceRevision=FuncionesExtras::GenerarArrayAssoc($getAvanceRevision);
        return $avanceRevision;
    }

    static public  function contarBoletas(){
       $pdo = Conexion::getDatabaseConnection();
        $contarBoletas = $pdo->query("SELECT COUNT(*) as total_boletas FROM boletas");
        $totalBoletas=FuncionesExtras::GenerarArrayAssoc($contarBoletas);
        return $totalBoletas[0]['total_boletas'];
    }

}
