<?php

namespace urvin\m3u;

use urvin\m3u\M3uException;

/**
 * Class M3uEntry
 * @package urvin\m3u
 */
class M3uEntry {

    const M3U_FORMAT_MINOR_DATA = 1;
    const M3U_FORMAT_EXT_DATA = 2;

    public $path;
    public $name;
    public $artist;
    public $length;
    public $group;
    public $logo;

    /**
     * M3uEntry constructor.
     * @param array $initData Inital data
     */
    public function __construct($initData = [])
    {
        foreach(get_object_vars($this) as $name => $value) {
            if(isset($initData[$name])) {
                $this->{$name} = $initData[$name];
            }
        }
    }

    /**
     * @param int $format
     * @return string
     */
    protected function formatExtInf($format)
    {
        $minor = [];
        $major = [];

        if(!is_null($this->length)) {
            $minor[] = $this->length;
        }
        if($format == self::M3U_FORMAT_MINOR_DATA) {
            if(!empty($this->group) || !empty($this->logo)) {
                if(is_null($this->length)) {
                    $minor[] = 0;
                }
                if(!empty($this->group)) {
                    $minor[] = 'group-title="' . addslashes($this->group) . '"';
                }
                if(!empty($this->logo)) {
                    $minor[] = 'tvg-logo="' . addslashes($this->logo) . '"';
                }
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
        if($format == self::M3U_FORMAT_EXT_DATA && !empty($this->group)) {
            $str .= '#EXTGRP:' . $this->group . PHP_EOL;
        }

        return $str;
    }


    /**
     * @param int $format
     * @return string
     */
    public function toString($format = self::M3U_FORMAT_MINOR_DATA) {
        if(empty($this->path)) {
            return '';
        }

        return $this->formatExtInf($format) . $this->path . PHP_EOL;
    }

    public function __toString()
    {
        return $this->toString();
    }
}