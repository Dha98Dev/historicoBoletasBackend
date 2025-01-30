<?php
require_once __DIR__ . "/../phpMailer/Exception.php";
require_once __DIR__ . "/../phpMailer/PHPMailer.php";
require_once __DIR__ . "/../phpMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: *, X-Requested-With, Content-Type, Accept");
class FuncionesExtras
{
    static public  function getJson()
    {
        $json = file_get_contents('php://input');
        return json_decode($json);
    }
    static public function  formatearFecha($fecha)
    {


        // Crear un objeto DateTime
        $date = new DateTime($fecha);

        // Configurar el formateador de fecha
        $formatter = new IntlDateFormatter(
            'es_ES', // Configuración regional en español
            IntlDateFormatter::FULL, // Formato completo
            IntlDateFormatter::NONE // No se usa la parte horaria
        );

        // Establecer el patrón para formatear la fecha
        $formatter->setPattern('EEEE d \'de\' MMMM \'de\' y');

        // Formatear la fecha
        $fecha_formateada = $formatter->format($date);

        return strtoupper($fecha_formateada); // Salida: lunes 11 de junio de 2024

    }
    static public function enviarRespuesta($error, $isValidToken, $mensaje, $data)
    {
        $respuesta=["error"=>$error, "isValidToken"=> $isValidToken,"mensaje"=>$mensaje,"data"=>$data ];

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    static public function GenerarArrayAssoc($query)
    {
        $datos = array();
        while ($fila = $query->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $fila;
        }
        return $datos;
    }



    static public function convertirStdAArray($std)
    {
        $nuevoArray = (array)$std;
        return $nuevoArray;
    }

    
    static public   function codificarUrl($idDecodificado)
    {
        $idDecodificado = $idDecodificado;
        $idDecodificado = str_replace('+', '=3D=', $idDecodificado);
        return $idDecodificado;
    }
    static public function decodificacionUrl($idCodificado)
    {
        $idCodificado = str_replace('=3D=', '+', $idCodificado);
        return $idCodificado;
    }


    static public function enviarMail($destinatario, $asunto, $mensaje, $cuerpoAlternativo)
    {

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Servidor SMTP de Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'al05-005-0319@utdelacosta.edu.mx'; // Tu dirección de correo de Gmail
            $mail->Password   = 'wcgl jrxk ntxt cliv';  // Tu contraseña de Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Habilitar TLS
            $mail->Port       = 587;  // Puerto SMTP para TLS
            $mail->CharSet = 'UTF-8'; 

            //Receptores
            $mail->setFrom('al05-005-0319@utdelacosta.edu.mx', 'Manuel Martinez sillas');
            $mail->addAddress($destinatario, ' ');

            //Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;
            $mail->AltBody = $cuerpoAlternativo;

            $mail->send();
            return  true;
        } catch (Exception $e) {
            return false;
        }
    }


    
static public function  generarArregloCalificaciones($data){
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
                        'calificacion' => $item['calificacion']
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
                'capturado_por' => $item['capturado_por'],
                'nivel' => $item['nivel'],
                'plan_estudio' => $item['nombre_plan_estudio'],
                'ciclo' => $item['ciclo'],
                'clave_centro_trabajo' => $item['clave_centro_trabajo'],
                'nombre_cct' => $item['nombre_cct'],
                'folio' => $item['folio'],
                'grupo' => $item['grupo'],
                'turno' => $item['turno'],
                'verificado'=> $item['verificado'],
                'localidad' => $item['localidad'],
                'localidad_dom'=>$item['localidad_dom'],
                'municipio_dom'=>$item['municipio_dom'],
                'domicilio_particular'=>$item['domicilio_particular'],
                'telefono'=>$item['telefono'],
                'zona' => $item['zona_escolar'],
                'estado_boleta' => $item['estado_boleta'],
                'boletaSolicitudServicio' =>FuncionesExtras::codificarUrl(Validaciones::encriptar($item['id_boleta'])),
                'calificacionesPrimaria' => [],
                'calificacionesSecundaria' => []
            ];
    
            // Agregamos las calificaciones según el nivel
            if ($item['nivel'] === 'PRIMARIA') {
                $nuevaPersona['calificacionesPrimaria'][] = [
                    'nombre_materia' => $item['nombre_materia'],
                    'calificacion' => $item['calificacion']
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
    return $personas;
    
}

static public function encriptarIdentificadores($arregloIdentificadores, $arreglo){
    for ($i=0; $i <count($arreglo) ; $i++) { 
        for ($j=0; $j <count($arregloIdentificadores) ; $j++) { 
            $arreglo[$i][$arregloIdentificadores[$j]] = Validaciones::encriptar( $arreglo[$i][$arregloIdentificadores[$j]]);
        }
    }
    return $arreglo;
}
}
