<?php

if (!function_exists('base32_decode'))
{
    function base32_decode($s)
    {
        static $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        $tmp = '';

        foreach (str_split($s) as $c) {
            if (false === ($v = strpos($alphabet, $c))) {
                $v = 0;
            }
            $tmp .= sprintf('%05b', $v);
        }
        $args = array_map('bindec', str_split($tmp, 8));
        array_unshift($args, 'C*');

        return rtrim(call_user_func_array('pack', $args), "\0");
    }
}