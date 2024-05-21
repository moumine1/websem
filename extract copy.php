<?php
// Fonction pour récupérer le contenu d'une page web
function getPageContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

// URLs des pages web en français et en anglais
$url_fr = 'https://fr.wikipedia.org/wiki/Zodiaque';
$url_en = 'https://en.wikipedia.org/wiki/Zodiac';

// Récupération du contenu des pages web
$content_fr = getPageContent($url_fr);
$content_en = getPageContent($url_en);

// Fonction pour extraire le titre et la description d'une page Wikipédia
function extractTitleAndDescription($htmlContent) {
    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent);
    
    $title = '';
    $description = '';
    
    // Extraire le titre de la page
    $titleElements = $dom->getElementsByTagName('title');
    if ($titleElements->length > 0) {
        $title = $titleElements->item(0)->textContent;
    }
    
    // Extraire le premier paragraphe comme description
    $paragraphs = $dom->getElementsByTagName('p');
    if ($paragraphs->length > 0) {
        $description = $paragraphs->item(0)->textContent;
    }
    
    return ['title' => $title, 'description' => $description];
}

// Extraire les informations des pages en français et en anglais
$info_fr = extractTitleAndDescription($content_fr);
$info_en = extractTitleAndDescription($content_en);

// Afficher les informations extraites pour vérification
print_r($info_fr);
print_r($info_en);
?>
