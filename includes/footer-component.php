<?php
/**
 * Anteas Lucca - Footer Component
 * Footer riutilizzabile per tutte le pagine
 * 
 * Uso: include 'includes/footer-component.php'; (dopo aver definito $basePath)
 */

$basePath = $basePath ?? '';
?>
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- Logo & Description -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="<?php echo $basePath; ?>public/logo/logo.svg" alt="Anteas Lucca" class="w-10 h-10 object-contain">
                        <span class="text-xl font-bold text-white">Anteas Lucca</span>
                    </div>
                    <p class="text-gray-400 mb-6">Associazione tutte le età attive per la solidarietà. Da oltre 20 anni al servizio del territorio.</p>
                    <div class="flex items-center gap-4">
                        <a href="https://www.facebook.com/anteas.lucca" target="_blank" rel="noopener" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition-colors">
                            <i data-lucide="facebook" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition-colors">
                            <i data-lucide="instagram" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Link Rapidi</h3>
                    <ul class="space-y-3">
                        <li><a href="<?php echo $basePath; ?>chi-siamo.html" class="hover:text-cyan-400 transition-colors">Chi Siamo</a></li>
                        <li><a href="<?php echo $basePath; ?>servizi.html" class="hover:text-cyan-400 transition-colors">Servizi</a></li>
                        <li><a href="<?php echo $basePath; ?>gite.html" class="hover:text-cyan-400 transition-colors">Gite</a></li>
                        <li><a href="<?php echo $basePath; ?>blog/index.html" class="hover:text-cyan-400 transition-colors">Blog</a></li>
                        <li><a href="<?php echo $basePath; ?>5x1000.html" class="hover:text-cyan-400 transition-colors">5×1000</a></li>
                    </ul>
                </div>
                
                <!-- Services -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Servizi</h3>
                    <ul class="space-y-3">
                        <li><a href="<?php echo $basePath; ?>servizi.html#trasporto" class="hover:text-cyan-400 transition-colors">Trasporto Sociale</a></li>
                        <li><a href="<?php echo $basePath; ?>servizi.html#compagnia" class="hover:text-cyan-400 transition-colors">Compagnia Anziani</a></li>
                        <li><a href="<?php echo $basePath; ?>servizi.html#dae" class="hover:text-cyan-400 transition-colors">Defibrillatori</a></li>
                        <li><a href="<?php echo $basePath; ?>gite.html" class="hover:text-cyan-400 transition-colors">Gite Culturali</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Contatti</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 mt-1 text-cyan-500"></i>
                            <span>Viale Puccini, 1780<br>55100 Lucca (LU)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4 text-cyan-500"></i>
                            <span>0583 508862</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="smartphone" class="w-4 h-4 text-cyan-500"></i>
                            <span>328 736 8068</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-cyan-500"></i>
                            <span>anteaslucca@pec.it</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm">
                    <p>&copy; <?php echo date('Y'); ?> Anteas Lucca. Tutti i diritti riservati.</p>
                    <div class="flex items-center gap-6">
                        <a href="<?php echo $basePath; ?>privacy.html" class="hover:text-cyan-400 transition-colors">Privacy Policy</a>
                        <a href="<?php echo $basePath; ?>cookie.html" class="hover:text-cyan-400 transition-colors">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="<?php echo $basePath; ?>assets/js/main.js"></script>
    <script>
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Mobile menu close button
        document.getElementById('menu-close-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.add('translate-x-full');
            document.body.style.overflow = '';
        });
    </script>
</body>
</html>
