/**
 * Anteas Lucca - Editor Drag-Drop
 * Editor a blocchi con SortableJS
 */

const Editor = {
    blocks: [],
    postId: null,
    sortable: null,
    autoSaveTimer: null,
    hasChanges: false,
    
    /**
     * Inizializza editor
     */
    async init() {
        // Verifica auth
        const isAuth = await Auth.requireAuth();
        if (!isAuth) return;
        
        // Ottieni ID post da URL
        const urlParams = new URLSearchParams(window.location.search);
        this.postId = urlParams.get('id');
        
        // Setup UI
        this.setupEventListeners();
        this.initSortable();
        
        // Carica post se in modifica
        if (this.postId) {
            await this.loadPost(this.postId);
        } else {
            // Nuovo post - aggiungi blocco testo di default
            this.addBlock('text');
        }
        
        // Avvia auto-save
        this.startAutoSave();
        
        // Warning prima di uscire con modifiche
        window.addEventListener('beforeunload', (e) => {
            if (this.hasChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    },
    
    /**
     * Inizializza SortableJS
     */
    initSortable() {
        const canvas = document.getElementById('editor-canvas');
        if (!canvas) return;
        
        this.sortable = Sortable.create(canvas, {
            group: 'editor-blocks',
            animation: 150,
            handle: '.block-handle',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: (evt) => {
                // Aggiorna ordine array
                const item = this.blocks.splice(evt.oldIndex, 1)[0];
                this.blocks.splice(evt.newIndex, 0, item);
                this.markAsChanged();
            }
        });
    },
    
    /**
     * Carica post esistente
     */
    async loadPost(id) {
        try {
            const response = await fetch(`api/posts.php?id=${id}`);
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            const post = data.post;
            
            // Popola campi
            document.getElementById('post-title').value = post.title;
            document.getElementById('post-slug').value = post.slug;
            document.getElementById('post-category').value = post.category || 'generale';
            document.getElementById('post-status').value = post.status;
            document.getElementById('post-excerpt').value = post.excerpt || '';
            document.getElementById('post-meta-title').value = post.meta_title || '';
            document.getElementById('post-meta-desc').value = post.meta_description || '';
            
            // Carica blocchi
            if (post.content && Array.isArray(post.content)) {
                this.blocks = post.content;
                this.renderBlocks();
            }
            
            // Mostra immagine in evidenza se presente
            if (post.featured_image) {
                this.showFeaturedImagePreview(post.featured_image);
            }
            
        } catch (error) {
            console.error('Load post error:', error);
            Toast.error('Errore nel caricare il post');
        }
    },
    
    /**
     * Aggiunge un nuovo blocco
     */
    addBlock(type, data = null) {
        const block = {
            id: 'block_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            type: type,
            data: data || this.getDefaultBlockData(type)
        };
        
        this.blocks.push(block);
        this.renderBlock(block);
        this.markAsChanged();
        
        // Scroll al nuovo blocco
        const el = document.querySelector(`[data-block-id="${block.id}"]`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },
    
    /**
     * Ottiene dati default per tipo blocco
     */
    getDefaultBlockData(type) {
        switch (type) {
            case 'text':
                return { content: '' };
            case 'heading':
                return { content: '', level: 2 };
            case 'image':
                return { src: '', alt: '', caption: '', alignment: 'center' };
            case 'list':
                return { items: [], ordered: false };
            case 'quote':
                return { content: '', author: '' };
            case 'separator':
                return {};
            case 'cta':
                return { title: '', text: '', buttonText: '', buttonUrl: '', bgColor: 'primary' };
            default:
                return {};
        }
    },
    
    /**
     * Renderizza tutti i blocchi
     */
    renderBlocks() {
        const canvas = document.getElementById('editor-canvas');
        canvas.innerHTML = '';
        this.blocks.forEach(block => this.renderBlock(block));
    },
    
    /**
     * Renderizza singolo blocco
     */
    renderBlock(block) {
        const canvas = document.getElementById('editor-canvas');
        const el = document.createElement('div');
        el.className = 'editor-block';
        el.dataset.blockId = block.id;
        el.dataset.blockType = block.type;
        
        const icon = this.getBlockIcon(block.type);
        const label = this.getBlockLabel(block.type);
        
        let contentHtml = '';
        
        switch (block.type) {
            case 'text':
                contentHtml = `
                    <textarea class="block-textarea w-full border-0 focus:ring-0 resize-y min-h-[120px] p-4" 
                              placeholder="Inserisci il testo...">${escapeHtml(block.data.content || '')}</textarea>
                `;
                break;
                
            case 'heading':
                contentHtml = `
                    <div class="p-4">
                        <select class="block-heading-level mb-2 text-sm border-gray-300 rounded" onchange="Editor.updateBlock('${block.id}', 'level', this.value)">
                            <option value="2" ${block.data.level === 2 ? 'selected' : ''}>H2 - Titolo</option>
                            <option value="3" ${block.data.level === 3 ? 'selected' : ''}>H3 - Sottotitolo</option>
                            <option value="4" ${block.data.level === 4 ? 'selected' : ''}>H4 - Sezione</option>
                        </select>
                        <input type="text" class="block-heading-input w-full text-2xl font-bold border-0 focus:ring-0 p-0" 
                               placeholder="Titolo..." value="${escapeHtml(block.data.content || '')}">
                    </div>
                `;
                break;
                
            case 'image':
                contentHtml = `
                    <div class="p-4">
                        ${block.data.src ? `
                            <div class="mb-3">
                                <img src="${escapeHtml(block.data.src)}" alt="${escapeHtml(block.data.alt || '')}" class="max-h-64 rounded-lg object-cover">
                            </div>
                        ` : `
                            <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-cyan-500 hover:bg-cyan-50 transition-colors" onclick="Editor.uploadImage('${block.id}')">
                                <i data-lucide="image-plus" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                                <p class="text-sm text-gray-500">Clicca per caricare un'immagine</p>
                            </div>
                        `}
                        <div class="mt-3 space-y-2">
                            <input type="text" class="block-image-alt w-full text-sm border-gray-300 rounded" 
                                   placeholder="Testo alternativo (accessibilità)" value="${escapeHtml(block.data.alt || '')}">
                            <input type="text" class="block-image-caption w-full text-sm border-gray-300 rounded" 
                                   placeholder="Didascalia (opzionale)" value="${escapeHtml(block.data.caption || '')}">
                            <select class="block-image-align text-sm border-gray-300 rounded" onchange="Editor.updateBlock('${block.id}', 'alignment', this.value)">
                                <option value="left" ${block.data.alignment === 'left' ? 'selected' : ''}>Sinistra</option>
                                <option value="center" ${block.data.alignment === 'center' ? 'selected' : ''}>Centro</option>
                                <option value="right" ${block.data.alignment === 'right' ? 'selected' : ''}>Destra</option>
                                <option value="full" ${block.data.alignment === 'full' ? 'selected' : ''}>Larghezza piena</option>
                            </select>
                        </div>
                    </div>
                `;
                break;
                
            case 'list':
                contentHtml = `
                    <div class="p-4">
                        <div class="flex gap-2 mb-2">
                            <button type="button" class="list-type-btn px-3 py-1 text-sm rounded ${!block.data.ordered ? 'bg-cyan-100 text-cyan-700' : 'bg-gray-100'}" onclick="Editor.setListType('${block.id}', false)">Punti</button>
                            <button type="button" class="list-type-btn px-3 py-1 text-sm rounded ${block.data.ordered ? 'bg-cyan-100 text-cyan-700' : 'bg-gray-100'}" onclick="Editor.setListType('${block.id}', true)">Numeri</button>
                        </div>
                        <textarea class="block-list-items w-full border-gray-300 rounded min-h-[120px] font-mono text-sm" 
                                  placeholder="Inserisci gli elementi, uno per riga...">${escapeHtml((block.data.items || []).join('\\n'))}</textarea>
                    </div>
                `;
                break;
                
            case 'quote':
                contentHtml = `
                    <div class="p-4">
                        <textarea class="block-quote-content w-full border-l-4 border-cyan-500 bg-cyan-50 p-4 rounded-r-lg min-h-[100px] italic resize-y" 
                                  placeholder="Inserisci la citazione...">${escapeHtml(block.data.content || '')}</textarea>
                        <input type="text" class="block-quote-author w-full mt-2 text-sm border-gray-300 rounded" 
                               placeholder="Autore (opzionale)" value="${escapeHtml(block.data.author || '')}">
                    </div>
                `;
                break;
                
            case 'separator':
                contentHtml = `
                    <div class="p-4 text-center">
                        <hr class="border-gray-300">
                    </div>
                `;
                break;
                
            case 'cta':
                contentHtml = `
                    <div class="p-4">
                        <input type="text" class="block-cta-title w-full text-lg font-bold border-gray-300 rounded mb-2" 
                               placeholder="Titolo..." value="${escapeHtml(block.data.title || '')}">
                        <textarea class="block-cta-text w-full border-gray-300 rounded mb-2" 
                                  placeholder="Testo descrittivo...">${escapeHtml(block.data.text || '')}</textarea>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" class="block-cta-button-text border-gray-300 rounded" 
                                   placeholder="Testo bottone" value="${escapeHtml(block.data.buttonText || '')}">
                            <input type="text" class="block-cta-button-url border-gray-300 rounded" 
                                   placeholder="URL" value="${escapeHtml(block.data.buttonUrl || '')}">
                        </div>
                        <select class="block-cta-bg mt-2 text-sm border-gray-300 rounded" onchange="Editor.updateBlock('${block.id}', 'bgColor', this.value)">
                            <option value="primary" ${block.data.bgColor === 'primary' ? 'selected' : ''}>Primario (Cyan)</option>
                            <option value="secondary" ${block.data.bgColor === 'secondary' ? 'selected' : ''}>Secondario (Blu)</option>
                            <option value="accent" ${block.data.bgColor === 'accent' ? 'selected' : ''}>Accent (Arancio)</option>
                            <option value="dark" ${block.data.bgColor === 'dark' ? 'selected' : ''}>Scuro</option>
                        </select>
                    </div>
                `;
                break;
        }
        
        el.innerHTML = `
            <div class="editor-block-header">
                <div class="flex items-center gap-2">
                    <span class="block-handle cursor-grab p-1 text-gray-400 hover:text-gray-600">
                        <i data-lucide="grip-vertical" class="w-4 h-4"></i>
                    </span>
                    <i data-lucide="${icon}" class="w-4 h-4 text-gray-400"></i>
                    <span class="text-xs font-medium text-gray-500 uppercase">${label}</span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" class="p-1 text-gray-400 hover:text-gray-600" onclick="Editor.moveBlockUp('${block.id}')">
                        <i data-lucide="chevron-up" class="w-4 h-4"></i>
                    </button>
                    <button type="button" class="p-1 text-gray-400 hover:text-gray-600" onclick="Editor.moveBlockDown('${block.id}')">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <button type="button" class="p-1 text-gray-400 hover:text-red-600" onclick="Editor.removeBlock('${block.id}')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div class="editor-block-content">
                ${contentHtml}
            </div>
        `;
        
        canvas.appendChild(el);
        lucide.createIcons();
        
        // Setup event listeners per input
        this.setupBlockListeners(el, block);
    },
    
    /**
     * Setup listeners per input del blocco
     */
    setupBlockListeners(el, block) {
        // Textarea text
        const textarea = el.querySelector('.block-textarea');
        if (textarea) {
            textarea.addEventListener('input', () => {
                block.data.content = textarea.value;
                this.markAsChanged();
            });
        }
        
        // Heading input
        const headingInput = el.querySelector('.block-heading-input');
        if (headingInput) {
            headingInput.addEventListener('input', () => {
                block.data.content = headingInput.value;
                this.markAsChanged();
            });
        }
        
        // Image fields
        const altInput = el.querySelector('.block-image-alt');
        if (altInput) {
            altInput.addEventListener('input', () => {
                block.data.alt = altInput.value;
                this.markAsChanged();
            });
        }
        
        // List textarea
        const listTextarea = el.querySelector('.block-list-items');
        if (listTextarea) {
            listTextarea.addEventListener('input', () => {
                block.data.items = listTextarea.value.split('\\n').filter(i => i.trim());
                this.markAsChanged();
            });
        }
        
        // Quote fields
        const quoteContent = el.querySelector('.block-quote-content');
        if (quoteContent) {
            quoteContent.addEventListener('input', () => {
                block.data.content = quoteContent.value;
                this.markAsChanged();
            });
        }
        
        // CTA fields
        const ctaTitle = el.querySelector('.block-cta-title');
        if (ctaTitle) {
            ctaTitle.addEventListener('input', () => {
                block.data.title = ctaTitle.value;
                this.markAsChanged();
            });
        }
    },
    
    /**
     * Ottiene icona per tipo blocco
     */
    getBlockIcon(type) {
        const icons = {
            text: 'type',
            heading: 'heading',
            image: 'image',
            list: 'list',
            quote: 'quote',
            separator: 'minus',
            cta: 'mouse-pointer'
        };
        return icons[type] || 'file';
    },
    
    /**
     * Ottiene label per tipo blocco
     */
    getBlockLabel(type) {
        const labels = {
            text: 'Testo',
            heading: 'Titolo',
            image: 'Immagine',
            list: 'Lista',
            quote: 'Citazione',
            separator: 'Separatore',
            cta: 'Call to Action'
        };
        return labels[type] || type;
    },
    
    /**
     * Rimuove blocco
     */
    removeBlock(id) {
        const index = this.blocks.findIndex(b => b.id === id);
        if (index > -1) {
            this.blocks.splice(index, 1);
            const el = document.querySelector(`[data-block-id="${id}"]`);
            if (el) el.remove();
            this.markAsChanged();
        }
    },
    
    /**
     * Sposta blocco su
     */
    moveBlockUp(id) {
        const index = this.blocks.findIndex(b => b.id === id);
        if (index > 0) {
            [this.blocks[index], this.blocks[index - 1]] = [this.blocks[index - 1], this.blocks[index]];
            this.renderBlocks();
            this.markAsChanged();
        }
    },
    
    /**
     * Sposta blocco giù
     */
    moveBlockDown(id) {
        const index = this.blocks.findIndex(b => b.id === id);
        if (index < this.blocks.length - 1) {
            [this.blocks[index], this.blocks[index + 1]] = [this.blocks[index + 1], this.blocks[index]];
            this.renderBlocks();
            this.markAsChanged();
        }
    },
    
    /**
     * Aggiorna dato blocco
     */
    updateBlock(id, key, value) {
        const block = this.blocks.find(b => b.id === id);
        if (block) {
            block.data[key] = value;
            this.markAsChanged();
        }
    },
    
    /**
     * Imposta tipo lista
     */
    setListType(id, ordered) {
        this.updateBlock(id, 'ordered', ordered);
        this.renderBlocks();
    },
    
    /**
     * Upload immagine
     */
    async uploadImage(blockId) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            const csrfToken = await Auth.getCSRFToken();
            const formData = new FormData();
            formData.append('image', file);
            formData.append('csrf_token', csrfToken);
            
            try {
                Toast.info('Caricamento immagine...');
                
                const response = await fetch('api/upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.updateBlock(blockId, 'src', data.upload.url);
                    this.renderBlocks();
                    Toast.success('Immagine caricata!');
                } else {
                    throw new Error(data.error);
                }
            } catch (error) {
                Toast.error('Errore upload: ' + error.message);
            }
        };
        input.click();
    },
    
    /**
     * Marca come modificato
     */
    markAsChanged() {
        this.hasChanges = true;
        const indicator = document.getElementById('save-indicator');
        if (indicator) {
            indicator.textContent = 'Modifiche non salvate';
            indicator.className = 'text-amber-600 text-sm';
        }
    },
    
    /**
     * Avvia auto-save
     */
    startAutoSave() {
        this.autoSaveTimer = setInterval(() => {
            if (this.hasChanges && this.postId) {
                this.savePost('draft', true); // silent save
            }
        }, 30000); // 30 secondi
    },
    
    /**
     * Salva post
     */
    async savePost(status = 'draft', silent = false) {
        const title = document.getElementById('post-title').value.trim();
        const slug = document.getElementById('post-slug').value.trim();
        
        if (!title) {
            if (!silent) Toast.error('Inserisci un titolo');
            return false;
        }
        
        // Filtra blocchi vuoti
        const validBlocks = this.blocks.filter(block => {
            if (block.type === 'text' || block.type === 'heading' || block.type === 'quote') {
                return block.data.content?.trim();
            }
            if (block.type === 'list') {
                return block.data.items?.length > 0;
            }
            if (block.type === 'image') {
                return block.data.src;
            }
            return true;
        });
        
        if (validBlocks.length === 0) {
            if (!silent) Toast.error('Aggiungi almeno un contenuto');
            return false;
        }
        
        const postData = {
            title,
            slug: slug || this.generateSlug(title),
            excerpt: document.getElementById('post-excerpt').value.trim(),
            category: document.getElementById('post-category').value,
            status,
            content: validBlocks.map(b => ({
                type: b.type,
                data: b.data
            })),
            meta_title: document.getElementById('post-meta-title')?.value || '',
            meta_description: document.getElementById('post-meta-desc')?.value || ''
        };
        
        try {
            const csrfToken = await Auth.getCSRFToken();
            
            const url = this.postId ? `api/posts.php?id=${this.postId}` : 'api/posts.php';
            const method = this.postId ? 'PUT' : 'POST';
            
            if (!silent) {
                const btn = document.getElementById('save-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<div class="spinner w-4 h-4 mr-2"></div> Salvo...';
                }
            }
            
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify(postData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.hasChanges = false;
                
                const indicator = document.getElementById('save-indicator');
                if (indicator) {
                    indicator.textContent = status === 'draft' ? 'Bozza salvata' : 'Pubblicato';
                    indicator.className = 'text-green-600 text-sm';
                }
                
                if (!silent) {
                    Toast.success(status === 'draft' ? 'Bozza salvata!' : 'Post pubblicato!');
                    
                    if (!this.postId && data.post) {
                        // Redirect su nuovo post
                        setTimeout(() => {
                            window.location.href = `editor.php?id=${data.post.id}`;
                        }, 500);
                    }
                }
                
                return true;
            } else {
                throw new Error(data.error || 'Errore di salvataggio');
            }
        } catch (error) {
            if (!silent) Toast.error(error.message);
            return false;
        } finally {
            if (!silent) {
                const btn = document.getElementById('save-btn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = 'Salva';
                }
            }
        }
    },
    
    /**
     * Genera slug da titolo
     */
    generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .substring(0, 100);
    },
    
    /**
     * Mostra anteprima
     */
    showPreview() {
        const modal = document.getElementById('preview-modal');
        const frame = document.getElementById('preview-frame');
        
        // Costruisci HTML preview
        const title = document.getElementById('post-title').value;
        const html = this.buildPreviewHTML(title);
        
        frame.srcdoc = html;
        modal.classList.add('active');
    },
    
    /**
     * Costruisce HTML per preview
     */
    buildPreviewHTML(title) {
        // Semplice preview HTML
        let content = '';
        
        this.blocks.forEach(block => {
            switch (block.type) {
                case 'text':
                    content += `<p class="mb-4">${escapeHtml(block.data.content || '')}</p>`;
                    break;
                case 'heading':
                    const tag = `h${block.data.level || 2}`;
                    content += `<${tag} class="text-2xl font-bold mb-4">${escapeHtml(block.data.content || '')}</${tag}>`;
                    break;
                case 'image':
                    content += `<img src="${escapeHtml(block.data.src || '')}" alt="${escapeHtml(block.data.alt || '')}" class="my-4 max-w-full">`;
                    break;
                case 'list':
                    const tagList = block.data.ordered ? 'ol' : 'ul';
                    content += `<${tagList} class="list-disc pl-5 mb-4">`;
                    (block.data.items || []).forEach(item => {
                        content += `<li>${escapeHtml(item)}</li>`;
                    });
                    content += `</${tagList}>`;
                    break;
                case 'quote':
                    content += `<blockquote class="border-l-4 border-cyan-500 pl-4 italic my-4">${escapeHtml(block.data.content || '')}</blockquote>`;
                    break;
            }
        });
        
        return `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"><\/script>
</head>
<body class="p-8 max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">${escapeHtml(title || 'Senza titolo')}</h1>
    ${content}
</body>
</html>`;
    },
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Titolo -> auto slug
        const titleInput = document.getElementById('post-title');
        const slugInput = document.getElementById('post-slug');
        if (titleInput && slugInput) {
            titleInput.addEventListener('blur', () => {
                if (!slugInput.value && titleInput.value) {
                    slugInput.value = this.generateSlug(titleInput.value);
                }
            });
            titleInput.addEventListener('input', () => this.markAsChanged());
        }
        
        // Altri input
        ['post-category', 'post-status', 'post-excerpt', 'post-meta-title', 'post-meta-desc'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', () => this.markAsChanged());
                el.addEventListener('input', () => this.markAsChanged());
            }
        });
        
        // Pulsanti blocchi
        document.querySelectorAll('[data-block-type]').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.blockType;
                this.addBlock(type);
            });
        });
        
        // Salva bozza
        document.getElementById('btn-save-draft')?.addEventListener('click', () => {
            this.savePost('draft');
        });
        
        // Pubblica
        document.getElementById('btn-publish')?.addEventListener('click', () => {
            this.savePost('published');
        });
        
        // Anteprima
        document.getElementById('btn-preview')?.addEventListener('click', () => {
            this.showPreview();
        });
        
        // Chiudi modal
        document.getElementById('close-preview')?.addEventListener('click', () => {
            document.getElementById('preview-modal').classList.remove('active');
        });
        
        // Featured image upload
        document.getElementById('featured-image-input')?.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            const csrfToken = await Auth.getCSRFToken();
            const formData = new FormData();
            formData.append('image', file);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('api/upload.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('featured-image-url').value = data.upload.url;
                    this.showFeaturedImagePreview(data.upload.url);
                }
            } catch (error) {
                Toast.error('Errore upload');
            }
        });
    },
    
    /**
     * Mostra preview immagine in evidenza
     */
    showFeaturedImagePreview(url) {
        const container = document.getElementById('featured-image-preview');
        if (container) {
            container.innerHTML = `<img src="${escapeHtml(url)}" class="max-h-32 rounded-lg object-cover">`;
        }
    }
};

/**
 * Escape HTML
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Inizializza
document.addEventListener('DOMContentLoaded', () => {
    Editor.init();
});
