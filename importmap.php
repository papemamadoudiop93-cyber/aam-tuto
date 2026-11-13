<?php

/**
 * Import map : quels modules JavaScript le navigateur peut importer.
 * Pas de npm ni de build : Stimulus est copié dans assets/vendor (versionné).
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'path' => './assets/vendor/stimulus/stimulus.js',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
];
