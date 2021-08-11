<?php

/*
@param array $prefix = string prefix, int length
@return string
examples
echo generateReferralCode(["fajar",4]); // output: FAJA1234
echo generateReferralCode(["fajar",4], 10); // output: FAJA123456
echo generateReferralCode(["Fajar",2], 6); // output: FA1234
note: 1234.. is random
*/
function generateReferralCode($prefix = [], $length = 8)
{
    $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $code = '';
    if (count($prefix) == 2) {
        $code = strtoupper($prefix[0]);
        $code = preg_replace('/[^A-Z]/i', '', $code);
        $prefix_length = is_numeric($prefix[1]) ? $prefix[1] : 1; // check $prefix[1] is number ?
        $prefix_length = strlen($code) < $prefix_length ? strlen($code) : $prefix_length; // check $prefix[1] is greater than $code length
        $code = substr($code, 0, $prefix_length);
        $length -= $prefix_length - 1;
    }
    $pool_length = strlen($pool) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= substr($pool, mt_rand(0, $pool_length), 1);
    }
    return $code;
}
