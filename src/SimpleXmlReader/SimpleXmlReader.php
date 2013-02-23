<?php

namespace SimpleXmlReader;


class SimpleXmlReader extends ExceptionThrowingXMLReader {

	const RETURN_DOM = 'RETURN_DOM';
	const RETURN_SIMPLE_XML = 'RETURN_SIMPLE_XML';

	public static function openXML($path, $encoding = 'UTF-8', $options = 0) {
		$reader = new static();
		$reader->open($path, $encoding, $options);
		return $reader;
	}

	public static function openGzippedXML($path, $encoding = 'UTF-8', $options = 0) {
		return static::openXML("compress.zlib://$path", $encoding, $options);
	}

	public static function openFromString($source, $encoding = 'UTF-8', $options = 0) {
		$reader = new static();
		$reader->XML($source, $encoding, $options);
		return $reader;
	}

	public function path($path, $returnType = self::RETURN_SIMPLE_XML) {
		return new SimpleXmlReaderIterator($this, $path, $returnType);
	}
}

