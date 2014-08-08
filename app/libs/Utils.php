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
        return round($minutes / 60);
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

    public static function progressText($row)
    {
        $span = '<span data-toggle="tooltip" data-placement="top" title="%title">%text</span>';

        $time = self::minutes2hour($row['time']);

        $html = str_replace(['%title', '%text'], [_('Worked time'), $time.'h'], $span);
        $html .= ' - ';
        $html .= str_replace(['%title', '%text'], [_('Percent over all activities'), $row['percent'].'%'], $span);

        if (empty($row['total_hours'])) {
            return $html;
        }

        $label = '<span class="label label-%label" data-toggle="tooltip" data-placement="top" title="%title">%text</span>';

        $hours = round($row['time'] / 60);
        $percent = round(($hours * 100) / $row['total_hours']);
        $diff = round($row['total_hours'] - $hours);
        $class = ($diff > 10) ? 'success' : (($diff > 0) ? 'warning' : 'danger');

        $html .= ' / ';
        $html .= str_replace(['%title', '%text'], [_('Estimated time'), $row['total_hours'].'h'], $span);
        $html .= ' - ';
        $html .= str_replace(['%title', '%text'], [_('Percent worked'), $percent.'%'], $span);
        $html .= ' ';
        $html .= str_replace(['%title', '%text', '%label'], [_('Hours to complete estimation'), round($diff).'h', $class], $label);

        return $html;
    }

    public static function progressBar($row)
    {
        $bar = '<div class="progress-bar progress-bar-%class" role="progressbar" aria-valuenow="%percent" aria-valuemin="0" aria-valuemax="100" style="width: %percent%;"></div>';

        if (empty($row['percent_hours'])) {
            return str_replace('%percent', $row['percent'], $bar);
        }

        if ($row['percent_hours'] > 100) {
            $row['percent_hours'] = 100;
        }

        $diff = abs($row['percent'] - $row['percent_hours']);

        if ($row['percent_hours'] < $row['percent']) {
            return str_replace('%percent', $row['percent_hours'], $bar)
                .str_replace(['%percent', '%class'], [$diff, 'danger'], $bar);
        }

        $class = ($diff > 20) ? 'success' : 'warning';

        return str_replace('%percent', $row['percent'], $bar)
            .str_replace(['%percent', '%class'], [$diff, $class], $bar);
    }
}
