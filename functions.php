<?php
// Récupérer l'ID du film depuis l'URL
$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier si l'ID du film est valide
if ($movieId === 0) {
    echo "Aucun ID de film trouvé dans l'URL.";
    exit;
}

function getMovieDetails($movieId) {
    $apiKey = '95560a7b6fc3d3df99b246b466ee5312';
    $apiUrl = "https://api.themoviedb.org/3/movie/$movieId?api_key=$apiKey&language=fr-FR&append_to_response=credits";
    $response = file_get_contents($apiUrl);
    $movieData = json_decode($response, true);
    
    // var_dump($movieData);


    // Vérifie si l'ID du film existe dans la réponse
    if (isset($movieData['id'])) {
        // Récupère le nom des genres
        $genres = isset($movieData['genres']) && is_array($movieData['genres']) 
            ? implode(', ', array_map(function($genre) {
                return $genre['name'];
            }, $movieData['genres']))
            : 'Non spécifié';  // Valeur par défaut si les genres sont absents ou invalides

        // Récupère les 5 premiers acteurs
        $actors = isset($movieData['credits']['cast']) && is_array($movieData['credits']['cast']) 
            ? array_map(function($actor) {
                return $actor['name'];
            }, array_slice($movieData['credits']['cast'], 0, 5))  // Limite à 5 acteurs
            : ['Acteurs non spécifiés'];  // Valeur par défaut si les acteurs sont absents ou invalides

        // Récupère le réalisateur
        $director = 'Non spécifié';
        if (isset($movieData['credits']['crew']) && is_array($movieData['credits']['crew'])) {
            foreach ($movieData['credits']['crew'] as $crewMember) {
                if ($crewMember['job'] === 'Director') {
                    $director = $crewMember['name'];
                    break;
                }
            }
        }

        // Retourne les détails du film avec des valeurs par défaut pour les informations manquantes
        return [
            'title' => $movieData['title'] ?? 'Titre non spécifié',
            'poster_path' => $movieData['poster_path'] ?? '',
            'genre' => $genres,
            'director' => $director,
            'actors' => $actors,
            'rating' => $movieData['vote_average'] ?? 'N/A',
            'rtScore' => $movieData['vote_count'] ?? 0,
            'summary' => $movieData['overview'] ?? 'Pas de résumé disponible',
            'backdrop_path' => $movieData['backdrop_path'] ?? '',
            'release_date' => $movieData['release_date'] ?? 'Date inconnue',
            'runtime' => $movieData['runtime'] ?? 0,
        ];
    }

    return null;  // Si les détails du film ne sont pas trouvés
}


function getHighQualityBackdropImage($movieId) {
    $apiKey = '95560a7b6fc3d3df99b246b466ee5312';
    $apiUrl = "https://api.themoviedb.org/3/movie/$movieId/images?api_key=$apiKey&language=fr-FR";
    $response = file_get_contents($apiUrl);
    $imageData = json_decode($response, true);

    // Chercher une image de fond de haute qualité
    if (isset($imageData['backdrops']) && count($imageData['backdrops']) > 0) {
        // Choisir la première image de fond de la plus haute qualité disponible
        $backdrop = $imageData['backdrops'][0];
        $backdropUrl = 'https://image.tmdb.org/t/p/original' . $backdrop['file_path'];
        return $backdropUrl;
    }

    return null;  // Si aucune image de fond n'est trouvée
}

$randomBackdropImage = getHighQualityBackdropImage($movieId);

?>
