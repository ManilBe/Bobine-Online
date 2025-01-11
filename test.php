<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Extraction Métadonnées Vidéo</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        color: white;
        background-color: black;
    }
</style>
<body>
    <h1>Test Extraction des Métadonnées Vidéo</h1>

    <div>
        <label for="videoTitle">Entrez un titre de vidéo :</label>
        <input type="text" id="videoTitle" value="Harry Potter à l'Ecole des Sorciers (2001) MULTi VFF 2160p 10bit 4KLight HDR BluRay AC3 5 1 x265-QTZ">
        <button id="extractBtn">Extraire les métadonnées</button>
    </div>

    <h2>Résultats de l'extraction :</h2>
    <pre id="result"></pre>

    <script>
        function extractMetadata(title) {
            const resolutions = ['2160p', '1080p', '4K', '720p', '480p', '1080i'];
            const codecs = ['HEVC', 'x265', 'x264', 'H264', 'VP9'];
            const sources = ['BluRay', 'BDRip', 'WEBRip', 'HDLight', 'UHD'];
            const audioFormats = ['AC3', 'AAC', 'DTS', 'DTS-HD',  '6CH', 'TrueHD'];
            const extraFeatures = ['10bit', 'HDR', '3D',  'SDR', 'Atmos', '5.1', '7.1'];
            const languages = ['MULTi', 'VFF', 'VFQ', 'VF2', 'VO', 'English', 'FRENCH', 'FR', 'SUB'];

            let resolution = 'Inconnu';
            let codec = 'Inconnu';
            let source = 'Inconnu';
            let audio = 'Inconnu';
            let extra = [];
            let languagesFound = [];

            // Recherche des résolutions dans le titre
            for (let res of resolutions) {
                if (title.includes(res)) {
                    resolution = res;
                    break;
                }
            }

            // Recherche des codecs dans le titre
            for (let cod of codecs) {
                if (title.includes(cod)) {
                    codec = cod;
                    break;
                }
            }

            // Recherche de la source dans le titre
            for (let src of sources) {
                if (title.includes(src)) {
                    source = src;
                    break;
                }
            }

            // Recherche des formats audio dans le titre
            for (let aud of audioFormats) {
                if (title.includes(aud)) {
                    audio = aud;
                    break;
                }
            }

            // Recherche des fonctionnalités supplémentaires comme 10bit, HDR, etc.
            for (let feature of extraFeatures) {
                if (title.includes(feature)) {
                    extra.push(feature);
                }
            }

            // Recherche de toutes les langues dans le titre
            for (let lang of languages) {
                if (title.includes(lang)) {
                    languagesFound.push(lang);
                }
            }

            // Utilisation d'une expression régulière pour extraire le titre et l'année
            const regex = /^(.*?)(?:\s\((\d{4})\))?/i;
            const match = title.match(regex);

            if (match) {
                const [, titleMatch, year] = match;

                return {
                    languages: languagesFound.length > 0 ? languagesFound : ['Langue non spécifiée'],
                    resolution: resolution,
                    codec: codec,
                    source: source,
                    audio: audio,
                    extra: extra.length > 0 ? extra : 'Aucune caractéristique supplémentaire'
                };
            } else {
                return null;
            }
        }

        document.getElementById('extractBtn').addEventListener('click', () => {
            const videoTitle = document.getElementById('videoTitle').value;
            const metadata = extractMetadata(videoTitle);

            const resultDiv = document.getElementById('result');
            if (metadata) {
                resultDiv.textContent = JSON.stringify(metadata, null, 2);
            } else {
                resultDiv.textContent = 'Aucune correspondance trouvée pour les métadonnées.';
            }
        });
    </script>
</body>
</html>
