<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/inc/controller.php' );
include_once( dirname( __FILE__ ) . '/inc/install.php' );
include_once( dirname( __FILE__ ) . '/inc/assets.php' );

class Alvaro_Module extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->setup    =   new Alvaro_Install;
        $this->assets   =   new Alvaro_Assets;
        $this->events->add_action( 'load_dashboard', [ $this, 'load_dashboard' ] );
        $this->events->add_filter( 'admin_menus', [ $this, 'admin_menus' ], 99 );
        $this->events->add_action( 'do_enable_module', [ $this->setup, 'install' ] );
        $this->events->add_action( 'do_remove_module', [ $this->setup, 'uninstall' ] );
        $this->events->add_action( 'dashboard_footer', [ $this, 'checkout_footer' ] );
        $this->events->add_action( 'nexo_after_install_tables', [ $this->setup, 'install_store_tables' ] );
        $this->events->add_action( 'delete_nexo_store', [ $this->setup, 'delete_store' ] );
        $this->events->add_action( 'nexo_delete_order', [ $this, 'delete_order' ] );
        $this->events->add_filter( 'grocery_registered_fields', array( $this, 'add_fields' ) );
		$this->events->add_filter( 'grocery_edit_fields', array( $this, 'add_fields' ) );
        $this->events->add_action( 'nexo_after_save_product', array( $this, 'save_item' ), 10, 2 );
		$this->events->add_action( 'nexo_after_update_product', array( $this, 'update_item' ), 10, 2 );
        $this->events->add_filter( 'load_product_crud', array( $this, 'crud_load' ) );
        $this->events->add_filter( 'grocery_input_fields', array( $this, 'input_fields' ) );
        $this->events->add_filter( 'dashboard_dependencies', [ $this, 'dependencies' ] );
        $this->events->add_filter( 'saveorder_title_field', '__return_false' );
        $this->events->add_filter( 'saveorder_confirm_condition', [ $this, 'saveorder_conditions' ] );
        $this->events->add_filter( 'order_history_title', [ $this, 'order_history_title' ] );
    }

    public function add_fields( $fields )
	{
		global $PageNow;

		if( $PageNow	==	'nexo/produits' ) {

			$field_commission	=	new stdClass;
			$field_commission->field_name		=	'COMMISSION';
			$field_commission->display_as		=	__( 'Commission', 'alvaro' );
			$field_commission->description		=	tendoo_info( __( 'Let you define a percentage of commission for this item', 'alvaro' ) );

			$fields[]	=	$field_commission;

            $field_commission                   =	new stdClass;
			$field_commission->field_name		=	'TIME';
			$field_commission->display_as		=	__( 'Time', 'alvaro' );
			$field_commission->description		=	tendoo_info( __( 'Let you define how many time last a service in minutes.', 'alvaro' ) );

			$fields[]	=	$field_commission;
		}

		return $fields;
	}

    /**
     *  Admin Menus
     *  @param array admin menu
     *  @return array
    **/

    public function admin_menus( $menus )
    {
        if( multistore_enabled() && ! is_multistore() ) {
            return $menus;
        }

        $menus[ 'nexo_settings' ][]     =   [
            'title'     =>      __( 'Calendario Settings', 'alvaro' ),
            'href'      =>      site_url( [ 'dashboard', store_slug(), 'calendario/settings' ] )
        ];

        if( @$menus[ 'sales' ] ) {
            $menus  =   array_insert_after( 'sales', $menus, 'alvaro-commissions', array(
                array(
                    'title' =>  __( 'Commissions', 'alvaro' ),
                    'href'  =>  site_url( [ 'dashboard', store_slug(), 'calendario/commissions' ] ),
                    'icon'  =>  'fa fa-money'
                )
            ) );
        }

        if( @$menus[ 'caisse' ] ) {
            $menus  =   array_insert_after( 'caisse', $menus, 'alvaro-appointment', array(
                [
                    'title'     =>  __( 'Place an Appoinment', 'alvaro' ),
                    'icon' => 'fa fa-calendar',
                    'href'       =>  site_url([ 'dashboard', store_slug(), 'calendario/appointment' ] )
                ]
            ) );
        }

        if( @$menus[ 'rapports' ] ) {
            $menus  =   array_insert_after( 'rapports', $menus, 'alvaro-log', array(
                [
                    'title'     =>  __( 'Delete Log', 'alvaro' ),
                    'icon'      => 'fa fa-user-secret',
                    'href'      =>  site_url([ 'dashboard', store_slug(), 'calendario/log' ] )
                ]
            ) );
        }

        return $menus;
    }

    /**
     *  Checkout Footer
     *  @param void
     *  @return void
    **/

    public function checkout_footer()
    {
        $this->load->module_view( 'alvaro', 'checkout_footer' );
    }

    /**
	 * Crud Load
	**/

	public function crud_load( $crud )
	{
		$crud->add_group( 'np_options', __( 'Services Setup', 'nexo-playground-manager' ), array( 'COMMISSION', 'TIME' ), 'fa-star' );
		return $crud;
	}

    /**
     *  load Dependencies
     *  @param
     *  @return
    **/

    public function dependencies( $dependencies )
    {
        $dependencies[]     =   'mwl.calendar';
        $dependencies[]     =   'ui.bootstrap';
        $dependencies[]     =   'ngTouch';

        ( ! in_array( 'ui.bootstrap.datetimepicker', $dependencies ) ? $dependencies[]  =   'ui.bootstrap.datetimepicker' : '' );
        return $dependencies;
    }

    /**
     * Delete order
     * @param int order id
    **/

    public function delete_order( $order_id )
    {
        $this->db->where( 'ref_order', $order_id )->delete( store_prefix() . 'alvaro_appointments' );
    }

    /**
	 * Input Fields
	 * @return object
	**/

	public function input_fields( $input_fields )
	{
		global $PageNow;

		if( $PageNow	==	'nexo/produits' ) {

			if( is_multistore() ) {
				$id			=		$this->uri->segment(8);
			} else {
				$id			=		$this->uri->segment(6);
			}

			$COMMISSION		=		null;

            if( $id ) {
				$data		=		$this->db->where( 'REF_ARTICLE', $id )
				->where( 'KEY', 'COMMISSION' )
				->get( store_prefix() . 'nexo_articles_meta' )
				->result_array();

				$COMMISSION	=		@$data[0][ 'VALUE' ];
			}

			$input_fields[ 'COMMISSION' ]					=	new stdClass;
			$input_fields[ 'COMMISSION' ]->name				=	'COMMISSION';
			$input_fields[ 'COMMISSION' ]->type				=	'varchar';
			$input_fields[ 'COMMISSION' ]->max_length		=	200;
			$input_fields[ 'COMMISSION' ]->primary_key		=	0;
			$input_fields[ 'COMMISSION' ]->default			=	null;
			$input_fields[ 'COMMISSION' ]->db_max_length	=	11;
			$input_fields[ 'COMMISSION' ]->db_type			=	'varchar';
			$input_fields[ 'COMMISSION' ]->db_null			=	false;
			$input_fields[ 'COMMISSION' ]->required			=	true;
			$input_fields[ 'COMMISSION' ]->display_as		=	__( 'Commission', 'alvaro' );
			$input_fields[ 'COMMISSION' ]->crud_type		=	false;
			$input_fields[ 'COMMISSION' ]->extras			=	false;
			$input_fields[ 'COMMISSION' ]->input			=	'<input name="COMMISSION" class="form-control" value="' . $COMMISSION . '">';

            $TIME		=		null;

            if( $id ) {
				$data		=		$this->db->where( 'REF_ARTICLE', $id )
				->where( 'KEY', 'TIME' )
				->get( store_prefix() . 'nexo_articles_meta' )
				->result_array();

				$TIME	=		@$data[0][ 'VALUE' ];
			}

			$input_fields[ 'TIME' ]					=	new stdClass;
			$input_fields[ 'TIME' ]->name				=	'TIME';
			$input_fields[ 'TIME' ]->type				=	'varchar';
			$input_fields[ 'TIME' ]->max_length		=	200;
			$input_fields[ 'TIME' ]->primary_key		=	0;
			$input_fields[ 'TIME' ]->default			=	null;
			$input_fields[ 'TIME' ]->db_max_length	=	11;
			$input_fields[ 'TIME' ]->db_type			=	'varchar';
			$input_fields[ 'TIME' ]->db_null			=	false;
			$input_fields[ 'TIME' ]->required			=	true;
			$input_fields[ 'TIME' ]->display_as		=	__( 'Time', 'alvaro' );
			$input_fields[ 'TIME' ]->crud_type		=	false;
			$input_fields[ 'TIME' ]->extras			=	false;
			$input_fields[ 'TIME' ]->input			=	'<input name="TIME" class="form-control" value="' . $TIME . '">';

		}

		return $input_fields;
	}

    /**
     *  Load Dashboard
     *  @param
     *  @return
    **/

    public function load_dashboard()
    {
        $this->Gui->register_page_object( 'calendario', new Alvaro_Controller );
        $this->events->add_filter( 'stores_controller_callback', function( $controller ) {
            $controller[ 'calendario' ]     =  new Alvaro_Controller;
            return $controller;
        });
        $this->assets->load();

        if( ! User::in_group( 'master' ) ) {
            // Access checker
            if( in_array( $this->uri->segment(3), [ 'users_control', 'store-settings' ] ) ) {
                redirect([ 'dashboard', 'access-denied' ] );
            }

            if( $this->uri->uri_string() == 'dashboard/nexo/stores/lists' ) {
                redirect([ 'dashboard', 'access-denied' ] );
            }

            $this->events->add_filter( 'nexo_store_menus', '__return_false' );

            if( ! is_multistore() && multistore_enabled() && $this->uri->uri_string() != 'dashboard/users/profile' ) {
                global $Options;
                // Control store access
                $this->load->model( 'Nexo_Stores' );
                $stores		=	$this->Nexo_Stores->get( 'opened', 'STATUS' );
                foreach( $stores as $store ) {
                    $access     =   @$Options[ 'store_access_' . User::id() . '_' . $store[ 'ID' ] ];
                    if( ! in_array( $access, [ null, 'no' ] ) ) {
                        return redirect([ 'dashboard', 'stores', $store[ 'ID' ] ] );
                        exit;
                    }
                }
                return redirect([ 'dashboard', 'users', 'profile' ]);
            }

            $this->events->add_filter( 'admin_menus', function( $menus ) {
                unset( $menus[ 'nexo_store_settings' ] );
                unset( $menus[ 'settings' ] );
                unset( $menus[ 'users' ][0] );
                unset( $menus[ 'elfinder' ] );

                $menus[ 'nexo_shop' ]   =   [
                    [
                        'title'     =>  __( 'Open Store', 'alvaro' ),
                        'icon'      =>  'fa fa-cubes',
                        'href'      =>  site_url([ 'dashboard' ] )
                    ]
                ];

                return $menus;
            }, 99 );
        }

    }

    /**
     *  order_history_title
     *  @param  string order title
     *  @return  string new order title
    **/

    public function order_history_title( $string )
    {
        return '{{ value.TITRE | titleFilter }}';
    }

    /**
    *  Save Item
    *  @param array
    *  @param int
    *  @return void
    **/

    public function save_item( $array, $id )
	{
		$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'COMMISSION' )
		->insert( store_prefix() . 'nexo_articles_meta', array(
			'DATE_CREATION'		=>	date_now(),
			'KEY'				=>	'COMMISSION',
			'VALUE'				=>	$array[ 'COMMISSION' ],
			'REF_ARTICLE'		=>	$id
		) );

        $this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'TIME' )
		->insert( store_prefix() . 'nexo_articles_meta', array(
			'DATE_CREATION'		=>	date_now(),
			'KEY'				=>	'TIME',
			'VALUE'				=>	$array[ 'TIME' ],
			'REF_ARTICLE'		=>	$id
		) );
    }

    /**
	 * While Updating item
	 * @param array item details
	 * @param int order id
	 * @return void
	**/

	public function update_item( $array, $id )
	{
		$query		=	$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'COMMISSION' )
		->get( store_prefix() . 'nexo_articles_meta' );

		if( ! $query->result_array() ) {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'COMMISSION' )
			->insert( store_prefix() . 'nexo_articles_meta', array(
				'DATE_CREATION'		=>	date_now(),
				'KEY'				=>	'COMMISSION',
				'VALUE'				=>	$array[ 'COMMISSION' ],
				'REF_ARTICLE'		=>	$id
			) );
		} else {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'COMMISSION' )
			->update( store_prefix() . 'nexo_articles_meta', array(
				'DATE_MOD'		=>	date_now(),
				'VALUE'			=>	$array[ 'COMMISSION' ]
			) );
		}

        $query		=	$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'TIME' )
		->get( store_prefix() . 'nexo_articles_meta' );

		if( ! $query->result_array() ) {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'TIME' )
			->insert( store_prefix() . 'nexo_articles_meta', array(
				'DATE_CREATION'		=>	date_now(),
				'KEY'				=>	'TIME',
				'VALUE'				=>	$array[ 'TIME' ],
				'REF_ARTICLE'		=>	$id
			) );
		} else {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'TIME' )
			->update( store_prefix() . 'nexo_articles_meta', array(
				'DATE_MOD'		=>	date_now(),
				'VALUE'			=>	$array[ 'TIME' ]
			) );
		}
    }

    /**
     *  Save Order Conditions
     *  @param string conditions
     *  @return string new conditions
    **/

    public function saveorder_conditions( $conditions )
    {
        return $conditions . ' && $scope.orderName.length > 0';
    }

}
new Alvaro_Module;
