<?php
// Inclure le fichier fetch_movies.php pour récupérer les films initiaux
require 'assets/api/fetch_movies.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Films Populaires</title>
    <!-- Ajouter le lien Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/homeView.css">
</head>

<body>
    <!-- Barre de recherche -->
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Rechercher un film..." oninput="searchMovies()" />
    </div>

    <!-- Liste des films -->
    <div class="movies-container">
        <h1 id="movies-title">Films Populaires</h1>
        <div class="movies-grid" id="movies-grid">
            <?php foreach ($movies as $movie): ?>
                <?php if (!empty($movie['poster_path'])): // Vérifier si le poster existe ?>
                    <div class="movie-item">
                        <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                            <img src="https://image.tmdb.org/t/p/w500<?php echo $movie['poster_path']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <p><?php echo htmlspecialchars($movie['title']); ?></p>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="loading" style="display: none;">Chargement...</div>

    <script>
        // Fonction pour effectuer une recherche en direct sur TMDB
        function searchMovies() {
            const query = document.getElementById('search-input').value.trim();
            const moviesGrid = document.getElementById('movies-grid');
            const moviesTitle = document.getElementById('movies-title');

            if (query.length > 2) {
                moviesTitle.textContent = `Résultats pour "${query}"`;
                moviesGrid.innerHTML = 'Chargement...'; // Afficher un message pendant la recherche

                fetch(`https://api.themoviedb.org/3/search/movie?api_key=95560a7b6fc3d3df99b246b466ee5312&query=${encodeURIComponent(query)}&language=fr-FR`)
                    .then(response => response.json())
                    .then(data => {
                        const movies = data.results;
                        moviesGrid.innerHTML = ''; // Effacer les anciens résultats

                        if (movies.length > 0) {
                            movies.forEach(movie => {
                                if (movie.poster_path) { // Vérifier si le poster existe
                                    const movieItem = document.createElement('div');
                                    movieItem.classList.add('movie-item');
                                    movieItem.innerHTML = `
                                        <a href="movie_details.php?id=${movie.id}">
                                            <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="${movie.title}">
                                            <p>${movie.title}</p>
                                        </a>
                                    `;
                                    moviesGrid.appendChild(movieItem);
                                }
                            });
                        } else {
                            moviesGrid.innerHTML = 'Aucun résultat trouvé.';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                        moviesGrid.innerHTML = 'Erreur lors de la recherche.';
                    });
            } else {
                // Réafficher les films populaires si la recherche est trop courte
                moviesTitle.textContent = 'Films Populaires';
                moviesGrid.innerHTML = `
                    <?php foreach ($movies as $movie): ?>
                        <?php if (!empty($movie['poster_path'])): ?>
                            <div class="movie-item">
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                    <img src="https://image.tmdb.org/t/p/w500<?php echo $movie['poster_path']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                    <p><?php echo htmlspecialchars($movie['title']); ?></p>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                `;
            }
        }

        // Initialiser la page pour charger les films suivants
        let currentPage = 1;

        // Fonction pour charger plus de films
        function loadMoreMovies() {
            currentPage++;

            // Afficher l'indicateur de chargement
            document.getElementById('loading').style.display = 'block';

            // Créer une requête AJAX pour récupérer plus de films
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'assets/api/fetchMoreMovies.php?page=' + currentPage, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const movies = JSON.parse(xhr.responseText); // Convertir la réponse JSON

                    // Si des films sont récupérés, les ajouter à la grille
                    if (movies.length > 0) {
                        const grid = document.querySelector('.movies-grid');
                        movies.forEach(movie => {
                            if (movie.poster_path) { // Vérifier si le poster existe
                                const movieItem = document.createElement('div');
                                movieItem.classList.add('movie-item');
                                movieItem.innerHTML = `
                                    <a href="movie_details.php?id=${movie.id}">
                                        <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="${movie.title}">
                                        <p>${movie.title}</p>
                                    </a>
                                `;
                                grid.appendChild(movieItem);
                            }
                        });
                    }
                }

                // Masquer l'indicateur de chargement
                document.getElementById('loading').style.display = 'none';
            };
            xhr.send();
        }

        // Fonction pour détecter quand l'utilisateur arrive au bas de la page
        window.onscroll = function() {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
                loadMoreMovies();
            }
        };
    </script>
</body>
</html>
