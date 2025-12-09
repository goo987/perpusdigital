<?php
// app/Auth.php

class Auth {
    private $db; // koneksi database (PDO)

    public function __construct($db){
        $this->db = $db;

        // pastikan session sudah aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // LOGIN USER
    public function login($username, $password){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $u = $stmt->fetch();

        if ($u) {
            $dbPass = $u['password'];
            $isCorrect = false;

            // Jika password disimpan dalam bentuk hash
            if (password_verify($password, $dbPass)) {
                $isCorrect = true;
            }
            // Jika password plaintext
            else if ($password === $dbPass) {
                $isCorrect = true;
            }

            // Jika password cocok maka simpan session user
            if ($isCorrect) {
                $_SESSION['user'] = [
                    'id'       => $u['id'],
                    'username' => $u['username'],
                    'role'     => $u['role'],
                    'nama'     => $u['nama_lengkap']
                ];

                return true;
            }
        }

        return false;
    }

    // LOGOUT USER
    public function logout(){
        session_unset();   // hapus semua session
        session_destroy(); // hancurkan session
    }

    // CEK APAKAH SUDAH LOGIN
    public function check(){
        return isset($_SESSION['user']);
    }

    // AMBIL DATA USER YANG LOGIN
    public function user(){
        return $_SESSION['user'] ?? null;
    }

    // BATASI ROLE
    public function requireRole($roles = []){
        // Jika belum login maka paksa ke login
        if (!$this->check()) {
            header('Location: login.php');
            exit;
        }

        // Jika role tidak sesuai tolak/blok akses
        if (!in_array($this->user()['role'], $roles)) {
            echo "Akses ditolak";
            exit;
        }
    }
}
