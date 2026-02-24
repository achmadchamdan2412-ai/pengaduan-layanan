<?php
// laporan/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika belum login → redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cegah cache agar tidak bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");