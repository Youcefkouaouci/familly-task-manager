# Family Task Manager

Une application web de gestion de tâches familiales construite avec Symfony.

### Présentation

Family Task Manager permet aux parents de créer, assigner et suivre des tâches pour leurs enfants. Les enfants peuvent accepter, refuser ou négocier les tâches qui leur sont assignées, favorisant la communication et l'organisation familiale.

## Prérequis

* PHP `8.1+` avec extensions usuelles (`ctype`, `iconv`, `pdo_pgsql`, `intl`, `mbstring`, `zip`)
* Composer
* Symfony CLI *(optionnel mais pratique)*
* Node.js `>=18` + `npm` ou `yarn`
* PostgreSQL 16+

## Installation rapide

```bash
git clone https://github.com/votre-repo/family-task-manager.git
cd family-task-manager
cp .env .env.local # et renseignez DATABASE_URL
composer install
npm install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n # charge des données de demo
symfony server:start -d # ou: php -S 0.0.0.0:8000 -t public
npm run dev # pour le mode dev (ou npm run build pour prod)
```

## Fonctionnalités

### Pour les parents (ROLE_PARENT)
- Création et édition de tâches avec catégories et échéances
- Assignment de tâches à un ou plusieurs enfants
- Suivi du statut des tâches (en attente, acceptées, terminées)
- Dashboard avec statistiques et tâches urgentes
- Gestion des récompenses

### Pour les enfants (ROLE_CHILD)
- Visualisation des tâches assignées
- Actions sur les tâches : accepter, refuser, négocier
- Marquage des tâches comme terminées
- Dashboard personnalisé avec tâches en cours

## Architecture technique

- **Framework :** Symfony 6.4 LTS
- **PHP :** 8.1+ 
- **Base de données :** PostgreSQL avec Doctrine ORM 3.x
- **Front-end :** Tailwind CSS + Stimulus (AssetMapper)
- **Sécurité :** Security Bundle avec authentification par email/mot de passe
- **Messages :** Symfony Messenger avec transport Doctrine

## Améliorations futures

- [ ] Tests automatisés (PHPUnit + fixtures)
- [ ] Analyse statique (PHPStan)
- [ ] CI/CD avec GitHub Actions
- [ ] Système de notifications en temps réel
- [ ] API REST pour mobile
- [ ] Cache Redis/Memcached
- [ ] Pagination des listes
- [ ] Gestion des pièces jointes
