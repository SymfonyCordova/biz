<?php


namespace Zler\Biz\Common;


class StringToolkit
{
    public static function template($string, array $variables)
    {
        if (empty($variables)) {
            return $string;
        }

        $search = array_keys($variables);
        array_walk($search, function (&$item) {
            $item = '{{'.$item.'}}';
        });

        $replace = array_values($variables);

        return str_replace($search, $replace, $string);
    }

    public static function sign($data, $key)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }
        ksort($data);

        return md5(json_encode($data).$key);
    }

    public static function secondsToText($value)
    {
        $minutes = intval($value / 60);
        $seconds = $value - $minutes * 60;

        return sprintf('%02d', $minutes).':'.sprintf('%02d', $seconds);
    }

    public static function textToSeconds($text)
    {
        if (strpos($text, ':') === false) {
            return 0;
        }
        list($minutes, $seconds) = explode(':', $text, 2);

        return intval($minutes) * 60 + intval($seconds);
    }

    public static function plain($text, $length = 0)
    {
        $text = strip_tags($text);

        $text = str_replace(array("\n", "\r", "\t"), '', $text);
        $text = str_replace('&nbsp;', ' ', $text);
        $text = trim($text);

        $length = (int) $length;
        if (($length > 0) && (mb_strlen($text) > $length)) {
            $text = mb_substr($text, 0, $length, 'UTF-8');
            $text .= '...';
        }

        return $text;
    }

    public static function createRandomString($length)
    {
        $start = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = null;
        for ($i = 0; $i < $length; ++$i) {
            $rand = rand(0, 61);
            $code = $code.$start[$rand];
        }

        return $code;
    }

    public static function createRandomNumber($length)
    {
        $start = '0123456789012345678901234567890123456789';
        $code = null;
        for ($i = 0; $i < $length; ++$i) {
            $rand = rand(0, 39);
            $code = $code.$start[$rand];
        }

        return $code;
    }

    public static function jsonPettry($json)
    {
        $result = '';
        $level = 0;
        $in_quotes = false;
        $in_escape = false;
        $ends_line_level = null;
        $json_length = strlen($json);

        for ($i = 0; $i < $json_length; ++$i) {
            $char = $json[$i];
            $new_line_level = null;
            $post = '';
            if ($ends_line_level !== null) {
                $new_line_level = $ends_line_level;
                $ends_line_level = null;
            }
            if ($in_escape) {
                $in_escape = false;
            } elseif ($char === '"') {
                $in_quotes = !$in_quotes;
            } elseif (!$in_quotes) {
                switch ($char) {
                    case '}':
                    case ']':
                        $level--;
                        $ends_line_level = null;
                        $new_line_level = $level;
                        break;

                    case '{':
                    case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = ' ';
                        break;

                    case ' ':
                    case "\t":
                    case "\n":
                    case "\r":
                        $char = '';
                        $ends_line_level = $new_line_level;
                        $new_line_level = null;
                        break;
                }
            } elseif ($char === '\\') {
                $in_escape = true;
            }
            if ($new_line_level !== null) {
                $result .= "\n".str_repeat("\t", $new_line_level);
            }
            $result .= $char.$post;
        }

        return $result;
    }

    public static function cutter($name, $leastLength, $prefixLength, $suffixLength)
    {
        $afterCutName = $name;
        $length = mb_strlen($name, 'UTF-8');
        if ($length > $leastLength) {
            $afterCutName = mb_substr($name, 0, $prefixLength, 'utf-8').'…';
            $afterCutName .= mb_substr($name, $length - $suffixLength, $length, 'utf-8');
        }

        return $afterCutName;
    }

    public static function jsonEncode($data)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            $data = urlencode(json_encode($data));

            return urldecode($data);
        }
    }

    public static function printMem($bytes)
    {
        $format = function ($number) {
            return number_format($number, 2);
        };
        if ($bytes < 1024 * 1024 * 1024) {
            return call_user_func($format, $bytes / 1024 / 1024).'M';
        } else {
            return call_user_func($format, $bytes / 1024 / 1024 / 1024).'G';
        }
    }

    public static function ltrimString($string, $prefixString)
    {
        if (strpos($string, trim($prefixString)) === 0) {
            return substr($string, strlen($prefixString));
        }

        return $string;
    }

    public static function toCamelCase($str, $ucfirst = false)
    {
        $str = ucwords(str_replace('_', ' ', $str));
        $str = str_replace(' ', '', lcfirst($str));

        return $ucfirst ? ucfirst($str) : $str;
    }

    public static function toUnderScore($str)
    {
        $arr = preg_split('/(?=[A-Z])/', $str);

        return strtolower(trim(implode('_', $arr), '_'));
    }

    public static function isUrlencodeStringUtf8OrGbk($str)
    {
        $url = urldecode($str);

        @trigger_error('error', E_USER_NOTICE);

        @iconv("GBK","UTF-8",$url);

        $error = error_get_last();

        return $error['message'] != 'error' ? 'utf8' : 'gbk';
    }

    public static function filterDecimalAfterZero($stringNumber){
        return floatval($stringNumber + 0)."%";
    }

    // 截取字符串，在不超过最大字节数前提下确保中文字符不被截断出现乱码
    public static function subString($strVal, $maxBytes)
    {
        $i = mb_strlen($strVal);
        while (strlen($strVal) > $maxBytes) {
            $i--;
            if ($i <= 0) {
                return '';
            }
            $strVal = mb_substr($strVal, 0, $i);
        }
        return $strVal;
    }

    public static function makeSign(array $array, $key = "", $signType = "MD5")
    {
        $sign = "";

        if(empty($array)){
            return $sign;
        }

        ksort($array);

        $temp = array();
        foreach ($array as $k => $value){
            $temp[] = $k.'='.$value;
        }

        if($key){
            $sign = implode("&", $temp)."&key=".$key;
        }else{
            $sign = implode("&", $temp);
        }

        if($signType == "MD5"){
            $sign = md5($sign);
        }

        return strtoupper($sign);
    }
}
