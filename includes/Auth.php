<?php
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static $secret_key = "tu_clave_secreta"; // Usa la misma clave en config.php
    private static $algorithm = "HS256"; 

    public static function generateToken($data) {
        $payload = [
            "iss" => "apitripnow",
            "iat" => time(),
            "exp" => time() + (3 * 60 * 60), // Expira en 3 hora (10800 segundos)
            "consecutivo" => $data["consecutivo"], 
            "email" => $data["email"],
            "id_reserva" => $data["id_reserva"]
        ];

        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    public static function verifyToken($token) {
        try {
            return JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
        } catch (Exception $e) {
            return false;
        }
    }
}

?>
