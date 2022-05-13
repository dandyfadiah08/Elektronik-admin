<?php
function renderMenuSidebar($data, $page_key)
{
    $out = '';
    $url = base_url();
    foreach ($data as $key => $val) {
        $is_active = $key == $page_key;
        if ($val['access']) {
            if ($val['type'] == 'nav-item-1') {
                $out .= '<li class="nav-item ' . ($is_active ? 'menu-open' : '') . '">
                <a href="' . $url . $val['url'] . '" class="nav-link ' . ($is_active ? 'active' : '') . '">
                ';
                if (isset($val['icon'])) $out .= '<i class="nav-icon ' . $val['icon'] . '"></i>';
                $out .= '<p>' . $val['text'] . '</p>
                </a>
                </li>
                ';
            } elseif ($val['type'] == 'nav-item-2') {
                if (isset($val['header'])) $out .= '<li class="nav-header">' . $val['header']['text'] . '</li>';
                if (count($val['body']) > 0) {
                    foreach ($val['body'] as $body) {
                        $has_parent = isset($body['parent']);
                        $temp_out = '';
                        $has_active_child = false;
                        if (count($body['data']) > 0) {
                            foreach ($body['data'] as $key => $data) {
                                if ($data['access']) {
                                    $is_active = $key == $page_key;
                                    if ($is_active) $has_active_child = true;
                                    if ($has_parent) $temp_out .= $body['parent']['access'] ? '<ul class="nav nav-treeview">' : '';
                                    $temp_out .= '
                                    <li class="nav-item' . ($is_active ? ' menu-open' : '') . ' ' . ($data['class'] ?? '') . '">
                                    <a href="' . $url . $data['url'] . '" class="nav-link ' . ($is_active ? 'active' : '') . '">
                                    ';
                                    if (isset($data['icon'])) $temp_out .= '<i class="nav-icon ' . $data['icon'] . '"></i>';
                                    $temp_out .= '<p>' . $data['text'];
                                    if (isset($data['badge'])) $temp_out .= '<span class="badge badge-' . $data['badge']['color'] . ' right ' . ($data['badge']['class'] ?? '') . '" id="' . ($data['badge']['id'] ?? '') . '">' . $data['badge']['text'] . '</span>';
                                    $temp_out .= '</p>
                                    </a>
                                    </li>';
                                    if ($has_parent) $temp_out .= $body['parent']['access'] ? '</ul>' : '';
                                }
                            }
                        }
                        $out .= '<li class="nav-item ' . ($has_active_child ? 'menu-is-opening menu-open' : '') . '">';
                        if ($has_parent) {
                            if ($body['parent']['access']) {
                                $out .= '
                                <a href="#" class="nav-link">';
                                if (isset($body['parent']['icon'])) $out .= '<i class="nav-icon ' . $body['parent']['icon'] . '"></i>';
                                $out .= '<p>
                                ' . $body['parent']['text'] . '
                                <i class="fas fa-angle-left right"></i>';
                                if (isset($body['parent']['badge'])) $out .= '<span class="badge badge-' . $body['parent']['badge']['color'] . ' right ' . ($body['parent']['badge']['class'] ?? '') . '">' . $body['parent']['badge']['text'] . '</span>';
                                $out .= '</p>
                                </a>';
                            }
                        }
                        $out .= $temp_out;
                        $out .= '</li>';
                    }
                }
            }
        }
    }
    return $out;
}

