<?php

/*
@return $output string
*/
function htmlSetData($data) {
    $output = "";
    foreach($data as $key => $val) {
        $output .= "data-$key=\"$val\" ";
    }
    return $output;
}

/*
@return $output string
*/
function htmlCreateButton($data) {
    $output = '
    <br><button class="btn btn-xs mb-2 py-2 btn-'.$data['color'].' btnAction '.$data['class'].'" title="'.$data['title'].'" '.$data['data'].'><i class="'.$data['icon'].'"></i> '.$data['text'].'</button>
    ';
    return $output;
}
