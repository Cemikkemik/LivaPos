<?php
class Nexo_Restaurant_Tables_Controllers extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * POS In
	**/
	
	public function index( $page = 'lists', $arg2 = null )
	{
		$data[ 'crud_content' ]    =    $this->crud_header();
		
		if( $page == 'lists' && $arg2 != 'add' ) {	
			$this->Gui->set_title( sprintf( __( 'Restaurant Tables &mdash; %s', 'nexo_restaurant'), get('core_signature')));
		} elseif( $page == 'lists' && $arg2 == 'add' ) {
			$this->Gui->set_title( sprintf( __( 'Create new table &mdash; %s', 'nexo_restaurant'), get('core_signature')));
		}
		
		$this->load->module_view( 'nexo_restaurant', 'dashboard/tables', $data );
	}
	
	/**
	 * CRUD Header
	**/
	
	private function crud_header()
	{
		if (
			! User::can('create_restaurant_tables')  &&
			! User::can('edit_restaurant_tables') &&
			! User::can('delete_restaurant_tables')
		) {
			redirect(array( 'dashboard', 'access-denied' ));
		}
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->set_subject(__('Restaurant Tables', 'nexo_restaurant'));
	
		$crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_tables'));
		$crud->columns( 'NAME', 'MAX_SEATS', 'STATUS', 'REF_GROUP', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
		$crud->fields( 'NAME', 'MAX_SEATS', 'STATUS',  'REF_GROUP', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
		
		$crud->order_by( 'DATE_CREATION', 'asc');
		
		$crud->display_as('NAME', __('Table name', 'nexo_restaurant'));
		$crud->display_as('DESCRIPTION', __('Table description', 'nexo_restaurant'));
		$crud->display_as('REF_GROUP', __('Area', 'nexo_restaurant'));
		$crud->display_as('STATUS', __('Status', 'nexo_restaurant'));
		$crud->display_as('AUTHOR', __('Author', 'nexo_restaurant'));
		$crud->display_as('DATE_CREATION', __('Created on', 'nexo_restaurant'));
		$crud->display_as('DATE_MOD', __('Edited on', 'nexo_restaurant'));
		$crud->display_as( 'MAX_SEATS', __( 'Max Seats', 'nexo_restaurant' ) );
		
		$crud->set_relation('REF_GROUP', store_prefix() . 'nexo_restaurant_tables_groups', 'NAME');
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
		
		$crud->field_type('STATUS', 'dropdown', $this->config->item( 'nexo_restaurant_table_status') );
		$crud->field_type( 'DATE_CREATION', 'hidden' );
		$crud->field_type( 'DATE_MOD', 'hidden' );
		$crud->field_type( 'AUTHOR', 'invisible' );
		
		$this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
		$this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
		
		// Callback Before Render
		$crud->callback_before_insert(array( $this, 'callback_creating_table' ));
		$crud->callback_before_update(array( $this, 'callback_editing_table' ));
		
		$crud->required_fields( 'NAME', 'REF_GROUP', 'STATUS', 'MAX_SEATS' );
		
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
	 * Callback Creating table
	**/
	
	public function callback_creating_table( $post ) 
	{
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_CREATION' ]	=	date_now();
	
		return $post;
	}
	
	/**
	 * Callback Editing Table
	**/
	
	public function callback_editing_table( $post ) 
	{
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_MOD' ]			=	date_now();
	
		return $post;
	}
}