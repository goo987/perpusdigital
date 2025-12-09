<?php
// app/Database.php

class Database {

    private $pdo; // Penyimpanan instance PDO

    public function __construct($host, $dbname, $user, $pass){

        // DSN = konfigurasi koneksi MySQL
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        // Opsi PDO: mode error & mode fetch default
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // Lempar error sebagai exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC   // Fetch default berupa array asosiatif
        ];

        // Membuat koneksi PDO
        $this->pdo = new PDO($dsn, $user, $pass, $opts);
    }

    // Getter untuk mengambil instance PDO dari luar
    public function pdo() {
        return $this->pdo;
    }
}
