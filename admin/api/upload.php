<?php
/**
 * Anteas Lucca - Upload API
 * POST upload image with validation
 * Protected by JWT auth + CSRF
 */

declare(strict_types=1);

define('ANTEAS_APP', true);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Require authentication
$user = requireAuth();

// Require CSRF protection
requireCSRF();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonError('Metodo non permesso', 405);
}

// Check if file uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    jsonError('Nessun file caricato', 400);
}

$file = $_FILES['image'];

// Check upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = match ($file['error']) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File troppo grande (max 2MB)',
        UPLOAD_ERR_PARTIAL => 'Upload incompleto, riprova',
        UPLOAD_ERR_NO_TMP_DIR => 'Errore server: directory temporanea mancante',
        UPLOAD_ERR_CANT_WRITE => 'Errore server: impossibile scrivere file',
        default => 'Errore durante l\'upload'
    };
    jsonError($errorMsg, 400);
}

// Validate file
$validated = validateImageUpload($file);

if (!$validated) {
    jsonError('File non valido. Solo JPG, PNG, WebP fino a 2MB', 400);
}

// Generate safe filename
$filename = generateSafeFilename($validated['name'], $validated['type']);

// Get upload path (year/month structure)
$uploadPath = getUploadPath();
$filepath = $uploadPath . $filename;

// Move uploaded file
if (!move_uploaded_file($validated['tmp_name'], $filepath)) {
    jsonError('Errore nel salvare il file', 500);
}

// Get image dimensions
$dimensions = getimagesize($filepath);
$width = $dimensions[0] ?? null;
$height = $dimensions[1] ?? null;

// Generate URL
$fileUrl = getUploadUrl($filename);

// Save to database
try {
    $uploadId = insert(
        "INSERT INTO uploads (filename, original_name, file_path, file_url, mime_type, file_size, width, height, uploaded_by) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $filename,
            $validated['name'],
            $filepath,
            $fileUrl,
            $validated['type'],
            $validated['size'],
            $width,
            $height,
            $user['id']
        ]
    );
    
    jsonResponse([
        'success' => true,
        'message' => 'File caricato con successo',
        'upload' => [
            'id' => $uploadId,
            'filename' => $filename,
            'url' => $fileUrl,
            'width' => $width,
            'height' => $height,
            'size' => $validated['size']
        ]
    ], 201);
    
} catch (Exception $e) {
    // Remove file if DB insert fails
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    error_log("Upload error: " . $e->getMessage());
    jsonError('Errore nel salvare i metadati', 500);
}
