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
        $this->setExpectedException('Exception');
	iterator_to_array($iterator);
    }

}
