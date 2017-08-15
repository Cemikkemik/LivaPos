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
        if( multistore_enabled() && ( User::in_group( 'shop_manager' ) || User::in_group( 'shop_tester' ) || User::in_group( 'master' ) ) ) {
            if( ! is_multistore() ) {
                // $menus[ 'nexo_store_settings' ][]   =   [
                //     'href'          =>      site_url( [ 'dashboard', 'stock-manager', 'settings' ] ),
                //     'title'         =>      __( 'Stock Manager', 'stock-manager' )
                // ];

                // 
                $menus          =   array_insert_after( 'nexo_shop', $menus, 'stock-manager', [
                    [
                        'title'     =>  __( 'Stock Transfert', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', 'stock-transfert' ]),
                        'icon'      =>  'fa fa-exchange',
                        'disable'   =>  true
                    ],
                    [
                        'title'     =>  __( 'Transfert History', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', 'stock-transfert', 'history' ]),
                    ],
                    [
                        'title'     =>  __( 'New Transfert', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', 'stock-transfert', 'history', 'new' ]),
                    ],
                    [
                        'title'     =>  __( 'Transfert Settings', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', 'stock-transfert', 'settings' ]),
                    ]
                ]);

                if (
                    User::can('create_shop_items') ||
                    User::can('edit_shop_items') ||
                    User::can('delete_shop_items') ||
                    User::can('create_shop_categories') ||
                    User::can('edit_shop_categories') ||
                    User::can('delete_shop_categories') ||
                    User::can('create_shop_radius') ||
                    User::can('edit_shop_radius') ||
                    User::can('delete_shop_radius') ||
                    User::can('create_shop_providers') ||
                    User::can('edit_shop_providers') ||
                    User::can('delete_shop_providers') ||
                    User::can('create_shop_shippings') ||
                    User::can('edit_shop_shippings') ||
                    User::can('delete_shop_shippings')
                ) {
                    $menus                      =   array_insert_after( 'stock-manager', $menus, 'arrivages', array(
                        array(
                            'title'        =>    __('Inventaire', 'nexo'),
                            'href'        =>    '#',
                            'disable'    =>    true,
                            'icon'        =>    'fa fa-archive'
                        ),
                        array(
                            'title'        =>    __('Liste des livraisons', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/arrivages/lists'),
                        ),
                        array(
                            'title'        =>    __('Nouvelle livraison', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/produits/add_supply'),
                        ),
                        array(
                            'title'        =>    __('Liste des articles', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/produits/lists'),
                        ),
                        array(
                            'title'        =>    __('Ajouter un article', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/produits/lists/add'),
                        ),
                        // @since 3.0.20
                        array(
                            'title'		=>	__( 'Ajustement des quantités', 'nexo' ),
                            'href'		=>	site_url([ 'dashboard', store_slug(), 'nexo', 'produits', 'stock_supply' ] )
                        ),
                        array(
                            'title'         =>  __( 'Importer les articles', 'nexo' ),
                            'href'          =>  site_url( array( 'dashboard', store_slug(), 'nexo_import', 'items' ) )
                        ),
                        array(
                            'title'        =>    __('Liste des taxes', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo_taxes'),
                        ),
                        array(
                            'title'        =>    __('Ajouter une taxe', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo_taxes/add'),
                        ),
                        array(
                            'title'        =>    __('Liste des départements', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/rayons/lists'),
                        ),
                        array(
                            'title'        =>    __('Ajouter un département', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/rayons/lists/add'),
                        ),
                        array(
                            'title'        =>    __('Liste des catégories', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/categories/lists'),
                        ),
                        array(
                            'title'        =>    __('Ajouter une catégorie', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/categories/lists/add'),
                        )
                    ));
                    
                    $menus                      =   array_insert_after( 'arrivages', $menus, 'vendors', array(
                        array(
                            'title'        =>    __('Fournisseurs', 'nexo'),
                            'disable'        =>  true,
                            'href'			=>	'#',
                            'icon'			=>	'fa fa-truck'
                        ),
                        array(
                            'title'        =>    __('Liste des fournisseurs', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/fournisseurs/lists'),
                        ),
                        array(
                            'title'        =>    __('Ajouter un fournisseur', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/fournisseurs/lists/add'),
                        ),
                    ) );

                    $menus                      =   array_insert_after( 'arrivages', $menus, 'warehouse-settings', array(
                        array(
                            'title'        =>    __('Warehouse Settings', 'nexo'),
                            'href'			=>	site_url([ 'dashboard', 'nexo', 'settings' ]),
                            'icon'			=>	'fa fa-wrench'
                        ),
                        array(
                            'title'        =>    __('Others Settings', 'nexo'),
                            'href'			=>	site_url([ 'dashboard', 'nexo', 'settings', 'checkout' ]),
                            'icon'			=>	'fa fa-wrench'
                        ),
                        array(
                            'title'        =>    __('Receipt & Invoice', 'nexo'),
                            'href'			=>	site_url([ 'dashboard', 'nexo', 'settings', 'invoices' ]),
                            'icon'			=>	'fa fa-wrench'
                        )
                    ) );
                }
            } else {
                $menus          =   array_insert_after( 'arrivages', $menus, 'stock-manager', [
                    [
                        'title'     =>  __( 'Stock Transfert', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', store_slug(), 'stock-transfert' ]),
                        'icon'      =>  'fa fa-exchange',
                        'disable'   =>  true
                    ],
                    [
                        'title'     =>  __( 'Transfert History', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', store_slug(), 'stock-transfert', 'history' ]),
                    ],
                    [
                        'title'     =>  __( 'New Transfert', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', store_slug(), 'stock-transfert', 'history', 'new' ]),
                    ],
                    [
                        'title'     =>  __( 'Transfert Settings', 'stock-manager' ),
                        'href'      =>  site_url([ 'dashboard', store_slug(), 'stock-transfert', 'settings' ]),
                    ]
                ]);
            }
        }
        return $menus;
    }
}