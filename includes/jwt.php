<?php
/**
 * Anteas Lucca - JWT Implementation
 * Simple JWT implementation without external dependencies
 * Based on JWT standard (RFC 7519)
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

class JWT {
    
    /**
     * Encode payload to JWT
     * 
     * @param array $payload
     * @param int|null $exp Expiration timestamp
     * @return string
     */
    public static function encode(array $payload, ?int $exp = null): string {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        $time = time();
        
        $payload['iat'] = $time;
        $payload['iss'] = JWT_ISSUER;
        $payload['aud'] = JWT_AUDIENCE;
        $payload['jti'] = bin2hex(random_bytes(16)); // Unique token ID
        
        if ($exp) {
            $payload['exp'] = $exp;
        } else {
            $payload['exp'] = $time + JWT_EXPIRATION;
        }
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    /**
     * Decode and verify JWT
     * 
     * @param string $jwt
     * @return array
     * @throws Exception
     */
    public static function decode(string $jwt): array {
        $parts = explode('.', $jwt);
        
        if (count($parts) !== 3) {
            throw new Exception('Invalid JWT format');
        }
        
        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        
        // Verify signature
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true);
        
        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception('Invalid signature');
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        if (!$payload) {
            throw new Exception('Invalid payload');
        }
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token expired');
        }
        
        // Check issuer
        if (isset($payload['iss']) && $payload['iss'] !== JWT_ISSUER) {
            throw new Exception('Invalid issuer');
        }
        
        return $payload;
    }
    
    /**
     * Base64 URL-safe encode
     * 
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL-safe decode
     * 
     * @param string $data
     * @return string
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}

/**
 * Set JWT cookie (HttpOnly, Secure, SameSite)
 * 
 * @param string $token
 */
function setJWTCookie(string $token): void {
    $expiry = time() + JWT_EXPIRATION;
    
    $cookieOptions = [
        'expires' => $expiry,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ];
    
    setcookie('auth_token', $token, $cookieOptions);
}

/**
 * Clear JWT cookie
 */
function clearJWTCookie(): void {
    $cookieOptions = [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ];
    
    setcookie('auth_token', '', $cookieOptions);
}

/**
 * Get JWT from cookie or Authorization header
 * 
 * @return string|null
 */
function getJWTFromRequest(): ?string {
    // Check cookie first
    if (isset($_COOKIE['auth_token'])) {
        return $_COOKIE['auth_token'];
    }
    
    // Check Authorization header
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return $matches[1];
    }
    
    return null;
}

/**
 * Validate JWT and return payload
 * 
 * @return array|null
 */
function validateJWT(): ?array {
    $token = getJWTFromRequest();
    
    if (!$token) {
        return null;
    }
    
    try {
        return JWT::decode($token);
    } catch (Exception $e) {
        return null;
    }
}
