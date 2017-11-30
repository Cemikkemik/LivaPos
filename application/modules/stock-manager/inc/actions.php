<?php
include_once( dirname( __FILE__ ) . '/install.php' );

class Nexo_Stock_Manager_Actions extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();

        $this->install      =   new Nexo_Stock_Manager_Install;
    }

    /**
     * Load Dashboard
     * @return void
    **/

    public function load_dashboard()
    {
        if( ! multistore_enabled() ) {
            nexo_notices([
                'user_id'       =>  User::id(),
                'link'          =>  site_url([ 'dashboard', 'nexo', 'settings', 'stores' ]),
                'icon'          =>  'fa fa-info',
                'type'          =>  'text-warning',
                'message'       =>  sprintf( __( 'The multistore feature should be enable in order to use the stock manager.', 'stock-manager' ) )
            ]);
        }
    }

    /**
     * Do Enable Module
     * @return void
    **/

    public function do_enable_module( $namespace )
    {
        if( $namespace == 'stock-manager' && get_option( 'stock-manager-installed' ) == null ) {
            set_option( 'stock-manager-installed', true );

            $this->install->complete();
        }
    }

    /**
     * Install tables
     * @param string table prefix
     * @return void
    **/

    public function install_tables( $table_prefix )
    {
        $this->install->sql( $table_prefix );
    }

    /**
     * Uninstall
     * @return void
    **/

    public function do_remove_module( $namespace )
    {
        // retrait des tables Nexo
        if ( $namespace === 'stock-manager' ) {
            $this->install->remove_all();
        }
    }

    /**
     * Delete tables
     * @param string table prefox
     * @return void
    **/

    public function remove_tables( $table_prefix )
    {
        $this->install->remove( $table_prefix );
    }
}