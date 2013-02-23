<?php

spl_autoload_register(function ($class) {
	$availableClasses = array(
		'SimpleXmlReader\ExceptionThrowingXMLReader',
		'SimpleXmlReader\SimpleXmlReader',
		'SimpleXmlReader\SimpleXmlReaderIterator',
	);
	if(in_array($class, $availableClasses)) {
		require(__DIR__ . '/../' . strtr($class, '\\', '/') . '.php');
	}
});

