<?php


namespace urvin\m3u\tests;

use PHPUnit\Framework\TestCase;
use urvin\m3u\M3uException;

class ExceptionTest extends TestCase
{
    public function testException()
    {
        $this->assertInstanceOf(
            \Exception::class,
            new M3uException()
        );
    }
}