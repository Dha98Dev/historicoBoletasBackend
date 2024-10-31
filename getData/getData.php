<?php
// require_once __DIR__ . '/../modelo/conexion.php';
// require_once __DIR__ . '/../getData/getData.php';
class getData
{
    static public function getMateriasPlan($idPlanEstudio)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getMaterias = $pdo->query(" select id_materia as valor, nombre_materia as nombre, id_plan_estudio, nombre_plan_estudio from catalogo_materias_plan_estudio cmpe
        join materias m on cmpe.materia_id=m.id_materia 
        join planes_estudios pe on cmpe.plan_estudio_id=pe.id_plan_estudio where id_plan_estudio='$idPlanEstudio'  order by nombre_materia asc");
        $materiasPlan = FuncionesExtras::GenerarArrayAssoc($getMaterias);
        return $materiasPlan;
    }

    static public function getMaterias()
    {
        $pdo = Conexion::getDatabaseConnection();
        $materias = $pdo->query("select id_materia as valor, nombre_materia as nombre from materias ");
        $materias = FuncionesExtras::GenerarArrayAssoc($materias);
        return $materias;
    }
    static public function getPlanesEstudios()
    {
        $pdo = Conexion::getDatabaseConnection();
        $planesEstudios = $pdo->query("SELECT count(id_catalogo_materia_plan) as numero_materias,
        CONCAT(SUBSTRING(periodo_inicio::text, 1, 4), '-',  SUBSTRING(periodo_fin::text, 1, 4)) as periodo_aplicacion, id_plan_estudio AS valor,
        CONCAT(nombre_plan_estudio, ' ', SUBSTRING(periodo_inicio::text, 1, 4)) AS nombre 
        FROM planes_estudios pe
        left join catalogo_materias_plan_estudio cmpe on pe.id_plan_estudio=cmpe.plan_estudio_id
        group by id_plan_estudio
        ORDER BY nombre_plan_estudio ASC;");
        $planes = FuncionesExtras::GenerarArrayAssoc($planesEstudios);
        return $planes;
    }
    static public function getNivelesEducativos()
    {
        $pdo = Conexion::getDatabaseConnection();
        $nivelesEducativos = $pdo->query("select id_nivel as valor, nivel as nombre from niveles");
        $niveles = FuncionesExtras::GenerarArrayAssoc($nivelesEducativos);
        return $niveles;
    }
    static public function getCiclosEscolares()
    {
        $pdo = Conexion::getDatabaseConnection();
        $ciclosEscolares = $pdo->query("select id_ciclo as valor, ciclo as nombre from ciclos_escolares");
        $ciclos = FuncionesExtras::GenerarArrayAssoc($ciclosEscolares);
        return $ciclos;
    }
    static public function getTurnos()
    {
        $pdo = Conexion::getDatabaseConnection();
        $turnos = $pdo->query("select id_turno as valor, turno as nombre from turnos");
        $turnos = FuncionesExtras::GenerarArrayAssoc($turnos);
        return $turnos;
    }

    static public function getInfoCct($cct)
    {
        $pdo = Conexion::getDatabaseConnection();
        $infoCct = $pdo->query("
        SELECT id_centro_trabajo,id_director_centro_trabajo ,ct.clave_centro_trabajo,ct.nombre_cct,ct.zona_escolar,dct.ciclo_escolar_id, ciclo, 
        id_persona, p.nombre ,p.apellido_paterno , p.apellido_materno, p.curp, nivel, id_nivel
        FROM centros_trabajos ct
        LEFT JOIN  directores_centro_trabajo dct ON ct.id_centro_trabajo = dct.centro_trabajo_id
        LEFT JOIN personas p ON dct.persona_id = p.id_persona
        LEFT join ciclos_escolares ce on dct.ciclo_escolar_id = ce.id_ciclo
		LEFT JOIN niveles n on ct.nivel_escolar_id= n.id_nivel
        WHERE ct.clave_centro_trabajo = '$cct';");
        $datos = FuncionesExtras::GenerarArrayAssoc($infoCct);
        return $datos;
    }

    static public  function getCalificaciones( $clausulaWhere)
    {
        $pdo = Conexion::getDatabaseConnection();
        $getCalificaciones = $pdo->query("	select distinct id_boleta, p.nombre, p.apellido_paterno, p.apellido_materno, p.localidad as localidad_dom, p.municipio as municipio_dom, p.domicilio_particular,p.curp, p.telefono, CONCAT(per.nombre, ' ', per.apellido_paterno , ' ', per.apellido_materno) AS capturado_por ,u.usuario, nivel,nombre_plan_estudio,id_ciclo, ciclo,id_centro_trabajo, clave_centro_trabajo,nombre_cct, zona_escolar ,
        folio, grupo,turno, b.fecha_registro  as fecha_registro_boleta, nombre_materia,id_calificacion_primaria, calificacion, calificacion_primero,calificacion_segundo, calificacion_tercero, promedio_final, ct.localidad,
		CONCAT(p_ver.nombre, ' ', p_ver.apellido_paterno , ' ', p_ver.apellido_materno) as  verificado, estado as estado_boleta
        from boletas b
        left join calificaciones_primaria cp on b.id_boleta=cp.boleta_id
        left join calificaciones_secundaria cs on  b.id_boleta=cs.boleta_id
        inner join personas p on b.persona_id=p.id_persona
        inner join niveles n on b.nivel_id=n.id_nivel
        inner join planes_estudios pe on b.plan_estudio_id=pe.id_plan_estudio
        inner join ciclos_escolares ce on b.ciclo_escolar_id=ce.id_ciclo
        inner join turnos t on b.turno_id=t.id_turno
        inner join centros_trabajos ct on b.centro_trabajo_id=ct.id_centro_trabajo
        left join materias m on cp.materia_id=m.id_materia
		left join usuarios u on  b.capturado_por= u.id_usuario
		left join personas per on u.persona_id = per.id_persona
		left join usuarios uver on b.verificada_por=uver.id_usuario
		left join personas p_ver on uver.persona_id=p_ver.id_persona
        inner join  estados e on b.estado_id=id_estado
		where $clausulaWhere");


        $calificaciones = FuncionesExtras::GenerarArrayAssoc($getCalificaciones);
        if ($calificaciones) {
            return $calificaciones;
        } else {
            return false;
        }
    }

    static public function getTiposUsuarios(){
        $pdo = Conexion::getDatabaseConnection();
        $tiposUsuarios = $pdo->query("SELECT id_tipo_usuario, tipo_usuario FROM tipos_usuarios");
        $tipos = FuncionesExtras::GenerarArrayAssoc($tiposUsuarios);
        return $tipos;
    }

    static public function getNumeroBoletasPorVerificar(){
        $pdo = Conexion::getDatabaseConnection();
        $numeroBoletas = $pdo->query("select count(id_boleta) as numero_boletas from boletas b join estados e on b.estado_id=e.id_estado where estado='En Captura'");
        $datos = $numeroBoletas->fetch(PDO::FETCH_ASSOC);
        return $datos;
    }

    static public function getUsuarios(){
        $pdo=Conexion::getDatabaseConnection();
        $getUsuarios= $pdo->query("select id_usuario,fecha_registro_user,  usuario, estado, nombre,apellido_paterno, apellido_materno, tipo_usuario
from usuarios u	
join personas p on u.persona_id=id_persona
join tipos_usuarios tu on u.tipo_usuario_id=tu.id_tipo_usuario
join estados e on u.estado_id=id_estado");

$usuarios= FuncionesExtras::GenerarArrayAssoc($getUsuarios);
return $usuarios;
    }

}
