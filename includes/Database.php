<?php
require_once '../config/config.php'; // Asegurar que solo se incluya una vez

class Database {
    private $conn;

    public function connect() {
        $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->conn;
    }
}


?>
