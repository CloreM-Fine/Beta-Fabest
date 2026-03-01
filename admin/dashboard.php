<?php
/**
 * Anteas Lucca - Admin Dashboard
 * Gestione post con tabella, filtri e paginazione
 */

declare(strict_types=1);

// Protezione accesso diretto
define('ANTEAS_APP', true);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Require authentication
$user = requireAuth();
$csrfToken = getCSRFToken();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Area Riservata | Anteas Lucca</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../public/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/favicon-16x16.png">
    <link rel="apple-touch-icon" href="../public/apple-touch-icon.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/css/tailwind.config.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    
    <!-- Sidebar -->
    <aside id="admin-sidebar" class="admin-sidebar">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-8">
                <img src="../public/logo/logo.svg" alt="Anteas Lucca" class="w-10 h-10 object-contain">
                <div>
                    <span class="text-white font-bold">Anteas</span>
                    <span class="text-cyan-300 text-sm block">Admin</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="space-y-1">
                <a href="dashboard.php" class="sidebar-nav-link active">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="editor.php" class="sidebar-nav-link">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Nuovo Post</span>
                </a>
                <a href="../blog/index.html" target="_blank" class="sidebar-nav-link">
                    <i data-lucide="external-link" class="w-5 h-5"></i>
                    <span>Vedi Sito</span>
                </a>
                <a href="../5x1000.html" target="_blank" class="sidebar-nav-link">
                    <i data-lucide="heart" class="w-5 h-5"></i>
                    <span>5×1000</span>
                </a>
            </nav>
        </div>
        
        <!-- User section -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="user-name" class="text-white font-medium text-sm truncate">
                        <?php echo e($user['display_name'] ?? $user['username']); ?>
                    </p>
                    <p class="text-slate-400 text-xs capitalize"><?php echo $user['role']; ?></p>
                </div>
                <button data-logout class="p-2 text-slate-400 hover:text-white transition-colors" title="Logout">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="editor.php" class="btn-admin btn-admin-primary">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Nuovo Post</span>
                </a>
            </div>
        </header>
        
        <!-- Content -->
        <div class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Totale Articoli</span>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        </div>
                    </div>
                    <p id="stat-total" class="text-3xl font-bold text-gray-900">-</p>
                </div>
                
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Pubblicati</span>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        </div>
                    </div>
                    <p id="stat-published" class="text-3xl font-bold text-gray-900">-</p>
                </div>
                
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Bozze</span>
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="file-clock" class="w-5 h-5 text-amber-600"></i>
                        </div>
                    </div>
                    <p id="stat-drafts" class="text-3xl font-bold text-gray-900">-</p>
                </div>
                
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Visualizzazioni</span>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="eye" class="w-5 h-5 text-purple-600"></i>
                        </div>
                    </div>
                    <p id="stat-views" class="text-3xl font-bold text-gray-900">-</p>
                </div>
            </div>
            
            <!-- Posts Table -->
            <div class="admin-card">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-lg font-bold text-gray-900">Articoli</h2>
                        
                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative">
                                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="search-input" 
                                       placeholder="Cerca per titolo..."
                                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none">
                            </div>
                            
                            <select id="filter-status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none">
                                <option value="">Tutti gli stati</option>
                                <option value="published">Pubblicati</option>
                                <option value="draft">Bozze</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titolo</th>
                                <th>Categoria</th>
                                <th>Stato</th>
                                <th>Data</th>
                                <th class="text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="posts-table-body">
                            <tr>
                                <td colspan="5" class="text-center py-8">
                                    <div id="table-loading" class="flex items-center justify-center gap-2 text-gray-500">
                                        <div class="spinner"></div>
                                        <span>Caricamento...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div id="pagination" class="p-4 border-t border-gray-100">
                    <!-- Pagination rendered by JS -->
                </div>
            </div>
        </div>
    </main>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="document.getElementById('admin-sidebar').classList.remove('mobile-open'); this.classList.add('hidden');"></div>
    
    <!-- Scripts -->
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script>
        lucide.createIcons();
        
        // Mobile sidebar toggle
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('hidden');
        });
    </script>
</body>
</html>
