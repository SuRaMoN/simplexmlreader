<?php

namespace SimpleXmlReader;

use XMLReader;
use Iterator;
use DOMDocument;

class PathIterator implements Iterator
{
    const IS_MATCH = 'IS_MATCH';
    const DESCENDANTS_COULD_MATCH = 'DESCENDANTS_COULD_MATCH';
    const DESCENDANTS_CANT_MATCH = 'DESCENDANTS_CANT_MATCH';

    /*
     * The list of return codes for filtering callback function
     */
    /*
     * Valid elem, no filtering.
     */
    const ELEMENT_IS_VALID = 1; // elem
    /*
     * Invalid elem and its descendants, so have to be filtered out.
     */
    const ELEMENT_IS_INVALID = 2;
    /*
     * The same as `ELEMENT_IS_INVALID`. Additionaly after it sibling elems(and its descendants) have to be filtered out too.
     */
    const SIBLINGS_ARE_INVALID = 3;

    protected $reader;
    protected $searchPath;
    protected $searchCrumbs;
    protected $crumbs;
    protected $currentDomExpansion;
    protected $rewindCount;
    protected $isValid;
    protected $returnType;

    /*
     * Filtering callback function
     */
    protected $callback;

    public function __construct(ExceptionThrowingXMLReader $reader, $path, $returnType, $callback = null)
    {
        $this->reader = $reader;
        $this->searchPath = $path;
        $this->searchCrumbs = explode('/', $path);
        $this->crumbs = array();
        $this->matchCount = -1;
        $this->rewindCount = 0;
        $this->isValid = false;
        $this->returnType = $returnType;
        $this->callback = $callback;
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
            throw new XmlException('Multiple rewinds not supported');
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
                $simplexml = simplexml_import_dom($this->reader->expand(new DOMDocument('1.0')));
                if (false === $simplexml) {
                    throw new XMlException('Failed to create a SimpleXMLElement from the current XML node (invalid XML?)');
                }

                return $simplexml;

            default:
                throw new Exception(sprintf("Unknown return type: %s", $this->returnType));
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

    protected function searchForOpenTag(XMLReader $r)
    {
        // search for open tag
        while ($r->nodeType != XMLReader::ELEMENT) {
            if (! $r->tryRead()) { return false; }
        }
        return true;
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
            if (! $this->searchForOpenTag($r)) { return false; }

            // fill crumbs
            array_splice($this->crumbs, $r->depth, count($this->crumbs), array($r->name));

            $matching = $this->pathIsMatching();

            $uf = self::ELEMENT_IS_VALID;
            if ($this->callback && is_callable($this->callback)
                && ($uf = call_user_func_array($this->callback, [$r, $this->crumbs])) !== self::ELEMENT_IS_VALID) {

                // extra check for sanity of a value returned by the user filter
                if ($uf !== self::SIBLINGS_ARE_INVALID && $uf !== self::ELEMENT_IS_INVALID ) {
                    $uf = self::ELEMENT_IS_INVALID;
                }

                $df = $r->depth;

                if ($uf === self::SIBLINGS_ARE_INVALID) { $df--; }
                $matching = self::DESCENDANTS_CANT_MATCH;
            }

            switch ($matching) {

                case self::DESCENDANTS_COULD_MATCH:
                    if (! $r->tryRead()) { return false; }
                    continue 2;

                case self::DESCENDANTS_CANT_MATCH:

                    if (! $r->tryNext()) { return false; }
                    if ($uf !== self::ELEMENT_IS_VALID) {
                        if (! $this->searchForOpenTag($r)) { return false; }
                        while ($r->depth > $df) {
                            if (! $r->tryNext()) { return false; }
                            if (! $this->searchForOpenTag($r)) { return false; }
                        }
                    }
                    continue 2;

                case self::IS_MATCH:
                    return true;
            }

            return false;
        }
    }
}
