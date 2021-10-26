<<<<<<< HEAD
<?php

namespace App\Libraries;

use App\Models\DeviceChecks;
use Hidehalo\Nanoid\Client;

class CheckCode
{

    var $DeviceCheck;
    public function __construct() {
        $this->DeviceCheck = new DeviceChecks();
        $this->Client = new Client();
    }

    /*
    @param $code string or false
    @return $code string
    construct check_code and check if it exist in database (return false)
    */
    // public function make($id, $key = '')
    public function make()
    {
        $code = date('y');
        // $code .= $this->addPrefixZero($id);
        // $code .= $key;
        $code .= $this->Client->formattedId('0123456789ABCDEFGHJKLMNPQRTUVW', 8);
        $existing_code = $this->DeviceCheck->where('check_code', $code)->select('check_id')->first();
        if($existing_code) $code = false;
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
    random key with custom length
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
    transform 5 into 0000005
    */
    function addPrefixZero($number, $length = 7)
    {
        $innitial_zeros = '0000000000';
        $zeros = substr($innitial_zeros, 0, $length);
        $number_length = strlen($number);
        $number_with_zero_prefix = substr($zeros, $number_length).$number;
        return $number_with_zero_prefix;
    }
}
=======
<?php

namespace App\Libraries;

use App\Models\DeviceChecks;
use Hidehalo\Nanoid\Client;

class CheckCode
{

    var $DeviceCheck;
    public function __construct() {
        $this->DeviceCheck = new DeviceChecks();
        $this->Client = new Client();
    }

    /*
    @param $code string or false
    @return $code string
    construct check_code and check if it exist in database (return false)
    */
    // public function make($id, $key = '')
    public function make()
    {
        $code = date('y');
        // $code .= $this->addPrefixZero($id);
        // $code .= $key;
        $code .= $this->Client->formattedId('0123456789ABCDEFGHJKLMNPQRTUVW', 8);
        $existing_code = $this->DeviceCheck->where('check_code', $code)->select('check_id')->first();
        if($existing_code) $code = false;
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
    random key with custom length
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
    transform 5 into 0000005
    */
    function addPrefixZero($number, $length = 7)
    {
        $innitial_zeros = '0000000000';
        $zeros = substr($innitial_zeros, 0, $length);
        $number_length = strlen($number);
        $number_with_zero_prefix = substr($zeros, $number_length).$number;
        return $number_with_zero_prefix;
    }
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
