<?php
/**
 * Anteas Lucca - Auth Middleware
 * Controlla autenticazione JWT per endpoint protetti
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/functions.php';

/**
 * Require authentication - returns user data or exits with 401
 * 
 * @return array User data
 */
function requireAuth(): array {
    $payload = validateJWT();
    
    if (!$payload) {
        http_response_code(401);
        jsonResponse(['error' => 'Autenticazione richiesta'], 401);
    }
    
    // Check if user still exists and is active
    $userId = $payload['sub'] ?? null;
    
    if (!$userId) {
        http_response_code(401);
        jsonResponse(['error' => 'Token non valido'], 401);
    }
    
    $user = fetchOne("SELECT id, username, email, display_name, role, is_active FROM users WHERE id = ?", [$userId]);
    
    if (!$user || !$user['is_active']) {
        http_response_code(401);
        jsonResponse(['error' => 'Utente non trovato o disattivato'], 401);
    }
    
    return $user;
}

/**
 * Require specific role
 * 
 * @param string|array $roles Required role(s)
 * @return array User data
 */
function requireRole(string|array $roles): array {
    $user = requireAuth();
    
    $roles = is_array($roles) ? $roles : [$roles];
    
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        jsonResponse(['error' => 'Permessi insufficienti'], 403);
    }
    
    return $user;
}

/**
 * Require admin role
 * 
 * @return array User data
 */
function requireAdmin(): array {
    return requireRole('admin');
}

/**
 * Check if user is authenticated (returns user or null)
 * 
 * @return array|null
 */
function checkAuth(): ?array {
    $payload = validateJWT();
    
    if (!$payload) {
        return null;
    }
    
    $userId = $payload['sub'] ?? null;
    
    if (!$userId) {
        return null;
    }
    
    return fetchOne(
        "SELECT id, username, email, display_name, role FROM users WHERE id = ? AND is_active = 1", 
        [$userId]
    );
}

/**
 * Get current user ID from JWT
 * 
 * @return int|null
 */
function getCurrentUserId(): ?int {
    $payload = validateJWT();
    return $payload['sub'] ?? null;
}

/**
 * Set security headers
 */
function setSecurityHeaders(): void {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (basic)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';");
}

// Set security headers on all requests
setSecurityHeaders();
