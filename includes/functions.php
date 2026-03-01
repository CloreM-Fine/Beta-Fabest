<?php
/**
 * Anteas Lucca - Helper Functions
 * Security & utility functions
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Sanitize input string
 * 
 * @param string $input
 * @return string
 */
function sanitize(string $input): string {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $input;
}

/**
 * Clean output for display
 * 
 * @param string $text
 * @return string
 */
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validate email
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate secure slug from string
 * 
 * @param string $text
 * @return string
 */
function generateSlug(string $text): string {
    // Replace non-alphanumeric with dash
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove non-alphanumeric and dash
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Lowercase
    $text = strtolower($text);
    // Limit length
    $text = substr($text, 0, 200);
    
    return $text ?: 'untitled';
}

/**
 * Generate unique slug with DB check
 * 
 * @param string $text
 * @param PDO $db
 * @param string $table
 * @param int|null $excludeId
 * @return string
 */
function generateUniqueSlug(string $text, PDO $db, string $table = 'posts', ?int $excludeId = null): string {
    $baseSlug = generateSlug($text);
    $slug = $baseSlug;
    $counter = 1;
    
    $sql = "SELECT COUNT(*) FROM {$table} WHERE slug = ?";
    $params = [$slug];
    
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    while (true) {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $count = (int) $stmt->fetchColumn();
        
        if ($count === 0) {
            break;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $params[0] = $slug;
        $counter++;
    }
    
    return $slug;
}

// ==================== CSRF PROTECTION ====================

/**
 * Start secure session
 */
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        
        session_name(SESSION_NAME);
        session_start();
        
        // Regenerate ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function generateCSRFToken(): string {
    startSecureSession();
    
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Get CSRF token (for forms)
 * 
 * @return string
 */
function getCSRFToken(): string {
    return generateCSRFToken();
}

/**
 * Validate CSRF token
 * 
 * @param string $token
 * @return bool
 */
function validateCSRFToken(string $token): bool {
    startSecureSession();
    
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * CSRF middleware - to use at start of protected endpoints
 * 
 * @return void
 * @throws Exception
 */
function requireCSRF(): void {
    $headers = getallheaders();
    $token = $_POST['csrf_token'] ?? 
             ($_GET['csrf_token'] ?? 
             ($headers['X-CSRF-Token'] ?? 
             ($headers['X-Csrf-Token'] ?? '')));
    
    if (!validateCSRFToken($token)) {
        http_response_code(403);
        jsonResponse(['error' => 'Token CSRF non valido'], 403);
        exit;
    }
}

// ==================== RATE LIMITING ====================

/**
 * Check rate limit
 * 
 * @param string $key Identifier (e.g., IP or username)
 * @param int $maxAttempts
 * @param int $windowSeconds
 * @return bool True if allowed
 */
function checkRateLimit(string $key, int $maxAttempts = RATE_LIMIT_ATTEMPTS, int $windowSeconds = RATE_LIMIT_WINDOW): bool {
    startSecureSession();
    
    $now = time();
    $rateKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$rateKey])) {
        $_SESSION[$rateKey] = [
            'attempts' => 1,
            'first_attempt' => $now
        ];
        return true;
    }
    
    $data = $_SESSION[$rateKey];
    
    // Reset if window passed
    if ($now - $data['first_attempt'] > $windowSeconds) {
        $_SESSION[$rateKey] = [
            'attempts' => 1,
            'first_attempt' => $now
        ];
        return true;
    }
    
    // Check attempts
    if ($data['attempts'] >= $maxAttempts) {
        return false;
    }
    
    $_SESSION[$rateKey]['attempts']++;
    return true;
}

/**
 * Record failed attempt for rate limiting
 * 
 * @param string $key
 */
function recordFailedAttempt(string $key): void {
    startSecureSession();
    $rateKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$rateKey])) {
        $_SESSION[$rateKey] = [
            'attempts' => 1,
            'first_attempt' => time()
        ];
    } else {
        $_SESSION[$rateKey]['attempts']++;
    }
}

// ==================== JSON RESPONSE ====================

/**
 * Send JSON response
 * 
 * @param array $data
 * @param int $statusCode
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Send error response
 * 
 * @param string $message
 * @param int $statusCode
 * @param array $details
 */
function jsonError(string $message, int $statusCode = 400, array $details = []): void {
    $response = ['error' => $message];
    if (!empty($details)) {
        $response['details'] = $details;
    }
    jsonResponse($response, $statusCode);
}

// ==================== UPLOAD VALIDATION ====================

/**
 * Validate uploaded image
 * 
 * @param array $file $_FILES['field']
 * @return array|bool ['name', 'tmp_name', 'size', 'type'] or false
 */
function validateImageUpload(array $file) {
    // Check upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return false;
    }
    
    // Verify MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime, UPLOAD_ALLOWED_TYPES, true)) {
        return false;
    }
    
    // Verify it's a valid image
    if (!getimagesize($file['tmp_name'])) {
        return false;
    }
    
    return [
        'name' => $file['name'],
        'tmp_name' => $file['tmp_name'],
        'size' => $file['size'],
        'type' => $mime
    ];
}

/**
 * Generate safe filename for upload
 * 
 * @param string $originalName
 * @param string $mimeType
 * @return string
 */
function generateSafeFilename(string $originalName, string $mimeType): string {
    $ext = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg'
    };
    
    return 'img_' . uniqid('', true) . '.' . $ext;
}

/**
 * Get upload path for current date
 * 
 * @return string
 */
function getUploadPath(): string {
    $year = date('Y');
    $month = date('m');
    $path = UPLOAD_DIR . $year . '/' . $month . '/';
    
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    
    return $path;
}

/**
 * Get upload URL for file
 * 
 * @param string $filename Just the filename
 * @return string
 */
function getUploadUrl(string $filename): string {
    $year = date('Y');
    $month = date('m');
    return UPLOAD_URL . $year . '/' . $month . '/' . $filename;
}
