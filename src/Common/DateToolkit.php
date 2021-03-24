<?php


namespace Zler\Biz\Common;


class DateToolkit
{
    public static function changeTimeType($seconds)
    {
        if ($seconds > 3600){
            $hours = intval($seconds/3600);
            $time = $hours."小时".gmstrftime('%M分钟%S秒', $seconds);
        }else{
            $time = gmstrftime('%M分钟%S秒', $seconds);
        }
        return $time;
    }

    public static function dateAddFromat($timestamps, $num = 1, $tag = 'month')
    {
        return strtotime("+{$num} {$tag}", $timestamps);
    }

    public static function dateSubFromat($timestamps, $num = 1, $tag = 'month')
    {
        return strtotime("-{$num} {$tag}", $timestamps);
    }

    public static function getZeroClockTimes($timestamp)
    {
        $zeroStr = date('Y-m-d', $timestamp);
        return strtotime($zeroStr);
    }

    /**
     * 用开始时间和结束时间计算中间的每一天的日期
     * @param $beginTime
     * @param $endTime
     * @return array
     */
    public static function createEachDays($beginTime, $endTime)
    {
        $arr = [];

        $beginTime = self::getZeroClockTimes($beginTime);
        $endTime   = self::getZeroClockTimes($endTime);

        while ($beginTime <= $endTime){
            $arr[] = date('Ymd',$beginTime);
            $beginTime = strtotime('+1 day',$beginTime);
        }

        return $arr;
    }

    public static function periodTimes($timestamps, $day){
        $d = date('d', $timestamps);
        $temp = strtotime(date("Y-m", $timestamps));
        if($d < $day){
            $day-=1;
            $end = strtotime("+{$day} day", $temp);
        } else {
            $day-=1;
            $end = strtotime("+1 month +{$day} day", $temp);
        }
        $start = strtotime("-1 month", $end);
        return  array(
            'start' => $start,
            'end' => $end,
        );
    }

    public static function getEveryPeriodTimes($timestamp, $day, $period)
    {
        $timestampDay = date('d', $timestamp);

        if($timestampDay < $day){
            $diffDay = $day - $timestampDay;
            $firstTh = strtotime("+${diffDay} day", strtotime(date('Y-m-d', $timestamp)));
        }else {
            $day -= 1;
            $firstTh = strtotime("+1 month +${day} day", strtotime(date('Y-m', $timestamp)));
        }

        $periodNth[] = $firstTh;
        for($i = 1; $i<$period; ++$i) {
            $tmp = strtotime("+${i} month", $firstTh);
            $periodNth[] = $tmp;
        }
        return $periodNth;
    }

    public static function getDiffTwoTimeMonth($first, $second)
    {
        $first = explode('-', date("Y-m", $first));
        $second = explode('-', date("Y-m", $second));

        return ($second[0]*12 + $second[1]) - ($first[0]*12+$first[1]);
    }

    /**
     * @param $day1 |时间戳
     * @param $day2  |时间戳
     * @return float|int
     * @desc  两个时间天数
     */
    public static function getDiffBetweenTwoDays ($second1, $second2)
    {
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }


    /**
     * @param int $timeStamp  当前时间戳
     * @return false|float|int  开始计算预收手续费时间戳
     * @liuHui
     * @time  2019年8月8日16:42:14
     */
    public static function getPrepaidStartDay(int $timeStamp)
    {

        if(in_array(date('m',$timeStamp),[5,10])){
            $month10day1TimeStamp = strtotime(date('Y-10-1'));
            $month10day8TimeStamp = strtotime(date('Y-10-8'));
            $month5day1TimeStamp = strtotime(date('Y-5-1'));
            $month5day4TimeStamp = strtotime(date('Y-5-4'));
            if(($timeStamp>= $month5day1TimeStamp and $timeStamp < $month5day4TimeStamp) ){
                return $month5day4TimeStamp;
            }else if ($timeStamp>= $month10day1TimeStamp and $timeStamp < $month10day8TimeStamp){
                return $month10day8TimeStamp;
            }else if( date('w',$timeStamp) == 6){
                return $timeStamp + 60*60*24 *2;
            }else if ( date('w',$timeStamp) == 0){
                return $timeStamp + 60*60*24;
            }else if (date('H',$timeStamp)>=15 and date('w',$timeStamp) == 5){
                return $timeStamp + 60*60*24 *3 ;
            }else if(date('H',$timeStamp)>=15){
                return $timeStamp + 60*60*24  ;
            } else{
                return $timeStamp;
            }
        }else{
            if( date('w',$timeStamp) == 6){
                return $timeStamp + 60*60*24 *2;
            }else if ( date('w',$timeStamp) == 0){
                return $timeStamp + 60*60*24;
            }else if (date('H',$timeStamp)>=15 and date('w',$timeStamp) == 5){
                return $timeStamp + 60*60*24 *3 ;
            }else if(date('H',$timeStamp)>=15){
                return $timeStamp + 60*60*24  ;
            } else{
                return $timeStamp;
            }
        }

    }

    /**
     * @use        [计算两日期相差几天]
     * @author     小海不小
     * @param $start_date
     * @param $end_date
     * @return false|int
     * @throws \Exception
     */
    public static function datetimeDiff($start_date, $end_date)
    {
        $datetime_start = new \DateTime($start_date);
        $datetime_end = new \DateTime($end_date);

        $interval = $datetime_start->diff($datetime_end);

        return $interval->days;
    }

    public static function microTimestamp()
    {
        $datetime = date("Y-m-d H:i:s");
        $datetime = preg_replace('/\s|:|-/','',$datetime);
        $microtime = self::millisecond();
        $timestamp = $datetime.$microtime;
        return $timestamp;
    }

    public static function millisecond()
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec=round($usec*1000);
        if(strlen($msec) == 2) {
            $msec = '0'.$msec;
        } else if(strlen($msec) == 1) {
            $msec = '00'.$msec;
        } else if(strlen($msec) == 0){
            $msec = '000';
        }
        return $msec;
    }
}