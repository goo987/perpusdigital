<?php
// app/config.php

// Mulai session untuk login/auth
session_start();

// Konfigurasi database dasar 
define('DB_HOST','127.0.0.1');
define('DB_NAME','perpus');   
define('DB_USER','root');      
define('DB_PASS','');        

// Base URL untuk path public(disesuaikan dengan folder project)
define('BASE_URL', '/perpus-digital/public');

// Load semua file inti aplikasi
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Models/User.php';
require_once __DIR__ . '/Models/Book.php';
require_once __DIR__ . '/Models/Peminjaman.php';
require_once __DIR__ . '/Models/Review.php';

// Buat instance database
$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
