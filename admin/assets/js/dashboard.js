/**
 * Anteas Lucca - Admin Dashboard
 * Gestione lista post, filtri, paginazione
 */

const Dashboard = {
    posts: [],
    pagination: {},
    currentFilters: {
        status: '',
        search: '',
        page: 1,
        limit: 10
    },
    
    /**
     * Inizializza dashboard
     */
    async init() {
        // Verifica auth
        const isAuth = await Auth.requireAuth();
        if (!isAuth) return;
        
        // Mostra nome utente
        this.updateUserInfo();
        
        // Carica post
        await this.loadPosts();
        
        // Setup event listeners
        this.setupEventListeners();
    },
    
    /**
     * Aggiorna info utente nell'header
     */
    updateUserInfo() {
        if (Auth.user) {
            const nameEl = document.getElementById('user-name');
            if (nameEl) {
                nameEl.textContent = Auth.user.display_name || Auth.user.username;
            }
        }
    },
    
    /**
     * Carica post dal server
     */
    async loadPosts() {
        this.showLoading(true);
        
        try {
            const params = new URLSearchParams({
                page: this.currentFilters.page,
                limit: this.currentFilters.limit
            });
            
            if (this.currentFilters.status) {
                params.append('status', this.currentFilters.status);
            }
            if (this.currentFilters.search) {
                params.append('search', this.currentFilters.search);
            }
            
            const response = await fetch(`api/posts.php?${params}`);
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            this.posts = data.posts || [];
            this.pagination = data.pagination || {};
            
            this.renderPosts();
            this.renderPagination();
            this.updateStats();
            
        } catch (error) {
            console.error('Load posts error:', error);
            Toast.error('Errore nel caricare i post: ' + error.message);
        } finally {
            this.showLoading(false);
        }
    },
    
    /**
     * Renderizza tabella post
     */
    renderPosts() {
        const tbody = document.getElementById('posts-table-body');
        if (!tbody) return;
        
        if (this.posts.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">
                        <div class="empty-state">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                            <p>Nessun post trovato</p>
                        </div>
                    </td>
                </tr>
            `;
            lucide.createIcons();
            return;
        }
        
        tbody.innerHTML = this.posts.map(post => {
            const date = new Date(post.created_at).toLocaleDateString('it-IT');
            const statusClass = post.status === 'published' ? 'status-published' : 'status-draft';
            const statusText = post.status === 'published' ? 'Pubblicato' : 'Bozza';
            const statusIcon = post.status === 'published' ? 'check-circle' : 'file-clock';
            
            return `
                <tr data-post-id="${post.id}">
                    <td>
                        <div class="font-medium text-gray-900">${escapeHtml(post.title)}</div>
                        <div class="text-sm text-gray-500">/${post.slug}</div>
                    </td>
                    <td>
                        <span class="text-sm text-gray-600">${post.category || 'Generale'}</span>
                    </td>
                    <td>
                        <span class="status-badge ${statusClass}">
                            <i data-lucide="${statusIcon}" class="w-3 h-3"></i>
                            ${statusText}
                        </span>
                    </td>
                    <td class="text-sm text-gray-500">${date}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="editor.php?id=${post.id}" 
                               class="p-2 text-gray-400 hover:text-cyan-600 transition-colors" 
                               title="Modifica">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <a href="../blog/articolo.html?slug=${post.slug}" 
                               target="_blank"
                               class="p-2 text-gray-400 hover:text-cyan-600 transition-colors" 
                               title="Vedi">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                            <button onclick="Dashboard.deletePost(${post.id})" 
                                    class="p-2 text-gray-400 hover:text-red-600 transition-colors" 
                                    title="Elimina">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        
        lucide.createIcons();
    },
    
    /**
     * Renderizza paginazione
     */
    renderPagination() {
        const container = document.getElementById('pagination');
        if (!container || !this.pagination.pages) return;
        
        const { page, pages } = this.pagination;
        
        if (pages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = '';
        
        // Prev button
        html += `
            <button onclick="Dashboard.goToPage(${page - 1})" 
                    class="pagination-btn" 
                    ${page <= 1 ? 'disabled' : ''}>
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
        `;
        
        // Page numbers
        const startPage = Math.max(1, page - 2);
        const endPage = Math.min(pages, page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <button onclick="Dashboard.goToPage(${i})" 
                        class="pagination-btn ${i === page ? 'active' : ''}">
                    ${i}
                </button>
            `;
        }
        
        // Next button
        html += `
            <button onclick="Dashboard.goToPage(${page + 1})" 
                    class="pagination-btn" 
                    ${page >= pages ? 'disabled' : ''}>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        `;
        
        container.innerHTML = html;
        lucide.createIcons();
    },
    
    /**
     * Aggiorna statistiche
     */
    updateStats() {
        // Le statistiche reali verrebbero caricate da API separate
        // Per ora usiamo i dati dei post caricati
        const totalPosts = this.pagination.total || 0;
        const published = this.posts.filter(p => p.status === 'published').length;
        const drafts = this.posts.filter(p => p.status === 'draft').length;
        
        const totalEl = document.getElementById('stat-total');
        const publishedEl = document.getElementById('stat-published');
        const draftsEl = document.getElementById('stat-drafts');
        
        if (totalEl) totalEl.textContent = totalPosts;
        if (publishedEl) publishedEl.textContent = published;
        if (draftsEl) draftsEl.textContent = drafts;
    },
    
    /**
     * Cambia pagina
     */
    goToPage(page) {
        this.currentFilters.page = page;
        this.loadPosts();
    },
    
    /**
     * Filtra per stato
     */
    filterByStatus(status) {
        this.currentFilters.status = status;
        this.currentFilters.page = 1;
        this.loadPosts();
    },
    
    /**
     * Cerca per titolo
     */
    search(query) {
        this.currentFilters.search = query;
        this.currentFilters.page = 1;
        this.loadPosts();
    },
    
    /**
     * Elimina post
     */
    async deletePost(id) {
        if (!confirm('Sei sicuro di voler eliminare questo post?\\nL\\'azione è irreversibile.')) {
            return;
        }
        
        try {
            const csrfToken = await Auth.getCSRFToken();
            
            const response = await fetch(`api/posts.php?id=${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                Toast.success('Post eliminato con successo');
                await this.loadPosts();
            } else {
                throw new Error(data.error || 'Errore nell\'eliminazione');
            }
        } catch (error) {
            console.error('Delete error:', error);
            Toast.error(error.message);
        }
    },
    
    /**
     * Mostra/nasconde loading
     */
    showLoading(show) {
        const loader = document.getElementById('table-loading');
        if (loader) {
            loader.style.display = show ? 'block' : 'none';
        }
    },
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Filtro stato
        const statusFilter = document.getElementById('filter-status');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.filterByStatus(e.target.value);
            });
        }
        
        // Ricerca
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.search(e.target.value.trim());
                }, 300);
            });
        }
        
        // Toggle sidebar mobile
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }
    }
};

/**
 * Toast Notification System
 */
const Toast = {
    container: null,
    
    init() {
        this.container = document.createElement('div');
        this.container.className = 'toast-container';
        document.body.appendChild(this.container);
    },
    
    show(message, type = 'info', duration = 4000) {
        if (!this.container) this.init();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = type === 'success' ? 'check-circle' : 
                    type === 'error' ? 'x-circle' : 
                    type === 'warning' ? 'alert-triangle' : 'info';
        
        toast.innerHTML = `
            <i data-lucide="${icon}" class="w-5 h-5"></i>
            <span>${escapeHtml(message)}</span>
        `;
        
        this.container.appendChild(toast);
        lucide.createIcons();
        
        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    
    success(message, duration) {
        this.show(message, 'success', duration);
    },
    
    error(message, duration) {
        this.show(message, 'error', duration);
    },
    
    warning(message, duration) {
        this.show(message, 'warning', duration);
    },
    
    info(message, duration) {
        this.show(message, 'info', duration);
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

// Inizializza dashboard
document.addEventListener('DOMContentLoaded', () => {
    Dashboard.init();
});
