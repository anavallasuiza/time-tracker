<?php
namespace App\Libs;

use Input, Request, Response, Session;

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
        $inputs = Input::all();

        foreach ($fake as $name) {
            if (!array_key_exists($name, $inputs) || $inputs[$name]) {
                return true;
            }
        }

        foreach ($data as $value) {
            if (is_array($value) && self::checkTags($value, $fake)) {
                return true;
            } elseif (is_string($value) && strstr($value, '<')) {
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
            $seconds += ($row->total_time * 60);
        }

        return sprintf('%02d:%02d', floor($seconds / 3600), ($seconds / 60) % 60);
    }

    public static function minutes2hour($minutes)
    {
        return sprintf('%01d:%02d', floor($minutes / 60), ($minutes % 60));
    }

    public static function startEndTime($start, $end, $total)
    {
        $user = Auth::user();

        if (empty($user->store_hours)) {
            list($start) = explode(' ', $start);
            list($end) = explode(' ', $end);
        }

        if (!($start = self::checkDate($start,  $user->dateFormat))) {
            throw new \Exception(sprintf(_('"%s" has not a valid date time format (%s)'), _('start_time'), $user->dateFormat));
        }

        if (!($end = self::checkDate($end, $user->dateFormat))) {
            throw new \Exception(sprintf(_('"%s" has not a valid date time format (%s)'), _('end_time'), $user->dateFormat));
        }

        if ($user->store_hours) {
            $total = (int)round(($end->getTimestamp() - $start->getTimestamp()) / 60);
        } else {
            $start->setTime(0, 0, 0);
            $end->setTime(0, 0, 0);

            list($hours, $minutes) = explode(':', $total);
            $total = ($hours * 60) + $minutes;
        }

        if ((int)$total <= 0) {
            throw new \Exception(_('Total time has not a valid value'));
        }

        return [$start, $end, $total];
    }

    public static function url($key, $value)
    {
        parse_str(parse_url(getenv('REQUEST_URI'), PHP_URL_QUERY), $query);

        if ($value === null) {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }

        return '?'.http_build_query($query);
    }

    public static function object2array($object)
    {
        return json_decode(json_encode($object), true);
    }    

    public static function objectColumn($object, $column)
    {
        return array_column(self::object2array($object), $column);
    }
}
