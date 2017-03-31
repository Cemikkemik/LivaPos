<?php
! defined('APPPATH') ? die() : null;

/**
 * Nexo Premium
 *
 * @author Blair Jersyer
 * @version 1.0
**/

class Nexo_Premium_Main extends CI_Model
{
    private $can_run    =    false;
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action('load_dashboard', array( $this, 'init' ));
        $this->events->add_filter('nexo_escape_nexoadds', '__return_true');

        // For installation Purpose
        include_once(dirname(__FILE__) . '/inc/Install.php');
    }

    /**
     * Init
     *
     * @return void
    **/

    public function init()
    {
        // disabling Nexo Ads
        if (Modules::is_active('nexoads')) {
            Modules::disable('nexoads');
            // redirect( current_url() );
        }

        $Nexo    =    Modules::get('nexo');

        // If Nexo exists

        if (! $Nexo) {
            $this->notice->push_notice(tendoo_warning(__('Nexo doit être installé pour que ce module fonctionne correctement.', 'nexo_premium')));
            $this->can_run    =    false;
        } else {
            $this->can_run    =    true;
        }

        // If is enabled

        if (! Modules::is_active('nexo')) {
            $this->notice->push_notice(tendoo_warning(__('Nexo doit être activé pour que ce module fonctionne correctement.', 'nexo_premium')));
            $this->can_run    =    false;
        }

        $this->boot();
    }

    /**
     * Boot
     *
     * check whether Nexo is active to start
     *
     * @return void
     * @author Blair
    **/

    private function boot()
    {
        global $Options;

        if ($this->can_run) {
            // Include UI
            include_once(dirname(__FILE__) . '/inc/UI.php');
            include_once(dirname(__FILE__) . '/inc/Controller.php');
            include_once(dirname(__FILE__) . '/inc/Filters.php');
            include_once(dirname(__FILE__) . '/inc/Actions.php');

            // Load Languges
            $this->lang->load_lines(dirname(__FILE__) . '/language/lines.php');

            // Start Object
            $this->UI           	=    new Nexo_Premium_UI;
            // $this->Controller    	=    new Nexo_Premium_Controller;
            $this->Filters        	=    new Nexo_Premium_Filters;
            $this->Actions        	=    new Nexo_Premium_Actions;
            // Load Nexo Misc
            $this->load->model('Nexo_Misc');

            $this->events->add_filter('admin_menus', array( $this->Filters, 'admin_menus' ), 15 );
            $this->events->add_filter('nexo_daily_details_link', array( $this->Filters, 'nexo_daily_details_link' ), 11, 2);


            $this->events->add_action('load_dashboard_home', array( $this->Actions, 'dashboard_home' ));
            $this->events->add_action('nexo_before_accounting', array( $this->Actions, 'Menu_Accounting' ));
            
            // $this->events->add_action( 'nexo_checkout_footer', array( $this->UI, 'Checkout_Script' ) );
            $this->events->add_action('dashboard_footer', array( $this->Actions, 'Clean_Quote_Orders' ));

            // Settings
            $this->events->add_action('load_nexo_general_settings', array( $this->Actions, 'Checkout_Settings' ));

            if (@$Options[ 'nexo_premium_enable_history' ] == 'yes') {
                // Historique
                $this->events->add_action('nexo_create_order', array( $this->Actions, 'Create_Order_History' ));
                $this->events->add_action('nexo_edit_order', array( $this->Actions, 'Edit_Order_History' ));
                $this->events->add_action('nexo_delete_order', array( $this->Actions, 'Delete_Order_History' ));
            }

            // Register Pages
            // $this->Gui->register_page('nexo_premium', array( $this->Controller, 'index' ));
            $this->Gui->register_page_object( 'nexo_premium', new Nexo_Premium_Controller );

			// Registers for Multistore
			$this->events->add_filter( 'stores_controller_callback', array( $this, 'multistore' ) );
        }
    }

	/**
	 * Register for Multistore
	**/

	public function multistore( $array )
	{
		// to match this uri
		// dashboard/stores/../nexo_premium/*
		$array[ 'nexo_premium' ]	=	new Nexo_Premium_Controller;

		return $array;
	}
}
new Nexo_Premium_Main;
