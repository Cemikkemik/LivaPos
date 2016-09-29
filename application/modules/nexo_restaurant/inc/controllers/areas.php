<?php
class Nexo_Restaurant_Areas_Controllers extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
		
	/**
	 * Area
	**/
	
	public function index( $page = 'lists', $arg2 = null ) 
	{
		$data[ 'crud_content' ]    =    $this->area_crud_header();
		
		if( $page == 'lists' && $arg2 != 'add' ) {	
			$this->Gui->set_title( sprintf( __( 'Tables areas &mdash; %s', 'nexo_restaurant'), get('core_signature')));
		} elseif( $page == 'lists' && $arg2 == 'add' ) {
			$this->Gui->set_title( sprintf( __( 'Create new area &mdash; %s', 'nexo_restaurant'), get('core_signature')));
		}
		
		$this->load->module_view( 'nexo_restaurant', 'dashboard/areas', $data );
	}
	
	
	
	/**
	 * Area CRUD header
	**/
	
	private function area_crud_header()
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
        $crud->set_subject(__('Restaurant area', 'nexo_restaurant'));

        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_tables_groups'));
        $crud->columns( 'NAME', 'REF_PARENT', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        $crud->fields( 'NAME', 'REF_PARENT', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        
        $crud->order_by( 'DATE_CREATION', 'asc');
        
        $crud->display_as('NAME', __('Area name', 'nexo_restaurant'));
        $crud->display_as('DESCRIPTION', __('Table description', 'nexo_restaurant'));
        $crud->display_as('REF_PARENT', __('Area parent', 'nexo_restaurant'));
		$crud->display_as('AUTHOR', __('Author', 'nexo_restaurant'));
		$crud->display_as('DATE_CREATION', __('Created on', 'nexo_restaurant'));
		$crud->display_as('DATE_MOD', __('Edited on', 'nexo_restaurant'));
		
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
		$crud->set_relation( 'REF_PARENT', store_prefix() . 'nexo_restaurant_tables_groups', 'NAME' );
		
		$crud->field_type( 'DATE_CREATION', 'hidden' );
		$crud->field_type( 'DATE_MOD', 'hidden' );
		$crud->field_type( 'AUTHOR', 'invisible' );
		
		// Callback Before Render
        $crud->callback_before_insert(array( $this, 'callback_creating_table_area' ));
        $crud->callback_before_update(array( $this, 'callback_editing_table_area' ));
      
        // Liste des produits
        // $crud->add_action(__('Etiquettes des articles', 'nexo_restaurant'), '', site_url(array( 'dashboard', 'nexo_restaurant', 'print', 'shipping_item_codebar' )) . '/', 'btn btn-success fa fa-file');
                
        // $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        // $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields( 'NAME' );
        
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
	 * Callback for creating table area
	 * @params Array POST data
	 * @return Array POST data
	**/
	
	public function callback_creating_table_area( $post ) 
	{
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_CREATION' ]	=	date_now();

		return $post;
	}
	
	/**
	 * Callback for editing table area
	 * @params Array POST Data
	 * @return Array POST data
	**/
	
	public function callback_editing_table_area( $post ) 
	{
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_MOD' ]			=	date_now();

		return $post;
	}
}