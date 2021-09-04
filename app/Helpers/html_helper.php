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
    $d = (object)$data;
    $output .= '
    <button class="btn btn-xs mb-2 btn-'.($d->color ?? 'default').' '.($d->class ?? '').'" title="'.($d->title ?? '').'" '.($d->data ?? '').'><i class="'.($d->icon ?? '').'"></i> '.($d->text ?? '').'</button>
    ';
    return $output;
}

/*
@return $output string
*/
function htmlCreateAnchor($data, $with_break = true) {
    $output = $with_break ? '<br>' : '';
    $d = (object)$data;
    $output .= '
    <a href="'.($d->href ?? '#').'" class="btn btn-xs mb-2 btn-'.($d->color ?? 'default').' '.($d->class ?? '').'" title="'.($d->title ?? '').'" '.($d->data ?? '').'><i class="'.($d->icon ?? '').'"></i> '.($d->text ?? '').'</button>
    ';
    return $output;
}

/*
@return $output string
*/
function htmlInput($data) {
    $d = (object)$data;
    $output = '';
    $output .= isset($d->label) ? '<label for="'.($d->id ?? '').'">'.$d->label.' <small class="invalid-errors"></small></label>' : '';
    $prepend = '';
    $append = '';
    $input_group_end = '';
    if(isset($d->prepend) || isset($d->append)) {
        $output .= '<div class="input-group mb-2">';
        $input_group_end = '</div>';
        $prepend .= isset($d->prepend) ? '<div class="input-group-prepend">
                <span class="input-group-text">'.$d->prepend.'</span>
            </div>' : '';
        $append .= isset($d->append) ? '<div class="input-group-append">
                <span class="input-group-text">'.$d->append.'</span>
            </div>' : '';
    }
    $output .= $prepend.'
    <input id="'.($d->id ?? '').'" type="'.($d->type ?? 'text').'" class="form-control '.($d->class ?? '').'" aria-label="'.($d->aria_label ?? '').'" placeholder="'.($d->placeholder ?? '').'" '.($d->attribute ?? '').'>
    '.$append.$input_group_end;

    return $output;
}
