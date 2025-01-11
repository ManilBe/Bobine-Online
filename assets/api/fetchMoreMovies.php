<?php
// Récupération des films populaires depuis l'API TMDb
$apiKey = '95560a7b6fc3d3df99b246b466ee5312';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Page depuis la requête GET
$language = 'fr-FR'; // Définir la langue
$apiUrl = "https://api.themoviedb.org/3/movie/popular?api_key=$apiKey&language=$language&page=$page";

// Effectuer la requête API
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// Vérifier si les données sont valides
if ($data && isset($data['results'])) {
    $movies = $data['results']; // Liste des films
} else {
    $movies = []; // Si aucune donnée n'est trouvée
}

// Retourner les films sous forme de JSON
echo json_encode($movies);
exit;  // Ajoute exit pour éviter tout autre affichage
?>
