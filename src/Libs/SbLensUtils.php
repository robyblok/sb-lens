<?php

namespace StoryblokLens\Libs;

class SbLensUtils
{
    public static function formatBytes(string|null $bytes, $precision = 2): string
    {
        if (is_null($bytes)) {
            $bytes = 0;
        }

        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;
        if ($bytes < $kilobyte) {
            return $bytes . ' B';
        }

        if ($bytes < $megabyte) {
            return number_format($bytes / $kilobyte, $precision) . ' KB';
        }

        if ($bytes < $gigabyte) {
            return number_format($bytes / $megabyte, $precision) . ' MB';
        }

        if ($bytes < $terabyte) {
            return number_format($bytes / $gigabyte, $precision) . ' GB';
        }

        return number_format($bytes / $terabyte, $precision) . ' TB';
    }

}
