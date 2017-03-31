<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/alvaro_library.php' );

class Alvaro_Controller extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     *  Crud Header
     *  @param void
     *  @return void
    **/

    public function crud_header()
    {
        /**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject( __( 'Commissions', 'alvaro' ) );
        $crud->set_table($this->db->dbprefix( store_prefix() . 'alvaro_commissions') );
        $crud->unset_add();
        $crud->unset_export();
        $crud->unset_delete();

		// $fields				=	array( 'TITRE', 'DESCRIPTION' );
		$crud->columns( 'ref_order', 'ref_author', 'commission_percentage', 'commission_amount', 'date_creation' );
        // $crud->fields( $fields );
        $crud->set_relation( 'ref_author', 'aauth_users', 'name' );
        $crud->set_relation( 'ref_order', store_prefix() . 'nexo_commandes', 'CODE' );
        $crud->order_by( 'id', 'desc' );

        $crud->display_as( 'ref_order', __( 'Order', 'alvaro'));
        $crud->display_as('ref_author', __( 'Beautican', 'alvaro'));
        $crud->display_as('commission_percentage', __( 'Commission Percentage', 'alvaro'));
        $crud->display_as('commission_amount', __( 'Commission Amount', 'alvaro'));
        $crud->display_as('date_creation', __( 'Created on', 'alvaro'));

        $this->load->model( 'Nexo_Misc' );
        $Nexo_Misc      =   $this->Nexo_Misc;
        $this->events->add_filter( 'grocery_filter_row', function( $row ) use( $Nexo_Misc ) {
            $row->commission_percentage     .=   ' %';
            $row->commission_amount         =   $Nexo_Misc->cmoney_format( $row->commission_amount );
            return $row;
        });

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->unset_jquery();
        $output = $crud->render();

        foreach ($output->js_files as $files) {
            $this->enqueue->js(substr($files, 0, -3), '');
        }
        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }

        return $output;
    }

    /**
     *  Commissions
     *  @param int page
     *  @return void
    **/

    public function commissions( $page = 0 )
    {
        $data[ 'crud_content' ]    =    $this->crud_header();
        $this->Gui->set_title( store_title( __( 'Commissions', 'alvaro' ) ) );
        $this->load->module_view( 'alvaro', 'commissions_gui', $data );
    }

    public function settings()
    {
        $this->Gui->set_title( store_title( __( 'Alvaro Settings', 'alvaro' ) ) );
        $this->load->module_view( 'alvaro', 'settings' );
    }

    /**
     *  calendar controller
     *  @param void
     *  @return void
    **/

    public function appointment()
    {
        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'alvaro', 'appointment_footer', [
                'Alvaro_Library'    =>  new Alvaro_Library
            ]);
        });
        $this->Gui->set_title( __( 'Calendar', 'alvaro' ) );
        $this->load->module_view( 'alvaro', 'calendar' );
    }

    /**
     *  Reason Listing
     *  @param void
     *  @return void
    **/

    private function delete_log_crud()
    {
        /**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject( __( 'Deleting Log', 'alvaro' ) );
        $crud->set_table($this->db->dbprefix( store_prefix() . 'alvaro_log') );
        $crud->unset_add();
        $crud->unset_export();
        $crud->unset_delete();

		// $fields				=	array( 'TITRE', 'DESCRIPTION' );
		$crud->columns( 'title', 'description', 'author', 'date_creation' );
        // $crud->fields( $fields );
        $crud->set_relation( 'author', 'aauth_users', 'name' );
        $crud->order_by( 'id', 'desc' );

        $crud->display_as( 'title', __( 'Reason', 'alvaro') );
        $crud->display_as( 'author', __( 'Author', 'alvaro') );
        $crud->display_as( 'description', __( 'Reason details', 'alvaro') );
        $crud->display_as( 'date_creation', __( 'Created on', 'alvaro') );

        $this->load->model( 'Nexo_Misc' );

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->unset_jquery();
        $output = $crud->render();

        foreach ($output->js_files as $files) {
            $this->enqueue->js(substr($files, 0, -3), '');
        }
        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }

        return $output;
    }

    /**
     *  Log
     *  @param void
     *  @return void
    **/

    public function log()
    {
        $data[ 'crud_content' ]    =    $this->delete_log_crud();
        $this->Gui->set_title( store_title( __( 'Log', 'alvaro' ) ) );
        $this->load->module_view( 'alvaro', 'alvaro_log', $data );
    }

}
