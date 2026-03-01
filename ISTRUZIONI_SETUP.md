# Istruzioni Setup - Anteas Lucca

## 🎯 Step 1: Importare Database su MySQL

### File da caricare:
**`sql/setup-database.sql`**

### Come importare:
1. Accedi a phpMyAdmin (o MySQL)
2. Seleziona il database: `dblz7emtetofi4`
3. Vai su "Importa"
4. Seleziona il file `sql/setup-database.sql`
5. Clicca "Esegui"

### Credenziali Database (già configurate)
```
Host:     localhost
Database: dblz7emtetofi4
Username: urb5jmirausb2
Password: beta123!
```

### Dopo l'importazione:
- Tabelle create: users, posts, uploads, rate_limit, sessions, settings
- Utente admin creato automaticamente

---

## 🔐 Step 2: Accesso Area Admin

**URL**: `tuodominio.com/admin/`

**Credenziali default**:
```
Username: anteasadmin
Password: admin123!
```

⚠️ **IMPORTANTE**: Cambia la password dopo il primo login!

---

## 📁 Step 3: File Header/Footer Componenti

Ho creato due componenti riutilizzabili:

### Componenti creati:
1. **`includes/header-component.php`** - Header completo
2. **`includes/footer-component.php`** - Footer completo

### Come usarli:
```php
<?php
$pageTitle = 'Titolo Pagina';
$pageDescription = 'Descrizione';
$activeMenu = 'home'; // home, chi-siamo, servizi, gite, blog, contatti, 5x1000
$basePath = ''; // '' per root, '../' per sottocartelle

include 'includes/header-component.php';
?>

<!-- Contenuto pagina -->

<?php
include 'includes/footer-component.php';
?>
```

### Pagina di esempio già convertita:
- ✅ `index.php` - Usa i componenti

---

## 🚀 Step 4: Deploy su Hosting

### File da caricare via FTP:

**Tutti i file nella cartella `anteaslucca-new/`**

Struttura da mantenere:
```
public_html/ (o root hosting)
├── index.php              ← Nuovo (usa componenti)
├── index.html             ← Vecchio (puoi cancellarlo)
├── chi-siamo.html
├── servizi.html
├── gite.html
├── contatti.html
├── 5x1000.html
├── blog/
├── admin/
├── api/
├── includes/              ← Header/Footer qui!
├── assets/
├── uploads/
├── public/                ← Logo & Favicon
└── sql/
```

### Permessi file:
```bash
chmod 755 uploads/
chmod 644 includes/config.php
```

---

## 🎨 Logo & Favicon (già configurati)

### File logo:
- `public/logo/logo.svg` - Logo ufficiale SVG
- `public/favicon-16x16.png` - Favicon piccola
- `public/favicon-32x32.png` - Favicon grande
- `public/apple-touch-icon.png` - Icona iOS
- `public/android-chrome-192x192.png` - Icona Android
- `public/site.webmanifest` - PWA manifest

Tutte le pagine sono già aggiornate con il logo ufficiale!

---

## ✅ Checklist Post-Deploy

- [ ] Importato database da `sql/setup-database.sql`
- [ ] Accesso admin funzionante (`/admin/`)
- [ ] Cambiata password admin
- [ ] Testato login/logout
- [ ] Verificato che logo appaia in tutte le pagine
- [ ] Testato form contatti
- [ ] Verificato upload immagini (permessi cartella `uploads/`)

---

## 🔧 Configurazione Database

Il file `includes/config.php` è già configurato con le tue credenziali:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dblz7emtetofi4');
define('DB_USER', 'urb5jmirausb2');
define('DB_PASS', 'beta123!');
```

---

## 📞 Supporto

In caso di problemi:
1. Verifica che il database sia importato correttamente
2. Controlla i permessi della cartella `uploads/` (deve essere 755)
3. Verifica che PHP 8.2+ sia attivo
4. Controlla error log di PHP

**Versione**: 3.1.0 - Componenti Header/Footer
