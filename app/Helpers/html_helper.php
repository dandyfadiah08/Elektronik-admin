<?php

/**
 * make something like data-key="value"
  * @return $output string
*/
function htmlSetData($data) {
    $output = "";
    foreach($data as $key => $val) {
        $output .= "data-$key=\"$val\" ";
    }
    return $output;
}

/**
 * make button
  * @return $output string
*/
function htmlButton($data, $with_break = true) {
    $output = $with_break ? '<br>' : '';
    $d = (object)$data;
    $output .= '
    <button class="btn btn-xs mb-2 btn-'.($d->color ?? 'default').' '.($d->class ?? '').'" title="'.($d->title ?? '').'" id="'.($d->id ?? '').'" '.($d->data ?? '').'><i class="'.($d->icon ?? '').'"></i> '.($d->text ?? '').'</button>
    ';
    return $output;
}

/**
 * make anchor link like button
  * @return $output string
*/
function htmlAnchor($data, $with_break = true) {
    $output = $with_break ? '<br>' : '';
    $d = (object)$data;
    $output .= '
    <a href="'.($d->href ?? '#').'" class="btn btn-xs mb-2 btn-'.($d->color ?? 'default').' '.($d->class ?? '').'" title="'.($d->title ?? '').'" '.($d->data ?? '').'><i class="'.($d->icon ?? '').'"></i> '.($d->text ?? '').'</a>
    ';
    return $output;
}

/**
 * make anchor link with icon
  * @return $output string
*/
function htmlLink($data, $with_break = true) {
    $output = $with_break ? '<br>' : '';
    $d = (object)$data;
    $output .= '
    <a href="'.($d->href ?? '#').'" class="'.($d->class ?? '').'" title="'.($d->title ?? '').'" '.($d->data ?? '').'><i class="'.($d->icon ?? '').'"></i> '.($d->text ?? '').'</a>
    ';
    return $output;
}

/**
 * make input field 
  * @return $output string
*/
function htmlInput($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
    $output .= isset($d->label) ? '<label for="'.($d->id ?? '').'">'.$d->label.' <small class="invalid-errors"></small></label>' : '';
    $prepend = '';
    $append = '';
    $input_group_end = '';
    if(isset($d->prepend) || isset($d->append)) {
        $output .= '<div class="input-group mb-2">';
        $input_group_end = '</div>';
        $prepend .= isset($d->prepend) ? '<div class="input-group-prepend">
                <span class="'.($d->prepend_class ?? '').' input-group-text">'.$d->prepend.'</span>
            </div>' : '';
        $append .= isset($d->append) ? '<div class="input-group-append">
                <span class="'.($d->append_class ?? '').' input-group-text">'.$d->append.'</span>
            </div>' : '';
    }
    $output .= $prepend.'
    <input id="'.($d->id ?? '').'" name="'.($d->name ?? $d->id).'" type="'.($d->type ?? 'text').'" class="form-control '.($d->class ?? '').'" aria-label="'.($d->aria_label ?? '').'" placeholder="'.($d->placeholder ?? '').'" '.($d->attribute ?? '').' value="'.($d->value ?? '').'">
    '.$append.$input_group_end.$form_group_end;

    return $output;
}

/**
 * make input file
  * @return $output string
*/
function htmlInputFile($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
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
    $output .=  '<div class="custom-file">
    '.$prepend.'
    <input id="'.($d->id ?? '').'" name="'.($d->name ?? $d->id).'" type="file" '.($d->attribute ?? '').'>
    <label class="custom-file-label" for="'.($d->id ?? '').'">'.($d->placeholder ?? 'Choose file..').'</label>
    '.$append.$input_group_end.'</div>'.$form_group_end;

    return $output;
}

/**
 * make select and its options
  * @return $output string
*/
function htmlSelect($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
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
    <select id="'.($d->id ?? '').'" class="form-control '.($d->class ?? '').'" '.($d->attribute ?? '').'>
    '.($d->option ?? '').'
    </select>
    '.$append.$input_group_end.$form_group_end;

    return $output;
}

/**
 * make checkbox custom
  * @return $output string
*/
function htmlCheckbox($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
    // $output .= isset($d->label) ? '<label for="'.($d->id ?? '').'">'.$d->label.' <small class="invalid-errors"></small></label>' : '';
    $output .= '<div class="custom-control custom-checkbox">
    <input class="custom-control-input '.($d->class ?? '').'" type="checkbox" id="'.$d->id.'" '.(isset($d->checked) ? 'checked' : '').' '.($d->attribute ?? '').'>
    <label for="'.($d->id ?? '').'" class="form-check-label custom-control-label" title="'.($d->title ?? '').'">'.($d->label ?? '').'</label>
    </div>'.$form_group_end;

    return $output;
}

