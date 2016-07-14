<?php

/**
 * Autoload is provided, but you better use
 * composer for autoloading
 */
spl_autoload_register(function ($class) {
    $availableClasses = array(
        'SimpleXmlReader\ExceptionThrowingXMLReader',
        'SimpleXmlReader\SimpleXmlReader',
        'SimpleXmlReader\PathIterator',
    );
    if (in_array($class, $availableClasses)) {
        require(__DIR__ . '/../' . strtr($class, '\\', '/') . '.php');
    }
});
