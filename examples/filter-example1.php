<?php

namespace SimpleXmlReader;

require(__DIR__ . '/../src/SimpleXmlReader/autoload.php');


$xml = SimpleXmlReader::openFromString('
 <root>
	<animal type="cat">
	   <hastail>yes</hastail>
	</animal>
	<animal type="dog">
	   <hastail>yes</hastail>
	</animal>
	<animal type="kakariki">
	   <hastail>no</hastail>
	</animal>
 </root>
');

foreach ($xml->path('root/animal', SimpleXMLReader::RETURN_SIMPLE_XML, function ($xr, $crumbs) {
    $path = implode("/", $crumbs);
    if ($path == "root/animal") {
        if (! in_array($xr->getAttribute('type'), ['dog', 'cat'])) {
            return false;
        }
    }
    return true;
}) as $animal) {
    echo "A {$animal->attributes()->type} has a tail? {$animal->hastail}!\n";
}