$_sidebar = [
    '1-dashboard' => [
        'access' => true,
        'type' => 'nav-item-1',
        'text' => 'Dashboard',
        'url' => '/dashboard',
        'icon' => 'fas fa-tachometer-alt',
    ],
    '1-tabs' => [
        'access' => true,
        'type' => 'nav-item-1',
        'text' => 'Tabs',
        'url' => '/dashboard/tabs',
        'icon' => 'fas fa-window-restore',
    ],
    '1-device_checks' => [
        'access' => hasAccess($role, 'r_device_check'),
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Device Checks',
        ],
        'body' => [
            [
                'data' => [
                    '2-unreviewed' => [
                        'access' => hasAccess($role, 'r_device_check'),
                        'text' => 'Unreviewed',
                        'url' => '/device_check',
                        'icon' => 'fas fa-clipboard',
                        'badge' => [
                            'color' => 'warning',
                            'text' => $unreviewed_count,
                            'id' => 'unreviewed_count',
                            'class' => 'unreviewed_count',
                        ],
                    ],
                    '2-reviewed' => [
                        'access' => hasAccess($role, 'r_device_check'),
                        'text' => 'Reviewed',
                        'url' => '/device_check/reviewed',
                        'icon' => 'fas fa-clipboard-check',
                    ],
                ],
            ],
        ],
    ],
    '1-finance' => [
        'access' => hasAccess($role, ['r_transaction', 'r_withdraw', 'r_request_payment', 'r_bonus_view']),
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Transaction',
        ],
        'body' => [
            [
                'data' => [
                    '2-transaction' => [
                        'access' => hasAccess($role, 'r_transaction'),
                        'text' => 'Transaction',
                        'url' => '/transaction',
                        'icon' => 'fas fa-money-bill-wave-alt',
                        'badge' => [
                            'color' => 'primary',
                            'text' => $transaction_count,
                            'id' => 'transaction_count',
                            'class' => 'transaction_count',
                        ],
                    ],
                    '2-transaction_success' => [
                        'access' => hasAccess($role, 'r_transaction_success'),
                        'text' => 'Success',
                        'url' => '/transaction/success',
                        'icon' => 'fas fa-money-bill-wave-alt',
                    ],
                    '2-request_payment' => [
                        'access' => hasAccess($role, 'r_request_payment'),
                        'text' => 'Request Payment',
                        'url' => '/transaction/request_payment',
                        'icon' => 'fas fa-comment-dollar',
                    ],
                    '2-withdraw' => [
                        'access' => hasAccess($role, 'r_withdraw'),
                        'text' => 'Withdraw',
                        'url' => '/withdraw',
                        'icon' => 'fas fa-wallet',
                        'badge' => [
                            'color' => 'success',
                            'text' => $withdraw_count,
                            'id' => 'withdraw_count',
                            'class' => 'withdraw_count',
                        ],
                    ],
                ],
            ],
            [
                'parent' => [
                    'access' => hasAccess($role, ['r_bonus_view', 'r_tax']),
                    'text' => 'Accounting',
                    'icon' => 'fas fa-file-invoice-dollar',
                ],
                'data' => [
                    '2-bonus' => [
                        'access' => hasAccess($role, 'r_bonus_view'),
                        'text' => 'Agent Bonus',
                        'url' => '/bonus',
                        'icon' => 'fas fa-coins',
                    ],
                    '2-tax' => [
                        'access' => hasAccess($role, 'r_tax'),
                        'text' => 'Tax Data',
                        'url' => '/tax',
                        'icon' => 'fas fa-file-invoice',
                    ],
                ],
            ],
        ],
    ],
    '1-master' => [
        'access' => hasAccess($role, ['r_admin', 'r_admin_role', 'r_commission_rate', 'r_user', 'r_promo', 'r_promo_view', 'r_price', 'r_price_view', 'r_courier', 'r_courier_view']),
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Master & Users',
        ],
        'body' => [
            [
                'parent' => [
                    'access' => hasAccess($role, ['r_admin', 'r_admin_role', 'r_promo', 'r_commission_rate', 'r_courier', 'r_courier_view']),
                    'text' => 'Master',
                    'icon' => 'fas fa-cog',
                ],
                'data' => [
                    '2-admin' => [
                        'access' => hasAccess($role, 'r_admin'),
                        'text' => 'Admin',
                        'url' => '/admin',
                        'icon' => 'fas fa-user-secret',
                    ],
                    '2-admin_role' => [
                        'access' => hasAccess($role, 'r_admin_role'),
                        'text' => 'Admin Role',
                        'url' => '/admin_role',
                        'icon' => 'fas fa-user-shield',
                    ],
                    // '2-promo_codes' => [
                    //     'access' => hasAccess($role, 'r_promo'), // cek role, belum
                    //     'text' => 'Promo Codes',
                    //     'url' => '/promo_codes',
                    //     'icon' => 'fas fa-tags',
                    // ],
                    '2-commission_rate' => [
                        'access' => hasAccess($role, 'r_commission_rate'),
                        'text' => 'Commision Rate',
                        'url' => '/commission_rate',
                        'icon' => 'fas fa-percent',
                    ],
                    '2-courier' => [
                        'access' => hasAccess($role, ['r_courier', 'r_courier_view']),
                        'text' => 'Courier',
                        'url' => '/courier',
                        'icon' => 'fas fa-truck',
                    ],
                ],
            ],
            [
                // 'parent' => [
                //     'access' => hasAccess($role, 'r_user'),
                //     'text' => 'Users',
                //     'icon' => 'fas fa-user-cog',
                // ],
                'data' => [
                    '2-users' => [
                        'access' => hasAccess($role, 'r_user'),
                        'text' => 'Users',
                        'url' => '/users',
                        'icon' => 'fas fa-users',
                        'badge' => [
                            'color' => 'danger',
                            'text' => $submission_count,
                            'id' => 'submission_count',
                            'class' => 'submission_count',
                        ],
                    ],
                ]
            ],
            [
                'data' => [
                    '2-merchants' => [
                        'access' => hasAccess($role, 'r_merchant'),
                        'text' => 'Merchants',
                        'url' => '/merchants',
                        'icon' => 'fas fa-user-tag',
                    ],
                ]
            ],
            [
                'data' => [
                    '2-promo' => [
                        'access' => hasAccess($role, ['r_promo', 'r_promo_view', 'r_price', 'r_price_view']),
                        'text' => 'Promo',
                        'url' => '/promo',
                        'icon' => 'fas fa-tags',
                    ],
                ]
            ],
        ],
    ],
    '1-settings' => [
        'access' => hasAccess($role, ['r_2fa', 'r_change_available_date_time', 'r_change_setting']),
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Settings',
        ],
        'body' => [
            [
                'parent' => [
                    'access' => hasAccess($role, 'r_2fa'),
                    'text' => 'Settings',
                    'icon' => 'fas fa-sliders-h',
                ],
                'data' => [
                    '2-google_authenticator' => [
                        'access' => hasAccess($role, 'r_2fa'),
                        'text' => 'Google Authenticator',
                        'url' => '/google_authenticator',
                        'icon' => 'fab fa-google',
                    ],
                    '2-setting_available_date_time' => [
                        'access' => hasAccess($role, 'r_change_available_date_time'),
                        'text' => 'Setting Time',
                        'url' => '/setting_time',
                        'icon' => 'far fa-clock',
                    ],
                    '3-setting' => [
                        'access' => hasAccess($role, 'r_change_setting'),
                        'text' => 'Setting',
                        'url' => '/setting',
                        'icon' => 'fas fa-cog',
                    ],
                ],
            ],
        ],
    ],
    '1-others' => [
        'access' => true, // karena ada logout
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Others',
        ],
        'body' => [
            [
                'data' => [
                    '2-logs' => [
                        'access' => hasAccess($role, 'r_logs'),
                        'text' => 'Logs',
                        'url' => '/logs',
                        'icon' => 'fas fa-history',
                    ],
                    '2-logout' => [
                        'access' => true,
                        'text' => 'Logout',
                        'url' => '/dashboard/logout',
                        'icon' => 'fas fa-sign-out-alt',
                        'class' => 'btnLogout',
                    ],
                ],
            ],
        ],
    ],

];

?>
<aside class="main-sidebar sidebar-dark-primary fixed elevation-4" style="position: fixed;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="<?= base_url() ?>/assets/images/logo-circle-light.png" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $page->title ?? env('app.name') ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2" style="
            overflow-y: auto;
            height: calc(100vh - 5rem);
            overflow-x: hidden;
        ">
            <ul class="nav nav-pills nav-sidebar flex-column pb-2" data-widget="treeview" role="menu" data-accordion="false">
                <?= renderMenuSidebar($_sidebar, $page->key ?? '1-dashboard'); ?>
            </ul>
        </nav>
    </div>
</aside>