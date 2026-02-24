<?php

declare(strict_types=1);

// ====== DB CONFIG ======
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'pengaduan_layanan');
define('DB_USER', 'postgres');
<<<<<<< HEAD
define('DB_PASS', '12345678');         // ganti
=======
define('DB_PASS', '');         // ganti
>>>>>>> 9b421fcd5f50a9a1d0263eacffa8405169ba88b6

$dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, __DIR__ . '/../logs/error.log');
    exit('Koneksi database gagal');
}
