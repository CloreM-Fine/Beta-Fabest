# Anteas Lucca - Sito Web Completo

Sito web professionale per l'associazione di volontariato Anteas Lucca con frontend, backend e CMS completo.

## 🎯 Funzionalità Complete

### ✅ Fase 1 - Frontend
- 7 pagine HTML responsive
- Design moderno Tailwind CSS
- Animazioni e interattività
- Blog statico

### ✅ Fase 2 - Backend
- API RESTful PHP 8.2+
- Database MySQL 8.0
- Autenticazione JWT
- Upload immagini sicuro

### ✅ Logo & Branding
- Logo SVG ufficiale in `public/logo/logo.svg`
- Favicon multipli (16x16, 32x32, Apple, Android)
- Manifest PWA per installazione app
- Open Graph meta tags per social sharing

### ✅ Fase 3 - CMS Admin
- **Login** con "Ricordami"
- **Dashboard** con statistiche, filtri, paginazione
- **Editor drag-drop** a blocchi
- 7 tipi di blocchi: Testo, Titolo, Immagine, Lista, Citazione, Separatore, CTA

## 📁 Struttura Progetto

```
anteaslucca-new/
├── 📄 index.html, chi-siamo.html, servizi.html, gite.html, contatti.html, 5x1000.html
├── 📁 blog/
│   ├── index.html
│   └── articolo.html
├── 📁 assets/               # Frontend assets
├── 📁 admin/                # 🔐 CMS Admin
│   ├── index.php           # Login
│   ├── dashboard.php       # Dashboard
│   ├── editor.php          # Editor drag-drop ⭐
│   ├── assets/
│   │   ├── css/admin.css   # Stili admin
│   │   └── js/
│   │       ├── auth.js     # Gestione login
│   │       ├── dashboard.js# Gestione post
│   │       ├── editor.js   # Editor drag-drop ⭐
│   │       └── components.js
│   └── api/                # API admin
├── 📁 api/
│   └── blog.php            # API pubblica
├── 📁 includes/            # Librerie PHP
├── 📁 sql/                 # Database
├── 📁 uploads/             # Immagini
└── 📁 articoli/            # Articoli MD
```

## 🚀 Installazione

### 1. Requisiti
- PHP 8.2+
- MySQL 8.0+
- Apache/Nginx con mod_rewrite

### 2. Database
```bash
mysql -u root -p
CREATE DATABASE anteas_lucca CHARACTER SET utf8mb4;
GRANT ALL ON anteas_lucca.* TO 'anteas_user'@'localhost';
```

### 3. Import Schema
```bash
cd anteaslucca-new
mysql -u anteas_user -p anteas_lucca < sql/schema.sql
```

### 4. Configura
```bash
# Modifica includes/config.php
define('DB_PASS', 'tua_password');
define('JWT_SECRET', 'chiave_casuale_lunga');
```

### 5. Migra Articoli
```bash
cd sql
php migrate.php
```

### 6. Permessi
```bash
chmod -R 755 uploads/
chmod 644 includes/config.php
```

## 🔐 Accesso Admin

**URL**: `/admin/`  
**Username**: `anteasadmin`  
**Password**: `password`  
⚠️ **Cambiare subito!**

## ✏️ Editor Drag-Drop

L'editor a blocchi permette di creare articoli professionali:

### Blocchi Disponibili

| Blocco | Descrizione | Icona |
|--------|-------------|-------|
| **Testo** | Paragrafo con formattazione | 📝 |
| **Titolo** | H2/H3/H4 | 🔤 |
| **Immagine** | Upload con didascalia | 🖼️ |
| **Lista** | Punti o numeri | • |
| **Citazione** | Blocco stilizzato | 💬 |
| **Separatore** | Linea orizzontale | — |
| **CTA** | Box con bottone | 🎯 |

### Funzionalità

- ✅ Drag & drop per riordinare blocchi
- ✅ Auto-save ogni 30 secondi
- ✅ Preview live
- ✅ Upload immagini drag-drop
- ✅ SEO meta title/description
- ✅ Immagine in evidenza

## 📡 API Reference

### Auth
```
POST /admin/api/auth.php    # login/logout
GET  /admin/api/auth.php    # verify
```

### Posts (JWT + CSRF)
```
GET    /admin/api/posts.php
POST   /admin/api/posts.php
PUT    /admin/api/posts.php?id=123
DELETE /admin/api/posts.php?id=123
```

### Upload
```
POST /admin/api/upload.php  # image upload
```

### Blog (Pubblico)
```
GET /api/blog.php?slug=articolo
GET /api/blog.php?page=1&limit=6
```

## 🛡️ Sicurezza

| Feature | Implementazione |
|---------|-----------------|
| Auth | JWT + HttpOnly Cookies |
| CSRF | Token validati |
| SQL | Prepared statements |
| XSS | htmlspecialchars |
| Rate Limit | 5 tentativi / 15 min |
| Upload | Whitelist MIME, 2MB |
| Headers | X-Frame, CSP, etc. |

## 📝 Dati Associazione

| Campo | Valore |
|-------|--------|
| Nome | Anteas Lucca |
| Indirizzo | Viale Puccini, 1780, 55100 Lucca |
| Telefono | 0583 508862 |
| Cell | 328 736 8068 (trasporto) |
| Email | anteaslucca@pec.it |
| CF | 92019070462 |
| IBAN | IT94F0538713704000048010478 |
| Presidente | Massimo Santoni |
| Referente | Alfonso (gite) |

## 🎨 Colori

```
Primary:   #0891B2 (Cyan)
Secondary: #1e40af (Blue)
Accent:    #f59e0b (Amber)
```

## 🌐 Deploy

### Apache
Copiare tutti i file. `.htaccess` configura rewrite e sicurezza.

### PHP Dev Server
```bash
php -S localhost:8000
```

### Produzione Checklist
- [ ] `ENVIRONMENT=production`
- [ ] HTTPS abilitato
- [ ] JWT_SECRET cambiato
- [ ] Password admin cambiata
- [ ] Backup DB automatici
- [ ] Error log monitoring

## 📚 Documentazione

- `README.md` - Questo file
- `BACKEND.md` - Dettagli backend

---

**Versione**: 3.0.0
**Aggiornato**: 1 Marzo 2026
