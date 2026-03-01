# Anteas Lucca - Backend Documentation

## 🚀 Fase 2 Completata - Backend PHP, Database & API

### Stack Tecnologico
- **PHP**: 8.2+ (strict_types=1)
- **Database**: MySQL 8.0 (utf8mb4_unicode_ci)
- **Auth**: JWT custom implementation + HttpOnly Cookies
- **Sicurezza**: CSRF tokens, Rate limiting, Prepared statements

### Struttura Backend

```
anteaslucca-new/
├── admin/                      # Area riservata
│   ├── index.php              # Login page
│   ├── dashboard.php          # Dashboard gestionale
│   ├── editor.php             # Editor drag-drop
│   └── api/
│       ├── auth.php           # Login/logout/verify
│       ├── posts.php          # CRUD posts
│       ├── upload.php         # Upload immagini
│       └── csrf.php           # CSRF token endpoint
├── api/
│   └── blog.php               # API pubblica blog
├── includes/
│   ├── config.php             # Configurazione
│   ├── db.php                 # Connessione PDO
│   ├── functions.php          # Helper functions
│   ├── jwt.php                # JWT implementation
│   └── auth_check.php         # Middleware auth
├── sql/
│   ├── schema.sql             # Schema database
│   └── migrate.php            # Import articoli
└── uploads/                   # Cartella uploads
    └── YYYY/MM/              # Struttura per data
```

### Installazione

#### 1. Creare Database
```bash
mysql -u root -p
CREATE DATABASE anteas_lucca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'anteas_user'@'localhost' IDENTIFIED BY 'password_sicura';
GRANT ALL PRIVILEGES ON anteas_lucca.* TO 'anteas_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 2. Importare Schema
```bash
cd anteaslucca-new
mysql -u anteas_user -p anteas_lucca < sql/schema.sql
```

#### 3. Configurare includes/config.php
Modificare le credenziali database:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'anteas_lucca');
define('DB_USER', 'anteas_user');
define('DB_PASS', 'password_sicura');
define('JWT_SECRET', 'genera_una_chiave_lunga_e_casuale_32+chars');
```

#### 4. Cambiare Password Admin
Generare nuova hash:
```php
echo password_hash('nuova_password', PASSWORD_BCRYPT);
```
Aggiornare in database:
```sql
UPDATE users SET password_hash = 'HASH_GENERATA' WHERE username = 'anteasadmin';
```

#### 5. Impostare Permessi
```bash
chmod -R 755 uploads/
chmod -R 644 includes/*.php
chmod -R 644 sql/*.sql
```

#### 6. Migrazione Articoli
```bash
cd sql
php migrate.php
```

### API Endpoints

#### Auth (admin/api/auth.php)
```
POST /admin/api/auth.php
Body: {"action": "login", "username": "...", "password": "..."}
Response: {success, user, csrf_token}

POST /admin/api/auth.php
Body: {"action": "logout"}
Response: {success}

GET /admin/api/auth.php
Response: {authenticated, user, csrf_token}
```

#### Posts CRUD (admin/api/posts.php) - Requires JWT
```
GET /admin/api/posts.php?id=123
GET /admin/api/posts.php?slug=article-slug
GET /admin/api/posts.php?page=1&limit=10&status=published

POST /admin/api/posts.php
Headers: X-CSRF-Token: xxx
Body: {title, slug, content: [...], excerpt, category, status}

PUT /admin/api/posts.php?id=123
Headers: X-CSRF-Token: xxx
Body: {title, slug, content: [...], status}

DELETE /admin/api/posts.php?id=123
Headers: X-CSRF-Token: xxx
```

#### Upload (admin/api/upload.php) - Requires JWT + CSRF
```
POST /admin/api/upload.php
Content-Type: multipart/form-data
Body: {csrf_token, image: [file]}
Response: {success, upload: {id, url, width, height}}
```

#### Public Blog (api/blog.php)
```
GET /api/blog.php?slug=article-slug
GET /api/blog.php?page=1&limit=6&category=eventi
Response: {posts, pagination, categories}
```

### Database Schema

#### Tabella users
- id, username, email, password_hash, role (admin/editor)
- display_name, is_active, created_at, last_login

#### Tabella posts
- id, title, slug (unique), excerpt, content (JSON)
- featured_image, category, tags (JSON), status
- author_id (FK), views_count, created_at, published_at

#### Tabella uploads
- id, filename, original_name, file_path, file_url
- mime_type, file_size, width, height, uploaded_by (FK)

#### Tabella sessions (JWT blacklist)
- id, token_jti, user_id, expires_at, revoked_at

### Sicurezza Implementata

✅ **Authentication**
- JWT con firma HMAC SHA256
- HttpOnly, Secure, SameSite=Strict cookies
- Scadenza token: 2 ore

✅ **CSRF Protection**
- Token in sessione PHP
- Validazione su POST/PUT/DELETE

✅ **Rate Limiting**
- Max 5 tentativi login ogni 15 min per IP
- Session-based tracking

✅ **SQL Injection Prevention**
- Solo prepared statements PDO
- Mai concatenazione stringhe

✅ **XSS Protection**
- htmlspecialchars() su ogni output
- Content Security Policy headers

✅ **Upload Security**
- Whitelist MIME types (jpg, png, webp)
- Max 2MB
- getimagesize() validation
- Rename file con uniqid()
- .htaccess blocca esecuzione PHP in uploads

✅ **Headers di Sicurezza**
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin

### Credenziali Default

**Username**: `anteasadmin`  
**Password**: `password` (cambiare subito!)

### Note

1. **Ambiente Development**: Errori PHP visibili per debug
2. **Ambiente Production**: Impostare `ENVIRONMENT=production` in config
3. **Backup**: Eseguire backup DB prima di migrate.php (importa articoli esistenti)
4. **Uploads**: La struttura è `uploads/YYYY/MM/filename.ext`

---

**Creato**: 1 Marzo 2026
**Versione**: 2.0.0 - Fase 2
