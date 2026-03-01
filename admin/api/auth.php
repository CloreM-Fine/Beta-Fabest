<?php
/**
 * Anteas Lucca - Auth API
 * POST /login, POST /logout, GET /verify
 */

declare(strict_types=1);

define('ANTEAS_APP', true);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/jwt.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handlePost();
        break;
    case 'GET':
        handleGet();
        break;
    default:
        jsonError('Metodo non permesso', 405);
}

/**
 * Handle POST requests (login/logout)
 */
function handlePost(): void {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'login':
            handleLogin($input);
            break;
        case 'logout':
            handleLogout();
            break;
        default:
            jsonError('Azione non specificata', 400);
    }
}

/**
 * Handle login
 */
function handleLogin(array $input): void {
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Validation
    if (empty($username) || empty($password)) {
        jsonError('Username e password richiesti', 400);
    }
    
    // Rate limiting check
    $rateKey = $ip . '_login';
    if (!checkRateLimit($rateKey, RATE_LIMIT_ATTEMPTS, RATE_LIMIT_WINDOW)) {
        jsonError('Troppi tentativi. Riprova tra 15 minuti.', 429);
    }
    
    try {
        // Get user from DB
        $user = fetchOne(
            "SELECT id, username, email, password_hash, display_name, role, is_active FROM users WHERE username = ?",
            [$username]
        );
        
        // Verify user exists and is active
        if (!$user || !$user['is_active']) {
            recordFailedAttempt($rateKey);
            jsonError('Credenziali non valide', 401);
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            recordFailedAttempt($rateKey);
            jsonError('Credenziali non valide', 401);
        }
        
        // Rehash password if needed (password_hash upgrade)
        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT)) {
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            executeQuery(
                "UPDATE users SET password_hash = ? WHERE id = ?",
                [$newHash, $user['id']]
            );
        }
        
        // Update last login
        executeQuery(
            "UPDATE users SET last_login = NOW() WHERE id = ?",
            [$user['id']]
        );
        
        // Generate JWT
        $payload = [
            'sub' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        
        $token = JWT::encode($payload);
        
        // Set cookie
        setJWTCookie($token);
        
        // Generate CSRF token
        $csrfToken = getCSRFToken();
        
        // Clear rate limit on success
        unset($_SESSION['rate_limit_' . md5($rateKey)]);
        
        // Return success (no sensitive data)
        jsonResponse([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'display_name' => $user['display_name'],
                'role' => $user['role']
            ],
            'csrf_token' => $csrfToken
        ]);
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        jsonError('Errore durante il login', 500);
    }
}

/**
 * Handle logout
 */
function handleLogout(): void {
    // Get token before clearing
    $token = getJWTFromRequest();
    
    if ($token) {
        try {
            $payload = JWT::decode($token);
            
            // Add to blacklist (optional but recommended)
            // This allows explicit logout before token expiry
            executeQuery(
                "INSERT INTO sessions (token_jti, user_id, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))",
                [$payload['jti'], $payload['sub'], $payload['exp']]
            );
        } catch (Exception $e) {
            // Token invalid, just clear cookie anyway
        }
    }
    
    // Clear cookie
    clearJWTCookie();
    
    // Destroy session
    startSecureSession();
    session_destroy();
    
    jsonResponse(['success' => true, 'message' => 'Logout effettuato']);
}

/**
 * Handle GET requests (verify token)
 */
function handleGet(): void {
    $user = checkAuth();
    
    if (!$user) {
        jsonError('Non autenticato', 401);
    }
    
    // Refresh CSRF token
    $csrfToken = getCSRFToken();
    
    jsonResponse([
        'authenticated' => true,
        'user' => $user,
        'csrf_token' => $csrfToken
    ]);
}
