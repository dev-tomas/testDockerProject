<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | The default title of your admin panel, this goes into the title tag
    | of your page. You can override it per page with the title section.
    | You can optionally also specify a title prefix and/or postfix.
    |
    */

    'title' => 'BAUTIFAK',

    'title_prefix' => '',

    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | This logo is displayed at the upper left corner of your admin panel.
    | You can use basic HTML here if you want. The logo has also a mini
    | variant, used for the mini side bar. Make it 3 letters or so
    |
    */

    'logo' => 'BAUTI<b>F</b>ACT',

    'logo_mini' => '<b>BF</b>',

    /*
    |--------------------------------------------------------------------------
    | Skin Color
    |--------------------------------------------------------------------------
    |
    | Choose a skin color for your admin panel. The available skin colors:
    | blue, black, purple, yellow, red, and green. Each skin also has a
    | ligth variant: blue-light, purple-light, purple-light, etc.
    |
    */

    'skin' => 'blue',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Choose a layout for your admin panel. The available layout options:
    | null, 'boxed', 'fixed', 'top-nav'. null is the default, top-nav
    | removes the sidebar and places your menu in the top navbar
    |
    */

    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Collapse Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we choose and option to be able to start with a collapsed side
    | bar. To adjust your sidebar layout simply set this  either true
    | this is compatible with layouts except top-nav layout option
    |
    */

    'collapse_sidebar' => false,

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Register here your dashboard, logout, login and register URLs. The
    | logout URL automatically sends a POST request in Laravel 5.3 or higher.
    | You can set the request to a GET or POST with logout_method.
    | Set register_url to null if you don't want a register link.
    |
    */

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'logout_method' => null,

    'login_url' => 'login',

    'register_url' => 'register',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Specify your menu items to display in the left sidebar. Each menu item
    | should have a text and and a URL. You can also specify an icon from
    | Font Awesome. A string instead of an array represents a header in sidebar
    | layout. The 'can' is a filter on Laravel's built in Gate functionality.
    |
    */

    'menu' => [
        [
            'text'      =>  'Comercial',
            'icon'      =>  'shopping-cart',
            'can'       =>  'acomercial.menu',
            'submenu'   =>  [
                [
                    'text'  =>  'Clientes',
                    'url'   =>  '/commercial.customers',
                    'can'   =>  'clientes.show'
                ],
                [
                    'text'  =>  'Cotizaciones',
                    'url'   =>  '/commercial.quotations',
                    'can'   =>  'cotizaciones.show'
                ],
                [
                    'text'  =>  'Ventas',
                    'url'   =>  '/commercial.sales',
                    'can'   =>  'ventas.show'
                ],
                [
                    'text'  =>  'POS',
                    'url'   =>  '/pos',
                    'can' => 'pos'
                ],
                [
                    'text' => 'Comunicaciones de Baja',
                    'url' => '/commercial/lows',
                    'can'   =>  'low.show'
                ],
                [
                    'text'  =>  'Resumen de Boletas',
                    'url'   =>  '/commercial.summary',
                    'can'   =>  'comprobantes.resumen'
                ],
                [
                    'text'  =>  'Cajas',
                    'url'  =>  '/commercial/cashes',
                    'can' => 'cashes'
                ],
                [
                    'text'  =>  'Reportes',
                    'url'   =>  '/commercial.reports',
                    'cam'   =>  'reportesventas.show'
                ],
                [
                    'text'  =>  'Reporte Diario',
                    'url'   =>  '/reporte/diario',
                    'cam'   =>  'reportediario.show'
                ],
                [
                    'text'  =>  'Reporte de Ingresos',
                    'url'   =>  '/reporte/ingresos',
                    'cam'   =>  'reporteingresos.show'
                ],
            ]
        ],
        [
            'text'      =>  'Logística',
            'icon'      =>  'truck',
            'can'       =>  'alogistica.menu',
            'submenu'   =>  [
                [
                    'text'  =>  'Producto/Servicios',
                    'can'   =>  'pservicios.show',
                    'url'   =>  '/warehouse.products',
                ],
                [
                    'text'  => 'Proveedores',
                    'url'   =>  '/logistic.providers',
                    'can'   =>  'proveedores.show',
                ],
                [
                    'text'  =>  'Centros de Costo',
                    'url'   =>  '/logistic.costcenter',
                ],
                [
                    'text'  =>  'Compras',
                    'url'   =>  '/logistic.purchases',
                    'can'   =>  'compras.show',
                ],
                [
                    'text'  =>  'Recibos por Honorarios',
                    'url'   =>  '/recibos-honorarios',
                    'can'   =>  'compras.show',
                ],
                [
                    'text'  =>  'Reporte de Compras',
                    'url'   =>  '/logistic.report.purchases'
                ],
            ]
        ],
        [
            'text'      =>  'Almacén',
            'icon'      =>  'archive',
            'can'       =>  'almacen.menu',
            'submenu'   =>  [
                [
                    'text'  =>  'Gestión Almacén',
                    'url'   =>  '/warehouse.list',
                    'can'   =>  'almacenes.show'
                ],
                [
                    'text'  =>  'Inventario',
                    'url'   =>  '/inventory',
                    'can'   =>  'inventario.show'
                ],
                [
                    'text'  =>  'Inventario Real',
                    'url'   =>  '/inventario/real',
                    'can'   =>  'inventario.show'
                ],
                [
                    'text'  =>  'Transferencias',
                    'url'   =>  '/transfer',
                    'can'   =>  'transfers.show'
                ],
                [
                    'text'  => 'Kardex',
                    'url'   => '/kardex',
                    'can'   =>  'kardex'
                ],
                [
                    'text'  =>  'G. de Remisión',
                    'url'   =>  '/reference-guide',
                    'can'   =>  'comprobantes.guiasremsion'
                ],
                [
                    'text'  =>  'Reporte Stock Almacen',
                    'url'   =>  '/reporte/stock-almacen',
                    'can'   =>  'report.stockwarehouse'
                ],
            ]
        ],
        [
            'text'      =>  'Contabilidad',
            'icon'      =>  'money',
            'can'   =>  'cantabilidad.show',
            'submenu'   =>  [
                [
                    'text'  =>  'Compras',
                    'can'   =>  'int.compras',
                    'url'   =>  '/accounting.purchase',
                ],
                [
                    'text'  =>  'Compras 2.13.6',
                    'can'   =>  'int.compras',
                    'url'   =>  '/accounting.purchase-2-13-6',
                ],
                [
                    'text'  =>  'Ventas',
                    'can'   =>  'int.ventas',
                    'url'   =>  '/accounting.sales',
                ],
                [
                    'text'  =>  'Ventas 2.13.6',
                    'can'   =>  'int.ventas',
                    'url'   =>  '/accounting.sales-2-13-6',
                ],
                [
                    'text'  =>  'Cuentas por Cobrar',
                    'can'   =>  'int.cuentas',
                    'url'   =>  '/accounting.accountsreceivable',
                ],
                [
                    'text'  =>  'Cuentas por Pagar',
                    'can'   =>  'int.cuentas',
                    'url'   =>  '/accounting.accountspedingpurchase',
                ],
                [
                    'text' => 'Recibos por Honorarios',
                    'can' => 'int.cuentas',
                    'url' =>'/accounting/receips-fees'
                ],
                [
                    'text' => 'Bancos',
                    'can' => 'int.cuentas',
                    'url' =>'/accounting/movimientos-bancos'
                ],
                [
                    'text'  =>  'Productos',
                    'can'   =>  'int.cuentas.producto',
                    'url'   =>  '/accounting.products',
                ],
                [
                    'text'  => 'Kardex Valorizado',
                    'url'   => '/kardex-valorizado',
                    'can'   =>  'kardex'
                ],
                [
                    'text'  => 'Kardex Fisico',
                    'url'   => '/kardex-fisic',
                    'can'   =>  '/accounting.kardex-fisic'
                ],
                [
                    'text'  =>  'Configuración',
                    'can'   =>  'int.cuentas.contables',
                    'url'   =>  '/accounting.configuration',
                ],
                    // 'text'  =>  'Comprobantes',
                    // 'can'   =>  'comprobantes.show',
                    // 'url'   =>  '/commercial.summary',
                    // 'submenu'   => [
                    //     [
                    //         'text'  =>  'Resumen de Boletas',
                    //         'url'   =>  '/commercial.summary',
                    //         'can'   =>  'comprobantes.resumen'
                    //     ],
                        /*[
                            'text'  =>  'Contingencias',
                            'url'   =>  '#',
                            'can'   =>  'comprobantes.contigencias'
                        ],
                        [
                            'text'  =>  'Retención',
                            'url'   =>  '/retentions',
                            'can'   =>  'comprobantes.retenciones'
                        ],
                        [
                            'text'  =>  'Percepción',
                            'url'   =>  '/perceptions',
                            'can'   =>  'comprobantes.percepcion'
                        ],
                        */
                    // ]
                // ],
                /*[
                    'text'  =>  'Anulaciones',
                    'can'   =>  'anulaciones.show',
                    'submenu'   =>  [
                        [
                            'text' => 'Comunicaciones',
                            'url' => '/commercial/lows',
                            'can'   =>  'low.show'
                        ],
                        [
                            'text' => 'Nota de  Crédito',
                            'url' => '/account/notes/credit',
                            'can'   =>  'creditnote.show'
                        ],
                        [
                            'text' => 'Nota de  Débito',
                            'url' => '/account/notes/debit',
                            'can'   =>  'debittnote.show'
                        ],
                        [
                            'text'  =>  'R. de Retención',
                             'url'   =>  '/accounting.guides',
                            'url'   =>  '#',
                            'can'   =>  'perception.show'
                        ],
                        [
                            'text'  =>  'R. de Percepción',
                             'url'   =>  '/accounting.guides',
                            'url'   =>  '#',
                            'can'   =>  'retention.show'
                        ],
                    ],
                     'url'   =>  '/accounting.cancellations'
                ],*/
                // [
                //     'text'  =>  'REPORTES',
                //     'url'   =>  '/report/sales'
                // ]
            ]
        ],
//         [
//             'text' => 'Finanzas',
//             'icon' => 'file',
//             'submenu' => [
//                 [
//                     'text' => 'Finanzas',
//                     'url' => '/analytics'
//                     //'can' => 'analytics.show'
//                 ],
//                 [
//                     'text' => 'Finanzas OLD',
//                     'url' => '/analytics/analytics'
//                     //'can' => 'analytics.show'
// ,                ]
//             ]
//         ],
        /*[
            'text' => 'Finanzas',
            'icon' => 'layout-menu-v',
            'submenu' => [
                [
                    'text' => 'Pago a Proveedores',
                    'url' => '/finances/payment-providers'
                ]
            ]
        ],*/
        [
            'text'      =>  'SUNAT',
            'icon'      =>  'settings',
            'submenu'   =>  [
                [
                    'text'  =>  'Gestión de Ventas e Ingresos',
                    'url'   =>  '/sire',
                ],
                [
                    'text'  =>  'Gestión de Compras',
                    'url'   =>  '/sire/compras',
                ]
            ]
        ],
        [
            'text'      =>  'Configuración',
            'icon'      =>  'settings',
            'can'       =>  'configuraciones.menu',
            'submenu'   =>  [
                [
                    'text'  =>  'Empresa',
                    'url'   =>  '/configuration.company',
                    'can'   =>  'empresa.show'
                ],
                [
                    'text'  =>  'Locales y Series',
                    'url'   =>  '/configuration.headquarters',
                    'can'   =>  'localserie.show'
                ],
                [
                    'text' => 'Impuestos',
                    'url' => '/impuestos'
                ],
                // [
                //     'text'  =>  'Apariencia',
                //     'url'   =>  '/configuration.appearance',
                //     'can'   =>  'apariencia.show'
                // ],
                [
                    'text'  =>  'Usuarios',
                    'url'   =>  '/configuration.users',
                    'can'   =>  'usuarios.show',
                ],
                /*[
                    'text'  =>  'Roles y Permisos',
                    'url'   =>  '/roles',
                    'can'   =>  'roles.show'
                ],*/
                // [
                //     'text'  =>  'Certificado Digital',
                //     'url'   =>  '/configuration/invoices'
                // ]
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Choose what filters you want to include for rendering the menu.
    | You can add your own filters to this array after you've created them.
    | You can comment out the GateFilter if you don't want to use Laravel's
    | built in Gate functionality
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Choose which JavaScript plugins should be included. At this moment,
    | only DataTables is supported as a plugin. Set the value to true
    | to include the JavaScript file from a CDN via a script tag.
    |
    */

    'plugins' => [
        'datatables' => true,
        'select2'    => true,
        'chartjs'    => true,
    ],
];
