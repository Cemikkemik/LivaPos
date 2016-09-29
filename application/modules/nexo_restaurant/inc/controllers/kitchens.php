<?php
class Nexo_Restaurant_Kitchens_Controllers extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
		
	/**
	 * Kitchen controllers
	 * 
	**/
	
	public function index( $page = 'lists', $arg2 = null ) 
	{
		$data						=	array();
		
		if( $page == 'lists' ) {
			
			$data[ 'crud_content' ]    =    $this->kitchen_crud_header();
			
			if( $page == 'lists' && $arg2 != 'add' ) {	
				$this->Gui->set_title( sprintf( __( 'Kitchen &mdash; %s', 'nexo_restaurant'), get('core_signature')));
			} elseif( $page == 'lists' && $arg2 == 'add' ) {
				$this->Gui->set_title( sprintf( __( 'Create a new kitchen &mdash; %s', 'nexo_restaurant'), get('core_signature')));
			}
			
			$this->load->module_view( 'nexo_restaurant', 'dashboard/kitchens', $data );
		} elseif( $page == 'open' ) {
			
			$this->load->model( 'Nexo_Restaurant' );
			
			$data[ 'kitchen' ]		=	$this->Nexo_Restaurant->get_kitchen( $arg2 );
			
			if( ! $data[ 'kitchen' ] ) {
				redirect( array( 'dashboard', 'unable-to-find-item' ) );
			}
			
			$this->Gui->set_title( sprintf( __( 'Open Kitchen : %s &mdash; %s', 'nexo_restaurant'), $data[ 'kitchen' ][0][ 'NAME' ], get('core_signature')));
			$this->load->module_view( 'nexo_restaurant', 'dashboard/open-kitchen-gui', $data );
		}
	}
	
	/**
	 * Kitchen controller CRUD header
	**/
	
	private function kitchen_crud_header()
	{
		if (
            ! User::can('create_restaurant_kitchens')  &&
            ! User::can('edit_restaurant_kitchens') &&
            ! User::can('delete_restaurant_kitchens')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
		
		$this->load->model( 'Nexo_Restaurant' );
        
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Restaurant Kitchen', 'nexo_restaurant'));
		
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_kitchens'));
        $crud->columns( 'NAME', 'REF_CATEGORY', 'PRINTER', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        $crud->fields( 'NAME', 'REF_CATEGORY', 'PRINTER', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        
        $crud->order_by( 'DATE_CREATION', 'asc');
        
        $crud->display_as('NAME', __('Name', 'nexo_restaurant'));
		$crud->display_as('PRINTER', __('Assigned Printer', 'nexo_restaurant'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo_restaurant'));
        $crud->display_as('REF_CATEGORY', __('Category', 'nexo_restaurant'));
		$crud->display_as('AUTHOR', __('Author', 'nexo_restaurant'));
		$crud->display_as('DATE_CREATION', __('Created on', 'nexo_restaurant'));
		$crud->display_as('DATE_MOD', __('Edited on', 'nexo_restaurant'));
		
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
		$crud->set_relation( 'REF_CATEGORY', store_prefix() . 'nexo_categories', 'NOM' );
		
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
        $crud->add_action(__('Open Kitchen', 'nexo_restaurant'), '', site_url(array( 'dashboard', 'nexo_restaurant_kitchens', 'open' )) . '/', 'btn btn-success fa fa-sign-in');
                
        // $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        // $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
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
}