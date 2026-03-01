<?php
/**
 * Anteas Lucca - Header Component
 * Header riutilizzabile per tutte le pagine
 * 
 * Uso: include 'includes/header-component.php'; (dopo aver definito $pageTitle)
 */

// Variabili di default
$pageTitle = $pageTitle ?? 'Anteas Lucca';
$pageDescription = $pageDescription ?? 'Associazione tutte le età attive per la solidarietà - Lucca';
$activeMenu = $activeMenu ?? '';
$basePath = $basePath ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://beta.etereastudio.it">
    <meta property="og:image" content="https://beta.etereastudio.it/public/logo/logo.svg">
    <meta property="og:site_name" content="Anteas Lucca">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $basePath; ?>public/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $basePath; ?>public/favicon-16x16.png">
    <link rel="apple-touch-icon" href="<?php echo $basePath; ?>public/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo $basePath; ?>public/site.webmanifest">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?php echo $basePath; ?>assets/css/tailwind.config.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans text-gray-800 bg-white">
    
    <!-- Header -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 bg-white transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="<?php echo $basePath; ?>index.html" class="flex items-center gap-3">
                    <img src="<?php echo $basePath; ?>public/logo/logo.svg" alt="Anteas Lucca" class="w-12 h-12 object-contain">
                    <div class="hidden sm:block">
                        <span class="text-xl font-bold text-gray-900">Anteas Lucca</span>
                        <p class="text-xs text-gray-500">Associazione di Volontariato</p>
                    </div>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="<?php echo $basePath; ?>index.html" class="<?php echo $activeMenu === 'home' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">Home</a>
                    <a href="<?php echo $basePath; ?>chi-siamo.html" class="<?php echo $activeMenu === 'chi-siamo' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">Chi Siamo</a>
                    <a href="<?php echo $basePath; ?>servizi.html" class="<?php echo $activeMenu === 'servizi' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">Servizi</a>
                    <a href="<?php echo $basePath; ?>gite.html" class="<?php echo $activeMenu === 'gite' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">Gite</a>
                    <a href="<?php echo $basePath; ?>blog/index.html" class="<?php echo $activeMenu === 'blog' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">Blog</a>
                    <a href="<?php echo $basePath; ?>contatti.html" class="<?php echo $activeMenu === 'contatti' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">Contatti</a>
                    <a href="<?php echo $basePath; ?>5x1000.html" class="<?php echo $activeMenu === '5x1000' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium transition-colors">5×1000</a>
                </nav>
                
                <!-- Right Side -->
                <div class="flex items-center gap-4">
                    <a href="/admin/index.php" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        Area Riservata
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button id="menu-toggle" class="lg:hidden p-2 text-gray-600 hover:text-cyan-600 transition-colors" aria-label="Apri menu">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu" class="fixed inset-0 z-50 translate-x-full transition-transform duration-300 lg:hidden">
        <div class="absolute inset-0 bg-black/50" id="menu-close"></div>
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-white shadow-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    <span class="text-xl font-bold text-gray-900">Menu</span>
                    <button id="menu-close-btn" class="p-2 text-gray-600 hover:text-cyan-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <nav class="flex flex-col gap-4">
                    <a href="<?php echo $basePath; ?>index.html" class="<?php echo $activeMenu === 'home' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">Home</a>
                    <a href="<?php echo $basePath; ?>chi-siamo.html" class="<?php echo $activeMenu === 'chi-siamo' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">Chi Siamo</a>
                    <a href="<?php echo $basePath; ?>servizi.html" class="<?php echo $activeMenu === 'servizi' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">Servizi</a>
                    <a href="<?php echo $basePath; ?>gite.html" class="<?php echo $activeMenu === 'gite' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">Gite</a>
                    <a href="<?php echo $basePath; ?>blog/index.html" class="<?php echo $activeMenu === 'blog' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">Blog</a>
                    <a href="<?php echo $basePath; ?>contatti.html" class="<?php echo $activeMenu === 'contatti' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">Contatti</a>
                    <a href="<?php echo $basePath; ?>5x1000.html" class="<?php echo $activeMenu === '5x1000' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'; ?> font-medium py-2 border-b border-gray-100">5×1000</a>
                    <a href="/admin/index.php" class="inline-flex items-center gap-2 px-4 py-3 bg-cyan-50 text-cyan-700 rounded-lg font-medium mt-4">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        Area Riservata
                    </a>
                </nav>
            </div>
        </div>
    </div>
