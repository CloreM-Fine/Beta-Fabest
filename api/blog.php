<?php
/**
 * Anteas Lucca - Public Blog API
 * GET list posts / single post
 * No authentication required - public endpoint
 */

declare(strict_types=1);

define('ANTEAS_APP', true);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    jsonError('Metodo non permesso', 405);
}

$slug = $_GET['slug'] ?? null;

// CORS headers for public API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Max-Age: 3600');

if ($slug) {
    getSinglePost($slug);
} else {
    listPosts();
}

/**
 * Get single published post by slug
 */
function getSinglePost(string $slug): void {
    try {
        $post = fetchOne(
            "SELECT p.*, u.display_name as author_name 
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             WHERE p.slug = ? AND p.status = 'published' AND p.published_at <= NOW()",
            [$slug]
        );
        
        if (!$post) {
            jsonError('Post non trovato', 404);
        }
        
        // Increment views (async would be better, but this is fine for small sites)
        executeQuery("UPDATE posts SET views_count = views_count + 1 WHERE id = ?", [$post['id']]);
        
        // Decode JSON fields
        $post['content'] = json_decode($post['content'], true);
        $post['tags'] = json_decode($post['tags'], true);
        
        // Remove sensitive/internal fields
        unset($post['author_id']);
        
        jsonResponse(['post' => $post]);
        
    } catch (Exception $e) {
        error_log("Get single post error: " . $e->getMessage());
        jsonError('Errore nel recupero del post', 500);
    }
}

/**
 * List published posts with pagination
 */
function listPosts(): void {
    // Parameters
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(20, max(1, (int)($_GET['limit'] ?? 6)));
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    $offset = ($page - 1) * $limit;
    
    try {
        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.category, p.tags,
                       p.created_at, p.published_at, p.featured_image, p.views_count,
                       u.display_name as author_name 
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                WHERE p.status = 'published' AND p.published_at <= NOW()";
        $params = [];
        
        // Category filter
        if ($category) {
            $sql .= " AND p.category = ?";
            $params[] = $category;
        }
        
        // Search filter (title and excerpt)
        if ($search) {
            $sql .= " AND (p.title LIKE ? OR p.excerpt LIKE ? OR MATCH(p.title, p.excerpt) AGAINST (? IN NATURAL LANGUAGE MODE))";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $search;
        }
        
        // Get total count
        $countSql = str_replace(
            "SELECT p.id, p.title, p.slug, p.excerpt, p.category, p.tags,
                       p.created_at, p.published_at, p.featured_image, p.views_count,
                       u.display_name as author_name",
            "SELECT COUNT(*)",
            $sql
        );
        $total = (int)fetchOne($countSql, $params)[0];
        
        // Add order and limit
        $sql .= " ORDER BY p.published_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $posts = fetchAll($sql, $params);
        
        // Decode tags for each post
        foreach ($posts as &$post) {
            $post['tags'] = json_decode($post['tags'], true);
        }
        
        // Get categories count for sidebar/filter
        $categories = fetchAll(
            "SELECT category, COUNT(*) as count 
             FROM posts 
             WHERE status = 'published' AND published_at <= NOW() 
             GROUP BY category 
             ORDER BY count DESC"
        );
        
        jsonResponse([
            'posts' => $posts,
            'categories' => $categories,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int)ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("List posts error: " . $e->getMessage());
        jsonError('Errore nel recupero dei post', 500);
    }
}
