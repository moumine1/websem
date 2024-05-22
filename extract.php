<?php
$url = "https://fr.wikipedia.org/wiki/Constellations_du_zodiaque";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$htmlContent = curl_exec($ch);
curl_close($ch);