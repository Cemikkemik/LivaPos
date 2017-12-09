<?php
class Nexo_Stock_Manager_Filters extends Tendoo_Module
{
    /**
     * Filter Admin Menus
     * @param array
     * @return array
    **/

    public function admin_menus( $menus )
    {
        if( multistore_enabled() && ( User::in_group( 'shop.manager' ) || User::in_group( 'shop.demo' ) || User::in_group( 'master' ) ) ) {
            if( ! is_multistore() ) {

                $menus          =   array_insert_after( 'nexo_shop', $menus, 'stock-manager', [
                    [
                        'title'     =>  __( 'Stock Transfert', 'stock-manager' ),
                        'href'      =>  '#',
                        'icon'      =>  'fa fa-exchange',
                        'disable'   =>  true
                    ],
                    [
                        'title'     =>  __( 'Transfert History', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transferts' ]),
                    ],
                    [
                        'title'     =>  __( 'New Transfert', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transferts', 'add' ]),
                    ],
                    [
                        'title'     =>  __( 'Transfert Settings', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'settings', 'stock' ]),
                    ]
                ]);

                if (
                    User::can('nexo.create.items') ||
                    User::can('nexo.create.categories') ||
                    User::can('nexo.create.providers') ||
                    User::can('nexo.create.shippings')
                ) {
                    $menus                      =   array_insert_after( 'stock-manager', $menus, 'arrivages', array(
                        array(
                            'title'        =>    __('Inventory', 'stock-manager'),
                            'href'        =>    '#',
                            'disable'    =>    true,
                            'icon'        =>    'fa fa-archive'
                        ),
                        array(
                            'title'        =>    __('Supplies', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'supplies' ]),
                        ),
                        array(
                            'title'        =>    __('New Supply', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'supplies', 'add' ]),
                        ),
                        array(
                            'title'        =>    __('Items List', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'items' ]),
                        ),
                        array(
                            'title'        =>    __('Add Item', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'items', 'add' ]),
                        ),
                        // @since 3.0.20
                        array(
                            'title'		=>	__( 'Quantity Adjustment', 'stock-manager' ),
                            'href'		=>	dashboard_url([ 'items-stock-adjustment' ] )
                        ),
                        array(
                            'title'         =>  __( 'Import Items', 'stock-manager' ),
                            'href'          =>  dashboard_url([ 'items', 'import' ])
                        ),
                        array(
                            'title'        =>    __('Tax List', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'taxes' ]),
                        ),
                        array(
                            'title'        =>    __('Add a tax', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'taxes', 'add' ]),
                        ),
                        array(
                            'title'        =>    __('Categories List', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'categories' ]),
                        ),
                        array(
                            'title'        =>    __('Add a categories', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'categories', 'add' ]),
                        )
                    ));
                    
                    $menus                      =   array_insert_after( 'arrivages', $menus, 'vendors', array(
                        array(
                            'title'        =>    __('Suppliers', 'stock-manager'),
                            'disable'        =>  true,
                            'href'			=>	'#',
                            'icon'			=>	'fa fa-truck'
                        ),
                        array(
                            'title'        =>    __('Suppliers List', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'providers']),
                        ),
                        array(
                            'title'        =>    __('Add a supplier', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'providers', 'add' ]),
                        ),
                    ) );

                    $menus                      =   array_insert_after( 'arrivages', $menus, 'warehouse-settings', array(
                        array(
                            'title'        =>    __('Warehouse Settings', 'stock-manager'),
                            'href'			=>	site_url([ 'dashboard', 'stock-manager', 'settings' ]),
                            'icon'			=>	'fa fa-wrench'
                        ),
                        array(
                            'title'        =>    __('Others Settings', 'stock-manager'),
                            'href'			=>	site_url([ 'dashboard', 'stock-manager', 'settings', 'checkout' ]),
                            'icon'			=>	'fa fa-wrench'
                        ),
                        array(
                            'title'        =>    __('Receipt & Invoice', 'stock-manager'),
                            'href'			=>	site_url([ 'dashboard', 'stock-manager', 'settings', 'invoices' ]),
                            'icon'			=>	'fa fa-wrench'
                        )
                    ) );
                }
            } else {
                $menus          =   array_insert_after( 'arrivages', $menus, 'stock-manager', [
                    [
                        'title'     =>  __( 'Stock Transfert', 'stock-manager' ),
                        'href'      =>  '#',
                        'icon'      =>  'fa fa-exchange',
                        'disable'   =>  true
                    ],
                    [
                        'title'     =>  __( 'Transfert History', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert' ]),
                    ],
                    [
                        'title'     =>  __( 'New Transfert', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert', 'add' ]),
                    ],
                    [
                        'title'     =>  __( 'Transfert Settings', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'settings', 'stock' ]),
                    ]
                ]);
            }
        }
        return $menus;
    }
}