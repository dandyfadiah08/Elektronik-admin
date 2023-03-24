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
    '1-master' => [
        'access' =>  hasAccess($role, ['admin', 'role', 'pameran']),
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Master Data',
        ],

        'body' => [
            [
                'data' => [
                    '2-MasterAdmin' => [
                        'access' => hasAccess($role, 'admin'),
                        'text' => 'Master Admin',
                        'url' => '/MasterAdmin',
                        'icon' => 'fas fa-users',
                        // 'badge' => [
                        //     'color' => 'warning',
                        //     'text' => 'unreviewed_count',
                        //     'id' => 'unreviewed_count',
                        //     'class' => 'unreviewed_count',
                        // ]
                    ],
                    '2-MasterRole' => [
                        'access' => hasAccess($role, 'role'),
                        'text' => 'Master Role',
                        'url' => '/MasterRole',
                        'icon' => 'fa fa-key',
                        // 'badge' => [
                        //     'color' => 'warning',
                        //     'text' => 'unreviewed_count',
                        //     'id' => 'unreviewed_count',
                        //     'class' => 'unreviewed_count',
                        // ]
                    ],
                    '2-MasterKategori' => [
                        'access' => hasAccess($role, 'kategori'),
                        'text' => 'Master kategori',
                        'url' => '/MasterKategori',
                        'icon' => 'fas fa-server',
                    ],
                    '2-MasterKuisioner' => [
                        'access' => hasAccess($role, 'kuisioner'),
                        'text' => 'Data Master Kuisioner',
                        'url' => '/MasterKuisioner',
                        'icon' => 'fas fa-clone',
                    ],
                    '2-JenisGrading' => [
                        'access' => hasAccess($role, 'grading'),
                        'text' => 'Jenis Grading',
                        'url' => '/jenis_grading',
                        'icon' => '	far fa-clone',
                    ],
                    '2-MasterPameran' => [
                        'access' => hasAccess($role, 'pameran'),
                        'text' => 'Master Pameran',
                        'url' => '/MasterPameran',
                        'icon' => 'fas fa-handshake',
                    ],


                ]
            ]
        ]
    ],
    '1-tradein' => [
        'access' => hasAccess($role, ['statistik', 'tradein', 'potongan']),
        'type' => 'nav-item-2',
        'header' => [
            'type' => 'nav-header',
            'text' => 'Main Menu',
        ],

        'body' => [
            [
                'data' => [
                    '2-Statistik' => [
                        'access' => hasAccess($role, 'statistik'),
                        'text' => 'Data Statistik',
                        'url' => '/statistik/loadstatistik',
                        'icon' => 'fas fa-chart-line',
                        // 'badge' => [
                        //     'color' => 'warning',
                        //     'text' => 'unreviewed_count',
                        //     'id' => 'unreviewed_count',
                        //     'class' => 'unreviewed_count',
                        // ]
                    ],
                    '2-Tradein' => [
                        'access' => hasAccess($role, 'tradein'),
                        'text' => 'Data Tradein',
                        'url' => '/tradein',
                        'icon' => 'fas fa-clipboard',
                        // 'badge' => [
                        //     'color' => 'warning',
                        //     'text' => 'unreviewed_count',
                        //     'id' => 'unreviewed_count',
                        //     'class' => 'unreviewed_count',
                        // ]
                    ],
                    // '2-Tradein' => [
                    //     'access' => hasAccess($role, 'tradein'),
                    //     'text' => 'Data Tradein',
                    //     'url' => '/tradein',
                    //     'icon' => 'fas fa-clipboard',
                    //     // 'badge' => [
                    //     //     'color' => 'warning',
                    //     //     'text' => 'unreviewed_count',
                    //     //     'id' => 'unreviewed_count',
                    //     //     'class' => 'unreviewed_count',
                    //     // ]
                    // ],
                    // '2-potongan' => [
                    //     'access' => hasAccess($role, 'potongan'),
                    //     'text' => 'Data Potongan',
                    //     'url' => '/potongan',
                    //     'icon' => 'fas fa-balance-scale',
                    // ],
                    // '2-grading' => [
                    //     'access' => hasAccess($role, 'potongan'),
                    //     'text' => 'Data Grading',
                    //     'url' => '/grading',
                    //     'icon' => 'fas fa-percent',
                    // ],
                ]
            ]
        ]
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
                        'access' => hasAccess($role, 'log'),
                        'text' => 'Logs',
                        'url' => '/log',
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
        <img src="<?= base_url() ?>/assets/images/logoelektro.jpg" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
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