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

function formatUri($uri) {
    return str_replace('http://dbpedia.org/resource/', 'dbpedia:', $uri);
}
