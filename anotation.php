<?php
require 'vendor/autoload.php';

use EasyRdf\Graph;
use EasyRdf\RdfNamespace;

RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
RdfNamespace::set('dbr', 'http://dbpedia.org/resource/');

$graph = new Graph();

// Ajouter des annotations pour la page en français
$resource_fr = $graph->resource('http://fr.wikipedia.org/wiki/Zodiaque');
$resource_fr->add('rdf:type', 'dbo:Constellation');
$resource_fr->add('rdfs:label', 'Constellation du zodiaque');
$resource_fr->add('rdfs:comment', 'Une constellation du zodiaque en astronomie.');

// Ajouter des annotations pour la page en anglais
$resource_en = $graph->resource('http://en.wikipedia.org/wiki/Zodiac');
$resource_en->add('rdf:type', 'dbo:Constellation');
$resource_en->add('rdfs:label', 'Zodiac constellation');
$resource_en->add('rdfs:comment', 'A zodiac constellation in astronomy.');

// Sauvegarder le graphe RDF en format Turtle
file_put_contents('constellations.ttl', $graph->serialise('turtle'));
?>
