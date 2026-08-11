# Mise en ligne — Marrakech Tours (Git + o2switch / cPanel)

## Vue d'ensemble

1. **Git local** → pousser vers GitHub / GitLab  
2. **cPanel** → cloner le dépôt sur l'hébergement  
3. **`.cpanel.yml`** → déploiement automatique à chaque `git pull`  
4. **`.env`** → configuration production sur le serveur (jamais dans Git)

---

## Étape 1 — Préparer Git en local (Windows)

Dans le dossier du projet :

```powershell
cd c:\xampp\htdocs\marrakechtours

git init
git add .
git commit -m "Initial commit — Marrakech Tours"
```

Créez un dépôt vide sur **GitHub** ou **GitLab**, puis :

```powershell
git branch -M main
git remote add origin https://github.com/VOTRE_COMPTE/marrakechtours.git
git push -u origin main
```

> Ne commitez **jamais** le fichier `.env` (déjà dans `.gitignore`).

---

## Étape 2 — Configurer `.cpanel.yml`

Ouvrez `.cpanel.yml` et remplacez `USER` par votre identifiant cPanel o2switch :

```yaml
export DEPLOYPATH=/home/VOTRE_ID_CPANEL/marrakechtours
```

Commitez et poussez :

```powershell
git add .cpanel.yml
git commit -m "Configure deploy path for o2switch"
git push
```

---

## Étape 3 — cPanel : Git Version Control

1. Connectez-vous à **cPanel** (o2switch).
2. **Git Version Control** → **Create**.
3. **Clone URL** : l'URL de votre dépôt (HTTPS ou SSH).
4. **Repository Path** : `marrakechtours` (→ `/home/USER/marrakechtours`).
5. Cochez **Deploy automatically** si disponible (lit `.cpanel.yml`).
6. Cliquez **Create** puis **Deploy** / **Update from Remote**.

---

## Étape 4 — Document root (important)

Le site Laravel doit servir le dossier **`public/`**.

**Option A — Sous-domaine ou domaine principal**

Dans cPanel → **Domains** ou **Subdomains** :

- Document root : `/home/USER/marrakechtours/public`

**Option B — Site dans un sous-dossier**

Créez un lien symbolique ou pointez le domaine vers `.../marrakechtours/public`.

---

## Étape 5 — Fichier `.env` sur le serveur

Sur le serveur (File Manager ou SSH) :

```bash
cd ~/marrakechtours
cp .env.example .env
nano .env   # ou éditeur cPanel
php artisan key:generate
```

Minimum à configurer :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=votre_base
DB_USERNAME=votre_user
DB_PASSWORD=votre_mot_de_passe

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

Puis :

```bash
php artisan migrate --force
php artisan db:seed   # optionnel (données de démo)
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

---

## Étape 6 — Cron (obligatoire)

cPanel → **Cron Jobs** :

```
* * * * * cd /home/USER/marrakechtours && php artisan schedule:run >> /dev/null 2>&1
```

Remplacez `USER` par votre identifiant cPanel.

---

## Mises à jour ultérieures

En local, après vos modifications :

```powershell
git add .
git commit -m "Description des changements"
git push
```

Sur cPanel → Git Version Control → **Pull** ou **Deploy** (automatique si activé).

---

## Vérifications après mise en ligne

- [ ] `https://votre-domaine.com` — page d'accueil OK  
- [ ] `https://votre-domaine.com/health` — statut OK  
- [ ] Images / CSS / JS chargés (`public/build` généré par `npm run build`)  
- [ ] Connexion client + admin  
- [ ] Paiement Stripe/PayPal en mode test  
- [ ] Webhooks HTTPS configurés  

---

## Dépannage

| Problème | Solution |
|----------|----------|
| Page blanche / 500 | `storage/logs/laravel.log`, permissions `storage/` et `bootstrap/cache/` en 775 |
| CSS/JS absents | `npm run build` sur le serveur |
| Images storage | `php artisan storage:link` |
| Erreur APP_KEY | `php artisan key:generate` |
| Deploy cPanel échoue | Vérifier chemin `DEPLOYPATH` et logs de déploiement cPanel |

---

## Seeders (première install)

```bash
php artisan db:seed --class=GuideSeeder
php artisan tours:setup-booking
```
