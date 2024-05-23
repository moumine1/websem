<?php
require '../vendor/autoload.php';

$url = "https://fr.wikipedia.org/wiki/Zodiaque";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$htmlContent = curl_exec($ch);
curl_close($ch);

$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($htmlContent);
libxml_clear_errors();

$xpath = new DOMXPath($doc);
$linkNodes = $xpath->query('//*[@id="mw-content-text"]//a[@href]');
$titleNodes = $xpath->query('//*[@id="mw-content-text"]//h1 | //*[@id="mw-content-text"]//h2 | //*[@id="mw-content-text"]//h3 | //*[@id="mw-content-text"]//h4 | //*[@id="mw-content-text"]//h5 | //*[@id="mw-content-text"]//h6');

$keywords = [];
foreach ($linkNodes as $node) {
    $keywords[] = $node->textContent;
}
foreach ($titleNodes as $node) {
    $keywords[] = $node->textContent;
}
$keywords = array_unique($keywords);

//die(var_dump($keywords));

$sparqlEndpoint = 'http://dbpedia.org/sparql';

function fetchSparqlData($keyword) {
    global $sparqlEndpoint;
    $resource = "http://dbpedia.org/resource/" . urlencode($keyword);
    $query = "
        SELECT ?property ?hasValue ?isValueOf
        WHERE {
            { <$resource> ?property ?hasValue }
            UNION
            { ?isValueOf ?property <$resource> }
        }
    ";

    $sparql = new EasyRdf\Sparql\Client($sparqlEndpoint);
    return $sparql->query($query);
}

function formatUri($value) {
    if ($value instanceof EasyRdf\Resource) {
        return $value->getUri();
    } elseif ($value instanceof EasyRdf\Literal) {
        return $value->getValue();
    }
    return '';
}

$annotations = [];
foreach ($keywords as $keyword) {
    $results = fetchSparqlData($keyword);
    foreach ($results as $row) {
        if(isset($row->hasValue)&&isset($row->isValueOf)){
            $annotations[$keyword][] = [
                'property' => $row->property ? formatUri($row->property) : null,
                'hasValue' => $row->hasValue ? formatUri($row->hasValue) : null,
                'isValueOf' => $row->isValueOf ? formatUri($row->isValueOf) : null,
            ];
        }
        else if(isset($row->hasValue)){
            $annotations[$keyword][] = [
                'property' => $row->property ? formatUri($row->property) : null,
                'hasValue' => $row->hasValue ? formatUri($row->hasValue) : null,
            ];
        }
        else if(isset($row->isValueOf)){
            $annotations[$keyword][] = [
                'property' => $row->property ? formatUri($row->property) : null,
                'isValueOf' => $row->isValueOf ? formatUri($row->isValueOf) : null,
            ];
        }
        else{
            $annotations[$keyword][] = [
                'property' => $row->property ? formatUri($row->property) : null,
            ];  
        }

    }
}

foreach ($linkNodes as $node) {
    $keyword = $node->textContent;
    if (isset($annotations[$keyword])) {
        foreach ($annotations[$keyword] as $annotation) {
            if ($annotation['property']) {
                $node->setAttribute('property', $annotation['property']);
            }
            if ($annotation['hasValue']) {
                $node->setAttribute('hasValue', $annotation['hasValue']);
            }
            if ($annotation['isValueOf']) {
                $node->setAttribute('isValueOf', $annotation['isValueOf']);
            }
        }
    }
}

foreach ($titleNodes as $node) {
    $keyword = $node->textContent;
    if (isset($annotations[$keyword])) {
        foreach ($annotations[$keyword] as $annotation) {
            if ($annotation['property']) {
                $node->setAttribute('property', $annotation['property']);
            }
            if ($annotation['hasValue']) {
                $node->setAttribute('hasValue', $annotation['hasValue']);
            }
            if ($annotation['isValueOf']) {
                $node->setAttribute('isValueOf', $annotation['isValueOf']);
            }
        }
    }
}

echo $doc->saveHTML();
?>
