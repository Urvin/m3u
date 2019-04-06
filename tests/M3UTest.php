<?php


namespace urvin\m3u\tests;

use PHPUnit\Framework\TestCase;
use urvin\m3u\M3u;
use urvin\m3u\M3uEntry;
use urvin\m3u\M3uException;

class M3uTest extends TestCase
{
    public function testEntries()
    {
        $m3u = new M3u();

        $entry = new M3uEntry([
            'path' => '/path/to/file',
            'name' => 'Name'
        ]);

        $this->assertEquals(
            0,
            count($m3u->getEntries())
        );

        $m3u->addEntry($entry);

        $this->assertEquals(
            1,
            count($m3u->getEntries())
        );

        $this->assertEquals(
            $entry,
            $m3u->getEntries()[0]
        );
    }

    public function testFailurePlaylist()
    {
        $this->expectException(M3uException::class);
        $playlist = 'Not an m3u';
        $m3u = new M3u();

        $m3u->parse($playlist);
    }

    public function testParsing()
    {
        $m3u = new M3u();

        for ($i = 1; $i <= 4; ++$i) {
            $fileNamePart = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'playlist_' . $i;
            $fileToRead = $fileNamePart . '.m3u';
            $fileToCheck = $fileNamePart . '_check.m3u';


            $m3u->parse(file_get_contents($fileToRead));

            $this->assertEquals(
                file_get_contents($fileToCheck),
                (string)$m3u
            );
        }
    }
}