<?php
/**
 * Anteas Lucca - CSRF Token API
 * GET /admin/api/csrf.php - Returns CSRF token
 */

declare(strict_types=1);

define('ANTEAS_APP', true);
require_once __DIR__ . '/../../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    jsonError('Metodo non permesso', 405);
}

// Generate and return CSRF token
$token = getCSRFToken();

jsonResponse([
    'csrf_token' => $token
]);
