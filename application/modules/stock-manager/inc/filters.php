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
                            'title'        =>    __('Inventaire', 'nexo'),
                            'href'        =>    '#',
                            'disable'    =>    true,
                            'icon'        =>    'fa fa-archive'
                        ),
                        array(
                            'title'        =>    __('Liste des livraisons', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/supplies'),
                        ),
                        array(
                            'title'        =>    __('Nouvelle livraison', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/supplies/add'),
                        ),
                        array(
                            'title'        =>    __('Liste des articles', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/items'),
                        ),
                        array(
                            'title'        =>    __('Ajouter un article', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . 'nexo/items/add'),
                        ),
                        // @since 3.0.20
                        array(
                            'title'		=>	__( 'Ajustement des quantités', 'nexo' ),
                            'href'		=>	dashboard_url([ 'items-stock-adjustment' ] )
                        ),
                        array(
                            'title'         =>  __( 'Importer les articles', 'nexo' ),
                            'href'          =>  dashboard_url([ 'items', 'import' ])
                        ),
                        array(
                            'title'        =>    __('Liste des taxes', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/taxes'),
                        ),
                        array(
                            'title'        =>    __('Ajouter une taxe', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/taxes/add'),
                        ),
                        array(
                            'title'        =>    __('Liste des catégories', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/categories'),
                        ),
                        array(
                            'title'        =>    __('Ajouter une catégorie', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/categories/add'),
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
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/providers'),
                        ),
                        array(
                            'title'        =>    __('Ajouter un fournisseur', 'nexo'),
                            'href'        =>    site_url('dashboard/' . store_slug() . '/nexo/providers/add'),
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