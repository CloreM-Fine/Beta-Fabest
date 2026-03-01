<?php
/**
 * Anteas Lucca - Posts API (Admin)
 * CRUD operations for blog posts
 * Protected by JWT auth
 */

declare(strict_types=1);

define('ANTEAS_APP', true);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Require authentication
$user = requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($user);
        break;
    case 'POST':
        requireCSRF();
        handlePost($user);
        break;
    case 'PUT':
        requireCSRF();
        handlePut($user);
        break;
    case 'DELETE':
        requireCSRF();
        handleDelete($user);
        break;
    default:
        jsonError('Metodo non permesso', 405);
}

/**
 * Handle GET - List or single post
 */
function handleGet(array $user): void {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $slug = $_GET['slug'] ?? null;
    
    // Filters
    $status = $_GET['status'] ?? null;
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;
    
    try {
        if ($id || $slug) {
            // Single post
            $sql = "SELECT p.*, u.display_name as author_name 
                    FROM posts p 
                    LEFT JOIN users u ON p.author_id = u.id 
                    WHERE 1=1";
            $params = [];
            
            if ($id) {
                $sql .= " AND p.id = ?";
                $params[] = $id;
            }
            if ($slug) {
                $sql .= " AND p.slug = ?";
                $params[] = $slug;
            }
            
            $post = fetchOne($sql, $params);
            
            if (!$post) {
                jsonError('Post non trovato', 404);
            }
            
            // Decode JSON fields
            $post['content'] = json_decode($post['content'], true);
            $post['tags'] = json_decode($post['tags'], true);
            
            jsonResponse(['post' => $post]);
            
        } else {
            // List posts
            $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.category, p.status, 
                           p.created_at, p.updated_at, p.published_at, p.featured_image,
                           u.display_name as author_name 
                    FROM posts p 
                    LEFT JOIN users u ON p.author_id = u.id 
                    WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND p.status = ?";
                $params[] = $status;
            }
            if ($category) {
                $sql .= " AND p.category = ?";
                $params[] = $category;
            }
            if ($search) {
                $sql .= " AND (p.title LIKE ? OR p.excerpt LIKE ?)";
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }
            
            // Get total count
            $countSql = str_replace("SELECT p.id, p.title, p.slug, p.excerpt, p.category, p.status, 
                           p.created_at, p.updated_at, p.published_at, p.featured_image,
                           u.display_name as author_name", "SELECT COUNT(*)", $sql);
            $total = (int)fetchOne($countSql, $params)[0];
            
            // Add order and limit
            $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $posts = fetchAll($sql, $params);
            
            jsonResponse([
                'posts' => $posts,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Get posts error: " . $e->getMessage());
        jsonError('Errore nel recupero dei post', 500);
    }
}

/**
 * Handle POST - Create post
 */
function handlePost(array $user): void {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        jsonError('Dati JSON non validi', 400);
    }
    
    // Validation
    $title = trim($input['title'] ?? '');
    $slug = trim($input['slug'] ?? '');
    $content = $input['content'] ?? null;
    $excerpt = trim($input['excerpt'] ?? '');
    $category = trim($input['category'] ?? 'generale');
    $status = $input['status'] ?? 'draft';
    $featuredImage = $input['featured_image'] ?? null;
    $tags = $input['tags'] ?? [];
    $metaTitle = trim($input['meta_title'] ?? '');
    $metaDescription = trim($input['meta_description'] ?? '');
    
    if (empty($title)) {
        jsonError('Titolo obbligatorio', 400);
    }
    
    if (!$content || !is_array($content)) {
        jsonError('Contenuto non valido', 400);
    }
    
    // Validate status
    if (!in_array($status, ['draft', 'published'], true)) {
        $status = 'draft';
    }
    
    // Generate slug if not provided
    if (empty($slug)) {
        $slug = generateSlug($title);
    }
    
    // Make slug unique
    $db = getDB();
    $slug = generateUniqueSlug($slug, $db);
    
    // Generate excerpt if not provided
    if (empty($excerpt)) {
        $textContent = '';
        foreach ($content as $block) {
            if ($block['type'] === 'text') {
                $textContent .= ' ' . strip_tags($block['content'] ?? '');
            }
        }
        $excerpt = substr(trim($textContent), 0, 150) . (strlen($textContent) > 150 ? '...' : '');
    }
    
    // Set published_at if publishing
    $publishedAt = null;
    if ($status === 'published') {
        $publishedAt = date('Y-m-d H:i:s');
    }
    
    try {
        $postId = insert(
            "INSERT INTO posts (title, slug, excerpt, content, featured_image, category, tags, status, author_id, meta_title, meta_description, published_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $title,
                $slug,
                $excerpt,
                json_encode($content),
                $featuredImage,
                $category,
                json_encode($tags),
                $status,
                $user['id'],
                $metaTitle ?: null,
                $metaDescription ?: null,
                $publishedAt
            ]
        );
        
        jsonResponse([
            'success' => true,
            'message' => 'Post creato con successo',
            'post' => [
                'id' => $postId,
                'title' => $title,
                'slug' => $slug,
                'status' => $status
            ]
        ], 201);
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            jsonError('Slug già esistente', 409);
        }
        error_log("Create post error: " . $e->getMessage());
        jsonError('Errore nella creazione del post', 500);
    }
}

