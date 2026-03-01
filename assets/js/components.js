// Anteas Lucca - Components

// Article list for blog pages
async function loadArticles(containerId, limit = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // List of articles (in production, this would come from an API or index.json)
    const articles = [
        {
            slug: 'addio-vecchia-carta-identita-benvenuta-cie',
            filename: '2026-01-13-addio-vecchia-carta-identita-benvenuta-cie.md',
            title: "Addio vecchia carta identità, benvenuta CIE!",
            date: "13 Gennaio 2026",
            excerpt: "La carta d'identità cartacea cesserà di essere valida dal 3 agosto 2026. Ecco tutto quello che c'è da sapere sulla Carta d'Identità Elettronica (CIE).",
            category: "Uncategorized"
        },
        {
            slug: 'buone-feste',
            filename: '2025-12-24-buone-feste.md',
            title: "Buone feste!",
            date: "24 Dicembre 2025",
            excerpt: "Vi auguriamo giorni sereni, ma anche il tempo per fare nuovi progetti insieme, per condividere momenti di felicità.",
            category: "Uncategorized"
        },
        {
            slug: 'marcia-perugiassisi-per-la-pace',
            filename: '2025-09-17-marcia-perugiassisi-per-la-pace-e-la-fraternita.md',
            title: "Marcia PerugiAssisi per la pace e la fraternità",
            date: "17 Settembre 2025",
            excerpt: "ANTEAS Lucca ha deciso di partecipare e sostenere la MARCIA PERUGIA ASSISI il 12 OTTOBRE per la pace e la fraternità.",
            category: "Eventi"
        },
        {
            slug: 'un-dae-per-barga',
            filename: '2025-07-12-un-dae-per-barga.md',
            title: "Un DAE per Barga",
            date: "12 Luglio 2025",
            excerpt: "Venerdì 11 luglio una nuova tappa nel percorso di installazione dei DAE nella nostra provincia. Barga è il comune che ha visto donare alla comunità da parte di Anteas un defibrillatore.",
            category: "Eventi"
        },
        {
            slug: 'cambio-al-vertice-di-anteas-lucca',
            filename: '2025-07-11-cambio-al-vertice-di-anteas-lucca.md',
            title: "Cambio al vertice di Anteas Lucca",
            date: "11 Luglio 2025",
            excerpt: "Eletto nei giorni scorsi il nuovo Presidente di Anteas Lucca. È Massimo Santoni che succede a Giovanni Bolognini.",
            category: "Eventi"
        },
        {
            slug: 'cantar-di-donne',
            filename: '2025-03-14-cantar-di-donne.md',
            title: "Cantar di donne",
            date: "14 Marzo 2025",
            excerpt: "Martedì 18 marzo alle ore 17 ad Artè in via Carlo Piaggia a Capannori un pomeriggio di parole e musica con 'Cantar di donne'.",
            category: "Eventi"
        }
    ];

    const displayArticles = limit ? articles.slice(0, limit) : articles;

    container.innerHTML = displayArticles.map(article => `
        <article class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group">
            <div class="h-48 bg-gradient-to-br from-cyan-500 to-blue-600 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                <div class="absolute bottom-4 left-4">
                    <span class="bg-white/90 text-cyan-700 text-xs font-semibold px-3 py-1 rounded-full">
                        ${article.category}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <time class="text-sm text-gray-500 flex items-center gap-2 mb-3">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    ${article.date}
                </time>
                <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-cyan-600 transition-colors">
                    ${article.title}
                </h3>
                <p class="text-gray-600 mb-4 line-clamp-3">${article.excerpt}</p>
                <a href="blog/articolo.html?slug=${article.slug}" 
                   class="inline-flex items-center gap-2 text-cyan-600 font-semibold hover:text-cyan-700 transition-colors">
                    Leggi tutto
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </article>
    `).join('');

    // Re-initialize icons for new content
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Load single article
async function loadSingleArticle() {
    const contentDiv = document.getElementById('article-content');
    const titleDiv = document.getElementById('article-title');
    const dateDiv = document.getElementById('article-date');
    
    if (!contentDiv) return;

    // Get slug from URL
    const params = new URLSearchParams(window.location.search);
    const slug = params.get('slug');

    if (!slug) {
        contentDiv.innerHTML = '<p class="text-red-600">Articolo non trovato.</p>';
        return;
    }

    // Article database
    const articles = {
        'addio-vecchia-carta-identita-benvenuta-cie': {
            title: "Addio vecchia carta identità, benvenuta CIE!",
            date: "13 Gennaio 2026",
            author: "Redazione",
            content: `
                <p class="mb-4 leading-relaxed">La carta d'identità cartacea, indipendentemente dalla sua scadenza indicata, cesserà di essere valida a partire dal <strong>3 agosto 2026</strong>, per effetto del Regolamento Europeo 2019/1157 che ha definito nuovi requisiti di sicurezza a tutela dei cittadini, e dovrà quindi essere sostituita con la Carta d'Identità Elettronica (CIE).</p>
                
                <p class="mb-4 leading-relaxed">Abbiamo pensato di offrire alcune informazioni che aiutino anche le persone meno esperte ad orientarsi per la sostituzione della carta e il pieno utilizzo di quella elettronica.</p>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-8">Quando rinnovare?</h2>
                
                <p class="mb-4 leading-relaxed">La prima informazione riguarda quando (in quale data) è opportuno sostituire la carta. La carta d'identità di regola si sostituisce alla scadenza, ma anche nei sei mesi precedenti è considerato un rinnovo ordinario.</p>
                
                <p class="mb-4 leading-relaxed">Il criterio generale è che le carte scadono il giorno nel compleanno del titolare, quindi ogni anno il giorno del vostro compleanno anche la carta compie gli anni con voi.</p>
                
                <p class="mb-4 leading-relaxed">Quindi rinnovare la carta di regola è un atto da compiere il giorno del compleanno o nei giorni successivi. Se invece procedete ad un rinnovo anticipato, il primo anno di validità si ridurrà alla quantità di giorni o mesi che intercorrono tra il rinnovo e la data del vostro compleanno.</p>
                
                <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 my-6">
                    <p class="font-semibold text-cyan-800">Attenzione: Tutte le carte d'identità cartacee scadono il 3 agosto 2026!</p>
                    <p class="text-cyan-700 mt-2">Tutte le persone che hanno ancora questo vecchio tipo di carta devono procedere al rinnovo prima del 3 agosto indipendentemente dalla data del loro compleanno.</p>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-8">Ma in caso di viaggi?</h2>
                
                <p class="mb-4 leading-relaxed">La carta d'identità elettronica arriva materialmente circa una settimana dopo il suo rinnovo, pertanto riceverete una carta temporanea da utilizzare per alcuni giorni che tuttavia <strong>non è valida per viaggi in aereo e traghetto</strong>. Pertanto se avete in programma un viaggio occorre procedere al rinnovo prudenzialmente almeno 15 giorni prima del viaggio stesso.</p>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-8">Se avete compiuto 70 anni?</h2>
                
                <p class="mb-4 leading-relaxed">Il Governo ha annunciato una norma per abolire l'obbligo del rinnovo dopo i 70 anni, non si conoscono ancora i dettagli applicativi della norma ma è certo che resta l'obbligo di passare alla CARTA D'IDENTITÀ ELETTRONICA (si chiama CIE) se ancora non la avete.</p>
                
                <p class="mb-4 leading-relaxed">Quindi non indugiate e provvedete alla sostituzione della carta entro il 3 agosto 2026 se siete ancora in possesso della vecchia tipologia di carta d'identità cartacea.</p>
            `
        },
        'buone-feste': {
            title: "Buone feste!",
            date: "24 Dicembre 2025",
            author: "Redazione",
            content: `
                <p class="mb-4 leading-relaxed">Sono giornate di messaggi, visite, telefonate, acquisti, scambi di regali e buon cibo su tavole imbandite.</p>
                
                <p class="mb-4 leading-relaxed">Vi auguriamo giorni sereni, ma anche il tempo per fare nuovi progetti insieme, per condividere momenti di felicità.</p>
                
                <p class="mb-4 leading-relaxed">Che il Natale e il nuovo anno portino pace, salute e tante nuove opportunità per tutti.</p>
                
                <div class="text-center my-8">
                    <p class="text-xl font-serif italic text-cyan-700">"Buone feste da tutta la squadra di Anteas Lucca"</p>
                </div>
            `
        },
        'marcia-perugiassisi-per-la-pace': {
            title: "Marcia PerugiAssisi per la pace e la fraternità",
            date: "17 Settembre 2025",
            author: "Redazione",
            content: `
                <p class="mb-4 leading-relaxed">Noi ci saremo: <strong>ANTEAS Lucca</strong> ha deciso di partecipare e sostenere la partecipazione alla <strong>MARCIA PERUGIA ASSISI</strong> il <strong>12 OTTOBRE</strong> prossimo.</p>
                
                <p class="mb-4 leading-relaxed">Insieme ad altre organizzazioni abbiamo promosso la partecipazione a questa importante manifestazione per la pace e la fraternità.</p>
                
                <p class="mb-4 leading-relaxed">La marcia Perugia-Assisi è un appuntamento storico che riunisce ogni anno migliaia di persone di tutte le età e di ogni estrazione sociale per unire le voci in un unico grande grido: <strong>BASTA ALLA GUERRA</strong>.</p>
                
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 my-6">
                    <p class="font-semibold text-amber-800">Informazioni pratiche</p>
                    <p class="text-amber-700 mt-2">Per informazioni su come partecipare e sulle modalità di trasporto, contattate la sede di Anteas Lucca.</p>
                </div>
            `
        },
        'un-dae-per-barga': {
            title: "Un DAE per Barga",
            date: "12 Luglio 2025",
            author: "Redazione",
            content: `
                <p class="mb-4 leading-relaxed">Venerdì 11 luglio una nuova tappa nel percorso di installazione dei DAE nella nostra provincia.</p>
                
                <p class="mb-4 leading-relaxed"><strong>Barga</strong> è il comune che ha visto donare alla comunità da parte di Anteas un defibrillatore semiautomatico (DAE), installato in un luogo strategico del centro storico.</p>
                
                <p class="mb-4 leading-relaxed">Alla cerimonia di consegna hanno partecipato il presidente di Anteas Lucca, autorità locali e rappresentanti della protezione civile. L'installazione di defibrillatori nei comuni della provincia è uno dei progetti prioritari dell'associazione.</p>
                
                <p class="mb-4 leading-relaxed">Il DAE (Defibrillatore Automatico Esterno) è un dispositivo che può salvare vite in caso di arresto cardiaco. La sua presenza in luoghi pubblici aumenta significativamente le possibilità di sopravvivenza in attesa dell'arrivo dei soccorsi.</p>
            `
        },
        'cambio-al-vertice-di-anteas-lucca': {
            title: "Cambio al vertice di Anteas Lucca",
            date: "11 Luglio 2025",
            author: "Redazione",
            content: `
                <p class="mb-4 leading-relaxed">Eletto nei giorni scorsi il nuovo Presidente di Anteas Lucca. È <strong>Massimo Santoni</strong> che succede a <strong>Giovanni Bolognini</strong> che ha guidato l'associazione di volontariato vicina alla CISL per oltre un quinquennio.</p>
                
                <p class="mb-4 leading-relaxed">Alla riunione dell'assemblea dei soci, molto partecipata, sono intervenuti anche:</p>
                
                <ul class="list-disc list-inside mb-4 space-y-2">
                    <li>Il Presidente regionale Anteas <strong>Mauro Scotti</strong></li>
                    <li>Il segretario della CISL Toscana nord <strong>Massimo Bani</strong></li>
                    <li>La reggente della federazione pensionati <strong>Alessandra Biagini</strong></li>
                </ul>
                
                <p class="mb-4 leading-relaxed">Molti gli interventi che sostanzialmente ringraziavano Bolognini per il cammino svolto e apprezzamento per le parole di Santoni che ha tracciato un percorso in continuità e la prosecuzione dei rapporti con pensionati e confederazione CISL.</p>
                
                <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 my-6">
                    <p class="font-semibold text-cyan-800">Auguri al nuovo Presidente!</p>
                    <p class="text-cyan-700 mt-2">Da tutti i soci e i volontari di Anteas Lucca, i migliori auguri di buon lavoro al presidente Massimo Santoni.</p>
                </div>
            `
        },
        'cantar-di-donne': {
            title: "Cantar di donne",
            date: "14 Marzo 2025",
            author: "Redazione",
            content: `
                <p class="mb-4 leading-relaxed">Martedì 18 marzo alle ore 17 ad <strong>Artè</strong> in via Carlo Piaggia a Capannori un pomeriggio di parole e musica con "Cantar di donne".</p>
                
                <p class="mb-4 leading-relaxed">Uno spettacolo, ad <strong>ingresso libero</strong>, promosso da Anteas Lucca in occasione della Giornata Internazionale della Donna.</p>
                
                <p class="mb-4 leading-relaxed">Lo spettacolo è un viaggio attraverso le voci e le storie di donne che hanno segnato la storia, attraverso la musica e la narrazione. Un momento di riflessione e di celebrazione del ruolo fondamentale delle donne nella società.</p>
                
                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 my-6">
                    <p class="font-semibold text-purple-800">Informazioni evento</p>
                    <ul class="text-purple-700 mt-2 space-y-1">
                        <li><strong>Data:</strong> Martedì 18 marzo 2025</li>
                        <li><strong>Ora:</strong> 17:00</li>
                        <li><strong>Luogo:</strong> Artè, Via Carlo Piaggia, Capannori</li>
                        <li><strong>Ingresso:</strong> Libero</li>
                    </ul>
                </div>
            `
        }
    };

    const article = articles[slug];

    if (!article) {
        contentDiv.innerHTML = `
            <div class="text-center py-12">
                <i data-lucide="file-x" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Articolo non trovato</h2>
                <p class="text-gray-600 mb-4">L'articolo che stai cercando non esiste o è stato rimosso.</p>
                <a href="index.html" class="inline-flex items-center gap-2 text-cyan-600 font-semibold hover:text-cyan-700">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Torna al blog
                </a>
            </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    if (titleDiv) titleDiv.textContent = article.title;
    if (dateDiv) dateDiv.textContent = article.date;
    contentDiv.innerHTML = article.content;

    // Re-initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
