<?php

namespace App\Library;

class StringHelper
{
    public static function toEnglish(string $string): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = range(0, 9);

        return str_replace($arabic, $english, str_replace($persian, $english, $string));
    }

    public static function toPersian(string $string): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = range(0, 9);

        return str_replace($english, $persian, $string);
    }

    public static function maskString(string $string, ?int $start = 0, ?int $length = null): string
    {
        return substr_replace(
            $string,
            str_repeat('*', $length ?? strlen($string) - $start),
            $start,
            $length ?? strlen($string) - $start
        );
    }

    public static function checkCardNumber(mixed $value): bool
    {
        if (!preg_match('/^\d{16}$/', $value)) {
            return false;
        }

        $sum = 0;

        for ($position = 1; $position <= 16; $position++) {
            $temp = $value[$position - 1];
            $temp = $position % 2 === 0 ? $temp : $temp * 2;
            $temp = $temp > 9 ? $temp - 9 : $temp;

            $sum += $temp;
        }

        return ($sum % 10 === 0);
    }
}