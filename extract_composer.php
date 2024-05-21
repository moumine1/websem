<?php
function getPageContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

$url_fr = 'https://fr.wikipedia.org/wiki/Zodiaque';
$url_en = 'https://en.wikipedia.org/wiki/Zodiac';

$content_fr = getPageContent($url_fr);
$content_en = getPageContent($url_en);
var_dump($content_fr);
var_dump($content_en);
?>
