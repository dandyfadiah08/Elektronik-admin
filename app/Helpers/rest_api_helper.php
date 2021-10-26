<<<<<<< HEAD
<?php

/*
@return object
examples
$response = initResponse();
$response = initResponse('Default value');
$response = initResponse('(Default is) Success', true);
$response = initResponse('(Default is) Success', true, ["data1" => "value"]);
*/
function initResponse($message = 'No message', $success = false, $data = []) {
    return (object)[
        'success'   => $success,
        'message'   => $message,
        'data'      => $data,
    ];
}
=======
<?php

/*
@return object
examples
$response = initResponse();
$response = initResponse('Default value');
$response = initResponse('(Default is) Success', true);
$response = initResponse('(Default is) Success', true, ["data1" => "value"]);
*/
function initResponse($message = 'No message', $success = false, $data = []) {
    return (object)[
        'success'   => $success,
        'message'   => $message,
        'data'      => $data,
    ];
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
