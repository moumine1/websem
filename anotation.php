<?php
include_once("extract.php");
include_once("info.php");

$htmlContent = file_get_contents('https://fr.wikipedia.org/wiki/Zodiaque');

$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($htmlContent);
libxml_clear_errors();

$xpath = new DOMXPath($doc);
$body = $xpath->query('//body')->item(0);

if ($body) {
    $newSection = $doc->createElement('div');
    $newSection->setAttribute('class', 'sparql-results');
    
    $title = $doc->createElement('h2', 'Informations supplémentaires sur Zodiac');
    $newSection->appendChild($title);
    
    $table = $doc->createElement('table');
    $table->setAttribute('border', '1');
    $headerRow = $doc->createElement('tr');
    
    $headers = ['Propriété', 'Valeur', 'Est valeur de'];
    foreach ($headers as $header) {
        $th = $doc->createElement('th', $header);
        $headerRow->appendChild($th);
    }
    $table->appendChild($headerRow);

    foreach ($results as $row) {
        $tr = $doc->createElement('tr');
        /*var_dump($row);
        print_r("<br>");
        print_r("<br>");
        var_dump($row->property);
        print_r("<br>");
        print_r("<br>");
        var_dump($row)->hasValue;
        print_r("<br>");
        print_r("<br>");
        print_r("<br>");
        var_dump($row->property);
        print_r("<br>");
        print_r("<br>");
        var_dump($row->hasValue);
        print_r("<br>");
        print_r("<br>");
        var_dump($row->isValueOf);
        print_r("<br>");
        print_r("<br>");
        die(var_dump($results));*/
        //$tdProperty = $doc->createElement('td', htmlspecialchars(formatUri($row->property->getUri())));
        $tdProperty = $doc->createElement('td', htmlspecialchars(formatUri($row->property->getUri())));
        //$tdHasValue = $doc->createElement('td', htmlspecialchars($row->hasValue ? formatUri($row->hasValue->getUri()) : ''));
        if(isset($row->hasValue)){
            //die(var_dump($row));
            $tdHasValue = $doc->createElement('td', htmlspecialchars(formatUri($row->hasValue)));
            $tr->appendChild($tdHasValue);

        }
        if(isset($row->isValueOf)){
            $tdIsValueOf = $doc->createElement('td', htmlspecialchars(formatUri($row->isValueOf)));
            $tr->appendChild($tdIsValueOf);

        }
        
        $tr->appendChild($tdProperty);
        
        $table->appendChild($tr);
    }
    
    $newSection->appendChild($table);
    $body->appendChild($newSection);
}

echo $doc->saveHTML();
?>
