<?php
class Nexo_Produits extends CI_Model
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
            ! User::can('edit_shop_items') &&
            ! User::can('create_shop_items') &&
            ! User::can('delete_shop_items')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $this->load->model('Nexo_Products');
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Articles', 'nexo'));
		

        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_articles'));
		
		$details_fields		=	$this->config->item( 'nexo_item_details_group' );
		
		$crud->add_group( 'details', __( 'Identification', 'nexo' ), $details_fields, 'fa-tag' );
		
		$crud->add_group( 'stock', __( 'Inventaire', 'nexo' ), $this->config->item( 'nexo_item_stock_group' ), 'fa-archive' );
		
		$crud->add_group( 'price', __( 'Prix', 'nexo' ), $this->config->item( 'nexo_item_price_group' ), 'fa-money' );
		
		$crud->add_group( 'spec', __( 'Caractéristiques', 'nexo' ), $this->config->item( 'nexo_item_spec_group' ), 'fa-paint-brush' );
		
        $crud->columns( 
			'SKU', 
			'DESIGN', 
			'REF_CATEGORIE', 
			'REF_SHIPPING', 
			'TAILLE', 
			'QUANTITY', 
			'DEFECTUEUX', 
			'QUANTITE_RESTANTE', 
			'QUANTITE_VENDU', 
			'PRIX_DE_VENTE', 
			'SHADOW_PRICE', 
			'PRIX_PROMOTIONEL', 
			'TYPE', 
			'STATUS', 
			'CODEBAR'
		);
		
		$crud->set_relation('REF_RAYON', store_prefix() . 'nexo_rayons', 'TITRE');
		$crud->set_relation('REF_CATEGORIE', store_prefix() . 'nexo_categories', 'NOM');
		$crud->set_relation('REF_SHIPPING', store_prefix() . 'nexo_arrivages', 'TITRE');
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
        
        $crud->display_as('DESIGN', __('Désignation', 'nexo'));
        $crud->display_as('SKU', __('UGS (Unité de gestion de stock)', 'nexo'));
        $crud->display_as('REF_RAYON', __('Assigner à un rayon', 'nexo'));
        $crud->display_as('REF_CATEGORIE', __('Assign à une catégorie', 'nexo'));
        $crud->display_as('REF_SHIPPING', __('Assign à un arrivage', 'nexo'));
        $crud->display_as('QUANTITY', __('Quantité Totale', 'nexo'));
        $crud->display_as('DEFECTUEUX', __('Quantité défectueuse', 'nexo'));
        $crud->display_as('FRAIS_ACCESSOIRE', __('Frais Accéssoires', 'nexo'));
        $crud->display_as('TAUX_DE_MARGE', __('Taux de marge', 'nexo'));
        $crud->display_as('PRIX_DE_VENTE', __('Prix de vente', 'nexo'));
        $crud->display_as('COUT_DACHAT', __("Cout d'achat", 'nexo'));
        $crud->display_as('HAUTEUR', __('Hauteur', 'nexo'));
        $crud->display_as('LARGEUR', __('Largeur', 'nexo'));
		$crud->display_as('TAILLE', __('Taille', 'nexo'));
        $crud->display_as('POIDS', __('Poids', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('COULEUR', __('Couleur', 'nexo'));
        $crud->display_as('APERCU', __('Aperçu de l\'article', 'nexo'));
        $crud->display_as('CODEBAR', __('Codebarre', 'nexo'));
        $crud->display_as('PRIX_DACHAT', __('Prix d\'achat', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Crée le', 'nexo'));
        $crud->display_as('DATE_MOD', __('Modifié le', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
        $crud->display_as('QUANTITE_RESTANTE', __('Qte Rest.', 'nexo'));
        $crud->display_as('QUANTITE_VENDU', __('Qte Vendue.', 'nexo'));
        $crud->display_as('PRIX_PROMOTIONEL', __('Prix promotionnel', 'nexo'));
        $crud->display_as('SPECIAL_PRICE_START_DATE', __('Début de la promotion', 'nexo'));
        $crud->display_as('SPECIAL_PRICE_END_DATE', __('Fin de la promotion', 'nexo'));
		$crud->display_as( 'SHADOW_PRICE', __( 'Prix de vente fictif', 'nexo' ) );
		$crud->display_as( 'TYPE', __( 'Type d\'article', 'nexo' ) );
		$crud->display_as( 'STATUS', __( 'Etat de l\'article', 'nexo' ) );
		$crud->display_as( 'STOCK_ENABLED', __( 'Gestion de stock', 'nexo' ) );
		
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields( 
			'DESIGN', 
			'SKU', 
			'REF_CATEGORIE', 
			'REF_SHIPPING', 
			'TAUX_DE_MARGE', 
			'FRAIS_ACCESSOIRE', 
			'PRIX_DE_VENTE', 
			'DEFECTUEUX', 
			'QUANTITY', 
			'PRIX_DACHAT', 
			'STATUS', 
			'TYPE', 
			'STOCK_ENABLED'
		);

        $crud->set_field_upload('APERCU', 'public/upload/');
        
        $crud->set_rules('QUANTITY', __('Quantité Totale', 'nexo'), 'is_natural_no_zero');
        $crud->set_rules('DEFECTUEUX', __('Quantité Defectueuse', 'nexo'), 'numeric');
        $crud->set_rules('PRIX_DE_VENTE', __('Prix de vente', 'nexo'), 'numeric');
        $crud->set_rules('PRIX_DACHAT', __('Prix d\'achat', 'nexo'), 'numeric');
        $crud->set_rules('PRIX_PROMOTIONEL', __('Prix promotionnel', 'nexo'), 'numeric');
        $crud->set_rules('TAUX_DE_MARGE', __('Taux de marge', 'nexo'), 'numeric');
        $crud->set_rules('FRAIS_ACCESSOIRE', __('Frais Accessoires', 'nexo'), 'numeric');
        
        // Masquer le champ codebar
        $crud->change_field_type('CODEBAR', 'invisible');
        $crud->change_field_type('COUT_DACHAT', 'invisible');
        $crud->change_field_type('QUANTITE_RESTANTE', 'invisible');
        $crud->change_field_type('QUANTITE_VENDU', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');
		
		// @since 2.8.2
		$crud->field_type('TYPE', 'dropdown', $this->config->item('nexo_item_type'));
		$crud->field_type('STATUS', 'dropdown', $this->config->item('nexo_item_status'));
		$crud->field_type('STOCK_ENABLED', 'dropdown', $this->config->item('nexo_item_stock'));
        
        // Callback Before Render
        $crud->callback_before_insert(array( $this->Nexo_Products, 'product_save' ));
        $crud->callback_before_update(array( $this->Nexo_Products, 'product_update' ));

        $output = $crud->render();
        
        foreach ($output->js_files as $files) {
            if (! strstr($files, 'jquery-1.11.1.min.js')) {
                $this->enqueue->js(substr($files, 0, -3), '');
            }
        }
        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }
        
        return $output;
    }
    
    public function lists($page = 'index', $id = null)
    {
        if ($page == 'index') {
            $this->Gui->set_title( store_title( __('Liste des articles', 'nexo') ) );
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_items');
            
            $this->load->model('Nexo_Products');
            $product    =    $this->Nexo_Products->get_product($id);
            if ($product) {
                // Checks whether an item is in use before delete
                nexo_availability_check($product[0][ 'CODEBAR' ], array(
                    array( 'col'    =>    'REF_PRODUCT_CODEBAR', 'table'    =>   store_prefix() . 'nexo_commandes_produits' )
                ));
            }
        } else {
            $this->Gui->set_title( store_title( __( 'Ajouter un nouvel article', 'nexo' ) ) );
        }
            
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'articles';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
        // Protecting
        if (! User::can('create_shop_items')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'articles';
        $this->Gui->set_title( store_title( __( 'Ajouter un nouvel article', 'nexo' ) ) );
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Produits($this->args);
