<?php

namespace urvin\m3u;

/**
 * Class M3u
 * @package urvin\m3u
 */
class M3u
{
    /**
     * @var M3uEntry[]
     */
    protected $entries = [];

    /**
     * @return M3uEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param \urvin\m3u\M3uEntry $entry
     */
    public function addEntry(M3uEntry $entry): void
    {
        $this->entries[] = $entry;
    }

    /**
     * @param string $str
     * @throws \urvin\m3u\M3uException
     */
    public function parse(string $str): void
    {
        $this->removeBom($str);

        $this->entries = [];
        $lines = explode("\n", $str);
        $linesCount = count($lines);

        if($linesCount == 0 || !$this->isExtM3u(trim($lines[0]))) {
            throw new M3uException('Wrong file format');
        }

        $entry = new M3uEntry();

        for($i = 1; $i < $linesCount; ++$i) {
            $line = trim($lines[$i]);

            if(empty($line)) {
                continue;
            }

            if($this->isComment($line)) {
                if($this->isExtInf($line)) {
                    $this->parseExtInf($line, $entry);
                } elseif ($this->isExtGrp($line)) {
                    $this->parseExtGrp($line, $entry);
                } elseif ($this->isExtByteRange($line)) {
                    $this->parseExtByteRange($line, $entry);
                }
            } else {
                $entry->path = $line;
                $this->addEntry($entry);
                $entry = new M3uEntry();
            }
        }

    }

    /**
     * @param string $str
     */
    protected function removeBom(string &$str): void
    {
        if (substr($str, 0, 3) === "\xEF\xBB\xBF") {
            $str = substr($str, 3);
        }
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isExtM3u(string $str): bool
    {
        return strtoupper(substr($str, 0, 7)) === '#EXTM3U';
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isComment(string $str): bool
    {
        return substr($str, 0, 1) === '#';
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isExtInf(string $str): bool
    {
        return strtoupper(substr($str, 0, 8)) === '#EXTINF:';
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isExtGrp(string $str): bool
    {
        return strtoupper(substr($str, 0, 8)) === '#EXTGRP:';
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isExtByteRange(string $str): bool
    {
        return strtoupper(substr($str, 0, 17)) === '#EXT-X-BYTERANGE:';
    }

    /**
     * @param string $str
     * @param M3uEntry $entry
     */
    protected function parseExtInf(string $str, M3uEntry &$entry): void
    {
        $str = trim(substr($str, 8));
        $majorData = explode(',', $str, 2);

        if(isset($majorData[1])) {
            $this->parseName($majorData[1], $entry);

            $minorData = explode(' ', $majorData[0]);
            $minorData = array_filter($minorData, 'strlen');

            $entry->length = $minorData[0];
            $l = count($minorData);
            if($l > 1) {
                for($i = 1; $i < $l; ++$i) {
                    $pv = explode('=', $minorData[$i], 2);

                    if(isset($pv[1])) {
                        $val = trim($pv[1]);
                        $pvl = strlen($pv[1]);

                        if($pv[1][0] == '"' && $pv[1][$pvl - 1] == '"') {
                            $val = stripslashes(substr($pv[1], 1, $pvl - 2));
                        }

                        switch(trim($pv[0])) {
                            case 'group-title':
                                $entry->group = $val;
                                break;
                            case 'tvg-name':
                                $entry->name = $val;
                                break;
                            case 'tvg-logo':
                                $entry->logo = $val;
                                break;
                        }

                    }
                }
            }
        } else {
            $entry->name = $str;
        }
    }

    /**
     * @param string $str
     * @param M3uEntry $entry
     */
    protected function parseExtGrp(string $str, M3uEntry &$entry): void
    {
        $str = trim(substr($str, 8));
        if(!empty($str)) {
            $entry->group = $str;
        }
    }

    /**
     * @param string $str
     * @param M3uEntry $entry
     */
    protected function parseExtByteRange(string $str, M3uEntry &$entry): void
    {
        $str = trim(substr($str, 17));
        if (!empty($str)) {
            $entry->byteRange = $str;
        }
    }

    /**
     * @param string $str
     * @param M3uEntry $entry
     */
    protected function parseName(string $str, M3uEntry &$entry): void
    {
        $data = explode(' - ', $str, 2);
        if(isset($data[1])) {
            $entry->artist = trim($data[0]);
            $entry->name = trim($data[1]);
        } else {
            $entry->name = $str;
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param bool $useExtFormat
     * @return string
     */
    public function toString(bool $useExtFormat = true): string
    {
        $str = '#EXTM3U' . PHP_EOL;
        foreach ($this->entries as &$entry) {
            $str .= $entry->toString($useExtFormat);
        }
        $str .= '#EXT-X-ENDLIST';
        return $str;
    }
}