<?php

$path = __DIR__ . '/../../resources/views/frontend/tours/show.blade.php';
$content = file_get_contents($path);

// Remove BOM / stray prefix before <x-app-layout>
$content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
$content = preg_replace('/^[\?\xEF\xBB\xBF\s]*(?=<x-app-layout>)/', '', $content);

$replacements = [
    'Ã©' => 'é',
    'Ã¨' => 'è',
    'Ãª' => 'ê',
    'Ã«' => 'ë',
    'Ã ' => 'à ',
    'Ã\u{00a0}' => 'à ', // nbsp variant
    "Ã\xC2\xA0" => 'à ',
    'Ã€' => 'À',
    'Ã‰' => 'É',
    'Ã§' => 'ç',
    'Ã®' => 'î',
    'Ã¯' => 'ï',
    'Ã´' => 'ô',
    'Ã»' => 'û',
    'Ã¢' => 'â',
    'Ã¯' => 'ï',
    'Ã?' => 'É', // Écouter corrupted
    'â??' => '—',
    'â€"' => '—',
    'â€"' => '–',
    'â€¢' => '•',
    'mÃªme' => 'même',
    'dÃ©jÃ ' => 'déjà ',
    'dÃ©jÃ ' => 'déjà',
    'bÃ©bÃ©s' => 'bébés',
    'clÃ©' => 'clé',
    'numÃ©rotÃ©' => 'numéroté',
    'configurÃ©s' => 'configurés',
    'approuvÃ©s' => 'approuvés',
    'personnalisÃ©' => 'personnalisé',
    'rÃ©servation' => 'réservation',
    'RÃ©cupÃ©rer' => 'Récupérer',
    'dÃ©faut' => 'défaut',
    'PrÃ©parer' => 'Préparer',
    'donnÃ©es' => 'données',
    'DonnÃ©es' => 'Données',
    'itinÃ©raire' => 'itinéraire',
    'sÃ©parateur' => 'séparateur',
    'spÃ©cifique' => 'spécifique',
    'Ã©levÃ©' => 'élevé',
    'dÃ©jÃ ' => 'déjà ',
    'trouvÃ©' => 'trouvé',
    'GÃ©rer' => 'Gérer',
    'dÃ©tail' => 'détail',
    'barrÃ©' => 'barré',
    'Ã©conomies' => 'économies',
    'VÃ©rifier' => 'Vérifier',
    'Ã©lÃ©ments' => 'éléments',
    'utilisÃ©' => 'utilisé',
    'terminÃ©' => 'terminé',
];

foreach ($replacements as $from => $to) {
    $content = str_replace($from, $to, $content);
}

// Fix "Mettre Ã jour" (à without trailing space in source)
$content = preg_replace('/Ã\s+jour/u', 'à jour', $content);
$content = preg_replace('/Ã\s+consulter/u', 'à consulter', $content);
$content = preg_replace('/Ã\s+400px/u', 'à 400px', $content);
$content = preg_replace('/est Ã\s+/u', 'est à ', $content);
$content = preg_replace('/mis Ã\s+jour/u', 'mis à jour', $content);

// Deduplicate Avis Google comment
$content = preg_replace(
    '/(\s*<!-- Avis Google \(API Places — même fiche que la home\) -->\s*){2,}/u',
    "\n                    <!-- Avis Google (API Places — même fiche que la home) -->\n",
    $content
);

file_put_contents($path, $content);

echo 'Remaining Ã count: ' . substr_count($content, 'Ã') . PHP_EOL;
