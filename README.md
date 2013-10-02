SimpleXmlReader
===============

Interface for the PHP XML Pull parser XMLReader that adds super-simplified xpath functionality. This is ideal for reading huge xml files without the memory costs of other xml libraries (eg SImpleXMLElement).

[![Build Status](https://travis-ci.org/SuRaMoN/simplexmlreader.png?branch=master)](https://travis-ci.org/SuRaMoN/simplexmlreader)

Example
-------

source code: https://github.com/SuRaMoN/simplexmlreader/blob/master/examples/simple-example.php

```php
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

foreach($xml->path('root/animal') as $animal) {
	// $animal is of type SimpleXMLElelent
	// only the current iterated $animal is in memory, so huge xml files can be read, without much memory consumption
	echo "A {$animal->attributes()->type} has a tail? {$animal->hastail}!\n";
} 
```
