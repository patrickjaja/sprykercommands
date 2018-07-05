<?php

$xml = new SimpleXMLElement(file_get_contents('coverage.xml'));

$result = $xml->xpath('/coverage/project//file/@name');

foreach ($result as $filePath) {
    $filePath->name=str_replace('/','\\',$filePath->name);
    $filePath->name=str_replace("\data\shop\development\current\src","D:\projekte\sprykerlekkerland\current\src",$filePath->name);
}
$xml->asXML('newFile'.time().'.xml');
die('done');