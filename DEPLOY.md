# 🚀 Deploy Automatico - Anteas Lucca

## Configurazione GitHub Actions

Il deploy automatico è configurato per funzionare con i secret GitHub già impostati.

### Secret configurati:
- ✅ `FTP_SERVER` - Server FTP SiteGround
- ✅ `FTP_UTENTE` - Username FTP
- ✅ `FTP_PASSWORD` - Password FTP

## Come funziona

### Deploy Automatico
Ogni volta che fai push su `main`:
```bash
git add .
git commit -m "Aggiornamento sito"
git push origin main
```
→ Il sito si aggiorna automaticamente su SiteGround! 🎉

### Deploy Manuale
Puoi anche eseguire il workflow manualmente da GitHub:
1. Vai su **Actions** → **Deploy to SiteGround**
2. Clicca **Run workflow**

## File caricati su SiteGround

Tutti i file del progetto eccetto:
- ❌ `.git/` (repository git)
- ❌ `.github/` (workflow)
- ❌ `sql/` (file database)
- ❌ `articoli/` (file markdown origine)
- ❌ `*.md` (documentazione)
- ❌ `.DS_Store` (file macOS)

## Struttura su SiteGround

```
public_html/
├── index.php              ← Homepage (con componenti)
├── index.html             ← Homepage statica (backup)
├── chi-siamo.html
├── servizi.html
├── gite.html
├── contatti.html
├── 5x1000.html
├── blog/
│   ├── index.html
│   └── articolo.html
├── admin/                 ← Area riservata CMS
├── api/                   ← API pubbliche
├── includes/              ← Header/Footer componenti
├── assets/                ← CSS/JS
├── uploads/               ← Immagini caricate
└── public/                ← Logo & Favicon
```

## Prima configurazione

### 1. Database (una sola volta)
Importa il file `sql/setup-database.sql` in phpMyAdmin:
- Host: `localhost`
- Database: `dblz7emtetofi4`
- Username: `urb5jmirausb2`
- Password: `beta123!`

### 2. Permessi cartelle
Assicurati che `uploads/` abbia permessi 755

### 3. Accesso Admin
URL: `https://www.anteaslucca.org/admin/`
```
Username: anteasadmin
Password: admin123!
```
⚠️ **Cambia subito la password!**

## Monitoraggio Deploy

Vai su **GitHub → Actions** per vedere:
- ✅ Stato ultimo deploy
- 📊 Cronologia deploy
- 📝 Log dettagliati

## Troubleshooting

### Deploy fallito?
1. Controlla i secret FTP in Settings → Secrets
2. Verifica che il server FTP sia raggiungibile
3. Controlla i log in GitHub Actions

### Problemi post-deploy?
1. Verifica che il database sia importato
2. Controlla permessi cartella `uploads/`
3. Verifica error log PHP in SiteGround

---

**Repository**: https://github.com/CloreM-Fine/Beta-Fabest
