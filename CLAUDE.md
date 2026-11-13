# Projet d'entraînement – Formations (Symfony + Twig + Stimulus + Doctrine + DSFR)

Projet personnel pour se former à la pile du socle applicatif de l'Académie (référentiel « Technologies et compétences »).
Données et organisation **fictives** : ne jamais y mettre de code, de données ou de configuration réels de l'Académie.

## Pile
- PHP 8.2+, Symfony 7.3. Pages rendues côté serveur avec Twig. **Pas de React/Vue/Angular.**
- JavaScript : contrôleurs Stimulus dans `assets/controllers/*_controller.js`, chargés via AssetMapper (pas de npm, pas de build).
- Interface : DSFR (copie locale dans `public/dsfr/`). Utiliser les classes `fr-*` des composants DSFR, pas de CSS maison.
- Données : Doctrine ORM (attributs PHP sur les entités), SQLite par défaut, PostgreSQL possible via `compose.yaml`.

## Organisation
- `src/Controller` : contrôleurs **fins**. Toute règle métier va dans `src/Service` (ex. `InscriptionManager`).
- Les règles métier lèvent `InscriptionImpossibleException` ; les contrôleurs la transforment en message flash.
- Droits : rôle par URL dans `config/packages/security.yaml`, droits objet par objet dans `src/Security/Voter`.
- Effets de bord (journalisation, mails...) : événement + subscriber (`src/Event`, `src/EventSubscriber`).
- Requêtes : dans les repositories (QueryBuilder/DQL), jamais dans les contrôleurs ou les templates. Penser au N+1 (`addSelect`).

## Règles
- Toute modification de schéma passe par une migration : `php bin/console make:migration`, puis relire le fichier avant `doctrine:migrations:migrate`.
- Formulaires en POST protégés par jeton CSRF.
- Ne pas lire ni afficher `.env.local`.
- Commits et PR : aucune mention de Claude (pas de Co-Authored-By, Claude-Session ni « Generated with Claude Code »), auteur papemamadoudiop93-cyber (cf. `.claude/settings.json`).
- Avant de dire qu'un changement est fini :
  - `php vendor/bin/phpunit`
  - `php bin/console lint:twig templates`
  - `php bin/console lint:container`
- Expliquer les changements de façon pédagogique : ce projet sert à apprendre Symfony.
