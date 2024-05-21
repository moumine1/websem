<?php 
function getPageContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function extractDataFromWikipedia($htmlContent) {
    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent);
    $xpath = new DOMXPath($dom);

    // Rechercher les titres des constellations du zodiaque
    $nodes = $xpath->query("//table[contains(@class, 'wikitable')]/tbody/tr/td[1]//a");

    $constellations = [];
    foreach ($nodes as $node) {
        $constellations[] = trim($node->nodeValue);
    }

    return $constellations;
var_dump($dom);
}

function extractDataFromConstellationGuide($htmlContent) {
    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent);
    $xpath = new DOMXPath($dom);

    // Rechercher les noms des constellations du zodiaque
    $nodes = $xpath->query("//div[contains(@class, 'entry-content')]/ul/li//a");

    $constellations = [];
    foreach ($nodes as $node) {
        $constellations[] = trim($node->nodeValue);
    }

    return $constellations;
var_dump($dom);
}

$wikipediaUrl = "https://fr.wikipedia.org/wiki/Zodiaque";
$constellationGuideUrl = "https://www.constellation-guide.com/constellation-map/zodiac-constellations/";

$wikipediaContent = getPageContent($wikipediaUrl);
$constellationGuideContent = getPageContent($constellationGuideUrl);

$wikipediaConstellations = extractDataFromWikipedia($wikipediaContent);
$constellationGuideConstellations = extractDataFromConstellationGuide($constellationGuideContent);

echo "Constellations du zodiaque (Wikipédia) :\n";
print_r($wikipediaConstellations);

echo "\nConstellations du zodiaque (Constellation Guide) :\n";
print_r($constellationGuideConstellations);