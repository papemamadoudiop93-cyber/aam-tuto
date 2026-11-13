<?php

// Routeur pour le serveur intégré de PHP (si la CLI Symfony n'est pas installée) :
//   php -S 127.0.0.1:8000 -t public bin/router.php
// Les fichiers existants de public/ sont servis tels quels, le reste passe par Symfony
// (y compris /assets/... que l'AssetMapper sert à la volée en développement).

$fichier = __DIR__.'/../public'.parse_url($_SERVER['REQUEST_URI'], \PHP_URL_PATH);
if (is_file($fichier)) {
    return false;
}

$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/../public/index.php';
require $_SERVER['SCRIPT_FILENAME'];
