# Marrakech Tours V2

Plateforme de réservation de tours et excursions depuis Marrakech.

## Stack Technique

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Livewire 4 + Alpine.js + Tailwind CSS v4
- **Base de données**: MySQL/MariaDB (o2switch) / SQLite (dev)
- **Cache & Queue**: Redis (o2switch natif)
- **Hébergement**: o2switch Cloud

## Prérequis

- PHP 8.3+ (pour production sur o2switch)
- PHP 8.2+ (pour développement local avec limitations)
- Composer 2.x
- Node.js 20+
- MySQL 8+ ou SQLite

## Installation

```bash
# Cloner le repo
git clone <repo-url> marrakechtours
cd marrakechtours

# Installer les dépendances PHP
composer install --ignore-platform-req=php

# Installer les dépendances JS
npm install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Créer la base de données SQLite (dev)
touch database/database.sqlite

# Exécuter les migrations
php artisan migrate

# Seeder les données de démo
php artisan db:seed

# Compiler les assets
npm run build
```

## Développement

```bash
# Démarrer le serveur de développement
php artisan serve

# Watcher Vite pour les assets
npm run dev

# Exécuter les tests
./vendor/bin/phpunit

# Analyse statique
./vendor/bin/phpstan analyse

# Formater le code
./vendor/bin/pint
```

## Structure du Projet

```
app/
├── Actions/           # Actions métier
├── Console/Commands/  # Commandes Artisan
├── Data/              # DTOs
├── Enums/             # Enums PHP 8.3
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Api/V1/
│   │   ├── Frontend/
│   │   └── Webhooks/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Observers/
├── Policies/
└── Services/

resources/views/
├── components/
│   ├── ui/            # Design system
│   └── livewire/      # Composants Livewire
├── frontend/
├── admin/
├── layouts/
└── emails/
```

## Fonctionnalités Clés

- **Multilingue**: FR, EN, ES, AR (avec RTL)
- **Multi-devise**: EUR, USD, GBP, MAD
- **Double authentification**: Clients + Admins
- **Réservation temps réel**: Gestion des disponibilités avec locks
- **Paiement**: Stripe + PayPal
- **SEO**: Sitemap dynamique, JSON-LD, hreflang

## Déploiement o2switch

1. Configurer PHP 8.3 dans cPanel
2. Activer Redis dans cPanel
3. Configurer le domaine vers `/public`
4. Utiliser Git Version Control cPanel
5. Configurer le cron: `* * * * * php artisan schedule:run`

## Qualité de Code

- Laravel Pint (PSR-12)
- Larastan niveau 6
- PHPUnit / Pest
- Coverage cible: 80%

## Licence

Propriétaire - Marrakech Tours © 2026
