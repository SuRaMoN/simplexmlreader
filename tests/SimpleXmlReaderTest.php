<?php

namespace SimpleXmlReader;

use Exception;
use PHPUnit_Framework_TestCase;


class SimpleXmlReaderTest extends PHPUnit_Framework_TestCase
{
	/** @test */
	public function testOpenFile()
	{
		$xml = SimpleXmlReader::openXML(__DIR__ . '/data/test.xml');
		$matches = iterator_to_array($xml->path('root/matchparent/match'));
		$this->assertEquals('match1', (string) $matches[0]);
		$this->assertEquals('match2', (string) $matches[1]);
		$this->assertEquals('match3', (string) $matches[2]->child);
		$this->assertEquals(3, count($matches));
	}
}

