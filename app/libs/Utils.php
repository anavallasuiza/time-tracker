<?php
namespace App\Libs;

class Utils
{
    public static function checkDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return ($d && ($d->format($format) === $date)) ? $d : false;
    }

    public static function sumHours($list)
    {
        $seconds = 0;

        foreach ($list as $row) {
            $seconds += $row->end_time->getTimestamp() - $row->start_time->getTimestamp();
        }

        return sprintf('%02d:%02d', floor($seconds / 3600), ($seconds / 60) % 60);
    }

    public static function url($key, $value)
    {
        $url = getenv('REQUEST_URI');

        if ($value === null) {
            return preg_replace('#([\?&])'.preg_quote($key, '#').'=[^&]*#', '', $url);
        }

        $sep = strstr($url, '?') ? '&' : '?';
        $value = urlencode($value);

        if (preg_match('#[\?&]'.preg_quote($key, '#').'=[^&]*#', $url)) {
            return preg_replace('#([\?&])'.preg_quote($key, '#').'=[^&]*#', '$1'.$key.'='.$value, $url);
        } else {
            return $url.$sep.$key.'='.$value;
        }
    }
}
