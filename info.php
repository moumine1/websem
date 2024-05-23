<?php
require 'vendor/autoload.php';

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
    // Vérifiez si la valeur est une ressource ou un littéral
    if ($value instanceof EasyRdf\Resource) {
        return str_replace('http://dbpedia.org/resource/', 'dbpedia:', $value->getUri());
    } elseif ($value instanceof EasyRdf\Literal) {
        return $value->getValue();
    }
    return '';
}
