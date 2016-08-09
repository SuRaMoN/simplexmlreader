<?php

namespace SimpleXmlReader;

use Exception;
use XMLReader;

class ExceptionThrowingXMLReader extends XMLReader
{
    public function open($URI, $encoding = null, $options = 0)
    {
        return static::ensureSuccess(parent::open($URI, $encoding, $options), 'open');
    }

    static protected function ensureSuccess($returnValue, $operation)
    {
        if (! $returnValue) {
            throw new Exception("Error while performing XMLReader::$operation");
        }
        return $returnValue;
    }

    public function read()
    {
        return static::ensureSuccess(parent::read(), 'read');
    }

    public function tryRead()
    {
        // We're ignoring any PHP errors, as we are trying to read
        return @parent::read();
    }

    public function next($localName = null)
    {
        if (null === $localName) {
            return static::ensureSuccess(parent::next(), 'next');
        }

        return static::ensureSuccess(parent::next($localName), 'next');
    }

    public function tryNext($localName = null)
    {
        // We're ignoring any PHP errors, as we are trying to fetch the next element
        if (null === $localName) {
            return @parent::next();
        }

        return @parent::next($localName);
    }
}
