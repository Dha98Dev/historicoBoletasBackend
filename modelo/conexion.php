<?php
Class Conexion{
   static public function getDatabaseConnection() {
        // Configuración de conexión
        $host = 'localhost';
        $dbname = 'historico_boletas';
        $user = 'postgres';
        $password = 'postgres';
        
        try {
            // Crear una instancia de PDO para la conexión a PostgreSQL
            $dsn = "pgsql:host=$host;dbname=$dbname";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Modo de errores: excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de obtención de datos: array asociativo
                PDO::ATTR_EMULATE_PREPARES => false, // Usar sentencias preparadas nativas
            ];
            
            $pdo = new PDO($dsn, $user, $password, $options);
            return $pdo; // Devolver la instancia de PDO
        } catch (PDOException $e) {
            // Manejo de errores de conexión
            echo "Error en la conexión: " . $e->getMessage();
            exit;
        }
    }
}