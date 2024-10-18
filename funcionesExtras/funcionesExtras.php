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

 static public  function limpiarCadena($input)
    {

        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Elimina javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Elimina las etiquetas HTML
            '@<style[^>]*?>.*?</style>@siU',    // Elimina las etiquetas de estilo
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Elimina los comentarios multi-línea
        );

        $output = preg_replace($search, '', $input);
        return $output;
    }
    

}
