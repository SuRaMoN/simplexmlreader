<?php

namespace SimpleXmlReader;

use SimpleXmlReader\PathIterator;

require(__DIR__ . '/../src/SimpleXmlReader/autoload.php');

$xml = SimpleXmlReader::openFromString('
<root id="123">
    <continent name="Africa">
        <birds>
            <creature predator="no">
                <name>parrot</name>
                <memo>parrot is ...</memo>
            </creature>
            <creature predator="yes">
                <name>falcon</name>
                <memo>falcon is ...</memo>
            </creature>
        </birds>
        <animals>
            <creature predator="no">
                <name>gazelle</name>
                <memo>gazelle is ...</memo>
            </creature>
            <creature predator="yes">
                <name>lion</name>
                <memo>lion is ...</memo>
            </creature>
        </animals>
    </continent>
    <continent name="Eurasia">
        <birds>
            <creature predator="no">
                <name>straus</name>
                <memo>straus is ...</memo>
            </creature>
            <creature predator="yes">
                <name>eagle</name>
                <memo>eagle is ...</memo>
            </creature>
        </birds>
        <animals>
            <creature predator="no">
                <name>panda</name>
                <memo>panda is ...</memo>
            </creature>
            <creature predator="yes">
                <name>tiger</name>
                <memo>tiger is ...</memo>
            </creature>
        </animals>
    </continent>
</root>
');

foreach ($xml->path('root/continent/*/creature', SimpleXmlReader::RETURN_SIMPLE_XML, function ($xr, $crumbs) {
    $path = implode("/", $crumbs);
    if ($path == "root") {
        if ($xr->getAttribute('id') != '123') {
            return PathIterator::ELEMENT_IS_INVALID;
        }
    } elseif ($path == "root/continent") {
        if ($xr->getAttribute('name') != 'Eurasia') {
            return PathIterator::ELEMENT_IS_INVALID;
        }
    } elseif (preg_match(chr(1) . 'root/continent/[^/]+/creature' . chr(1), $path)) {
        if ($xr->getAttribute('predator') != 'yes') {
            return PathIterator::ELEMENT_IS_INVALID;
        }
    }
    return PathIterator::ELEMENT_IS_VALID;
}) as $animal) {
    echo "A {$animal->name} is predator! {$animal->memo}\n";
}
