<?php
// ── Database Configuration ────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // WAMP default username
define('DB_PASS', '');       // WAMP default password (blank)
define('DB_NAME', 'projex_db');

// ── Connect ───────────────────────────────────────────────
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('
    <div style="font-family:sans-serif;padding:2rem;background:#0a0a1a;color:#ff4081;min-height:100vh">
        <h2>❌ Database Connection Failed</h2>
        <p style="margin-top:1rem;color:#e0e0ff">'.mysqli_connect_error().'</p>
        <p style="margin-top:1rem;color:#9090b0">Check your credentials in <strong>config/db.php</strong></p>
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');
