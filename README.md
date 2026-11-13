# aam-tuto – Formations des agents (projet d'entraînement)

Petite application Symfony pour se préparer à la mission sur le socle applicatif de l'Académie.
Elle reprend **la même pile** que le référentiel technique : PHP/Symfony, Twig, Stimulus, Doctrine ORM et migrations, Symfony Security, DSFR, PHPUnit et la console Symfony.

Le métier est volontairement simple et **fictif** : un catalogue de formations, des agents qui demandent à s'inscrire, et un gestionnaire qui valide ou refuse.

## Démarrer

Prérequis : PHP 8.2+ (extensions `pdo_sqlite`, `intl`), [Composer](https://getcomposer.org) et, en option, Docker et la [CLI Symfony](https://symfony.com/download).

```bash
composer install
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
symfony serve
```

Sans la CLI Symfony, utiliser le serveur intégré de PHP avec le routeur fourni :

```bash
php -S 127.0.0.1:8000 -t public bin/router.php
```

Ouvrir http://127.0.0.1:8000. Comptes de démonstration (mot de passe `motdepasse`) :

| Compte | Rôle |
|---|---|
| agent@exemple.fr | Agent |
| agent2@exemple.fr | Agent |
| gestion@exemple.fr | Gestionnaire |

Tests :

```bash
php vendor/bin/phpunit
```

## Où trouver chaque notion du référentiel

| Notion | Fichier(s) |
|---|---|
| Contrôleurs, routing par attributs | `src/Controller/*` |
| Services et injection de dépendances | `src/Service/InscriptionManager.php`, `config/services.yaml` |
| Paramètre injecté (`#[Autowire]`) | `FormationController::index()` + `app.places_alerte` |
| Événements Symfony | `src/Event/InscriptionValideeEvent.php`, `src/EventSubscriber/*` |
| Entités, relations, enum | `src/Entity/*` |
| QueryBuilder, DQL, éviter le N+1 | `src/Repository/*` |
| Migrations | `migrations/` |
| Twig : héritage, include, macros | `templates/base.html.twig`, `templates/_partials/*` |
| Stimulus : targets, values, actions | `assets/controllers/*` |
| DSFR | `templates/**` (classes `fr-*`), `public/dsfr/` |
| Formulaires et validation | `src/Form/InscriptionType.php`, `templates/formation/show.html.twig` |
| Security : firewall, provider, rôles | `config/packages/security.yaml` |
| Voters (droits objet par objet) | `src/Security/Voter/InscriptionVoter.php` |
| CSRF | macro `bouton_post`, contrôleurs `Inscription` et `Gestion` |
| Commande console | `src/Command/StatistiquesCommand.php` |
| Tests unitaires et fonctionnels | `tests/Unit`, `tests/Functional` |

## Commandes console utiles

```bash
php bin/console debug:router                     # toutes les routes
php bin/console debug:container InscriptionManager
php bin/console debug:event-dispatcher "App\Event\InscriptionValideeEvent"
php bin/console debug:autowiring Logger
php bin/console app:formations:stats            # commande du projet
php bin/console dbal:run-sql "SELECT * FROM formation"
php bin/console cache:clear
```

## Exercices

À faire de préférence avec Claude Code : demander d'abord un **plan**, puis faire écrire le code, faire lancer les tests, et relire chaque diff.

1. **Doctrine + migration** : ajouter un champ `public` (texte, par exemple « enseignants », « personnels administratifs ») à `Formation`. Générer la migration, la relire, l'appliquer, l'afficher dans le catalogue.
2. **QueryBuilder** : ajouter un filtre par lieu (liste déroulante DSFR) côté serveur, en paramètre GET, dans `FormationRepository`.
3. **Stimulus** : convertir le filtre texte en un filtre combiné texte + lieu, entièrement côté navigateur.
4. **Événement** : quand une inscription est refusée, émettre un `InscriptionRefuseeEvent` et le journaliser.
5. **Voter** : un gestionnaire ne peut pas valider sa propre inscription. Ajouter un test fonctionnel.
6. **Console** : commande `app:formations:rappel` qui liste les formations des 7 prochains jours avec leurs inscrits validés.
7. **PostgreSQL** : `docker compose up -d`, régler `DATABASE_URL` dans `.env.local`, rejouer les migrations. Vous constaterez que la migration fournie est écrite en SQL SQLite : c'est l'occasion de comprendre pourquoi une migration dépend du SGBD (supprimer `migrations/*`, régénérer avec `make:migration` sur PostgreSQL, comparer).
8. **Sécurité (avancé)** : remplacer `form_login` par un authenticator OpenID Connect (par ex. avec un Keycloak local en Docker). Repère : `security.firewalls.main.access_token` ou un authenticator personnalisé.
9. **Formulaire** : créer l'écran gestionnaire « Nouvelle formation » avec un `FormationType` et la validation des entités.
10. **jQuery → Stimulus** : écrire un petit script jQuery (ex. afficher/masquer la description), puis le réécrire en contrôleur Stimulus.

## Note

Le DSFR (`public/dsfr/`, paquet `@gouvfr/dsfr` 1.12.1) est utilisé ici uniquement pour s'entraîner. Son usage en production est réservé aux sites de l'État.
