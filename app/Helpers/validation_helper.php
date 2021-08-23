<?php

/*
@return array
*/
function getValidationRules($rule)
{
    $rules['name'] = [
        'label'     => 'Name',
        'rules'     => 'required|min_length[3]|max_length[100]|alpha_space',
        'errors'    => [
            'required'      => '{field} is required.',
            'min_length'    => 'Minimum {field} length is {param} characters.',
            'max_length'    => 'Maximum {field} length is {param} characters.',
            'alpha_space'   => '{field} is invalid, should be alphabet and space only.',
        ]
    ];
    $rules['email'] = [
        'label'     => 'Email',
        'rules'     => 'required|valid_email|min_length[6]|max_length[100]',
        'errors'    => [
            'required'      => '{field} is required.',
            'min_length' => 'Minimum {field} length is {param} characters.',
            'max_length' => 'Maximum {field} length is {param} characters.',
            'valid_email'   => '{field} is in invalid format.',
        ]
    ];
    $rules['phone'] = [
        'label'     => 'Phone',
        'rules'     => 'required|min_length[10]|max_length[14]|numeric|regex_match[/([\b6\b]+[\b2\b]+[\b8\b][0-9]+)/]',
        'errors'    => [
            'required'      => '{field} is required.',
            'min_length'    => 'Minimum {field} length is {param} numbers.',
            'max_length'    => 'Maximum {field} length is {param} numbers.',
            'numeric'       => '{field} is invalid, must be numbers.',
            'regex_match'   => '{field} is invalid, must start with 628.',
        ]
    ];
    $rules['type_user'] = [
        'label'     => 'Type User',
        'rules'     => 'required|in_list[1,2]',
        'errors'    => [
            'required'  => '{field} is required.',
            'in_list'   => '{field} is invalid (must be one of: {param}).',
        ]
    ];
    $rules['nik'] = [
        'label'     => 'NIK',
        'rules'     => 'required|min_length[16]|max_length[16]|numeric',
        'errors'    => [
            'required'      => '{field} is required.',
            'min_length'    => '{field} length should be {param} numbers.',
            'max_length'    => '{field} length should be {param} numbers.',
            'numeric'       => '{field} is invalid, must be numbers.',
        ],
    ];
    $rules['photo_id'] = [
        'label'     => 'Photo ID',
        'rules'     => 'uploaded[photo_id]|max_size[photo_id,2048]|mime_in[photo_id,image/png,image/jpg,image/jpeg]',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is 2048 kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['otp'] = [
        'label'     => 'OTP Code',
        'rules'     => 'required|numeric|min_length['.env('otp.length').']|max_length['.env('otp.length').']',
        'errors'    => [
            'required'      => '{field} is required.',
            'numeric'       => '{field} is invalid, must be numbers.',
            'min_length'    => '{field} length should be {param}.',
            'max_length'    => '{field} length should be {param}.',
        ]
    ];

    // menambahkan rules tambahan dari rules default
    $temp_rules['email'] = $rules['email'];
    $temp_rules['email']['rules'] .= '|is_unique[users.email,users.phone_no_verified,n]'; // is email unique, ignore phone_no_verified=n
    $temp_rules['email']['errors'] += ['is_unique' => '{field} has been used.'];
    $temp_rules['phone'] = $rules['phone'];
    $temp_rules['phone']['rules'] .= '|is_unique[users.phone_no,users.phone_no_verified,n]'; // is phone_no unique, phone_no_verified=n
    $temp_rules['phone']['errors'] += ['is_unique' => '{field} has been used.'];
    $temp_rules['nik'] = $rules['nik'];
    $temp_rules['nik']['rules'] .= '|is_unique[users.nik,users.phone_no_verified,n]'; // is nik unique, ignore phone_no_verified=n
    $temp_rules['nik']['errors'] += ['is_unique' => '{field} has been used.'];
    $rules['register'] = [
        'name'  => $rules['name'],
        'email' => $temp_rules['email'],
        'phone' => $temp_rules['phone'],
        'type'  => $rules['type_user'],
    ];

    $rules['register_agent'] = [
        'nik'       => $temp_rules['nik'],
        'photo_id'  => $rules['photo_id'],
    ];

    $rules['verify_phone'] = [
        'phone' => $rules['phone'],
        'otp'   => $rules['otp'],
    ];

    // device check rules
    $rules['notification_token'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['check_id'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['check_code'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['brand'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['model'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['storage'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['fcm_token'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['os'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['price_id'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['simcard'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['cpu'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['harddisk'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['battery'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['root'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['button_back'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['button_volume'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['button_power'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['camera_back'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['camera_front'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['screen'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];

    $rules['app_1:get_price'] = [
        'brand'     => $rules['brand'],
        'model'     => $rules['model'],
        'storage'   => $rules['storage'],
    ];

    $rules['app_1:software_check'] = [
        'fcm_token'     => $rules['fcm_token'],
        'os'            => $rules['os'],
        'price_id'      => $rules['price_id'],
        'simcard'       => $rules['simcard'],
        'cpu'           => $rules['cpu'],
        'harddisk'      => $rules['harddisk'],
        'battery'       => $rules['battery'],
        'root'          => $rules['root'],
        'button_back'   => $rules['button_back'],
        'button_volume' => $rules['button_volume'],
        'button_power'  => $rules['button_power'],
        'camera_back'   => $rules['camera_back'],
        'camera_front'  => $rules['camera_front'],
        'screen'        => $rules['screen'],
    ];
    
    if(isset($rules[$rule])) return $rules[$rule];
    else return $rules;
}
