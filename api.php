<?php
/**
 * api.php — Point d'entrée API pour le robot AgroBot
 * Le microcontrôleur envoie : GET /api.php?pin=XXXX
 * Réponse JSON : {"authorized": true/false, "message": "...", "timestamp": "..."}
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Demo PIN database (en production : requête MySQL)
$validPins = [
    '4872' => ['name' => 'Pierre Dupont',  'authorized' => true],
    '2951' => ['name' => 'Marie Lambert',  'authorized' => false],
    '7364' => ['name' => 'Jean Martin',    'authorized' => true],
    '1539' => ['name' => 'Sophie Renard',  'authorized' => true],
];

$pin = trim($_GET['pin'] ?? '');
$timestamp = date('Y-m-d H:i:s');

if (empty($pin)) {
    echo json_encode([
        'authorized' => false,
        'message' => 'PIN manquant',
        'timestamp' => $timestamp,
    ]);
    exit;
}

if (!preg_match('/^\d{4}$/', $pin)) {
    echo json_encode([
        'authorized' => false,
        'message' => 'Format PIN invalide (4 chiffres requis)',
        'timestamp' => $timestamp,
    ]);
    exit;
}

if (!isset($validPins[$pin])) {
    echo json_encode([
        'authorized' => false,
        'message' => 'PIN inconnu',
        'timestamp' => $timestamp,
    ]);
    exit;
}

$user = $validPins[$pin];

echo json_encode([
    'authorized' => $user['authorized'],
    'user'       => $user['name'],
    'message'    => $user['authorized'] ? 'Accès accordé — Robot activé' : 'Accès refusé — Utilisateur suspendu',
    'timestamp'  => $timestamp,
]);