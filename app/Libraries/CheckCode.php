<?php

namespace App\Libraries;

class CheckCode
{

    public function __construct() { }

    /*
    @param $key string
    @return $code string
    */
    public function make($id, $key = '')
    {
        $code = date('y');
        $code .= $this->addPrefixZero($id);
        $code .= $key;
        return $code;
    }

    /*
    @return $key string
    */
    public function makeKey()
    {
        $key = $this->generateRandomAlphabet();
        return $key;
    }

    /*
    @param $length integer
    @return $random string
    */
    function generateRandomAlphabet($length = 1)
    {
        $pool = 'ABCDEFHJKLMNPKRSTUVWXYZ';
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
        }
        return $random;
    }

    /*
    @param $number integer
    @param $length integer
    @return $number_with_zero_prefix string
    */
    function addPrefixZero($number, $length = 8)
    {
        $innitial_zeros = '0000000000';
        $zeros = substr($innitial_zeros, 0, $length);
        $number_length = strlen($number);
        $number_with_zero_prefix = substr($zeros, $number_length).$number;
        return $number_with_zero_prefix;
    }
}
