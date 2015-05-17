<?php

namespace SimpleXmlReader;

use Exception;
use PHPUnit_Framework_TestCase;


class MemoryUsageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group slow
     */
    public function testMemoryUsageStaysLow()
    {
        $startMemory = memory_get_usage();

        $xml = SimpleXmlReader::openGzippedXML(__DIR__ . '/testdata/test-large.xml.gz');
        foreach($xml->path('icontroller/documents/document') as $i => $doc) {
            if($i % 10000 == 0) {
                $this->assertLessThan($startMemory + 1024 * 1024, memory_get_usage());
            }
        }
    }
}

 
