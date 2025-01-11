<?php
require 'functions.php';

// Récupérer l'ID du film depuis l'URL
$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupérer les détails du film avec l'ID
$movie = getMovieDetails($movieId);

if (!$movie) {
    echo "Film non trouvé.";
    exit;
}

// var_dump($movie);

// Utiliser le backdrop_path comme image de fond
$backdropUrl = 'https://image.tmdb.org/t/p/original' . $movie['backdrop_path'];

// Extraire les genres sous forme de chaîne, avec une vérification pour éviter l'erreur
$genres = isset($movie['genre']) 
    ? $movie['genre'] 
    : 'Non spécifié';  // Valeur par défaut si les genres sont absents ou invalides

// Extraire les acteurs sous forme de chaîne
$actors = isset($movie['actors']) && is_array($movie['actors']) 
    ? implode('<br>', $movie['actors']) 
    : 'Acteurs non spécifiés';  // Valeur par défaut si les acteurs sont absents ou invalides

// Extraire la durée sous format "x h et y min"
$runtime = isset($movie['runtime']) && is_numeric($movie['runtime']) 
    ? floor($movie['runtime'] / 60) . 'h et ' . ($movie['runtime'] % 60) . ' min'
    : 'Durée non spécifiée';  // Valeur par défaut si la durée est absente ou invalide
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/movieView.css">
    <link rel="stylesheet" href="assets/css/watchPopup.css">
    <script src="assets/js/popup.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Bouton de retour en arrière -->
    <button class="btn btn-close" id="backButton" aria-label="Close"></button>
    
    <div class="movie-container">
        <!-- Image de fond -->
        <div class="background-image" style="background-image: url('<?php echo $backdropUrl; ?>');"></div>

        <div class="container position-relative">
            <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>

            <div class="row movie-details">
                <div class="col-12 col-md-4 info-column left">
                    <p class="info-label">GENRE</p>
                    <p class="info-value"><?php echo $genres; ?></p>

                    <p class="info-label">RÉALISATEUR</p>
                    <p class="info-value"><?php echo htmlspecialchars($movie['director']); ?></p>

                    <p class="info-label">EN VEDETTE</p>
                    <p class="info-value"><?php echo $actors; ?></p>
                </div>

                <div class="col-12 col-md-8 info-column right">
                    <div class="ratings">
                        <span class="runtime"><?php echo $runtime; ?></span> 
                    </div>
                    <span class="release-year"><?php echo substr($movie['release_date'], 0, 4); ?></span>
                    <div class="scores">
                        <div class="score imdb">IMDb <?php echo $movie['rating']; ?></div>
                        <div class="score rt">🍅 <?php echo $movie['rtScore']; ?> votes</div>
                    </div>
                    <p class="summary"><?php echo htmlspecialchars($movie['summary']); ?></p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="buttons d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <div class="button">
                    <i class="bi bi-camera-reels"></i>
                    <span>Bande-annonce</span>
                </div>
                <div class="button" id="watchButton">
                    <i class="bi bi-play-fill"></i>
                    <span>Regarder</span>
                </div>
                <div class="button">
                    <i class="bi bi-plus-circle"></i>
                    <span>À Regarder</span>
                </div>
                <div class="button">
                    <i class="bi bi-eye-fill"></i>
                    <span>Visionné</span>
                </div>
                <div class="button">
                    <i class="bi bi-download"></i>
                    <span>Télécharger</span>
                </div>
            </div>


            <!-- Popup de lecture -->
            <?php 
            $name = $movie['title']; // Ou une autre valeur provenant des détails du film
            $year = substr($movie['release_date'], 0, 4); // Extraire l'année
            include 'watchPopup.php';
            ?>

        </div>
    </div>
</body>
</html>


<script>
    // Sélectionne le bouton de retour
const backButton = document.getElementById('backButton');

// Ajoute un événement de clic pour revenir en arrière
backButton.addEventListener('click', function() {
    window.history.back();
});


    // Sélectionner tous les boutons
const buttons = document.querySelectorAll('.button');

// Sélectionner l'élément de fond
const backgroundImage = document.querySelector('.background-image');

// Fonction pour enlever le flou de l'image
function removeBlur() {
    backgroundImage.style.filter = 'blur(15px) brightness(90%)';
}

// Fonction pour ajouter le flou de l'image
function addBlur() {
    backgroundImage.style.filter = 'blur(2px) brightness(90%)';
}

// Ajouter les événements de survol
buttons.forEach(button => {
    button.addEventListener('mouseenter', removeBlur); // Quand la souris entre dans le bouton
    button.addEventListener('mouseleave', addBlur);   // Quand la souris quitte le bouton
});

</script>