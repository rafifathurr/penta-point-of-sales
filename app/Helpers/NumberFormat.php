<?php

namespace App\Helpers;

class NumberFormat
{
    public static function formatCurrency(int $number)
    {
       return number_format($number,0,',','.');
    }
}