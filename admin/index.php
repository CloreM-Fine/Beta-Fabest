<?php
/**
 * Anteas Lucca - Admin Login
 * Enhanced login page with "Remember me"
 */

declare(strict_types=1);

// Protezione accesso diretto
define('ANTEAS_APP', true);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Se già autenticato, vai alla dashboard
if (checkAuth()) {
    header('Location: dashboard.php');
    exit;
}

$csrfToken = getCSRFToken();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Area Riservata | Anteas Lucca</title>
    
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
    
    <style>
        body {
            background: linear-gradient(135deg, #0891B2 0%, #1e40af 50%, #0f172a 100%);
            min-height: 100vh;
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl p-2">
                <img src="../public/logo/logo.svg" alt="Anteas Lucca" class="w-full h-full object-contain">
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Anteas Lucca</h1>
            <p class="text-cyan-100 text-lg">Area Riservata</p>
        </div>
        
        <!-- Login Card -->
        <div class="login-card rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Benvenuto</h2>
            <p class="text-gray-500 text-center mb-8">Accedi per gestire i contenuti</p>
            
            <form id="login-form" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required autocomplete="username"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all"
                               placeholder="Il tuo username">
                    </div>
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required minlength="4" autocomplete="current-password"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all"
                               placeholder="La tua password">
                    </div>
                </div>
                
                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="remember" name="remember" 
                               class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                        <span class="text-sm text-gray-600">Ricordami</span>
                    </label>
                    <a href="#" class="text-sm text-cyan-600 hover:text-cyan-700">
                        Password dimenticata?
                    </a>
                </div>
                
                <!-- Error Message -->
                <div id="error-message" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                        <p class="text-red-700 text-sm font-medium"></p>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="submit-btn"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-cyan-600 text-white font-semibold rounded-lg hover:bg-cyan-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-cyan-600/30">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span>Accedi all'Area Riservata</span>
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="../index.html" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-cyan-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Torna al sito pubblico
                </a>
            </div>
        </div>
        
        <p class="text-center text-cyan-100 text-sm mt-8">
            © <?php echo date('Y'); ?> Anteas Lucca - Tutti i diritti riservati
        </p>
    </div>
    
    <!-- Scripts -->
    <script src="assets/js/auth.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
