<?php

/*
@return array
*/
function getValidationRules($rule)
{
    $default_photo_max_size = '2048'; // 2MB
    $default_photo_mime_type = 'image/png,image/jpg,image/jpeg'; // 2MB

    // users
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
        'rules'     => 'required|exact_length[16]|numeric',
        'errors'    => [
            'required'      => '{field} is required.',
            'exact_length'  => '{field} length should be {param} numbers.',
            'numeric'       => '{field} is invalid, must be numbers.',
        ],
    ];
    $rules['photo_id'] = [
        'label'     => 'Photo ID',
        'rules'     => 'uploaded[photo_id]|max_size[photo_id,' . $default_photo_max_size . ']|mime_in[photo_id,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['otp'] = [
        'label'     => 'OTP Code',
        'rules'     => 'required|numeric|exact_length[' . env('otp.length') . ']',
        'errors'    => [
            'required'      => '{field} is required.',
            'numeric'       => '{field} is invalid, must be numbers.',
            'exact_length'  => '{field} length should be {param}.',
        ]
    ];
    $rules['pin'] = [
        'label'     => 'PIN',
        'rules'     => 'required|numeric|exact_length[6]',
        'errors'    => [
            'required'      => '{field} is required.',
            'numeric'       => '{field} is invalid, must be numbers.',
            'exact_length'  => '{field} length should be {param}.',
        ]
    ];
    $rules['pin_confirm'] = [
        'label'     => 'PIN Confirmation',
        'rules'     => 'required|numeric|exact_length[6]|matches[pin]',
        'errors'    => [
            'required'      => '{field} is required.',
            'numeric'       => '{field} is invalid, must be numbers.',
            'exact_length'  => '{field} length should be {param}.',
            'matches'       => '{field} did not match with {param}.',
        ]
    ];

    // device_checks & device_check_details
    $rules['notification_token'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['grade'] = [
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
    $rules['imei'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['imei_registered'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['quiz_1'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['quiz_2'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['quiz_3'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['quiz_4'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['customer_name'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['photo_device_1'] = [
        'rules'     => 'uploaded[photo_device_1]|max_size[photo_device_1,' . $default_photo_max_size . ']|mime_in[photo_device_1,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_device_2'] = [
        'rules'     => 'uploaded[photo_device_2]|max_size[photo_device_2,' . $default_photo_max_size . ']|mime_in[photo_device_2,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_device_3'] = [
        'rules'     => 'uploaded[photo_device_3]|max_size[photo_device_3,' . $default_photo_max_size . ']|mime_in[photo_device_3,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_device_4'] = [
        'rules'     => 'uploaded[photo_device_4]|max_size[photo_device_4,' . $default_photo_max_size . ']|mime_in[photo_device_4,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_device_5'] = [
        'rules'     => 'uploaded[photo_device_5]|max_size[photo_device_5,' . $default_photo_max_size . ']|mime_in[photo_device_5,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_device_6'] = [
        'rules'     => 'uploaded[photo_device_6]|max_size[photo_device_6,' . $default_photo_max_size . ']|mime_in[photo_device_6,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_fullset'] = [
        'rules'     => 'uploaded[photo_fullset]|max_size[photo_fullset,' . $default_photo_max_size . ']|mime_in[photo_fullset,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_imei_registered'] = [
        'rules'     => 'uploaded[photo_imei_registered]|max_size[photo_imei_registered,' . $default_photo_max_size . ']|mime_in[photo_imei_registered,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['photo_id'] = [
        'rules'     => 'uploaded[photo_id]|max_size[photo_id,' . $default_photo_max_size . ']|mime_in[photo_id,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['transfer_proof'] = [
        'rules'     => 'uploaded[transfer_proof]|max_size[transfer_proof,' . $default_photo_max_size . ']|mime_in[transfer_proof,' . $default_photo_mime_type . ']',
        'errors'    => [
            'uploaded'  => '{field} can not uploaded.',
            'max_size'  => 'Maximum size of {field} is ' . $default_photo_max_size . ' kb.',
            'mime_in'   => '{field} must be in png/jpg/jpeg only.',
        ],
    ];
    $rules['token'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];

    // user_addresses
    $rules['district_id'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['postal_code'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['address_name'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];

    // user_payments
    $rules['payment_method_id'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['account_number'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['account_name'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];

    // user_payouts
    $rules['user_payment_id'] = [
        'rules'     => 'required',
        'errors'    => ['required' => '{field} is required.']
    ];
    $rules['amount'] = [
        'rules'     => 'required|numeric',
        'errors'    => [
            'required'  => '{field} is required.',
            'numeric'   => '{field} is invalid, must be numbers.',
        ]
    ];



    /* bellow is the composite validation */
    /* validasi gabungan dari masing-masing validasi per input */

    // menambahkan rules tambahan dari rules default
    // validasi register
    $temp_rules['email'] = $rules['email'];
    $temp_rules['email']['rules'] .= '|is_unique[users.email,users.phone_no_verified,n]'; // is email unique, ignore phone_no_verified=n
    $temp_rules['email']['errors'] += ['is_unique' => '{field} has been used.'];
    $temp_rules['phone'] = $rules['phone'];
    $temp_rules['phone']['rules'] .= '|is_unique[users.phone_no,users.phone_no_verified,n]'; // is phone_no unique, phone_no_verified=n
    $temp_rules['phone']['errors'] += ['is_unique' => '{field} has been used.'];
    $temp_rules['nik'] = $rules['nik'];
    $temp_rules['nik']['rules'] .= '|is_unique[users.nik,users.nik_verified,n]'; // is nik unique, ignore phone_no_verified=n
    $temp_rules['nik']['errors'] += ['is_unique' => '{field} has been used.'];
    $rules['register'] = [
        'name'  => $rules['name'],
        'email' => $temp_rules['email'],
        'phone' => $temp_rules['phone'],
        'type'  => $rules['type_user'],
    ];

    // validasi register agent
    $rules['register_agent'] = [
        'nik'       => $temp_rules['nik'],
        'photo_id'  => $rules['photo_id'],
    ];

    // validasi verify otp
    $rules['verify_phone'] = [
        'phone' => $rules['phone'],
        'otp'   => $rules['otp'],
    ];

    // validasi set pin
    $rules['set_pin'] = [
        'pin'           => $rules['pin'],
        'pin_confirm'   => $rules['pin_confirm'],
    ];

    // validasi update otp
    $temp_rules['current_pin'] = $rules['pin'];
    $temp_rules['new_pin'] = $rules['pin'];
    $temp_rules['new_pin_confirm'] = $rules['pin'];
    $temp_rules['current_pin']['label'] = 'Current PIN';
    $temp_rules['new_pin']['label'] = 'New PIN';
    $temp_rules['new_pin_confirm']['label'] = 'New PIN Confirmation';
    $temp_rules['new_pin_confirm']['rules'] .= '|matches[new_pin]';
    $temp_rules['new_pin_confirm']['errors'] += ['matches' => '{field} did not match with {param}.'];
    $rules['update_pin'] = [
        'current_pin'       => $temp_rules['current_pin'],
        'new_pin'           => $temp_rules['new_pin'],
        'new_pin_confirm'   => $temp_rules['new_pin_confirm'],
    ];

    // validasi get price
    $rules['app_1:get_price'] = [
        'brand'     => $rules['brand'],
        'model'     => $rules['model'],
        'storage'   => $rules['storage'],
    ];

    // validasi software check
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

    // validasi save address
    $rules['saveAddress'] = [
        'district_id'     => $rules['district_id'],
        'postal_code'     => $rules['postal_code'],
        'address_name'   => $rules['address_name'],
    ];

    // validasi save payment
    $rules['savePaymentUser'] = [
        'payment_method_id'     => $rules['payment_method_id'],
        'account_number'     => $rules['account_number'],
        'account_name'   => $rules['account_name'],
    ];

    // validasi withdraw
    $rules['withdraw'] = [
        'user_payment_id'   => $rules['user_payment_id'],
        'amount'            => $rules['amount'],
    ];

    // validasi nik terdaftar
    $rules['validate_nik'] = [
        'nik' => $temp_rules['nik'],
    ];

    // validasi email terdaftar
    $rules['validate_email'] = [
        'email' => $temp_rules['email'],
    ];

    // validasi phone terdaftar
    $rules['validate_phone'] = [
        'phone' => $temp_rules['phone'],
    ];

    $rules['app_2:save_photos'] = [
        'check_id'  => $rules['check_id'],
        'imei'      => $rules['imei'],
        'photo_device_1'   => $rules['photo_device_1'],
        'photo_device_2'   => $rules['photo_device_2'],
        'photo_device_3'   => $rules['photo_device_3'],
        'photo_device_4'   => $rules['photo_device_4'],
        'photo_device_5'   => $rules['photo_device_5'],
        'photo_device_6'   => $rules['photo_device_6'],
    ];

    $rules['app_2:save_quiz'] = [
        'check_id'              => $rules['check_id'],
        'quiz_1'                => $rules['quiz_1'],
        'quiz_2'                => $rules['quiz_2'],
        'quiz_3'                => $rules['quiz_3'],
        'quiz_4'                => $rules['quiz_4'],
        'imei_registered'       => $rules['imei_registered'],
        'photo_imei_registered' => $rules['photo_imei_registered'],
    ];

    $rules['app_1:save_identity'] = [
        'token'             => $rules['token'],
        'customer_name'     => $rules['name'],
        'customer_phone'    => $rules['phone'],
    ];

    $rules['app_1:save_photo_id'] = [
        'token'     => $rules['token'],
        'photo_id'  => $rules['photo_id'],
    ];

    $rules['transfer_manual'] = [
        'check_id'          => $rules['check_id'],
        'transfer_proof'    => $rules['transfer_proof'],
    ];

    if (isset($rules[$rule])) return $rules[$rule];
    else return $rules;
}
