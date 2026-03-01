<?php
/**
 * Anteas Lucca - Configuration File
 * 
 * IMPORTANTE: Proteggere questo file da accesso web con .htaccess
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ANTEAS_APP')) {
    die('Accesso diretto non permesso');
}

// Database Configuration - Production (SiteGround)
define('DB_HOST', 'localhost');
define('DB_NAME', 'dblz7emtetofi4');
define('DB_USER', 'urb5jmirausb2');
define('DB_PASS', 'beta123!');
define('DB_CHARSET', 'utf8mb4');

// JWT Configuration
// IMPORTANTE: Cambiare questa chiave in produzione!
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'cambia_questa_chiave_segreta_in_produzione_32caratteri!');
define('JWT_ISSUER', 'anteaslucca.org');
define('JWT_AUDIENCE', 'anteaslucca.org');
define('JWT_EXPIRATION', 7200); // 2 ore in secondi

// Session & Security
define('SESSION_NAME', 'anteas_session');
define('SESSION_LIFETIME', 7200); // 2 ore
define('CSRF_TOKEN_NAME', 'csrf_token');
define('RATE_LIMIT_ATTEMPTS', 5);
define('RATE_LIMIT_WINDOW', 900); // 15 minuti

// Upload Configuration
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024); // 2MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/uploads/');

// Environment
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'development'); // 'development' o 'production'

// Error Reporting (diverso per dev/prod)
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Timezone
date_default_timezone_set('Europe/Rome');
