<?php
/**
 * Anteas Lucca - Migration Script
 * Import articles from ./articoli/ to database
 * 
 * Usage: php migrate.php
 * Requirements: Database must be created first with schema.sql
 */

declare(strict_types=1);

define('ANTEAS_APP', true);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Config
$articlesDir = __DIR__ . '/../articoli/';
$adminUserId = 1; // Default admin ID

echo "=== Anteas Lucca - Migration Script ===\n\n";

// Check directory exists
if (!is_dir($articlesDir)) {
    die("ERRORE: Directory articoli non trovata: {$articlesDir}\n");
}

// Get all markdown files
$files = glob($articlesDir . '*.md');

if (empty($files)) {
    die("Nessun file .md trovato in {$articlesDir}\n");
}

echo "Trovati " . count($files) . " file markdown\n\n";

// Statistics
$stats = [
    'imported' => 0,
    'skipped' => 0,
    'errors' => 0
];

// Process each file
foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip non-article files (month files like 2024-01.md, etc.)
    if (preg_match('/^\d{4}-\d{2}\.md$/', $filename)) {
        echo "[SKIP] {$filename} (file mensile)\n";
        $stats['skipped']++;
        continue;
    }
    
    try {
        // Extract slug from filename
        $slug = extractSlugFromFilename($filename);
        
        // Check if already exists
        $existing = fetchOne("SELECT id FROM posts WHERE slug = ?", [$slug]);
        if ($existing) {
            echo "[SKIP] {$filename} (già esistente: {$slug})\n";
            $stats['skipped']++;
            continue;
        }
        
        // Parse file
        $content = file_get_contents($file);
        $article = parseArticle($content, $filename);
        
        // Convert content to JSON blocks
        $contentBlocks = convertToBlocks($article['content']);
        
        // Insert into database
        $postId = insert(
            "INSERT INTO posts 
             (title, slug, excerpt, content, category, status, author_id, published_at, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $article['title'],
                $slug,
                $article['excerpt'],
                json_encode($contentBlocks),
                $article['category'],
                'published',
                $adminUserId,
                $article['date'],
                $article['date']
            ]
        );
        
        echo "[OK] #{$postId} {$article['title']}\n";
        $stats['imported']++;
        
    } catch (Exception $e) {
        echo "[ERROR] {$filename}: " . $e->getMessage() . "\n";
        $stats['errors']++;
    }
}

echo "\n=== Riassunto ===\n";
echo "Importati: {$stats['imported']}\n";
echo "Saltati: {$stats['skipped']}\n";
echo "Errori: {$stats['errors']}\n";
echo "\nCompletato!\n";

// ============== HELPER FUNCTIONS ==============

/**
 * Extract slug from filename
 */
function extractSlugFromFilename(string $filename): string {
    // Remove .md extension
    $name = preg_replace('/\.md$/', '', $filename);
    
    // Remove date prefix (YYYY-MM-DD-)
    $name = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $name);
    
    return generateSlug($name);
}

/**
 * Parse article content
 */
function parseArticle(string $content, string $filename): array {
    $article = [
        'title' => 'Senza titolo',
        'content' => '',
        'excerpt' => '',
        'date' => date('Y-m-d H:i:s'),
        'category' => 'generale'
    ];
    
    // Extract date from filename
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $filename, $dateMatch)) {
        $article['date'] = "{$dateMatch[1]}-{$dateMatch[2]}-{$dateMatch[3]} 10:00:00";
    }
    
    // Extract title from first # heading
    if (preg_match('/^#\s*(.+)$/m', $content, $titleMatch)) {
        $article['title'] = trim($titleMatch[1]);
    }
    
    // Extract content section
    if (preg_match('/## Content\s*\n\n([\s\S]*?)(?=\n## |$)/', $content, $contentMatch)) {
        $rawContent = $contentMatch[1];
        
        // Clean up content
        $rawContent = preg_replace('/^\*\*URL:.*$/m', '', $rawContent);
        $rawContent = preg_replace('/^\*\*Analyzed:.*$/m', '', $rawContent);
        $rawContent = preg_replace('/^Salta al contenuto$/m', '', $rawContent);
        $rawContent = preg_replace('/^\d{1,2}\s+\w+\s+\d{4}$/m', '', $rawContent);
        $rawContent = preg_replace('/^Uncategorized$/m', '', $rawContent);
        $rawContent = preg_replace('/^Redazione$/m', '', $rawContent);
        
        $article['content'] = trim($rawContent);
        
        // Generate excerpt from first meaningful paragraph
        $lines = array_filter(explode("\n", $rawContent), function($line) {
            return strlen(trim($line)) > 30;
        });
        
        if (!empty($lines)) {
            $firstLine = strip_tags(array_values($lines)[0]);
            $article['excerpt'] = substr($firstLine, 0, 150);
            if (strlen($firstLine) > 150) {
                $article['excerpt'] .= '...';
            }
        }
    }
    
    // Extract category from file
    if (preg_match('/## H2 Headings.*?(eventi|5x1000|insieme|riflessioni|nonni|notizie)/s', $content, $catMatch)) {
        $article['category'] = strtolower($catMatch[1]);
    }
    
    // If no excerpt, use title
    if (empty($article['excerpt'])) {
        $article['excerpt'] = substr($article['title'], 0, 150);
    }
    
    return $article;
}

/**
 * Convert markdown content to JSON blocks
 */
function convertToBlocks(string $content): array {
    $blocks = [];
    
    // Split by paragraphs
    $paragraphs = preg_split('/\n\s*\n/', $content, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);
        
        if (empty($paragraph)) {
            continue;
        }
        
        // Skip navigation/footer lines
        if (preg_match('/^(Navigazione|Come trovarci|You missed|Utilizziamo i cookie)/i', $paragraph)) {
            continue;
        }
        
        // Check if it's a heading
        if (preg_match('/^(#{1,3})\s+(.+)$/m', $paragraph, $headingMatch)) {
            $level = strlen($headingMatch[1]);
            $blocks[] = [
                'type' => 'heading',
                'level' => $level,
                'content' => trim($headingMatch[2])
            ];
            continue;
        }
        
        // Check if it's a list
        if (preg_match('/^[\*\-\+]\s/m', $paragraph)) {
            $items = [];
            foreach (explode("\n", $paragraph) as $line) {
                if (preg_match('/^[\*\-\+]\s+(.+)$/', trim($line), $itemMatch)) {
                    $items[] = $itemMatch[1];
                }
            }
            if (!empty($items)) {
                $blocks[] = [
                    'type' => 'list',
                    'items' => $items
                ];
                continue;
            }
        }
        
        // Regular text paragraph
        // Bold/italic conversion
        $paragraph = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $paragraph);
        $paragraph = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $paragraph);
        
        $blocks[] = [
            'type' => 'text',
            'content' => $paragraph
        ];
    }
    
    // If no blocks, add default
    if (empty($blocks)) {
        $blocks[] = [
            'type' => 'text',
            'content' => 'Contenuto in arrivo...'
        ];
    }
    
    return $blocks;
}
