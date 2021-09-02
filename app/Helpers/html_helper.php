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
function htmlCreateButton($data, $with_break = true) {
    $output = $with_break ? '<br>' : '';
    $output .= '
    <button class="btn btn-xs mb-2 btn-'.$data['color'].' '.$data['class'].'" title="'.$data['title'].'" '.$data['data'].'><i class="'.$data['icon'].'"></i> '.$data['text'].'</button>
    ';
    return $output;
}

/*
@return $output string
*/
function htmlCreateAnchor($data, $with_break = true) {
    $output = $with_break ? '<br>' : '';
    $output .= '
    <a href="'.$data['href'].'" class="btn btn-xs mb-2 btn-'.$data['color'].' '.$data['class'].'" title="'.$data['title'].'" '.$data['data'].'><i class="'.$data['icon'].'"></i> '.$data['text'].'</button>
    ';
    return $output;
}