/**
 * Handle PUT - Update post
 */
function handlePut(array $user): void {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$id) {
        jsonError('ID post richiesto', 400);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        jsonError('Dati JSON non validi', 400);
    }
    
    // Check post exists
    $post = fetchOne("SELECT id, author_id FROM posts WHERE id = ?", [$id]);
    
    if (!$post) {
        jsonError('Post non trovato', 404);
    }
    
    // Check permissions (admin can edit all, editor only own)
    if ($user['role'] !== 'admin' && $post['author_id'] !== $user['id']) {
        jsonError('Non autorizzato a modificare questo post', 403);
    }
    
    // Build update fields
    $fields = [];
    $params = [];
    
    if (isset($input['title'])) {
        $fields[] = "title = ?";
        $params[] = trim($input['title']);
    }
    
    if (isset($input['slug'])) {
        $slug = trim($input['slug']);
        if (!empty($slug)) {
            $db = getDB();
            $slug = generateUniqueSlug($slug, $db, 'posts', $id);
            $fields[] = "slug = ?";
            $params[] = $slug;
        }
    }
    
    if (isset($input['content'])) {
        $fields[] = "content = ?";
        $params[] = json_encode($input['content']);
    }
    
    if (isset($input['excerpt'])) {
        $fields[] = "excerpt = ?";
        $params[] = trim($input['excerpt']);
    }
    
    if (isset($input['category'])) {
        $fields[] = "category = ?";
        $params[] = trim($input['category']);
    }
    
    if (isset($input['tags'])) {
        $fields[] = "tags = ?";
        $params[] = json_encode($input['tags']);
    }
    
    if (isset($input['featured_image'])) {
        $fields[] = "featured_image = ?";
        $params[] = $input['featured_image'];
    }
    
    if (isset($input['meta_title'])) {
        $fields[] = "meta_title = ?";
        $params[] = trim($input['meta_title']) ?: null;
    }
    
    if (isset($input['meta_description'])) {
        $fields[] = "meta_description = ?";
        $params[] = trim($input['meta_description']) ?: null;
    }
    
    // Handle status change
    if (isset($input['status'])) {
        $newStatus = $input['status'];
        if (in_array($newStatus, ['draft', 'published', 'archived'], true)) {
            $fields[] = "status = ?";
            $params[] = $newStatus;
            
            // Set published_at if publishing for first time
            if ($newStatus === 'published') {
                $current = fetchOne("SELECT status, published_at FROM posts WHERE id = ?", [$id]);
                if ($current && !$current['published_at']) {
                    $fields[] = "published_at = NOW()";
                }
            }
        }
    }
    
    if (empty($fields)) {
        jsonError('Nessun campo da aggiornare', 400);
    }
    
    $sql = "UPDATE posts SET " . implode(', ', $fields) . " WHERE id = ?";
    $params[] = $id;
    
    try {
        $affected = update($sql, $params);
        
        jsonResponse([
            'success' => true,
            'message' => 'Post aggiornato',
            'affected_rows' => $affected
        ]);
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            jsonError('Slug già esistente', 409);
        }
        error_log("Update post error: " . $e->getMessage());
        jsonError('Errore nell\'aggiornamento', 500);
    }
}

/**
 * Handle DELETE - Delete post
 */
function handleDelete(array $user): void {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$id) {
        jsonError('ID post richiesto', 400);
    }
    
    // Check post exists
    $post = fetchOne("SELECT id, author_id FROM posts WHERE id = ?", [$id]);
    
    if (!$post) {
        jsonError('Post non trovato', 404);
    }
    
    // Check permissions
    if ($user['role'] !== 'admin' && $post['author_id'] !== $user['id']) {
        jsonError('Non autorizzato a eliminare questo post', 403);
    }
    
    try {
        // Delete associated uploads references (files stay in storage)
        executeQuery("UPDATE uploads SET post_id = NULL WHERE post_id = ?", [$id]);
        
        // Delete post
        $affected = delete("DELETE FROM posts WHERE id = ?", [$id]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Post eliminato',
            'affected_rows' => $affected
        ]);
        
    } catch (Exception $e) {
        error_log("Delete post error: " . $e->getMessage());
        jsonError('Errore nell\'eliminazione', 500);
    }
}
