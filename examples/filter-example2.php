<?php

namespace SimpleXmlReader;

use SimpleXmlReader\PathIterator;

require(__DIR__ . '/../src/SimpleXmlReader/autoload.php');


$xml = SimpleXmlReader::openFromString('
 <root>
 	<group>
 		<type>pet</type>
		<animal type="cat">
		   <hastail>yes</hastail>
		</animal>
		<animal type="dog">
		   <hastail>yes</hastail>
		</animal>
	</group>
	<group>
		<type>wild</type>
		<animal type="kakariki">
		   <hastail>no</hastail>
		</animal>
	</group>
 </root>
');

foreach ($xml->path('root/group/animal', SimpleXmlReader::RETURN_SIMPLE_XML, function ($xr, $crumbs) {
    $path = implode("/", $crumbs);
    if ($path == "root/group/type") {
        if ($xr->readString() != 'pet') {
            return PathIterator::SIBLINGS_ARE_INVALID;
        }
    }
    return PathIterator::ELEMENT_IS_VALID;
}) as $animal) {
    echo "A {$animal->attributes()->type} has a tail? {$animal->hastail}!\n";
}
