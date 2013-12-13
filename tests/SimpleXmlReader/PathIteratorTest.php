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
}

