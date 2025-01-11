<?php
if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['name']) && !empty($_GET['name'])) {
    $id = $_GET['id'];
    $name = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $_GET['name']); // Nettoyage pour un nom de fichier sûr
    $downloadUrl = "http://5.9.21.220:5000/engine/download_torrent?id=$id";

    // Télécharger le fichier original
    $fileContent = file_get_contents($downloadUrl);

    if ($fileContent === false) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Impossible de télécharger le fichier original.']);
        exit;
    }

    // Recherche et remplacement
    $search = 'c3uxQ8ZWddSm0bOQTZAurRKuu2N1Ayhd';
    $replace = 'T5nEwrCnQ6ZWtuXmrnAa1jqPDvXmlwDS';
    $modifiedContent = str_replace($search, $replace, $fileContent);

    // Définir les en-têtes pour le téléchargement avec le nom de fichier personnalisé
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"$name.torrent\"");
    header('Content-Length: ' . strlen($modifiedContent));

    // Envoyer le contenu modifié
    echo $modifiedContent;
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID ou nom manquant.']);
}
?>
