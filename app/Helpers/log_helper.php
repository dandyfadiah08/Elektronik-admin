<<<<<<< HEAD
<?php

/*
@return void
examples
initResponse();
$response = initResponse('Default value');
$response = initResponse('(Default is) Success', true);
$response = initResponse('(Default is) Success', true, ["data1" => "value"]);
*/

use Config\Paths;

function writeLog($name, $content)
{
    $path = date('Y/m');
    $path = ROOTPATH."/writable/logs/$path/";
    if (!file_exists($path)) mkdir($path, 0777, true);

    $string = "[" . date('H:i:s') . "] $content\n\n";

    $handle = fopen($path.date('d')."-$name-log.txt", 'a');
    fwrite($handle, $string);
    fclose($handle);
=======
<?php

/*
@return void
examples
initResponse();
$response = initResponse('Default value');
$response = initResponse('(Default is) Success', true);
$response = initResponse('(Default is) Success', true, ["data1" => "value"]);
*/

use Config\Paths;

function writeLog($name, $content)
{
    $path = date('Y/m');
    $path = ROOTPATH."/writable/logs/$path/";
    if (!file_exists($path)) mkdir($path, 0777, true);

    $string = "[" . date('H:i:s') . "] $content\n\n";

    $handle = fopen($path.date('d')."-$name-log.txt", 'a');
    fwrite($handle, $string);
    fclose($handle);
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
}