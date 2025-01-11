<?php
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $name = $_GET['name'];

    if (preg_match('/\((\d{4})\)$/', $name, $matches)) {
        $year = $matches[1];
        $name = trim(str_replace("($year)", "", $name));
    } else {
        $year = null;
    }

    $name = urlencode($name);

    if ($year) {
        $name .= " $year";
    }
} else {
    header('Content-Type: application/json'); // Important: Définir l'en-tête
    echo json_encode(['error' => 'Le paramètre "name" est manquant ou vide.']);
    exit;
}

$url = "http://5.9.21.220:5000/engine/search?name=$name+$year&description=&file=&uploader=&category=2145&sub_category=all&do=search&order=desc&sort=seed";

$options = [
    "http" => [
        "method" => "GET",
        "header" => "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);

$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    header('Content-Type: application/json'); // Important: Définir l'en-tête
    echo json_encode(['error' => 'Erreur lors de la récupération de l\'URL.']);
} else {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($response);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//div[contains(@class, "table-responsive results")]//tbody/tr');

    $data = [];

    foreach ($nodes as $node) {
        $row = [];
        $columns = $xpath->query('td', $node);
    
        if ($columns->length > 0) {
            $row['type'] = trim($columns->item(0)->textContent);
            $row['name'] = trim($columns->item(1)->textContent);
    
            // Récupération du href dans la colonne contenant le nom
            $linkElement = $xpath->query('.//a[@id="torrent_name"]', $columns->item(1));
            if ($linkElement->length > 0) {
                $href = $linkElement->item(0)->getAttribute('href');
                $row['href'] = $href;
    
                // Extraction de l'ID entre les chemins possibles et le premier tiret
                if (preg_match('#/(film|animation)/(\d+)-#', $href, $matches)) {
                    $row['category'] = $matches[1]; // "film" ou "animation"
                    $row['film_id'] = $matches[2]; // L'ID numérique
                } else {
                    $row['category'] = null; // Si aucun chemin ne correspond
                    $row['film_id'] = null; 
                }
            }
    
            $row['nfo'] = trim($columns->item(2)->textContent);
            $row['comments'] = trim($columns->item(3)->textContent);
            $row['age'] = trim($columns->item(4)->textContent);
            $row['size'] = trim($columns->item(5)->textContent);
            $row['completed'] = trim($columns->item(6)->textContent);
            $row['seed'] = trim($columns->item(7)->textContent);
            $row['leech'] = trim($columns->item(8)->textContent);
            $data[] = $row;
        }
    }
    

    // IMPORTANT: Nettoyage des caractères spéciaux et encodage UTF-8
    array_walk_recursive($data, function (&$item, $key) {
        if (is_string($item)) {
            $item = html_entity_decode($item, ENT_QUOTES, 'UTF-8'); // Décode les entités HTML
            $item = preg_replace('/\s+/', ' ', $item); // Remplace les espaces multiples par un seul
             $item = trim($item);
        }
    });

    header('Content-Type: application/json; charset=utf-8'); // En-tête crucial
    echo json_encode($data, JSON_UNESCAPED_UNICODE); // Important pour les caractères UTF-8
}
?>