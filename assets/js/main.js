// Anteas Lucca - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Mobile menu toggle
    initMobileMenu();

    // Sticky header
    initStickyHeader();

    // Scroll animations
    initScrollAnimations();

    // Smooth scroll for anchor links
    initSmoothScroll();
});

// Mobile Menu
function initMobileMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuClose = document.getElementById('menu-close');
    const mobileLinks = mobileMenu?.querySelectorAll('a');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        });
    }

    if (menuClose && mobileMenu) {
        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
            document.body.style.overflow = '';
        });
    }

    // Close menu on link click
    if (mobileLinks) {
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('translate-x-full');
                document.body.style.overflow = '';
            });
        });
    }
}

// Sticky Header
function initStickyHeader() {
    const header = document.getElementById('main-header');
    if (!header) return;

    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.classList.add('shadow-md');
            header.classList.add('bg-white/95');
            header.classList.add('backdrop-blur-md');
        } else {
            header.classList.remove('shadow-md');
            header.classList.remove('bg-white/95');
            header.classList.remove('backdrop-blur-md');
        }

        lastScroll = currentScroll;
    });
}

// Scroll Animations using Intersection Observer
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if (!animatedElements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                entry.target.classList.remove('opacity-0', 'translate-y-6');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animatedElements.forEach(el => {
        el.classList.add('opacity-0', 'translate-y-6', 'transition-all', 'duration-700');
        observer.observe(el);
    });
}

// Smooth Scroll
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Format date helper
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('it-IT', options);
}

// Parse markdown content (simple version)
function parseMarkdown(markdown) {
    if (!markdown) return '';
    
    // Extract content between Content section
    const contentMatch = markdown.match(/## Content\s*\n\n([\s\S]*?)(?=\n## |$)/);
    if (contentMatch) {
        let content = contentMatch[1];
        
        // Remove meta info lines
        content = content.replace(/^\*\*URL:.*$/gm, '');
        content = content.replace(/^\*\*Analyzed:.*$/gm, '');
        content = content.replace(/^Salta al contenuto$/gm, '');
        content = content.replace(/^\d+ \w+ \d{4}$/gm, '');
        
        // Basic markdown to HTML
        content = content.replace(/^# (.*$)/gm, '<h1 class="text-3xl font-bold mb-4">$1</h1>');
        content = content.replace(/^## (.*$)/gm, '<h2 class="text-2xl font-bold mb-3 mt-6">$1</h2>');
        content = content.replace(/^### (.*$)/gm, '<h3 class="text-xl font-bold mb-2 mt-4">$1</h3>');
        content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        content = content.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Paragraphs
        const lines = content.split('\n').filter(line => line.trim());
        let html = '';
        let inParagraph = false;
        
        lines.forEach(line => {
            if (line.startsWith('<h') || line.startsWith('<ul') || line.startsWith('<ol')) {
                if (inParagraph) {
                    html += '</p>';
                    inParagraph = false;
                }
                html += line;
            } else {
                if (!inParagraph) {
                    html += '<p class="mb-4 leading-relaxed">';
                    inParagraph = true;
                }
                html += line + ' ';
            }
        });
        
        if (inParagraph) html += '</p>';
        
        return html;
    }
    
    return markdown;
}

// Extract excerpt from markdown
function extractExcerpt(markdown, maxLength = 150) {
    // Get content section
    const contentMatch = markdown.match(/## Content\s*\n\n([\s\S]*?)(?=\n## |$)/);
    if (!contentMatch) return '';
    
    let content = contentMatch[1];
    
    // Remove headers and clean up
    content = content.replace(/^#+ .*$/gm, '');
    content = content.replace(/\*\*/g, '');
    content = content.replace(/^\*\*URL:.*$/gm, '');
    content = content.replace(/^\*\*Analyzed:.*$/gm, '');
    content = content.replace(/^Salta al contenuto$/gm, '');
    content = content.replace(/^\d+ \w+ \d{4}$/gm, '');
    content = content.replace(/Uncategorized/g, '');
    content = content.replace(/Redazione/g, '');
    content = content.replace(/Di\s*$/gm, '');
    
    // Get first meaningful paragraph
    const paragraphs = content.split('\n').filter(p => p.trim().length > 30);
    if (paragraphs.length > 0) {
        let excerpt = paragraphs[0].trim();
        if (excerpt.length > maxLength) {
            excerpt = excerpt.substring(0, maxLength) + '...';
        }
        return excerpt;
    }
    
    return '';
}

// Extract date from filename or content
function extractDate(filename, content) {
    // Try to extract from filename (YYYY-MM-DD format)
    const dateMatch = filename.match(/(\d{4})-(\d{2})-(\d{2})/);
    if (dateMatch) {
        const year = dateMatch[1];
        const month = dateMatch[2];
        const day = dateMatch[3];
        return `${day}/${month}/${year}`;
    }
    
    // Try to find date in content
    const contentDateMatch = content.match(/(\d{1,2})\s+(Gen|Feb|Mar|Apr|Mag|Giu|Lug|Ago|Set|Ott|Nov|Dic)[a-z]*\s+(\d{4})/i);
    if (contentDateMatch) {
        return contentDateMatch[0];
    }
    
    return '';
}

// Extract title from markdown
function extractTitle(markdown) {
    // Try H1
    const h1Match = markdown.match(/^# (.+)$/m);
    if (h1Match) {
        return h1Match[1].trim();
    }
    
    // Try H1 Headings section
    const headingsMatch = markdown.match(/## H1 Headings\s*\n- (.+)$/m);
    if (headingsMatch && headingsMatch[1].trim()) {
        return headingsMatch[1].trim();
    }
    
    return 'Articolo';
}

// Create slug from filename
function createSlug(filename) {
    return filename
        .replace(/\.md$/, '')
        .replace(/^\d{4}-\d{2}-\d{2}-/, '');
}
