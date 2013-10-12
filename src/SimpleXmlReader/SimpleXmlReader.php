<?php

namespace SimpleXmlReader;


class SimpleXmlReader
{
	const RETURN_DOM = 'RETURN_DOM';
	const RETURN_SIMPLE_XML = 'RETURN_SIMPLE_XML';
	const RETURN_INNER_XML_STRING = 'RETURN_INNER_XML_STRING';
	const RETURN_OUTER_XML_STRING = 'RETURN_OUTER_XML_STRING';

	protected $xmlReader;

	protected function __construct()
	{
		$this->xmlReader = new ExceptionThrowingXMLReader();
	}

	public static function openXML($path, $encoding = 'UTF-8', $options = 0)
	{
		$simpleXmlReader = new self();
		$simpleXmlReader->xmlReader->open($path, $encoding, $options);
		return $simpleXmlReader;
	}

	public static function openGzippedXML($path, $encoding = 'UTF-8', $options = 0)
	{
		return self::openXML("compress.zlib://$path", $encoding, $options);
	}

	public static function openFromString($source, $encoding = 'UTF-8', $options = 0)
	{
		$simpleXmlReader = new self();
		$simpleXmlReader->xmlReader->XML($source, $encoding, $options);
		return $simpleXmlReader;
	}

	public function path($path, $returnType = self::RETURN_SIMPLE_XML)
	{
		return new PathIterator($this->xmlReader, $path, $returnType);
	}
}

