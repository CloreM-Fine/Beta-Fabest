<?php
/**
 * Anteas Lucca - Editor Drag-Drop
 * Editor a blocchi per creazione/modifica articoli
 */

declare(strict_types=1);

// Protezione accesso diretto
define('ANTEAS_APP', true);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Require authentication
$user = requireAuth();
$csrfToken = getCSRFToken();

// Get post ID if editing
$postId = isset($_GET['id']) ? (int)$_GET['id'] : null;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $postId ? 'Modifica' : 'Nuovo'; ?> Articolo - Anteas Lucca</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../public/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/favicon-16x16.png">
    <link rel="apple-touch-icon" href="../public/apple-touch-icon.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/css/tailwind.config.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    
    <!-- Header -->
    <header class="admin-header fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center gap-4">
            <a href="dashboard.php" class="p-2 text-gray-600 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    <?php echo $postId ? 'Modifica Articolo' : 'Nuovo Articolo'; ?>
                </h1>
                <span id="save-indicator" class="text-gray-400 text-sm">Pronto</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button id="btn-preview" class="btn-admin btn-admin-secondary">
                <i data-lucide="eye" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Anteprima</span>
            </button>
            <button id="btn-save-draft" class="btn-admin btn-admin-secondary">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Salva Bozza</span>
            </button>
            <button id="btn-publish" class="btn-admin btn-admin-primary">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Pubblica</span>
            </button>
        </div>
    </header>
    
    <!-- Editor Layout -->
    <div class="pt-20 pb-8 min-h-screen">
        <div class="editor-layout flex flex-col lg:flex-row h-[calc(100vh-5rem)]">
            
            <!-- COLONNA SINISTRA: Palette Blocchi -->
            <aside class="editor-sidebar w-full lg:w-64 bg-white border-r border-gray-200 flex-shrink-0 lg:overflow-y-auto">
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="blocks" class="w-5 h-5 text-cyan-600"></i>
                        Blocchi
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">Clicca per aggiungere un blocco</p>
                    
                    <div class="space-y-2">
                        <button data-block-type="text" class="block-palette-item w-full text-left">
                            <i data-lucide="type" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Testo</span>
                                <p class="text-xs text-gray-400">Paragrafo formattato</p>
                            </div>
                        </button>
                        
                        <button data-block-type="heading" class="block-palette-item w-full text-left">
                            <i data-lucide="heading" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Titolo</span>
                                <p class="text-xs text-gray-400">H2, H3, H4</p>
                            </div>
                        </button>
                        
                        <button data-block-type="image" class="block-palette-item w-full text-left">
                            <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Immagine</span>
                                <p class="text-xs text-gray-400">Con didascalia</p>
                            </div>
                        </button>
                        
                        <button data-block-type="list" class="block-palette-item w-full text-left">
                            <i data-lucide="list" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Lista</span>
                                <p class="text-xs text-gray-400">Punti o numeri</p>
                            </div>
                        </button>
                        
                        <button data-block-type="quote" class="block-palette-item w-full text-left">
                            <i data-lucide="quote" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Citazione</span>
                                <p class="text-xs text-gray-400">Blocco stilizzato</p>
                            </div>
                        </button>
                        
                        <button data-block-type="separator" class="block-palette-item w-full text-left">
                            <i data-lucide="minus" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Separatore</span>
                                <p class="text-xs text-gray-400">Linea orizzontale</p>
                            </div>
                        </button>
                        
                        <button data-block-type="cta" class="block-palette-item w-full text-left">
                            <i data-lucide="mouse-pointer" class="w-5 h-5 text-gray-400"></i>
                            <div>
                                <span class="font-medium text-gray-700">Call to Action</span>
                                <p class="text-xs text-gray-400">Box con bottone</p>
                            </div>
                        </button>
                    </div>
                </div>
            </aside>
            
            <!-- COLONNA CENTRALE: Canvas Editor -->
            <main class="editor-canvas-area flex-1 bg-slate-100 overflow-y-auto p-4 lg:p-8">
                <div class="max-w-3xl mx-auto">
                    <!-- Titolo -->
                    <div class="mb-6">
                        <input type="text" id="post-title" 
                               placeholder="Titolo dell'articolo"
                               class="w-full text-3xl lg:text-4xl font-bold text-gray-900 placeholder-gray-400 border-0 bg-transparent focus:ring-0 p-0">
                    </div>
                    
                    <!-- Slug -->
                    <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                        <i data-lucide="link" class="w-4 h-4"></i>
                        <span>ant easlucca.org/blog/</span>
                        <input type="text" id="post-slug" 
                               placeholder="titolo-articolo"
                               class="flex-1 bg-transparent border-0 focus:ring-0 text-gray-600 underline decoration-dotted">
                    </div>
                    
                    <!-- Canvas -->
                    <div id="editor-canvas" class="editor-canvas min-h-[500px]">
                        <!-- I blocchi verranno aggiunti qui -->
                    </div>
                    
                    <p class="text-center text-gray-400 text-sm mt-4">
                        Clicca un blocco dalla sidebar per aggiungerlo
                    </p>
                </div>
            </main>
            
            <!-- COLONNA DESTRA: Proprietà -->
            <aside class="w-full lg:w-80 bg-white border-l border-gray-200 flex-shrink-0 lg:overflow-y-auto">
                <div class="p-4 space-y-6">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i data-lucide="settings" class="w-5 h-5 text-cyan-600"></i>
                        Proprietà
                    </h3>
                    
                    <!-- Categoria -->
                    <div>
                        <label class="admin-label">Categoria</label>
                        <select id="post-category" class="admin-input">
                            <option value="generale">Generale</option>
                            <option value="eventi">Eventi</option>
                            <option value="5x1000">5×1000</option>
                            <option value="insieme">Insieme</option>
                            <option value="riflessioni">Riflessioni</option>
                            <option value="notizie">Notizie</option>
                            <option value="servizi">Servizi</option>
                        </select>
                    </div>
                    
                    <!-- Stato -->
                    <div>
                        <label class="admin-label">Stato</label>
                        <select id="post-status" class="admin-input">
                            <option value="draft">Bozza</option>
                            <option value="published">Pubblicato</option>
                        </select>
                    </div>
                    
                    <!-- Immagine in evidenza -->
                    <div>
                        <label class="admin-label">Immagine in evidenza</label>
                        <input type="file" id="featured-image-input" accept="image/*" class="hidden">
                        <div id="featured-image-preview" class="mb-2">
                            <!-- Preview verrà mostrata qui -->
                        </div>
                        <button type="button" onclick="document.getElementById('featured-image-input').click()" 
                                class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-cyan-500 hover:text-cyan-600 transition-colors">
                            <i data-lucide="image-plus" class="w-4 h-4 inline mr-1"></i>
                            Carica immagine
                        </button>
                        <input type="hidden" id="featured-image-url">
                    </div>
                    
                    <!-- Estratto -->
                    <div>
                        <label class="admin-label">Estratto</label>
                        <textarea id="post-excerpt" rows="3" class="admin-input resize-none"
                                  placeholder="Breve descrizione dell'articolo..."></textarea>
                    </div>
                    
                    <!-- Meta Title -->
                    <div>
                        <label class="admin-label">Meta Title (SEO)</label>
                        <input type="text" id="post-meta-title" class="admin-input" 
                               placeholder="Titolo per i motori di ricerca">
                    </div>
                    
                    <!-- Meta Description -->
                    <div>
                        <label class="admin-label">Meta Description (SEO)</label>
                        <textarea id="post-meta-desc" rows="2" class="admin-input resize-none"
                                  placeholder="Descrizione per i motori di ricerca"></textarea>
                    </div>
                    
                    <!-- Help -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-1 flex items-center gap-1">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            Suggerimenti
                        </h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Usa titoli descrittivi</li>
                            <li>• Aggiungi almeno un'immagine</li>
                            <li>• L'estratto appare nelle anteprime</li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
    
    <!-- Preview Modal -->
    <div id="preview-modal" class="modal-overlay preview-modal">
        <div class="modal-content">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Anteprima</h3>
                <button id="close-preview" class="p-2 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <iframe id="preview-frame" class="preview-frame"></iframe>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/editor.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
