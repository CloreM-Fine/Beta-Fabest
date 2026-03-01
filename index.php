<?php
/**
 * Anteas Lucca - Homepage
 * Usa componenti header/footer
 */

$pageTitle = 'Anteas Lucca - Associazione Tutte le Età Attive per la Solidarietà';
$pageDescription = 'Associazione di volontariato presente da oltre 20 anni sul territorio con iniziative, progetti e aiuti in campo sociale.';
$activeMenu = 'home';
$basePath = '';

include 'includes/header-component.php';
?>

    <!-- Hero Section -->
    <section class="relative min-h-[600px] flex items-center justify-center pt-20">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=1920&q=80" 
                 alt="Volontariato" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 via-gray-900/70 to-gray-900/50"></div>
        </div>
        
        <!-- Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/20 backdrop-blur-sm rounded-full text-cyan-300 text-sm font-medium mb-6">
                    <i data-lucide="heart" class="w-4 h-4"></i>
                    Associazione di Volontariato dal 2003
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    Anteas <span class="text-cyan-400">Lucca</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8 leading-relaxed">
                    Associazione <strong>tutte le età attive per la solidarietà</strong>. Da oltre 20 anni al servizio del territorio lucchese con iniziative, progetti e aiuti in campo sociale.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="servizi.html" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-cyan-600 text-white font-semibold rounded-lg hover:bg-cyan-700 transition-colors shadow-lg shadow-cyan-600/30">
                        <i data-lucide="hand-heart" class="w-5 h-5"></i>
                        Scopri i servizi
                    </a>
                    <a href="5x1000.html" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-lg hover:bg-white/20 transition-colors border border-white/20">
                        <i data-lucide="gift" class="w-5 h-5"></i>
                        Dona il 5×1000
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-16 h-16 bg-cyan-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar-days" class="w-8 h-8 text-cyan-600"></i>
                    </div>
                    <div class="text-4xl font-bold text-cyan-600 mb-2">20+</div>
                    <p class="text-gray-600 font-medium">Anni di attività</p>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="building-2" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">500+</div>
                    <p class="text-gray-600 font-medium">Associazioni partner</p>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="w-8 h-8 text-amber-600"></i>
                    </div>
                    <div class="text-4xl font-bold text-amber-600 mb-2">80.000+</div>
                    <p class="text-gray-600 font-medium">Soci in Italia</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Services Preview -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 animate-on-scroll">
                <span class="text-cyan-600 font-semibold text-sm uppercase tracking-wider">Cosa facciamo</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 mb-4">I nostri servizi</h2>
                <p class="text-gray-600 text-lg">Offriamo supporto concreto alle persone in difficoltà attraverso progetti di volontariato e servizi sociali.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Trasporto Sociale -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 border border-gray-100 animate-on-scroll">
                    <div class="w-14 h-14 bg-cyan-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="truck" class="w-7 h-7 text-cyan-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Trasporto Sociale</h3>
                    <p class="text-gray-600 mb-4">Servizio di accompagnamento per visite mediche, terapie e commissioni per anziani e persone con difficoltà motorie.</p>
                    <a href="servizi.html#trasporto" class="inline-flex items-center gap-1 text-cyan-600 font-medium hover:text-cyan-700">
                        Scopri di più <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <!-- Compagnia Anziani -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 border border-gray-100 animate-on-scroll">
                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="heart-handshake" class="w-7 h-7 text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Compagnia Anziani</h3>
                    <p class="text-gray-600 mb-4">Visite domiciliari e attività di socializzazione per combattere la solitudine dei nostri anziani.</p>
                    <a href="servizi.html#compagnia" class="inline-flex items-center gap-1 text-cyan-600 font-medium hover:text-cyan-700">
                        Scopri di più <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <!-- Gite -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 border border-gray-100 animate-on-scroll">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="map" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Gite e Viaggi</h3>
                    <p class="text-gray-600 mb-4">Organizzazione di escursioni, visite culturali e viaggi per scoprire il territorio e socializzare.</p>
                    <a href="gite.html" class="inline-flex items-center gap-1 text-cyan-600 font-medium hover:text-cyan-700">
                        Scopri di più <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <!-- DAE -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 border border-gray-100 animate-on-scroll">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="heart-pulse" class="w-7 h-7 text-amber-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Defibrillatori (DAE)</h3>
                    <p class="text-gray-600 mb-4">Installazione di defibrillatori semiautomatici nei comuni della provincia per la sicurezza dei cittadini.</p>
                    <a href="servizi.html#dae" class="inline-flex items-center gap-1 text-cyan-600 font-medium hover:text-cyan-700">
                        Scopri di più <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <!-- Eventi -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 border border-gray-100 animate-on-scroll">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="calendar-heart" class="w-7 h-7 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Eventi Culturali</h3>
                    <p class="text-gray-600 mb-4">Incontri, conferenze, spettacoli e attività di animazione per la comunità.</p>
                    <a href="blog/index.html" class="inline-flex items-center gap-1 text-cyan-600 font-medium hover:text-cyan-700">
                        Scopri di più <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <!-- Informatica -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 border border-gray-100 animate-on-scroll">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="monitor" class="w-7 h-7 text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Informatica per Anziani</h3>
                    <p class="text-gray-600 mb-4">Corsi di alfabetizzazione digitale per avvicinare gli anziani alle nuove tecnologie.</p>
                    <a href="servizi.html#informatica" class="inline-flex items-center gap-1 text-cyan-600 font-medium hover:text-cyan-700">
                        Scopri di più <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- 5x1000 Banner -->
    <section class="py-16 bg-gradient-to-r from-secondary-800 to-secondary-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 rounded-full text-amber-300 text-sm font-medium mb-4">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                        Sostieni la nostra associazione
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Dona il tuo 5×1000 ad Anteas Lucca</h2>
                    <p class="text-gray-300 text-lg max-w-2xl">Il tuo contributo ci aiuta a essere ancora più vicini alle persone, incontrando bisogni ed esigenze della comunità.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 text-center">
                    <p class="text-gray-300 text-sm mb-2">Codice Fiscale</p>
                    <p class="text-3xl sm:text-4xl font-bold text-white font-mono tracking-wider">92019070462</p>
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <p class="text-gray-300 text-xs mb-1">IBAN per contributi</p>
                        <p class="text-white font-mono text-sm">IT94F0538713704000048010478</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Blog Preview -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="text-cyan-600 font-semibold text-sm uppercase tracking-wider">Novità</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Ultimi articoli</h2>
                </div>
                <a href="blog/index.html" class="inline-flex items-center gap-2 text-cyan-600 font-semibold hover:text-cyan-700 transition-colors">
                    Vedi tutti gli articoli
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
            
            <div id="blog-preview" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Articles will be loaded by JS -->
            </div>
        </div>
    </section>
    
    <!-- Contact Mini -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-on-scroll">
                    <span class="text-cyan-600 font-semibold text-sm uppercase tracking-wider">Contatti</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 mb-6">Come trovarci</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="map-pin" class="w-6 h-6 text-cyan-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Sede</h3>
                                <p class="text-gray-600">Viale Puccini, 1780<br>55100 Lucca (LU)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="phone" class="w-6 h-6 text-cyan-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Telefono</h3>
                                <p class="text-gray-600">0583 508862</p>
                                <p class="text-sm text-gray-500">Cell. per trasporto: 328 736 8068</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-6 h-6 text-cyan-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email</h3>
                                <p class="text-gray-600">anteaslucca@pec.it<br>sociale@anteaslucca.it</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="contatti.html" class="inline-flex items-center gap-2 mt-8 px-6 py-3 bg-cyan-600 text-white font-semibold rounded-lg hover:bg-cyan-700 transition-colors">
                        Scrivici
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <div class="animate-on-scroll">
                    <!-- Map -->
                    <div class="bg-gray-100 rounded-2xl h-96 flex items-center justify-center relative overflow-hidden">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2883.0479240989857!2d10.494!3d43.8418!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDPCsDUwJzMwLjUiTiAxMMKwMjknMzguNCJF!5e0!3m2!1sit!2sit!4v1609459200000!5m2!1sit!2sit"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            class="absolute inset-0">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
include 'includes/footer-component.php';
?>

<!-- Blog preview loader -->
<script src="assets/js/components.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadArticles('blog-preview', 3);
    });
</script>
