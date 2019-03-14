<?php

namespace SimpleXmlReader;

use PHPUnit_Framework_TestCase;

class PathIteratorTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function testBasicFunctionality()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/simpletest.xml');
        $matches = iterator_to_array($xml->path('root/matchparent/match'));
        $this->assertEquals('match1', (string) $matches[0]);
        $this->assertEquals('match2', (string) $matches[1]);
        $this->assertEquals('match3', (string) $matches[2]->child);
        $this->assertEquals(3, count($matches));
    }

    /** @test */
    public function testStart()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/simpletest.xml');
        $matches = iterator_to_array($xml->path('root/*/match'));
        $this->assertEquals(3, count($matches));
    }

    /** @test */
    public function testPathOuterXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/simpletest.xml');
        $matches = iterator_to_array($xml->path('root/matchparent/match', SimpleXmlReader::RETURN_OUTER_XML_STRING));
        $this->assertEquals('<match><child>match3</child></match>', preg_replace('/\s/', '', (string) $matches[2]));
    }

    /** @test */
    public function testPathInnerXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/simpletest.xml');
        $matches = iterator_to_array($xml->path('root/matchparent/match', SimpleXmlReader::RETURN_INNER_XML_STRING));
        $this->assertEquals('<child>match3</child>', preg_replace('/\s/', '', (string) $matches[2]));
    }

    /** @test */
    public function testEmptyXmlShortTagNotiation()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/empty1.xml');
        $matches = iterator_to_array($xml->path('root/matchparent/match'));
        $this->assertCount(0, $matches);
    }

    /** @test */
    public function testEmptyXmlOpenCloseTagNotiation()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/empty2.xml');
        $matches = iterator_to_array($xml->path('root/matchparent/match'));
        $this->assertCount(0, $matches);
    }

    /** @test */
    public function testEmptyXmlWithHeader()
    {
        $xml = SimpleXmlReader::openGzippedXML(__DIR__ . '/testdata/empty3.xml.gz');
        $matches = iterator_to_array($xml->path('root/matchparent/match'));
        $this->assertCount(0, $matches);
    }

    /** @test */
    public function testAutoOpen()
    {
        $xml = SimpleXmlReader::autoOpenXML(__DIR__ . '/testdata/empty3.xml.gz');
        $this->assertCount(0, iterator_to_array($xml->path('root/matchparent/match')));
        $xml = SimpleXmlReader::autoOpenXML(__DIR__ . '/testdata/empty2.xml');
        $this->assertCount(0, iterator_to_array($xml->path('root/matchparent/match')));
    }

    /** @test */
    public function testInvalidXml1()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/invalid1.xml');
        // No PHP errors should be raised
        $iterator = $xml->path('root/foo/bar');
        $result = iterator_to_array($iterator);
        $this->assertCount(2, $result);
        foreach ($result as $bar) {
            $this->assertEquals('foobarbaz', (string) $bar->baz);
        }
    }

    /** @test */
    public function testInvalidXml2()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/invalid2.xml');
        // No PHP errors should be raised, but result is empty
        $iterator = $xml->path('root/foo/bar');
        $result = iterator_to_array($iterator);
        $this->assertCount(0, $result);
    }

    /** @test */
    public function testInvalidXml3()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/invalid3.xml');
        // No PHP errors should be raised, but an exception must be thrown
        $iterator = $xml->path('response/result/log/logs/entry');
        $this->setExpectedException('SimpleXmlReader\XmlException');
        iterator_to_array($iterator);
    }


    /** @test */
    public function testPathCbAttr1OuterXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/cb.xml');
        $res = implode('', iterator_to_array($xml->path('root/zoo/animal', SimpleXmlReader::RETURN_OUTER_XML_STRING, function ($xr, $crumbs) {
            $path = implode("/", $crumbs);
            if ($path == "root/zoo") {
                if ($xr->getAttribute('city') != "Banghok") {
                    return PathIterator::ELEMENT_IS_INVALID;
                }
            }
            return PathIterator::ELEMENT_IS_VALID;
        })));
        $this->assertEquals('<animal>kakariki</animal>', preg_replace('/\s/', '', (string) $res));
    }

     /** @test */
    public function testPathCbAttr2OuterXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/cb.xml');
        $res = implode('', iterator_to_array($xml->path('root/zoo/animal', SimpleXmlReader::RETURN_OUTER_XML_STRING, function ($xr, $crumbs) {
            $path = implode("/", $crumbs);
            if ($path == "root/zoo") {
                if ($xr->getAttribute('contenent') != "Europe") {
                    return PathIterator::ELEMENT_IS_INVALID;
                }
            }
            return PathIterator::ELEMENT_IS_VALID;
        })));
        $this->assertEquals('<animal>cat</animal><animal>bear</animal>', preg_replace('/\s/', '', (string) $res));
    }

    /** @test */
    public function testPathCbElemOuterXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/cb.xml');
        $res = implode('', iterator_to_array($xml->path('root/zoo/animal', SimpleXmlReader::RETURN_OUTER_XML_STRING, function ($xr, $crumbs) {
            $path = implode("/", $crumbs);
            if ($path == "root/zoo/work") {
                if ($xr->readString() != "yes") {
                    return PathIterator::SIBLINGS_ARE_INVALID;
                }
            }
            return PathIterator::ELEMENT_IS_VALID;
        })));
        $this->assertEquals('<animal>kakariki</animal><animal>bear</animal>', preg_replace('/\s/', '', (string) $res));
    }

    /** @test */
    public function testPathCbElemAttrOuterXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/cb.xml');
        $res = implode('', iterator_to_array($xml->path('root/zoo/animal', SimpleXmlReader::RETURN_OUTER_XML_STRING, function ($xr, $crumbs) {
            $path = implode("/", $crumbs);
            if ($path == "root/zoo") {
                if ($xr->getAttribute('contenent') != "Europe") {
                    return PathIterator::ELEMENT_IS_INVALID;
                }
            } elseif ($path == "root/zoo/work") {
                if ($xr->readString() != "yes") {
                    return PathIterator::SIBLINGS_ARE_INVALID;
                }
            }
            return PathIterator::ELEMENT_IS_VALID;
        })));
        $this->assertEquals('<animal>bear</animal>', preg_replace('/\s/', '', (string) $res));
    }

    /** @test */
    public function testPathCb2ElemOuterXml()
    {
        $xml = SimpleXmlReader::openXML(__DIR__ . '/testdata/cb2.xml');
        $res = implode('', iterator_to_array($xml->path('root/group/zoos/zoo/animal', SimpleXmlReader::RETURN_OUTER_XML_STRING, function ($xr, $crumbs) {
            $path = implode("/", $crumbs);
            if ($path == "root/group/work") {
                if ($xr->readString() != "yes") {
                    return PathIterator::SIBLINGS_ARE_INVALID;
                }
            }
            return PathIterator::ELEMENT_IS_VALID;
        })));
        $this->assertEquals('<animal>kakariki</animal><animal>bear</animal>', preg_replace('/\s/', '', (string) $res));
    }
}
