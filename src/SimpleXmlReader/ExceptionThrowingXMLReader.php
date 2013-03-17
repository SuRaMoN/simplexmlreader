<?php

namespace SimpleXmlReader;

use Exception;
use XMLReader;


class ExceptionThrowingXMLReader extends XMLReader {

	static protected function ensureSuccess($returnValue) {
		if(!$returnValue) {
			throw new Exception('Error while performing XMLReader operation');
		}
		return $returnValue;
	}

	public function read() {
		return static::ensureSuccess(parent::read());
	}

	public function tryRead() {
		return parent::read();
	}

	public function next($localname = null) {
		if($localname === null) {
			return static::ensureSuccess(parent::next());
		} else {
			return static::ensureSuccess(parent::next($localname));
		}
	}

	public function tryNext($localname = null) {
		if($localname === null) {
			return parent::next();
		} else {
			return parent::next($localname);
		}
	}

	public function open($URI, $encoding = null, $options = 0) {
		return static::ensureSuccess(parent::open($URI, $encoding, $options));
	}
}
 
