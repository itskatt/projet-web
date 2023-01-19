<?php

/**
 * Script pour recuperer une image par son nom pour éviter de placer un dossier
 * où des utilisateurs peuvent mettre leurs images dans un lieu publiquement accesible
 * Merci : https://stackoverflow.com/a/38712921
 */

if (!isset($_GET["id"])) {
    http_response_code(400);
    echo "Pas d'identifiant d'image spécifié...";
    die();
}

$id = $_GET["id"];

// Est-ce que on essaye de sortir du repertoire ?
if (strpos($id, "..") or strpos($id, "/") or strpos($id, "\\")) {
    http_response_code(400);
    echo "Bien tenté petit filou";
    die();
}

$path = realpath(__DIR__ . "/../uploads/" . $id);

// Est-ce que l'image existe
if (!$path) {
    http_response_code(404);
    echo "L'image n'existe pas";
    die();
}

header('Content-Description: File Transfer');
header('Content-Length: ' . filesize($path));
header("Content-Type: " . getimagesize($path)["mime"]);
header("Cache-Control: max-age=31536000, immutable");
readfile($path);
