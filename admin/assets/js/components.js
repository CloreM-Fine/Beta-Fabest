/**
 * Anteas Lucca - Admin Components
 * Componenti UI riutilizzabili per l'admin
 */

/**
 * Modal Component
 */
class Modal {
    constructor(id) {
        this.element = document.getElementById(id);
        if (!this.element) return;
        
        this.content = this.element.querySelector('.modal-content');
        
        // Close on overlay click
        this.element.addEventListener('click', (e) => {
            if (e.target === this.element) {
                this.close();
            }
        });
        
        // Close button
        const closeBtn = this.element.querySelector('[data-close-modal]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }
        
        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });
    }
    
    open() {
        this.element.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    close() {
        this.element.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    isOpen() {
        return this.element.classList.contains('active');
    }
    
    setContent(html) {
        const body = this.element.querySelector('.modal-body');
        if (body) {
            body.innerHTML = html;
            lucide.createIcons();
        }
    }
}

/**
 * Confirm Dialog
 */
const ConfirmDialog = {
    show(message, options = {}) {
        return new Promise((resolve) => {
            const {
                title = 'Conferma',
                confirmText = 'Conferma',
                cancelText = 'Annulla',
                type = 'warning' // warning, danger, info
            } = options;
            
            const colors = {
                warning: 'text-amber-600 bg-amber-50 border-amber-200',
                danger: 'text-red-600 bg-red-50 border-red-200',
                info: 'text-blue-600 bg-blue-50 border-blue-200'
            };
            
            const icons = {
                warning: 'alert-triangle',
                danger: 'alert-circle',
                info: 'info'
            };
            
            // Create modal
            const modal = document.createElement('div');
            modal.className = 'modal-overlay active';
            modal.innerHTML = `
                <div class="modal-content max-w-md">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 ${colors[type]} rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="${icons[type]}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">${title}</h3>
                                <p class="text-gray-600">${message}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <button class="btn-cancel px-4 py-2 text-gray-600 hover:text-gray-900 font-medium">
                                ${cancelText}
                            </button>
                            <button class="btn-confirm px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                                ${confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            lucide.createIcons();
            
            // Handlers
            modal.querySelector('.btn-cancel').addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });
            
            modal.querySelector('.btn-confirm').addEventListener('click', () => {
                modal.remove();
                resolve(true);
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            });
        });
    }
};

/**
 * Image Uploader Component
 */
class ImageUploader {
    constructor(options = {}) {
        this.options = {
            multiple: false,
            maxSize: 2 * 1024 * 1024, // 2MB
            acceptedTypes: ['image/jpeg', 'image/png', 'image/webp'],
            onUpload: () => {},
            onError: () => {},
            ...options
        };
    }
    
    render() {
        const div = document.createElement('div');
        div.className = 'image-uploader border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-cyan-500 transition-colors cursor-pointer';
        div.innerHTML = `
            <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
            <p class="text-gray-600 font-medium mb-1">Trascina qui le immagini</p>
            <p class="text-gray-400 text-sm">o clicca per selezionare</p>
            <input type="file" class="hidden" ${this.options.multiple ? 'multiple' : ''} accept="${this.options.acceptedTypes.join(',')}">
        `;
        
        const input = div.querySelector('input');
        
        // Click to select
        div.addEventListener('click', () => input.click());
        
        // File selection
        input.addEventListener('change', (e) => {
            this.handleFiles(e.target.files);
        });
        
        // Drag & drop
        div.addEventListener('dragover', (e) => {
            e.preventDefault();
            div.classList.add('border-cyan-500', 'bg-cyan-50');
        });
        
        div.addEventListener('dragleave', () => {
            div.classList.remove('border-cyan-500', 'bg-cyan-50');
        });
        
        div.addEventListener('drop', (e) => {
            e.preventDefault();
            div.classList.remove('border-cyan-500', 'bg-cyan-50');
            this.handleFiles(e.dataTransfer.files);
        });
        
        return div;
    }
    
    async handleFiles(files) {
        const validFiles = Array.from(files).filter(file => {
            if (!this.options.acceptedTypes.includes(file.type)) {
                this.options.onError(`Tipo file non supportato: ${file.name}`);
                return false;
            }
            if (file.size > this.options.maxSize) {
                this.options.onError(`File troppo grande: ${file.name}`);
                return false;
            }
            return true;
        });
        
        for (const file of validFiles) {
            await this.uploadFile(file);
        }
    }
    
    async uploadFile(file) {
        try {
            const csrfToken = await Auth.getCSRFToken();
            const formData = new FormData();
            formData.append('image', file);
            formData.append('csrf_token', csrfToken);
            
            const response = await fetch('api/upload.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.options.onUpload(data.upload);
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.options.onError(error.message);
        }
    }
}

/**
 * Tabs Component
 */
class Tabs {
    constructor(container) {
        this.container = container;
        this.tabs = container.querySelectorAll('[data-tab]');
        this.panels = container.querySelectorAll('[data-tab-panel]');
        
        this.tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                this.activate(tab.dataset.tab);
            });
        });
    }
    
    activate(tabId) {
        // Deactivate all
        this.tabs.forEach(t => t.classList.remove('active'));
        this.panels.forEach(p => p.classList.add('hidden'));
        
        // Activate selected
        const tab = this.container.querySelector(`[data-tab="${tabId}"]`);
        const panel = this.container.querySelector(`[data-tab-panel="${tabId}"]`);
        
        if (tab) tab.classList.add('active');
        if (panel) panel.classList.remove('hidden');
    }
}

/**
 * Tooltip Component
 */
const Tooltip = {
    init() {
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.addEventListener('mouseenter', (e) => {
                this.show(e.target, e.target.dataset.tooltip);
            });
            
            el.addEventListener('mouseleave', () => {
                this.hide();
            });
        });
    },
    
    show(target, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'fixed z-50 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg pointer-events-none';
        tooltip.textContent = text;
        tooltip.id = 'active-tooltip';
        
        document.body.appendChild(tooltip);
        
        const rect = target.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        
        tooltip.style.left = `${rect.left + (rect.width - tooltipRect.width) / 2}px`;
        tooltip.style.top = `${rect.top - tooltipRect.height - 8}px`;
    },
    
    hide() {
        const tooltip = document.getElementById('active-tooltip');
        if (tooltip) tooltip.remove();
    }
};

/**
 * Date Formatter
 */
const DateFormatter = {
    format(date, format = 'long') {
        const d = new Date(date);
        
        if (format === 'long') {
            return d.toLocaleDateString('it-IT', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        if (format === 'short') {
            return d.toLocaleDateString('it-IT', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
        
        if (format === 'relative') {
            const now = new Date();
            const diff = now - d;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            
            if (days === 0) return 'Oggi';
            if (days === 1) return 'Ieri';
            if (days < 7) return `${days} giorni fa`;
            if (days < 30) return `${Math.floor(days / 7)} settimane fa`;
            
            return this.format(date, 'short');
        }
        
        return d.toISOString();
    }
};

/**
 * Form Validator
 */
const FormValidator = {
    validate(form) {
        const errors = [];
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            // Required
            if (input.required && !input.value.trim()) {
                errors.push({
                    field: input.name,
                    message: `${input.name} è obbligatorio`
                });
                input.classList.add('border-red-500');
            }
            
            // Email
            if (input.type === 'email' && input.value) {
                const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
                if (!emailRegex.test(input.value)) {
                    errors.push({
                        field: input.name,
                        message: 'Email non valida'
                    });
                    input.classList.add('border-red-500');
                }
            }
            
            // Min length
            if (input.minLength && input.value.length < input.minLength) {
                errors.push({
                    field: input.name,
                    message: `Minimo ${input.minLength} caratteri`
                });
                input.classList.add('border-red-500');
            }
        });
        
        return {
            valid: errors.length === 0,
            errors
        };
    },
    
    clearErrors(form) {
        form.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    Tooltip.init();
});

// Export for use in other scripts
window.AdminComponents = {
    Modal,
    ConfirmDialog,
    ImageUploader,
    Tabs,
    Tooltip,
    DateFormatter,
    FormValidator
};
