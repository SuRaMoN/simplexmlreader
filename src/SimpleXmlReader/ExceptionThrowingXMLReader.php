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
        if(! $returnValue) {
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
        return parent::read();
    }

    public function next($localName = null)
    {
        if(null === $localName) {
            return static::ensureSuccess(parent::next(), 'next');
        } else {
            return static::ensureSuccess(parent::next($localName), 'next');
        }
    }

    public function tryNext($localName = null)
    {
        if(null === $localName) {
            return parent::next();
        } else {
            return parent::next($localName);
        }
    }
}
 
