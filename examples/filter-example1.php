<?php

namespace SimpleXmlReader;

use SimpleXmlReader\PathIterator;

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

foreach ($xml->path('root/animal', SimpleXmlReader::RETURN_SIMPLE_XML, function ($xr, $crumbs) {
    $path = implode("/", $crumbs);
    if ($path == "root/animal") {
        if (! in_array($xr->getAttribute('type'), ['dog', 'kakariki'])) {
            return PathIterator::ELEMENT_IS_INVALID;
        }
    }
    return PathIterator::ELEMENT_IS_VALID;
}) as $animal) {
    echo "A {$animal->attributes()->type} has a tail? {$animal->hastail}!\n";
}
