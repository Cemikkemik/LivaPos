<?php
class Nexo_Categories extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->defaults();
            }
        }
        return $this->defaults();
    }
    
    public function crud_header()
    {
        if (
            ! User::can('edit_shop_providers')    &&
            ! User::can('create_shop_providers')    &&
            ! User::can('delete_shop_providers')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
		
		/**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/
		
		if( ( multistore_enabled() && ! is_multistore() ) && $this->events->add_filter( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}
        
        $crud = new grocery_CRUD();
        $crud->set_subject(__('Fournisseurs', 'nexo'));
        $crud->set_theme('bootstrap');
        // $crud->set_theme( 'bootstrap' );
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_fournisseurs'));
		
		// If Multi store is enabled
		// @since 2.8		
		$fields					=	array( 'NOM', 'BP', 'TEL', 'EMAIL', 'DESCRIPTION' );
		
		$crud->columns('NOM', 'BP', 'TEL', 'EMAIL', 'DESCRIPTION');
        $crud->fields( $fields );
        
        $crud->display_as('NOM', __('Nom du fournisseur', 'nexo'));
        $crud->display_as('EMAIL', __('Email du fournisseur', 'nexo'));
        $crud->display_as('BP', __('BP du fournisseur', 'nexo'));
        $crud->display_as('TEL', __('Tel du fournisseur', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description du fournisseur', 'nexo'));
        
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields('NOM');
        
        $crud->set_rules('EMAIL', 'Email', 'valid_email');
        // $crud->columns('customerName','phone','addressLine1','creditLimit');

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
    
    public function lists($page = 'index', $id = null)
    {
		global $PageNow;
		$PageNow			=	'nexo/fournisseurs/list';
		
        if ($page == 'index') {
            $this->Gui->set_title( store_title( __('Liste des fournisseurs', 'nexo' ) ) );
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_providers');
            
            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'FOURNISSEUR_REF_ID', 'table'    =>    store_prefix() . 'nexo_arrivages' )
            ));
        } else {
            $this->Gui->set_title( store_title( __( 'Ajouter un nouveau fournisseur', 'nexo') ) );
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'fournisseurs';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
		global $PageNow;
		$PageNow			=	'nexo/fournisseurs/add';
		
        if (! User::can('create_shop_providers')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'fournisseurs';
        $this->Gui->set_title( store_title( __('Ajouter un nouveau fournisseur', 'nexo' ) ) );
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Categories($this->args);
