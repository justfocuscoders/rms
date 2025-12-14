<?php

if (!function_exists('now_ist')) {
    function now_ist($format = 'Y-m-d H:i:s')
    {
        $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        return $dt->format($format);
    }
}
