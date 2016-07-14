<?php

namespace SimpleXmlReader;

use XMLReader;
use Exception;
use Iterator;
use DOMDocument;

class PathIterator implements Iterator
{
    const IS_MATCH = 'IS_MATCH';
    const DESCENDANTS_COULD_MATCH = 'DESCENDANTS_COULD_MATCH';
    const DESCENDANTS_CANT_MATCH = 'DESCENDANTS_CANT_MATCH';

    protected $reader;
    protected $searchPath;
    protected $searchCrumbs;
    protected $crumbs;
    protected $currentDomExpansion;
    protected $rewindCount;
    protected $isValid;
    protected $returnType;

    public function __construct(ExceptionThrowingXMLReader $reader, $path, $returnType)
    {
        $this->reader = $reader;
        $this->searchPath = $path;
        $this->searchCrumbs = explode('/', $path);
        $this->crumbs = array();
        $this->matchCount = -1;
        $this->rewindCount = 0;
        $this->isValid = false;
        $this->returnType = $returnType;
    }

    public function current()
    {
        return $this->currentDomExpansion;
    }

    public function key()
    {
        return $this->matchCount;
    }

    public function next()
    {
        $this->isValid = $this->tryGotoNextIterationElement();

        if ($this->isValid) {
            $this->matchCount += 1;
            $this->currentDomExpansion = $this->getXMLObject();
        }
    }

    public function rewind()
    {
        $this->rewindCount += 1;
        if ($this->rewindCount > 1) {
            throw new Exception('Multiple rewinds not supported');
        }
        $this->next();
    }

    public function valid()
    {
        return $this->isValid;
    }

    protected function getXMLObject()
    {
        switch ($this->returnType) {
            case SimpleXMLReader::RETURN_DOM:
                return $this->reader->expand();

            case SimpleXMLReader::RETURN_INNER_XML_STRING:
                return $this->reader->readInnerXML();

            case SimpleXMLReader::RETURN_OUTER_XML_STRING:
                return $this->reader->readOuterXML();

            case SimpleXMLReader::RETURN_SIMPLE_XML:
                return simplexml_import_dom($this->reader->expand(new DOMDocument('1.0')));

            default:
                throw new Exception("Unknow return type: {$this->returnType}");
        }
    }

    protected function pathIsMatching()
    {
        if (count($this->crumbs) > count($this->searchCrumbs)) {
            return self::DESCENDANTS_CANT_MATCH;
        }
        foreach ($this->crumbs as $i => $crumb) {
            $searchCrumb = $this->searchCrumbs[$i];
            if ($searchCrumb == $crumb || $searchCrumb == '*') {
                continue;
            }
            return self::DESCENDANTS_CANT_MATCH;
        }
        if (count($this->crumbs) == count($this->searchCrumbs)) {
            return self::IS_MATCH;
        }
        return self::DESCENDANTS_COULD_MATCH;
    }

    public function tryGotoNextIterationElement()
    {
        $r = $this->reader;

        if ($r->nodeType == XMLReader::NONE) {
            // first time we do a read from the xml
            if (! $r->tryRead()) { return false; }
        } else {
            // if we have already had a match
            if (! $r->tryNext()) { return false; }
        }

        while (true) {
            // search for open tag
            while ($r->nodeType != XMLReader::ELEMENT) {
                if (! $r->tryRead()) { return false; }
            }

            // fill crumbs
            array_splice($this->crumbs, $r->depth, count($this->crumbs), array($r->name));

            switch ($this->pathIsMatching()) {

                case self::DESCENDANTS_COULD_MATCH:
                    if (! $r->tryRead()) { return false; }
                    continue 2;

                case self::DESCENDANTS_CANT_MATCH:
                    if (! $r->tryNext()) { return false; }
                    continue 2;

                case self::IS_MATCH:
                    return true;
            }
        }
    }
}
