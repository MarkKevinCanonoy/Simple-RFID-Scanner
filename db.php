<?php
// ============================================
//  db.php — Database Connection
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default XAMPP user
define('DB_PASS', '');           // Default XAMPP password (empty)
define('DB_NAME', 'rfid_system');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('<div style="font-family:monospace;color:#ff2d78;background:#121212;padding:2rem;">
        <strong>Database Connection Failed:</strong><br>' 
        . mysqli_connect_error() . 
        '</div>');
}

mysqli_set_charset($conn, 'utf8mb4');