/**
 * make switch
  * @return $output string
*/
function htmlSwitch($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
    $output .= isset($d->label) ? '<label for="'.($d->id ?? '').'"
    '.(isset($d->title) ? 'title="'.$d->title.'"' : '').'"
    >'.$d->label.' <small class="invalid-errors"></small></label><br>' : '';
    $output .= '<input type="checkbox" id="'.$d->id.'" name="'.($d->name ?? $d->id).'" '.(isset($d->checked) ? 'checked' : '').(isset($d->class) ? ' class="'.$d->class.'"' : '').' 
    data-bootstrap-switch=""
    data-off-text="'.($d->off ?? 'OFF').'"
    data-on-text="'.($d->on ?? 'ON').'"
    data-off-color="'.($d->off_color ?? 'danger').'"
    data-on-color="'.($d->on_color ?? 'success').'"
    '.(isset($d->width) ? 'data-label-width="'.$d->width.'"' : '').'"
    >
    '.$form_group_end;

    return $output;
}

/**
 * make table row (< tr>)
  * @return $output string
*/
function htmlTr($data) {
    $d = (object)$data;
    $output = '<tr class="'.($d->class_tr ?? '').'">
        <td class="text-left">'.$d->text.'</td>
        <td> : </td>
        <td class="'.($d->class ?? 'font-weight-bold').'" id="'.($d->id ?? '').'">'.($d->text2 ?? '').'</td>
    </tr>';
    return $output;
}

/**
 * make editor wysiwyg (summernote)
  * @return $output string
*/
function htmlSummernote($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
    $output .= isset($d->label) ? '<label for="'.($d->id ?? '').'">'.$d->label.' <small class="invalid-errors"></small></label>' : '';
    $prepend = '';
    $append = '';
    $input_group_end = '';
    if(isset($d->prepend) || isset($d->append)) {
        $output .= '<div class="input-group mb-2">';
        $input_group_end = '</div>';
        $prepend .= isset($d->prepend) ? '<div class="input-group-prepend">
                <span class="'.($d->prepend_class ?? '').' input-group-text">'.$d->prepend.'</span>
            </div>' : '';
        $append .= isset($d->append) ? '<div class="input-group-append">
                <span class="'.($d->append_class ?? '').' input-group-text">'.$d->append.'</span>
            </div>' : '';
    }
    $output .= $prepend.'
    <div id="'.($d->id ?? '').'" name="'.($d->name ?? $d->id).'" type="'.($d->type ?? 'text').'" class="form-control '.($d->class ?? '').'" aria-label="'.($d->aria_label ?? '').'" placeholder="'.($d->placeholder ?? '').'" '.($d->attribute ?? '').'">'.($d->value ?? '').'
    '.$append.$input_group_end.$form_group_end;

    return $output;
}

/**
 * make text area input
  * @return $output string
*/
function htmlInputTextArea($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.'">';
        $form_group_end = '</div>';
    }
    $output .= isset($d->label) ? '<label for="'.($d->id ?? '').'">'.$d->label.' <small class="invalid-errors"></small></label>' : '';
    $prepend = '';
    $append = '';
    $input_group_end = '';
    if(isset($d->prepend) || isset($d->append)) {
        $output .= '<div class="input-group mb-2">';
        $input_group_end = '</div>';
        $prepend .= isset($d->prepend) ? '<div class="input-group-prepend">
                <span class="'.($d->prepend_class ?? '').' input-group-text">'.$d->prepend.'</span>
            </div>' : '';
        $append .= isset($d->append) ? '<div class="input-group-append">
                <span class="'.($d->append_class ?? '').' input-group-text">'.$d->append.'</span>
            </div>' : '';
    }
    $output .= $prepend.'
    <textarea id="'.($d->id ?? '').'" name="'.($d->name ?? $d->id).'" class="form-control '.($d->class ?? '').'" aria-label="'.($d->aria_label ?? '').'" placeholder="'.($d->placeholder ?? '').'" '.($d->attribute ?? '').' > ' . ($d->value ?? "")  . " </textarea>"
    .$append.$input_group_end.$form_group_end;

    return $output;
}

/**
 * make checkbox custom 2 (icheck)
  * @return $output string
*/
function htmlIcheckbox($data) {
    $d = (object)$data;
    $output = '';
    $form_group_end = '';
    if(isset($d->form_group)) {
        $output .= '<div class="form-group '.$d->form_group.' clearfix" style="place-self: center">';
        $form_group_end = '</div>';
    }
    $output .= '<div class="icheck-'.($d->color ?? 'primary').' d-inline">
    <input type="checkbox" class="'.($d->class ?? '').'" id="'.$d->id.'" '.(isset($d->checked) ? 'checked' : '').' '.($d->attribute ?? '').'>
    <label for="filter-submission" title="'.($d->title ?? '').'">'.($d->label ?? '').'</label>
    </div>'.$form_group_end;

    return $output;
}

/**
 * make <div></div> with 1 row and 2 cols class
  * @return $output string
*/
function renderSummary($title, $value, $col = [], $dots = ': ')
{
  $col1 = 4;
  $col2 = 8;
  if (count($col) == 2) {
    $col1 = $col[0];
    $col2 = $col[1];
  }
  return '<div class="row">
    <div class="col-' . $col1 . '">' . $title . '</div>
    <div class="col-' . $col2 . '">' . $dots . $value . '</div>
  </div>';
}
