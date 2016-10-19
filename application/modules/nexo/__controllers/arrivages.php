<?php
class Nexo_Arrivages extends CI_Model
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
            ! User::can('create_shop_shippings')  &&
            ! User::can('edit_shop_shippings') &&
            ! User::can('delete_shop_shippings')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
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
        $crud->set_subject(__('Livraisons', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_arrivages' ) );
		
		// fields
		$fields			=	array( 'TITRE', 'FOURNISSEUR_REF_ID', 'DESCRIPTION' );
        $crud->columns('TITRE', 'FOURNISSEUR_REF_ID', 'DESCRIPTION');
        $crud->fields( $fields );
		
		$crud->set_relation('FOURNISSEUR_REF_ID', store_prefix() . 'nexo_fournisseurs', 'NOM');        
        $crud->order_by('TITRE', 'asc');
        
        $crud->display_as('TITRE', __('Nom de la livraison', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('FOURNISSEUR_REF_ID', __('Fournisseur', 'nexo'));
        
        // Liste des produits
        $crud->add_action(__('Etiquettes des articles', 'nexo'), '', site_url(array( 'dashboard', 'nexo', 'print', 'shipping_item_codebar' )) . '/', 'btn btn-success fa fa-file');
                
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields('TITRE', 'FOURNISSEUR_REF_ID');
        
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
		$PageNow			=	'nexo/arrivages/list';
		
        if ($page == 'index') {
            $this->Gui->set_title( store_title( __('Liste des livraisons', 'nexo')) ); 
        } elseif ($page == 'delete') { // Check Deletion permission

            nexo_permission_check('delete_shop_shippings');
            
            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_SHIPPING', 'table'    =>    store_prefix() . 'nexo_articles' )
            ));
        } else {
            $this->Gui->set_title( store_title( __('Ajouter une nouvelle livraison', 'nexo')) );
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'arrivages';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
		global $PageNow;
		$PageNow			=	'nexo/arrivages/add';
		
        if (! User::can('create_shop_shippings')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'arrivages';
        $this->Gui->set_title( store_title( __( 'Ajouter une nouvelle livraison', 'nexo') ) );
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
	}

}
new Nexo_Arrivages($this->args);
