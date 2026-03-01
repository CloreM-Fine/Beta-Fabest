/**
 * Anteas Lucca - Admin Auth Module
 * Gestione login/logout e autenticazione JWT
 */

const Auth = {
    user: null,
    csrfToken: null,
    
    /**
     * Inizializza il modulo auth
     */
    init() {
        this.loadUser();
        this.setupEventListeners();
    },
    
    /**
     * Carica dati utente da localStorage
     */
    loadUser() {
        const stored = localStorage.getItem('anteas_admin_user');
        if (stored) {
            try {
                this.user = JSON.parse(stored);
            } catch (e) {
                this.user = null;
            }
        }
    },
    
    /**
     * Salva dati utente
     */
    saveUser(userData) {
        this.user = userData;
        localStorage.setItem('anteas_admin_user', JSON.stringify(userData));
    },
    
    /**
     * Verifica se l'utente è autenticato
     */
    isAuthenticated() {
        return !!this.user;
    },
    
    /**
     * Effettua il login
     */
    async login(username, password, remember = false) {
        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'login',
                    username,
                    password
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.saveUser(data.user);
                this.csrfToken = data.csrf_token;
                
                // Se remember me, salva nel localStorage
                if (remember) {
                    localStorage.setItem('anteas_remember', 'true');
                }
                
                return { success: true, user: data.user };
            } else {
                return { success: false, error: data.error || 'Credenziali non valide' };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: 'Errore di connessione. Riprova.' };
        }
    },
    
    /**
     * Effettua il logout
     */
    async logout() {
        try {
            await fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'logout' })
            });
        } catch (e) {
            console.error('Logout error:', e);
        } finally {
            // Pulisci in ogni caso
            this.user = null;
            this.csrfToken = null;
            localStorage.removeItem('anteas_admin_user');
            localStorage.removeItem('anteas_remember');
            window.location.href = 'index.php';
        }
    },
    
    /**
     * Verifica la validità del token
     */
    async verify() {
        try {
            const response = await fetch('api/auth.php');
            const data = await response.json();
            
            if (data.authenticated) {
                this.saveUser(data.user);
                this.csrfToken = data.csrf_token;
                return true;
            } else {
                this.user = null;
                localStorage.removeItem('anteas_admin_user');
                return false;
            }
        } catch (error) {
            console.error('Verify error:', error);
            return false;
        }
    },
    
    /**
     * Ottieni token CSRF
     */
    async getCSRFToken() {
        if (this.csrfToken) return this.csrfToken;
        
        try {
            const response = await fetch('api/csrf.php');
            const data = await response.json();
            this.csrfToken = data.csrf_token;
            return this.csrfToken;
        } catch (error) {
            console.error('CSRF error:', error);
            return null;
        }
    },
    
    /**
     * Richiede autenticazione o reindirizza
     */
    async requireAuth() {
        const isValid = await this.verify();
        if (!isValid) {
            window.location.href = 'index.php';
            return false;
        }
        return true;
    },
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Logout button
        document.querySelectorAll('[data-logout]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('Sei sicuro di voler uscire?')) {
                    this.logout();
                }
            });
        });
    }
};

// Inizializza
Auth.init();

/**
 * Gestione form login
 */
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember')?.checked || false;
            const submitBtn = document.getElementById('submit-btn');
            const errorDiv = document.getElementById('error-message');
            
            // Validazione
            if (!username || !password) {
                showLoginError('Inserisci username e password');
                return;
            }
            
            // Loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="spinner"></div> Accesso...';
            errorDiv.classList.add('hidden');
            
            // Login
            const result = await Auth.login(username, password, remember);
            
            if (result.success) {
                window.location.href = 'dashboard.php';
            } else {
                showLoginError(result.error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i data-lucide="log-in" class="w-5 h-5"></i><span>Accedi</span>';
                lucide.createIcons();
            }
        });
    }
});

function showLoginError(message) {
    const errorDiv = document.getElementById('error-message');
    const errorText = errorDiv.querySelector('p');
    errorText.textContent = message;
    errorDiv.classList.remove('hidden');
}
