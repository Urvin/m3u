<?php

namespace urvin\m3u;

/**
 * Class M3uEntry
 * @package urvin\m3u
 */
class M3uEntry {

    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $artist;
    /**
     * @var string
     */
    public $length;
    /**
     * @var string
     */
    public $group;
    /**
     * @var string
     */
    public $logo;
    /**
     * @var string
     */
    public $byteRange;

    /**
     * M3uEntry constructor.
     * @param array $initData Inital data
     */
    public function __construct(array $initData = [])
    {
        foreach(get_object_vars($this) as $name => $value) {
            if(isset($initData[$name])) {
                $this->{$name} = $initData[$name];
            }
        }
    }

    /**
     * @param bool $useExtFormat
     * @return string
     */
    protected function formatExtInf(bool $useExtFormat): string
    {
        $minor = [];
        $major = [];

        if(!is_null($this->length)) {
            $minor[] = $this->length;
        }

        if(!empty($this->group) || !empty($this->logo) || !empty($this->name)) {
            if(is_null($this->length)) {
                $minor[] = 0;
            }
            if(!empty($this->group)) {
                $minor[] = 'group-title="' . addslashes($this->group) . '"';
            }
            if(!empty($this->name)) {
                $minor[] = 'tvg-name="' . addslashes($this->name) . '"';
            }
            if(!empty($this->logo)) {
                $minor[] = 'tvg-logo="' . addslashes($this->logo) . '"';
            }
        }

        if(!empty($this->artist)) {
            $major[] = $this->artist;
        }
        if(!empty($this->name)) {
            $major[] = $this->name;
        }

        if(empty($minor) && empty($major)) {
            return '';
        }
        if(empty($minor)) {
            $minor[] = 0;
        }

        $str = '#EXTINF:' . join(' ', $minor) . ',' . join(' - ', $major) . PHP_EOL;
        if($useExtFormat && !empty($this->group)) {
            $str .= '#EXTGRP:' . $this->group . PHP_EOL;
        }

        if (!empty($this->byteRange)) {
            $str .= '#EXT-X-BYTERANGE:' . $this->byteRange . PHP_EOL;
        }

        return $str;
    }


    /**
     * @param bool $useExtFormat
     * @return string
     */
    public function toString(bool $useExtFormat = true): string
    {
        if(empty($this->path)) {
            return '';
        }

        return $this->formatExtInf($useExtFormat) . $this->path . PHP_EOL;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}