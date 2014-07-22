<?php
namespace App\Libs;

use Request, Response, Session;

class Utils
{
    public static function setMessage($data, $code = 200)
    {
        if (Request::ajax()) {
            return Response::make($data['message'], $code);
        } elseif (Request::isJson()) {
            return Response::json(array(
                'code' =>  $code,
                'message' => $data['message']
            ), $code);
        }

        Session::flash('flash-message', [
            'message' => $data['message'],
            'status' => $data['status']
        ]);
    }

    public static function isBot(array $data = [])
    {
        $bots = [
            'ask jeeves','baiduspider','butterfly','fast','feedfetcher-google','firefly','gigabot',
            'googlebot','infoseek','me.dium','mediapartners-google','nationaldirectory','rankivabot',
            'scooter','slurp','sogou web spider','spade','tecnoseek','technoratisnoop','teoma',
            'tweetmemebot','twiceler','twitturls','url_spider_sql','webalta crawler','webbug',
            'webfindbot','zyborg','alexa','appie','crawler','froogle','girafabot','inktomi',
            'looksmart','msnbot','rabaz','www.galaxy.com','rogerbot'
        ];

        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        foreach ($bots as $bot) {
            if (strstr($agent, $bot) !== false) {
                return true;
            }
        }

        return $data ? self::checkTags($data) : false;
    }

    public static function checkTags(array $data, array $fake = [])
    {
        foreach ($fake as $name) {
            if (!array_key_exists($name, $data) || $data[$name]) {
                return true;
            }
        }

        foreach ($data as $value) {
            if (strstr($value, '<')) {
                return true;
            }
        }

        return false;
    }

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

    public static function minutes2hour($minutes)
    {
        return floor($minutes / 60).':'.($minutes % 60);
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

    public static function objectColumn($object, $column)
    {
        return array_column(json_decode(json_encode($object), true), $column);
    }
}
