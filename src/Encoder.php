<?php

namespace com\funto\Converter;

final class Encoder
{
    public static function toJson(array $arr, $options = null)
    {
        if ($options === 'default') {
            return json_encode($arr);
        }
        if ($options === null) {
            $options = JSON_UNESCAPED_UNICODE;
        }
        return json_encode($arr, $options);
    }
}
