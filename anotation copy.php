<?php
include_once("extract.php");
include_once("info.php");

$url = "https://fr.wikipedia.org/wiki/Zodiaque";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$htmlContent = curl_exec($ch);
curl_close($ch);

// Configuration du point d'accès SPARQL et exécution de la requête
$sparqlEndpoint = 'http://dbpedia.org/sparql';
$query = "
    SELECT ?property ?hasValue ?isValueOf
    WHERE {
        { <http://dbpedia.org/resource/Zodiac> ?property ?hasValue }
        UNION
        { ?isValueOf ?property <http://dbpedia.org/resource/Zodiac> }
    }
";

$sparql = new EasyRdf\Sparql\Client($sparqlEndpoint);
$results = $sparql->query($query);

function formatUri($value) {
    if ($value instanceof EasyRdf\Resource) {
        return str_replace('http://dbpedia.org/resource/', 'dbpedia:', $value->getUri());
    } elseif ($value instanceof EasyRdf\Literal) {
        return $value->getValue();
    }
    return '';
}

// Parsez le contenu HTML de la page Wikipédia
$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($htmlContent);
libxml_clear_errors();

$xpath = new DOMXPath($doc);
$body = $xpath->query('//body')->item(0);

// Ajouter les annotations RDFa
if ($body) {
    $div = $doc->createElement('div');
    $div->setAttribute('typeof', 'schema:CreativeWork');
    $div->setAttribute('resource', 'http://dbpedia.org/resource/Zodiac');

    foreach ($results as $row) {
        if ($row->property instanceof EasyRdf\Resource && $row->hasValue) {
            $span = $doc->createElement('span', htmlspecialchars(formatUri($row->hasValue)));
            $span->setAttribute('property', htmlspecialchars(formatUri($row->property)));
            $div->appendChild($span);
            $div->appendChild($doc->createElement('br'));
        }
        if ($row->property instanceof EasyRdf\Resource && $row->isValueOf) {
            $span = $doc->createElement('span', htmlspecialchars(formatUri($row->isValueOf)));
            $span->setAttribute('property', htmlspecialchars(formatUri($row->property)));
            $div->appendChild($span);
            $div->appendChild($doc->createElement('br'));
        }
    }

    $body->appendChild($div);
}

// Afficher le contenu HTML enrichi
echo $doc->saveHTML();
?>
