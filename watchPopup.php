<!-- popup.html -->
<div id="popupContainer" class="popup-container">
    <button id="closePopup" class="close-button">
        <i class="bi bi-x"></i>
    </button>    
    <div class="popup-content">
        
        <!-- Section de filtres -->
        <div id="filterContainer" class="filter-container">
            
            <!-- Filtres de langue -->
            <div class="filter-group">
                <h4>Langues</h4>
                <label><input type="checkbox" id="multiFilter"> MULTI</label>
                <label><input type="checkbox" id="vffFilter"> VFF</label>
                <label><input type="checkbox" id="vfqFilter"> VFQ</label>
                <label><input type="checkbox" id="vf2Filter"> VF2</label>
            </div>

            <!-- Filtres de résolution -->
            <div class="filter-group">
                <h4>Résolution</h4>
                <label><input type="checkbox" id="resolution2160p"> 2160p</label>
                <label><input type="checkbox" id="resolution1080p"> 1080p</label>
                <label><input type="checkbox" id="resolution4k"> 4K</label>
            </div>

            <!-- Filtres de codec -->
            <div class="filter-group">
                <h4>Codec</h4>
                <label><input type="checkbox" id="x265Filter"> x265</label>
                <label><input type="checkbox" id="h264Filter"> H264</label>
            </div>

            <!-- Filtres de source -->
            <div class="filter-group">
                <h4>Source</h4>
                <label><input type="checkbox" id="blurayFilter"> BluRay</label>
                <label><input type="checkbox" id="webripFilter"> WEBRip</label>
            </div>

            <!-- Filtres audio -->
            <div class="filter-group">
                <h4>Audio</h4>
                <label><input type="checkbox" id="ac3Filter"> AC3</label>
                <label><input type="checkbox" id="aacFilter"> AAC</label>
            </div>

            <!-- Filtres supplémentaires -->
            <div class="filter-group">
                <h4>Caractéristiques supplémentaires</h4>
                <label><input type="checkbox" id="hdrFilter"> HDR</label>
                <label><input type="checkbox" id="atmosFilter"> Atmos</label>
            </div>

        </div>
        
        <!-- Conteneur pour les cartes -->
        <div id="cardsContainer" class="cards-container">
            <!-- Les cartes seront ajoutées ici dynamiquement -->
        </div>
    </div>
</div>

