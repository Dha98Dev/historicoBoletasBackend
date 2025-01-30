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

    static public  function limpiarCadena($datos)
    {
    // Si es un arreglo, aplicar la limpieza a cada elemento
    if (is_array($datos)) {
        foreach ($datos as $clave => $valor) {
            $datos[$clave] = Validaciones::limpiarCadena($valor);
        }
        return $datos;
    } else {
        // Elimina espacios en los extremos
        $datos = trim($datos);
        // Elimina las etiquetas HTML y PHP
        $datos = strip_tags($datos);
        // Convierte caracteres especiales en entidades HTML
        $datos = htmlspecialchars($datos, ENT_QUOTES, 'UTF-8');
        // Escapa caracteres especiales para SQL
        $datos = addslashes($datos);
        return $datos;
    }
    }

    static public function validarLongitud($longitud, $aValidar, $opcionValidacion)
    {
        // Validación individual
        if ($longitud !== "" && $opcionValidacion === "1") {
            return strlen($aValidar) <= $longitud;
        }
        
        // Validación de un conjunto de elementos
        if ($longitud === "" && $opcionValidacion === "2") {
            foreach ($aValidar as $item) {
                if (!isset($item['aValidar'], $item['longitud'])) {
                    // Si el formato del array no es correcto
                    return false;
                }
    
                $esValido = Validaciones::validarLongitud($item['longitud'], $item['aValidar'], '1');
                if (!$esValido) {
                    return false; // Detenemos si algún elemento no es válido
                }
            }
            return true; // Todos los elementos son válidos
        }
    
        // Si las condiciones no coinciden
        return false;
    }
}
