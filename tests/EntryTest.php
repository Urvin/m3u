<?php


namespace urvin\m3u\tests;

use PHPUnit\Framework\TestCase;
use urvin\m3u\M3uEntry;

class EntryTest extends TestCase
{
    public function testConstructor()
    {
        $entry = new M3uEntry([
            'path' => '/some/path',
            'name' => 'Some Name',
            'group' => 'Some Group',
        ]);

        $this->assertEquals(
            '/some/path',
            $entry->path
        );

        $this->assertEquals(
            'Some Name',
            $entry->name
        );

        $this->assertEquals(
            'Some Group',
            $entry->group
        );

        $this->assertEmpty(
            $entry->artist
        );

        $this->assertEmpty(
            $entry->length
        );

        $this->assertEmpty(
            $entry->logo
        );

        $this->assertEmpty(
            $entry->byteRange
        );
    }

    public function testToString()
    {
        $entry = new M3uEntry([
            'path' => '/some/path',
            'name' => 'Some Name',
            'group' => 'Some Group',
        ]);


        $this->assertEquals(
            "#EXTINF:0 group-title=\"Some Group\" tvg-name=\"Some Name\",Some Name\n#EXTGRP:Some Group\n/some/path\n",
            (string)$entry
        );

        $this->assertEquals(
            "#EXTINF:0 group-title=\"Some Group\" tvg-name=\"Some Name\",Some Name\n/some/path\n",
            $entry->toString(false)
        );

        $entry->path = '';
        $this->assertEmpty(
            (string)$entry
        );
    }
}