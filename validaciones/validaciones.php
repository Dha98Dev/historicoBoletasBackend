<?php
// define("CLAVE", "#$%&hdDYs/$#DFDS$%");
// define("COD","AES-128-ECB");
class Validaciones
{

    static public function generarToken($json)
    {
        // codificamos a json el arreglo asociativo que recivimos
        $json = json_encode($json);
        // codificamos a base 64 el json
        $json64 = base64_encode($json);
        $key = base64_decode('PWB+AE83m8lCfTcJYR18DQ==');
        $iv = base64_decode('TT3Fg1IFjODrg/kb4fKWEQ==');
        // encriptamos el resultado
        $ciphertext = openssl_encrypt($json64, 'AES-128-CBC', $key, 0, $iv);
        // retornamos el valor encriptado que esta vez sera  nuestro token 
        return $ciphertext;
    }

    // este es el metodo para validar el token
    static public function validarToken($token)
    {
        $pdo = Conexion::getDatabaseConnection();
        $validarTokenBD = $pdo->query("select id_usuario, count(id_usuario) as existencia, estado 
from usuarios u
join estados e on u.estado_id=e.id_estado
where token='$token'
group by id_usuario, estado
");
        $validarTokenBD = $validarTokenBD->fetch(PDO::FETCH_ASSOC);
        if ($validarTokenBD && $validarTokenBD['estado'] == 'activo') {

            $key = base64_decode('PWB+AE83m8lCfTcJYR18DQ==');
            $iv = base64_decode('TT3Fg1IFjODrg/kb4fKWEQ==');
            // desencriptamos  el token  que recibimos 
            $json64 = openssl_decrypt($token, 'AES-128-CBC', $key, 0, $iv);
            // decodificamos el json que esta codificado en base 64 
            $json = base64_decode($json64);
            //    convertimos a arreglo asociativo
            $json = json_decode($json, true);

            if ($json != null) {  // verificamos que el json no este null  
                if ($json['vigencia'] > time()) { // verificamos que aun este viegente  el token 
                    return $datos = [
                        "valido" => true,
                        "mensaje" => "token valido",
                        "token" => $token,
                        "datos" => $json
                    ];
                } else {
                    return $datos = [
                        "valido" => false,
                        "mensaje" => "el token ya caduco",
                        "token" => "",
                        "datos" => ""
                    ];
                    Validaciones::limpiarToken($json["token"]);
                }
            } else {
                return $datos = [
                    "valido" => false,
                    "mensaje" => "el token fue alterado",
                    "token" => "",
                    "datos" => ""
                ];
            }
        } else {
            return $datos = [
                "valido" => false,
                "mensaje" => "Debe de Iniciar sesion de nuevo",
                "token" => "",
                "datos" => ""
            ];
        }
    }

    static public function limpiarToken($token)
    {
        $pdo = Conexion::getDatabaseConnection();
        $actualizarTokenBD = $pdo->prepare("UPDATE usuarios SET token='' WHERE token='$token'");
    }

    static public function encriptar($toEncrypt)
    {
        $key = base64_decode('PWB+AE83m8lCfTcJYR18DQ==');
        $iv = base64_decode('TT3Fg1IFjODrg/kb4fKWEQ==');
        // encriptamos el resultado
        $ciphertext = openssl_encrypt($toEncrypt, 'AES-128-CBC', $key, 0, $iv);
        return $ciphertext;
    }


    static public function  desencriptar($toDecrypt)
    {
        $key = base64_decode('PWB+AE83m8lCfTcJYR18DQ==');
        $iv = base64_decode('TT3Fg1IFjODrg/kb4fKWEQ==');
        // desencriptamos  el token  que recivimos 
        $desencriptado = openssl_decrypt($toDecrypt, 'AES-128-CBC', $key, 0, $iv);
        // decodificamos el json que esta codificado en base 64 
        return $desencriptado;
    }


    static public function enviarMail($destinatario, $asunto, $mensaje)
    {

        $headers = "From: al05-005-0319@utdelacosta.edu.mx\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

        if (mail($destinatario, $asunto, $mensaje, $headers)) {
            return "ok";
        } else {
            $datos = ["error" => true, "mensaje" => "ocurrio un error durante el proceso", "datos" => ""];
        }
    }

    // static public function ValidarRangoFechas($fechaInicio, $fechaFin, $limite)

    static public function validarRangoFechas($fechaInicio, $fechaFin, $limite)
    {
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        $fechaFinMaxima = null;

        switch ($limite) {
            case '180':
                $fechaFinMaxima = clone $inicio;
                $fechaFinMaxima->modify('+180 days');
                break;
            case '90':
                $fechaFinMaxima = clone $inicio;
                $fechaFinMaxima->modify('+90 days');
                break;
            case '365':
                $fechaFinMaxima = clone $inicio;
                $fechaFinMaxima->modify('+365 days');
                break;
            case '0000-12-31':
                $fechaFinMaxima = new DateTime($inicio->format('Y') . '-12-31');
                break;
            case '':
                $fechaFinMaxima = new DateTime(($inicio->format('Y') + 2) . '-12-31');
                break;
            case null:
                $fechaFinMaxima = new DateTime(($inicio->format('Y') + 2) . '-12-31');
                break;
            default:
                throw new Exception('Límite inválido');
        }

        // Verificar si la fecha de fin está dentro del límite permitido
        return $fin <= $fechaFinMaxima && $fin >= $inicio;
    }
}
