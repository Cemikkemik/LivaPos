<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Nexo_Restaurant_Controller extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->load->module_config( 'nexo-restaurant' );
    }

    /**
     *  index
     *  @param
     *  @return
    **/

    public function index()
    {
        $this->tables();
    }

    /**
     *  Tables Header
     *  @param  void
     *  @return void
    **/

    private function __tables_crud()
    {
        if (
            ! User::can('create_restaurant_tables')  &&
            ! User::can('edit_restaurant_tables') &&
            ! User::can('delete_restaurant_tables')
        ) {
            // redirect(array( 'dashboard', 'access-denied' ));
        }

        /**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();

        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Tables', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_restaurant_tables' ) );

        $fields					=	array( 'NAME', 'MAX_SEATS', 'STATUS',  'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR', 'DESCRIPTION' );
        $required_fields        =   [ 'NAME', 'STATUS', 'REF_AREA' ];

        if( store_option( 'disable_area_rooms' ) != 'yes' ) {
            array_splice( $fields, 1, 0, 'REF_AREA' );
            $required_fields[]  =   'MAX_SEATS';
        }

		$crud->columns( 'NAME', 'MAX_SEATS', 'STATUS', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR' );
        
        $crud->fields( $fields );

        $crud->display_as('NAME', __('Name', 'nexo-restaurant'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('STATUS', __('Status', 'nexo'));
        $crud->display_as('AUTHOR', __('Author', 'nexo'));
        $crud->display_as('MAX_SEATS', __('Maximum Seats', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Created On', 'nexo'));
        $crud->display_as('DATE_MODIFICATION', __('Edited On', 'nexo'));
        $crud->display_as('REF_AREA', __('Area', 'nexo'));

        $crud->field_type( 'STATUS', 'dropdown', $this->config->item( 'nexo-restaurant-table-status-for-crud' ) );

        $crud->field_type( 'DATE_MODIFICATION', 'hidden' );
        $crud->field_type( 'DATE_CREATION', 'hidden' );
        $crud->field_type( 'AUTHOR', 'hidden' );

        $crud->set_relation('REF_AREA', store_prefix() . 'nexo_restaurant_areas', 'NAME');

        $crud->required_fields( $required_fields );

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
     *  Tables
     *  @param
     *  @return
    **/

    public function tables()
    {
        $this->Gui->set_title( store_title( __( 'Tables Lists', 'nexo-restaurant' ) ) );
        $data[ 'crud_content' ]    =    $this->__tables_crud();
        $this->load->module_view( 'nexo-restaurant', 'table-list-gui', $data );
    }

    /**
     *  table Selection
     *  @param void
     *  @return void
    **/

    public function templates( $template )
    {
        return $this->load->module_view( 'nexo-restaurant', 'templates.' . $template );
    }

    /**
     *  tables Area CRud
     *  @param
     *  @return
    **/

    private function __areas_crud()
    {
        if (
            ! User::can('create_restaurant_areas')  &&
            ! User::can('edit_restaurant_areas') &&
            ! User::can('delete_restaurant_areas')
        ) {
            // redirect(array( 'dashboard', 'access-denied' ));
        }

        /**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Areas', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_restaurant_areas' ) );

        $fields					=	array( 'NAME', 'REF_ROOM', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR', 'DESCRIPTION' );

		$crud->columns( 'NAME', 'REF_ROOM', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR' );
        $crud->fields( $fields );

        $crud->display_as('NAME', __('Name', 'nexo-restaurant'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('AUTHOR', __('Author', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Created On', 'nexo'));
        $crud->display_as('DATE_MODIFICATION', __('Edited On', 'nexo'));
        $crud->display_as('REF_ROOM', __('Room', 'nexo'));

        $crud->field_type( 'DATE_MODIFICATION', 'hidden' );
        $crud->field_type( 'DATE_CREATION', 'hidden' );
        $crud->field_type( 'AUTHOR', 'hidden' );

        $crud->set_relation('REF_ROOM', store_prefix() . 'nexo_restaurant_rooms', 'NAME');

        $crud->required_fields('NAME', 'REF_ROOM' );

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
     *  Areas
     *  @param
     *  @return
    **/

    public function areas()
    {
        $this->Gui->set_title( store_title( __( 'Restaurant Areas', 'nexo-restaurant' ) ) );
        $data[ 'crud_content' ]    =    $this->__areas_crud();
        $this->load->module_view( 'nexo-restaurant', 'areas-list-gui', $data );
    }

    /**
     *  Rooms Crud
     *  @param
     *  @return
    **/

    private function __rooms_crud()
    {
        if (
            ! User::can('create_restaurant_areas')  &&
            ! User::can('edit_restaurant_areas') &&
            ! User::can('delete_restaurant_areas')
        ) {
            // redirect(array( 'dashboard', 'access-denied' ));
        }

        /**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Rooms', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_restaurant_rooms' ) );

        $fields					=	array( 'NAME', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR', 'DESCRIPTION' );

		$crud->columns( 'NAME', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR' );
        $crud->fields( $fields );

        $crud->display_as('NAME', __('Name', 'nexo-restaurant'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('AUTHOR', __('Author', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Created On', 'nexo'));
        $crud->display_as('DATE_MODIFICATION', __('Edited On', 'nexo'));
        $crud->display_as('REF_ROOM', __('Room', 'nexo'));

        $crud->field_type( 'DATE_MODIFICATION', 'hidden' );
        $crud->field_type( 'DATE_CREATION', 'hidden' );
        $crud->field_type( 'AUTHOR', 'hidden' );

        $crud->add_action(__('Watch Room orders', 'nexo_restaurant'), '', site_url(array( 'dashboard', 'nexo-restaurant', 'rooms_watch' )) . '/', 'btn btn-default fa fa-eye');

        $crud->required_fields('NAME', 'REF_ROOM' );

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
     *  Restaurant Rooms
     *  @param
     *  @return
    **/

    public function rooms()
    {
        $this->Gui->set_title( store_title( __( 'Restaurant Rooms', 'nexo-restaurant' ) ) );
        $data[ 'crud_content' ]    =    $this->__rooms_crud();
        $this->load->module_view( 'nexo-restaurant', 'areas-list-gui', $data );
    }

    /**
     *  Settings
     *  @param void
     *  @return void
    **/

    public function settings()
    {
        $this->Gui->set_title( store_title( __( 'Restaurant Settings', 'nexo-restaurant' ) ) );
        $this->load->module_view( 'nexo-restaurant', 'settings' );
    }

    public function kitchens( $page = 'lists', $arg2 = null )
	{
		$data						=	array();

		if( $page == 'lists' ) {

			$data[ 'crud_content' ]    =    $this->kitchen_crud_header();

			if( $page == 'lists' && $arg2 != 'add' ) {
				$this->Gui->set_title( sprintf( __( 'Kitchen &mdash; %s', 'nexo_restaurant'), get('core_signature')));
			} elseif( $page == 'lists' && $arg2 == 'add' ) {
				$this->Gui->set_title( sprintf( __( 'Create a new kitchen &mdash; %s', 'nexo_restaurant'), get('core_signature')));
			}

			$this->load->module_view( 'nexo-restaurant', 'kitchens', $data );

		} elseif( $page == 'open' ) {

			$this->load->model( 'Nexo_Restaurant' );

			$data[ 'kitchen' ]		=	$this->Nexo_Restaurant->get_kitchen( $arg2 );

			if( ! $data[ 'kitchen' ] ) {
				redirect( array( 'dashboard', 'unable-to-find-item' ) );
			}

			$this->Gui->set_title( sprintf( __( 'Open Kitchen : %s &mdash; %s', 'nexo_restaurant'), $data[ 'kitchen' ][0][ 'NAME' ], get('core_signature' ) ) );

			$this->load->module_view( 'nexo-restaurant', 'open-kitchen-gui', $data );

		} elseif( $page == 'watch' ) {
            // angular dependencies
            $this->events->add_filter( 'dashboard_dependencies', function( $array ) {
                $array[]    =   'angularMoment';
                return $array;
            });

            // enqueue new style
            $this->enqueue->js( 'bower_components/angular-moment/angular-moment.min', module_url( 'nexo-restaurant' ) );

            // Save Footer
            $this->events->add_action( 'dashboard_footer', function() {
                get_instance()->load->module_view( 'nexo-restaurant', 'watch-kitchen-script' );
            });

            $this->Gui->set_title( store_title( __( 'Watch Kitchen', 'nexo-restaurant' ) ) );
            $this->load->module_model( 'nexo-restaurant', 'Nexo_Restaurant_Kitchens' );

            $data[ 'kitchen' ]      =   $this->Nexo_Restaurant_Kitchens->get( $arg2 );

            $this->load->module_view( 'nexo-restaurant', 'watch-kitchen', $data );
        }
	}

	/**
	 * Kitchen controller CRUD header
	**/

	private function kitchen_crud_header()
	{
        if( store_option( 'disable_kitchen_screen' ) == 'yes' ) { 
            redirect([ 'dashboard', 'feature-disabled' ]);
        }

		// if (
        //     ! User::can('create_restaurant_kitchens')  &&
        //     ! User::can('edit_restaurant_kitchens') &&
        //     ! User::can('delete_restaurant_kitchens')
        // ) {
        //     redirect(array( 'dashboard', 'access-denied' ));
        // }

		$this->load->model( 'Nexo_Restaurant' );

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Restaurant Kitchen', 'nexo_restaurant'));

        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_kitchens'));
        $crud->columns( 'NAME', 'REF_ROOM', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        $crud->fields( 'NAME', 'REF_ROOM', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );

        $crud->order_by( 'DATE_CREATION', 'asc');

        $crud->display_as('NAME', __('Name', 'nexo_restaurant'));
		// $crud->display_as('PRINTER', __('Assigned Printer', 'nexo_restaurant'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo_restaurant'));
        // $crud->display_as('REF_CATEGORY', __('Category', 'nexo_restaurant'));
        $crud->display_as('REF_ROOM', __('Room', 'nexo_restaurant'));
		$crud->display_as('AUTHOR', __('Author', 'nexo_restaurant'));
		$crud->display_as('DATE_CREATION', __('Created on', 'nexo_restaurant'));
		$crud->display_as('DATE_MOD', __('Edited on', 'nexo_restaurant'));

        $crud->field_description( 'REF_ROOM', __( 'All order proceeded from that room will be send to that kitchen (even to that kitchen printer).', 'nexo-restaurant' ) );

		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
		$crud->set_relation( 'REF_CATEGORY', store_prefix() . 'nexo_categories', 'NOM' );
        $crud->set_relation( 'REF_ROOM', store_prefix() . 'nexo_restaurant_rooms', 'NAME' );

		$crud->field_type( 'DATE_CREATION', 'hidden' );
		$crud->field_type( 'DATE_MOD', 'hidden' );
		$crud->field_type( 'AUTHOR', 'invisible' );

		$printers			=	$this->Nexo_Restaurant->get_printer();
		$printers[ 'disabled' ]		=	__( 'Don\'t assign printer', 'nexo_restaurant' );

		$crud->field_type( 'PRINTER', 'dropdown', $printers );

		// Callback Before Render
        $crud->callback_before_insert(array( $this, 'callback_creating_kitchen' ));
        $crud->callback_before_update(array( $this, 'callback_editing_kitchen' ));

        // Liste des produits
        $crud->add_action(__('Open Kitchen', 'nexo_restaurant'), '', site_url(array( 'dashboard', store_slug(), 'nexo-restaurant', 'kitchens', 'watch' )) . '/', 'btn btn-success fa fa-sign-in');

        // $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        // $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

		$crud->required_fields( 'NAME', 'REF_ROOM' );

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
	 * Callback for creating kitchen
	 * @params Array POST data
	 * @return Array POST data
	**/

	public function callback_creating_kitchen( $post )
	{
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_CREATION' ]	=	date_now();

		return $post;
	}

	/**
	 * Callback for editing kitchen
	 * @params Array POST Data
	 * @return Array POST data
	**/

	public function callback_editing_kitchen( $post )
	{
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_MOD' ]			=	date_now();

		return $post;
	}

    /**
     *  Rooms Watch
     *  @param int room id
     *  @return void
    **/

    public function rooms_watch( $room_id )
    {
        $this->Gui->set_title( store_title( __( 'Watch Room', 'nexo-restaurant' ) ) );
        $this->load->module_view( 'nexo-restaurant', 'watch' );
    }

    /**
     *  Get orders
     *  @param  void
     *  @return array json
    **/

    public function get_orders()
    {
        $this->db
        ->select( '
        aauth_users.name as AUTHOR_NAME,
        ' . store_prefix() . 'nexo_commandes.CODE as CODE,
        ' . store_prefix() . 'nexo_commandes.TYPE as TYPE,
        ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
        ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE_CREATION,
        ' . store_prefix() . 'nexo_commandes.DATE_MOD as DATE_MOD,
        ' . store_prefix() . 'nexo_clients.NOM as CUSTOMER_NAME,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ORDER_ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "room_id"
        ) as ROOM_ID,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_rooms.NAME FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_rooms
            WHERE ROOM_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_rooms.ID
        ) as ROOM_NAME,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ORDER_ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "table_id"
        ) as TABLE_ID,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables.NAME FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables
            WHERE TABLE_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables.ID
        ) as TABLE_NAME,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ORDER_ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "area_id"
        ) as AREA_ID,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_areas.NAME FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_areas
            WHERE ROOM_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_areas.ID
        ) as AREA_NAME,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ORDER_ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "order_real_type"
        ) as REAL_TYPE' )
        
        ->from( store_prefix() . 'nexo_commandes' )
        ->join( store_prefix() . 'nexo_clients', store_prefix() . 'nexo_commandes.REF_CLIENT = ' . store_prefix() . 'nexo_clients.ID' )
        ->join( store_prefix() . 'nexo_commandes_meta', store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ' . store_prefix() . 'nexo_commandes.ID' )
        ->join( 'aauth_users', 'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR' )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', Carbon::parse( date_now() )->startOfDay()->toDateTimeString() )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', Carbon::parse( date_now() )->endOfDay()->toDateTimeString() );

        if( $this->input->get( 'from-room' ) != 0 ) {
            $this->db->where( store_prefix() . 'nexo_commandes_meta.VALUE', $this->input->get( 'from-room' ) )
            ->where( store_prefix() . 'nexo_commandes_meta.KEY', 'room_id' );
        }

        $this->db->or_where( '( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes.ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes.DATE_CREATION >= "' . Carbon::parse( date_now() )->startOfDay()->toDateTimeString() . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes.DATE_CREATION <= "' . Carbon::parse( date_now() )->endOfDay()->toDateTimeString() . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "order_real_type"
        ) = "takeaway"' );

        $this->db->group_by( store_prefix() . 'nexo_commandes.CODE' );
        
        $query    =    $this->db->order_by( store_prefix() . 'nexo_commandes.ID', 'desc' )
        ->get();

        $data   =   $query->result_array();

        if ( $data ) {
            foreach( $data as $key => $order ) {
                $sub_query        =    $this->db
                ->select('
                ' . store_prefix() . 'nexo_articles.CODEBAR as CODEBAR,
    			' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QTE_ADDED,
                ' . store_prefix() . 'nexo_commandes_produits.ID as COMMAND_PRODUCT_ID,
    			' . store_prefix() . 'nexo_articles.DESIGN as DESIGN,
                ' . store_prefix() . 'nexo_articles.REF_CATEGORIE as REF_CATEGORIE,
                ' . store_prefix() . 'nexo_commandes_produits.NAME as NAME,

                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_note"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as FOOD_NOTE,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_food_status"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as FOOD_STATUS,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "meal"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as MEAL,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_food_issue"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as FOOD_ISSUE,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "modifiers"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as MODIFIERS')
                ->from( store_prefix() . 'nexo_commandes')
                ->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner')
                ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'left')
                ->join( store_prefix() . 'nexo_commandes_produits_meta', store_prefix() . 'nexo_commandes_produits.ID = ' . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT', 'left' )
                ->group_by( store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT' )
                ->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $order[ 'CODE' ])
                ->get();

                $data[ $key ][ 'items' ]    =   $sub_query->result_array();
            }

            echo json_encode( $data );
            return;
        }
        echo json_encode( [ ] );
        return false;
    }

    /**
     * Modifiers Group header
    **/

    private function modifiers_header()
    {
        if (
            ! User::in_group( [ 'master', 'shop_manager', 'shop_tester', 'administrator' ] )
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_subject(__( 'Modifiers', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_modifiers'));

		$fields				=	array( 'NAME', 'REF_CATEGORY', 'DEFAULT', 'PRICE', 'IMAGE', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );

		$crud->set_theme('bootstrap');

        $crud->columns( 'NAME', 'REF_CATEGORY', 'PRICE', 'DEFAULT', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );
        $crud->fields( $fields );

        $crud->display_as( 'NAME', __( 'Name', 'nexo-restaurant' ) );
        $crud->display_as( 'REF_CATEGORY', __( 'Group', 'nexo-restaurant' ) );
        $crud->display_as( 'DEFAULT', __( 'Default', 'nexo-restaurant' ) );
        $crud->field_description( 'DEFAULT', tendoo_info( __( 'That is the default modifier which will be selected by default. If there is already a default modifiers, setting this as "default" will replace the previous default modifier.', 'nexo' ) ) );
        $crud->display_as( 'AUTHOR', __( 'Author', 'nexo-restaurant' ) );
        $crud->display_as( 'PRICE', __( 'Price', 'nexo-restaurant' ) );
        $crud->display_as( 'DATE_CREATION', __( 'Created On', 'nexo-restaurant' ) );
        $crud->display_as( 'DATE_MODIFICATION', __( 'Edited On', 'nexo-restaurant' ) );
        $crud->display_as( 'DESCRIPTION', __( 'Description', 'nexo-restaurant' ) );
        $crud->display_as( 'IMAGE', __( 'Thumb', 'nexo-restaurant' ) );
        
        $crud->set_field_upload('IMAGE', get_store_upload_path() . '/items-images/' );
    
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $crud->set_relation('REF_CATEGORY', store_prefix() . 'nexo_restaurant_modifiers_categories', 'NAME' );
        
        $options    =   [
            0   =>  __( 'No', 'nexo-restaurant' ),
            1   =>  __( 'Yes', 'nexo-restaurant' )
        ];

        // Load Field Type
        $crud->field_type( 'DEFAULT', 'dropdown', $options );

        // Callback avant l'insertion
        $crud->callback_before_insert(array( $this, '__modifiers_insert' ));
        $crud->callback_before_update(array( $this, '__modifiers_update' ));

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        // Field Visibility
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MODIFICATION', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');

        $crud->required_fields( 'NAME', 'REF_CATEGORY' );

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
     * Modifiers
     * @param int modifiers
    **/

    public function modifiers()
    {
        $data[ 'crud' ]     =   $this->modifiers_header();
        $this->Gui->set_title( store_title( __( 'Modifiers', 'nexo-restaurant' ) ) );
        $this->load->module_view( 'nexo-restaurant', 'modifiers.gui', $data );
    }

    public function __modifiers_insert( $data ) 
    {
        $data[ 'AUTHOR' ]           =   User::id();
        $data[ 'DATE_CREATION' ]    =   date_now();

        // change default modifeirs
        $defaults       =   $this->db
        ->where( 'REF_CATEGORY', $data[ 'REF_CATEGORY' ] )
        ->where( 'DEFAULT', 1 )
        ->get( store_prefix() . 'nexo_restaurant_modifiers' )
        ->result_array();

        // change current default.
        if( $defaults ) {
            $this->db->where( 'ID', $defaults[0][ 'ID' ] )
            ->update( store_prefix() . 'nexo_restaurant_modifiers', [
                'DEFAULT'   =>  0
            ]);
        }

        return $data;
    }

    /**
     * Update modifers
    **/

    public function __modifiers_update( $data, $primary_key )
    {
        $data[ 'AUTHOR' ]               =   User::id();
        $data[ 'DATE_MODIFICATION' ]    =   date_now();

        // change default modifeirs
        $defaults       =   $this->db
        ->where( 'REF_CATEGORY', $data[ 'REF_CATEGORY' ] )
        ->where( 'ID !=', $primary_key )
        ->where( 'DEFAULT', 1 )
        ->get( store_prefix() . 'nexo_restaurant_modifiers' )
        ->result_array();

        // change current default.
        if( $defaults ) {
            $this->db->where( 'ID', $defaults[0][ 'ID' ] )
            ->update( store_prefix() . 'nexo_restaurant_modifiers', [
                'DEFAULT'   =>  0
            ]);
        }

        return $data;
    }

    /**
     * Modifiers Group header
    **/

    private function modifiers_groups_header()
    {
        if (
            ! User::in_group( [ 'master', 'shop_manager', 'shop_tester', 'administrator' ] )
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_subject(__( 'Modifiers Groups', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_modifiers_categories'));

		$fields				=	array( 'NAME', 'FORCED', 'MULTISELECT', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );

		$crud->set_theme('bootstrap');

        $crud->columns( 'NAME', 'AUTHOR', 'FORCED', 'MULTISELECT', 'DATE_CREATION', 'DATE_MODIFICATION');
        $crud->fields( $fields );

        $crud->display_as( 'NAME', __( 'Name', 'nexo-restaurant' ) );
        $crud->display_as( 'FORCED', __( 'Forced Modifiers', 'nexo-restaurant' ) );
        $crud->field_description( 'FORCED', __( 'If enabled, will force modifier selection.', 'nexo-restaurant' ) );
        $crud->display_as( 'MULTISELECT', __( 'Multiselect', 'nexo-restaurant' ) );
        $crud->field_description( 'MULTISELECT', __( 'if enabled this will allow more than 1 modifiers per item.', 'nexo-restaurant' ) );
        $crud->display_as( 'AUTHOR', __( 'Author', 'nexo-restaurant' ) );
        $crud->display_as( 'DATE_CREATION', __( 'Created On', 'nexo-restaurant' ) );
        $crud->display_as( 'DATE_MODIFICATION', __( 'Edited On', 'nexo-restaurant' ) );
        $crud->display_as( 'DESCRIPTION', __( 'Description', 'nexo-restaurant' ) );
    
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $options    =   [
            0   =>  __( 'No', 'nexo-restaurant' ),
            1   =>  __( 'Yes', 'nexo-restaurant' )
        ];

        // Load Field Type
        $crud->field_type( 'MULTISELECT', 'dropdown', $options );
        $crud->field_type( 'FORCED', 'dropdown', $options );

        // Callback avant l'insertion
        $crud->callback_before_insert(array( $this, '__group_insert' ));
        $crud->callback_before_update(array( $this, '__group_update' ));

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        // Field Visibility
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MODIFICATION', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');

        $crud->required_fields( 'NAME', 'FORCED', 'MULTISELECT' );

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
     * Modifiers Groups
     * @param int modifiers
    **/

    public function modifiers_groups()
    {
        $data[ 'crud' ]     =   $this->modifiers_groups_header();
        $this->Gui->set_title( store_title( __( 'Modifiers Groups', 'nexo-restaurant' ) ) );
        $this->load->module_view( 'nexo-restaurant', 'modifiers.groups-gui', $data );
    }

    /**
     * Group Insert
    **/

    public function __group_insert( $data ) 
    {
        $data[ 'AUTHOR' ]           =   User::id();
        $data[ 'DATE_CREATION' ]    =   date_now();

        return $data;
    }

    /**
     * Group update
    **/

    public function __group_update( $data ) 
    {
        $data[ 'AUTHOR' ]               =   User::id();
        $data[ 'DATE_MODIFICATION' ]    =   date_now();

        return $data;
    }

    /**
     * NexoPOS restaurant Callback
     * 
     * @return void
    **/

    public function callback()
    {
        if( ! empty( @$_GET[ 'app_code' ] ) ) {
            // save app code
            $this->options->set( store_prefix() . 'nexopos_app_code', $_GET[ 'app_code' ], true );

            return redirect([ 'dashboard', store_slug(), 'nexo-restaurant', 'settings?notice=app_connected' ]); 
        }
        return redirect([ 'dashboard', 'error', '404' ]);
    }

    /**
     * Revoke a connection
     * @return void
    **/

    public function revoke()
    {
        global $Options;
        if( ! empty( $_GET[ 'app_code' ] ) ) {
            if( $_GET[ 'app_code' ] == @$Options[ store_prefix() . 'nexopos_app_code' ] ) {
                $this->options->delete( store_prefix() . 'nexopos_app_code' );
                return redirect([ 'dashboard', store_slug(), 'nexo-restaurant', 'settings?notice=app_code_deleted' ]); 
            }
        }
        return redirect([ 'dashboard', store_slug(), 'nexo-restaurant', 'settings?notice=unknow_app' ]); 
    }
}
