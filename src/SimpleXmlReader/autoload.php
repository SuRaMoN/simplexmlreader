<?php

/**
 * Autoload is provided, but you better use
 * composer for autoloading
 */
spl_autoload_register(function ($class) {
    $availableClasses = array(
        'SimpleXmlReader\ExceptionThrowingXMLReader',
        'SimpleXmlReader\PathIterator',
        'SimpleXmlReader\SimpleXmlReader',
        'SimpleXmlReader\XmlException',
    );
    if (in_array($class, $availableClasses)) {
        require(__DIR__ . '/../' . strtr($class, '\\', '/') . '.php');
    }
});
