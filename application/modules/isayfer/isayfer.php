<?php
class isayfer_module extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_filter( 'admin_menus', [ $this, 'admin_menus' ], 30 );
        $this->events->add_action( 'load_dashboard', [ $this, 'dashboard' ] );
        $this->events->add_filter( 'product_required_fields', [ $this, 'product_required_fields' ]);
        $this->events->add_filter( 'checkout_header_menus_1', [ $this, 'checkout_header_menus_1' ]);
    }

    /**
     * Admin Menu
     * @param array menu
     * @return array
    **/

    public function admin_menus( $menus )
    {
        if( @$menus[ 'nexo_settings' ] ) {
            foreach( $menus[ 'nexo_settings' ] as $key => $menu ) {
                if( strtolower( $menu[ 'title' ] ) == 'about' ) {
                    unset( $menus[ 'nexo_settings' ][ $key ] );
                }
            }
        }

        if( User::in_group( 'shop_cashier' ) ) {
            if( @$menus[ 'sales' ] ) {
                unset( $menus[ 'sales' ] );
            }

            if( @$menus[ 'coupons' ] ) {
                unset( $menus[ 'coupons' ] );
            }

            if( @$menus[ 'clients' ] ) {
                unset( $menus[ 'clients' ] );
            }

            if( @$menus[ 'dashboard' ] ) {
                unset( $menus[ 'dashboard' ] );
            }
        }        

        unset( $menus[ 'modules' ] );
        return $menus;
    }

    /**
     * product_required_fields
     * @return array
    **/

    public function product_required_fields( $fields )
    {
        foreach( ( array ) $fields as $key => $field ) {
            if( in_array( $field, [ 'QUANTITY' ] ) ){
                unset( $fields[ $key ] );
            }
        }
        return $fields;
    }

    /**
     * Load Dashboard
     * @return void
    **/

    public function dashboard()
    {
        if( User::in_group( 'shop_cashier' ) ) {
            if( is_multistore() ) {
                if( in_array( store_option( 'default_compte_client' ),  [ '', null ] ) ) {
                    return show_error( __( 'This store is not completely configured. Please contact the store manager.' , 'isayfer' ) );
                } else if( $this->uri->segment( 4 ) == null ) {
                    redirect([ 'dashboard', store_slug(), 'nexo', 'registers', '__use', 'default' ]);
                }
            }
        }
    }

    /**
     * Filter POS ui
     * @param array
     * @return array
    **/

    public function checkout_header_menus_1( $menus ) 
    {
        $menus[0]       =   [
            'class' =>  'default',
            'text'  =>  __( 'Log out', 'nexo' ),
            'icon'  =>  'home',
            'attrs' =>  [
                'ng-click'  =>  'goTo( \'' . site_url([ 'sign-out' ] ) . '\' )'
            ]
        ];

        return $menus;
    }
}
new isayfer_module;