<script>
    // Fonction pour corriger les erreurs d'encodage
    function decodeUTF8(input) {
        try {
            return decodeURIComponent(escape(input)); // Décodage UTF-8
        } catch (e) {
            console.error('Erreur lors du décodage UTF-8:', input, e);
            return input; // Retourne le texte brut si le décodage échoue
        }
    }

    // Fonction pour nettoyer les points dans le texte
    function removeDots(input) {
        return input.replace(/\./g, '.'); // Remplace les points par des espaces
    }

    // Fonction pour convertir un timestamp en durée lisible
    function formatAge(timestampWithAge) {
        const [timestamp, humanReadableAge] = timestampWithAge.split(" ");
        const timestampDiff = Date.now() / 1000 - parseInt(timestamp, 10);

        const secondsInYear = 365 * 24 * 60 * 60;
        const secondsInMonth = 30 * 24 * 60 * 60;
        const secondsInDay = 24 * 60 * 60;

        if (timestampDiff >= secondsInYear) {
            const years = Math.floor(timestampDiff / secondsInYear);
            return `${years} an${years > 1 ? 's' : ''}`;
        } else if (timestampDiff >= secondsInMonth) {
            const months = Math.floor(timestampDiff / secondsInMonth);
            return `${months} mois`;
        } else if (timestampDiff >= secondsInDay) {
            const days = Math.floor(timestampDiff / secondsInDay);
            return `${days} jour${days > 1 ? 's' : ''}`;
        }
        return "Aujourd'hui";
    }

    // Fonction pour charger et afficher les données dans des cartes
    function loadPopupData() {
        const name = <?= json_encode($name) ?>;
        const year = <?= json_encode($year) ?>;

        const url = `assets/api/search_ygg.php?name=${name}+${year}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const cardsContainer = document.getElementById('cardsContainer');
                cardsContainer.innerHTML = '';

                if (Array.isArray(data)) {
                    data.forEach(item => {
                        const card = document.createElement('div');
                        card.classList.add('card');

                        // URL pour le script de modification et téléchargement
                        const modifyAndDownloadUrl = `assets/api/download_and_modify.php?id=${item.film_id}&name=${encodeURIComponent(item.name)}`;

                        card.innerHTML = `
                            <h4>${removeDots(decodeUTF8(item.name || 'N/A'))}</h4>
                            <div class="card-data">
                                <p><strong>ID :</strong> ${item.film_id || 'N/A'}</p>
                                <p><strong>Âge :</strong> ${formatAge(item.age || 'N/A')}</p>
                                <p><strong>Taille :</strong> ${item.size || 'N/A'}</p>
                                <p><strong>Complété :</strong> ${item.completed || 'N/A'}</p>
                                <p><strong>Seed :</strong> ${item.seed || 'N/A'}</p>
                                <p><strong>Leech :</strong> ${item.leech || 'N/A'}</p>
                            </div>
                        `;

                        // Ajouter un événement de clic pour télécharger et modifier
                        card.addEventListener('click', () => {
                            window.location.href = modifyAndDownloadUrl;
                        });

                        cardsContainer.appendChild(card);
                    });
                } else {
                    console.error('La réponse n\'est pas un tableau:', data);
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des données:', error));
    }



    // Appel de la fonction pour charger les données
    document.addEventListener('DOMContentLoaded', loadPopupData);

        // Fonction pour appliquer les filtres et afficher les résultats
        function applyFilters() {
        const filters = {
            languages: [],
            resolution: [],
            codec: [],
            source: [],
            audio: [],
            extra: []
        };

        // Appliquer les filtres choisis par l'utilisateur
        if (document.getElementById('multiFilter').checked) filters.languages.push('MULTI');
        if (document.getElementById('vffFilter').checked) filters.languages.push('VFF');
        if (document.getElementById('vfqFilter').checked) filters.languages.push('VFQ');
        if (document.getElementById('vf2Filter').checked) filters.languages.push('VF2');
        if (document.getElementById('resolution2160p').checked) filters.resolution.push('2160P');
        if (document.getElementById('resolution1080p').checked) filters.resolution.push('1080P');
        if (document.getElementById('resolution4k').checked) filters.resolution.push('4K');
        if (document.getElementById('x265Filter').checked) filters.codec.push('X265');
        if (document.getElementById('h264Filter').checked) filters.codec.push('H264');
        if (document.getElementById('blurayFilter').checked) filters.source.push('BLURAY');
        if (document.getElementById('blurayFilter').checked) filters.source.push('BLUERAY');
        if (document.getElementById('blurayFilter').checked) filters.source.push('BLUE-RAY');
        if (document.getElementById('blurayFilter').checked) filters.source.push('BLU-RAY');
        if (document.getElementById('webripFilter').checked) filters.source.push('WEBRip');
        if (document.getElementById('ac3Filter').checked) filters.audio.push('AC3');
        if (document.getElementById('aacFilter').checked) filters.audio.push('AAC');
        if (document.getElementById('hdrFilter').checked) filters.extra.push('HDR');
        if (document.getElementById('atmosFilter').checked) filters.extra.push('Atmos');

        // Récupérer toutes les cartes
        const allCards = document.querySelectorAll('.card');

        allCards.forEach(card => {
            const cardTitle = card.querySelector('h4').innerText;

            // Vérification des filtres sur le titre de la carte
            const matchesFilters = checkFilters(cardTitle, filters);

            if (matchesFilters) {
                card.style.display = 'block'; // Afficher la carte
            } else {
                card.style.display = 'none'; // Cacher la carte
            }
        });
    }

    // Fonction pour vérifier si une carte correspond aux filtres sélectionnés
    function checkFilters(cardTitle, filters) {
        // Vérification des filtres de langue
        if (filters.languages.length > 0 && !filters.languages.some(lang => cardTitle.includes(lang))) {
            return false;
        }

        // Vérification des filtres de résolution
        if (filters.resolution.length > 0 && !filters.resolution.some(res => cardTitle.includes(res))) {
            return false;
        }

        // Vérification des filtres de codec
        if (filters.codec.length > 0 && !filters.codec.some(codec => cardTitle.includes(codec))) {
            return false;
        }

        // Vérification des filtres de source
        if (filters.source.length > 0 && !filters.source.some(src => cardTitle.includes(src))) {
            return false;
        }

        // Vérification des filtres audio
        if (filters.audio.length > 0 && !filters.audio.some(audio => cardTitle.includes(audio))) {
            return false;
        }

        // Vérification des filtres extras
        if (filters.extra.length > 0 && !filters.extra.some(extra => cardTitle.includes(extra))) {
            return false;
        }

        return true; // Si tous les filtres sont respectés
    }

    // Ajouter un écouteur d'événements pour chaque case à cocher
    const checkboxes = document.querySelectorAll('.filter-container input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters); // Appliquer les filtres dès que la case change
    });

    // Charger les données au démarrage (si nécessaire)
    document.addEventListener('DOMContentLoaded', loadPopupData);

</script>
