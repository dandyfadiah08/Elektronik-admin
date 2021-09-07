<?php
function renderMenuSidebar($data, $page_key) {
    $out = '';
    $url = base_url();
    foreach ($data as $key => $val) {
        $is_active = $key == $page_key;
        if($val['access']) {
            if($val['type'] == 'nav-item-1') {
                $out .= '<li class="nav-item '.($is_active ? 'menu-open' : '').'">
                <a href="'.$url.$val['url'].'" class="nav-link '.($is_active ? 'active' : '').'">
                ';
                if(isset($val['icon'])) $out .= '<i class="nav-icon '.$val['icon'].'"></i>';
                $out .='<p>'.$val['text'].'</p>
                </a>
                </li>
                ';
            } elseif($val['type'] == 'nav-item-2') {
                if(isset($val['header'])) $out .= '<li class="nav-header">'.$val['header']['text'].'</li>';
                if(count($val['body']) > 0) {
                    foreach ($val['body'] as $body) {
                        $has_parent = isset($body['parent']);
                        $temp_out = '';
                        $has_active_child = false;
                        if(count($body['data']) > 0) {
                            foreach ($body['data'] as $key => $data) {
                                if($data['access']) {
                                    $is_active = $key == $page_key;
                                    if($is_active) $has_active_child = true;
                                    if($has_parent) $temp_out .= '<ul class="nav nav-treeview">';
                                    $temp_out .= '
                                    <li class="nav-item '.($is_active ? 'menu-open' : '').'">
                                    <a href="'.$url.$data['url'].'" class="nav-link '.($is_active ? 'active' : '').'">
                                    ';
                                    if(isset($data['icon'])) $temp_out .= '<i class="nav-icon '.$data['icon'].'"></i>';
                                    $temp_out .='<p>'.$data['text'];
                                    if(isset($data['badge'])) $temp_out .= '<span class="badge badge-'.$data['badge']['color'].' right">'.$data['badge']['text'].'</span>';
                                    $temp_out .='</p>
                                    </a>
                                    </li>';
                                    if($has_parent) $temp_out .= '</ul>';
                                }
                            }
                        }
                        $out .= '<li class="nav-item '.($has_active_child ? 'menu-is-opening menu-open' : '').'">';
                        if($has_parent) {
                            $out .= '
                            <a href="#" class="nav-link">';
                            if(isset($body['parent']['icon'])) $out .= '<i class="nav-icon '.$body['parent']['icon'].'"></i>';
                            $out .= '<p>
                            '.$body['parent']['text'].'
                            <i class="fas fa-angle-left right"></i>';
                            if(isset($body['parent']['badge'])) $out .= '<span class="badge badge-'.$body['parent']['badge']['color'].' right">'.$body['parent']['badge']['text'].'</span>';
                            $out .= '</p>
                            </a>';
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
        'access' => true, // cek role
        'type' => 'nav-item-1',
        'text' => 'Dashboard',
        'url' => '/dashboard',
        'icon' => 'fas fa-tachometer-alt',
    ],
    '1-tabs' => [
        'access' => true, // cek role
        'type' => 'nav-item-1',
        'text' => 'Tabs',
        'url' => '/dashboard/tabs',
        'icon' => 'fas fa-window-restore',
    ],
    '1-device_checks' => [
        'access' => true, // cek role
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Device Checks',
        ],
        'body' => [
            [
                'data' => [
                    '2-unreviewed' => [
                        'access' => true, // cek role
                        'text' => 'Unreviewed',
                        'url' => '/device_check',
                        'icon' => 'fas fa-clipboard',
                        'badge' => [
                            'color' => 'warning',
                            'text' => '6',
                        ],
                    ],
                    '2-reviewed' => [
                        'access' => true, // cek role
                        'text' => 'Reviewed',
                        'url' => '/device_check/reviewed',
                        'icon' => 'fas fa-clipboard-check',
                    ],
                    '2-transaction' => [
                        'access' => true, // cek role
                        'text' => 'Transaction',
                        'url' => '/transaction',
                        'icon' => 'fas fa-money-bill-wave-alt',
                        'badge' => [
                            'color' => 'primary',
                            'text' => '5',
                        ],
                    ],
                ],
            ],
        ],
    ],
    '1-finance' => [
        'access' => true, // cek role
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Finance',
        ],
        'body' => [
            [
                'data' => [
                    '2-withdraw' => [
                        'access' => true, // cek role
                        'text' => 'Withdraw',
                        'url' => '/withdraw',
                        'icon' => 'fas fa-clipboard',
                        'badge' => [
                            'color' => 'warning',
                            'text' => '6',
                        ],
                    ],
                ],
            ],
        ],
    ],
    '1-master' => [
        'access' => true, // cek role
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Master & Users',
        ],
        'body' => [
            [
                'parent' => [
                    'text' => 'Master',
                    'icon' => 'fas fa-cog',
                ],
                'data' => [
                    '2-admin' => [
                        'access' => true, // cek role
                        'text' => 'Admin',
                        'url' => '/admins',
                        'icon' => 'fas fa-user-secret',
                    ],
                    '2-admin_roles' => [
                        'access' => true, // cek role
                        'text' => 'Admin Roles',
                        'url' => '/adminroles',
                        'icon' => 'fas fa-user-shield',
                    ],
                    '2-promo_codes' => [
                        'access' => false, // cek role
                        'text' => 'Promo Codes',
                        'url' => '/master_promo_codes',
                        'icon' => 'fas fa-tags',
                    ],
                    '2-commission_rate' => [
                        'access' => true, // cek role
                        'text' => 'Commision Rate',
                        'url' => '/commission_rate',
                        'icon' => 'fas fa-percent',
                    ],
                ],
            ],
            [
                'parent' => [
                    'text' => 'Users',
                    'icon' => 'fas fa-user-cog',
                ],
                'data' => [
                    '2-users' => [
                        'access' => true, // cek role
                        'text' => 'Users',
                        'url' => '/users',
                        'icon' => 'fas fa-users',
                    ],
                ]        
            ],
            [
                'data' => [
                    '2-promo' => [
                        'access' => true, // cek role
                        'text' => 'Promo',
                        'url' => '/promo',
                        'icon' => 'fas fa-tags',
                    ],
                ]        
            ],
        ],
    ],
    '1-settings' => [
        'access' => true, // cek role
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Settings',
        ],
        'body' => [
            [
                'parent' => [
                    'text' => 'Settings',
                    'icon' => 'fas fa-sliders-h',
                ],
                'data' => [
                    '2-google_authenticator' => [
                        'access' => true, // cek role
                        'text' => 'Google Authenticator',
                        'url' => '/google_authenticator',
                        'icon' => 'fab fa-google',
                    ],
                ],
            ],
        ],
    ],
    '1-others' => [
        'access' => true, // cek role
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Others',
        ],
        'body' => [
            [
                'data' => [
                    '2-logs' => [
                        'access' => true, // cek role
                        'text' => 'Logs',
                        'url' => '/logs',
                        'icon' => 'fas fa-history',
                    ],
                    '2-logout' => [
                        'access' => true, // cek role
                        'text' => 'Logout',
                        'url' => '/dashboard/logout',
                        'icon' => 'fas fa-sign-out-alt',
                    ],
                ],
            ],
        ],
    ],

];

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="<?= base_url() ?>/assets/adminlte3/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $page->title ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">Hi, <?= $admin->name ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column pb-2" data-widget="treeview" role="menu" data-accordion="false">
                <?= renderMenuSidebar($_sidebar, $page->key ?? '1-dashboard'); ?>
            </ul>
        </nav>
    </div>
</aside>