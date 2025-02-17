<?php
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    public static function generateToken($user_id) {
        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60), // Expira en 1 hora
            "sub" => $user_id
        ];
        return JWT::encode($payload, SECRET_KEY, 'HS256');
    }

    public static function verifyToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
