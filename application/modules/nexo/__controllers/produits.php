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

		/**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $this->load->model('Nexo_Products');
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Articles', 'nexo'));

		global $PageNow;
		$PageNow			=	'nexo/produits';


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

        $crud->display_as( 'DESIGN', __('Désignation', 'nexo'));
        $crud->display_as( 'SKU', __('UGS (Unité de gestion de stock)', 'nexo'));
        $crud->display_as( 'REF_RAYON', __('Assigner à un rayon', 'nexo'));
        $crud->display_as( 'REF_CATEGORIE', __('Assign à une catégorie', 'nexo'));
        $crud->display_as( 'REF_SHIPPING', __('Assign à un arrivage', 'nexo'));
        $crud->display_as( 'QUANTITY', __('Quantité Totale', 'nexo'));
        $crud->display_as( 'DEFECTUEUX', __('Quantité défectueuse', 'nexo'));
        $crud->display_as( 'FRAIS_ACCESSOIRE', __('Frais Accéssoires', 'nexo'));
        $crud->display_as( 'TAUX_DE_MARGE', __('Taux de marge', 'nexo'));
        $crud->display_as( 'PRIX_DE_VENTE', __('Prix de vente', 'nexo'));
        $crud->display_as( 'COUT_DACHAT', __("Cout d'achat", 'nexo'));
        $crud->display_as( 'HAUTEUR', __('Hauteur', 'nexo'));
        $crud->display_as( 'LARGEUR', __('Largeur', 'nexo'));
		$crud->display_as( 'TAILLE', __('Taille', 'nexo'));
        $crud->display_as( 'POIDS', __('Poids', 'nexo'));
        $crud->display_as( 'DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as( 'COULEUR', __('Couleur', 'nexo'));
        $crud->display_as( 'APERCU', __('Aperçu de l\'article', 'nexo'));
        $crud->display_as( 'CODEBAR', __('Codebarre', 'nexo'));
        $crud->display_as( 'PRIX_DACHAT', __('Prix d\'achat', 'nexo'));
        $crud->display_as( 'DATE_CREATION', __('Crée le', 'nexo'));
        $crud->display_as( 'DATE_MOD', __('Modifié le', 'nexo'));
        $crud->display_as( 'AUTHOR', __('Auteur', 'nexo'));
        $crud->display_as( 'QUANTITE_RESTANTE', __('Qte Rest.', 'nexo'));
        $crud->display_as( 'QUANTITE_VENDU', __('Qte Vendue.', 'nexo'));
        $crud->display_as( 'PRIX_PROMOTIONEL', __('Prix promotionnel', 'nexo'));
        $crud->display_as( 'SPECIAL_PRICE_START_DATE', __('Début de la promotion', 'nexo'));
        $crud->display_as( 'SPECIAL_PRICE_END_DATE', __('Fin de la promotion', 'nexo'));
		$crud->display_as( 'SHADOW_PRICE', __( 'Prix de vente fictif', 'nexo' ) );
		$crud->display_as( 'TYPE', __( 'Type d\'article', 'nexo' ) );
		$crud->display_as( 'STATUS', __( 'Etat de l\'article', 'nexo' ) );
		$crud->display_as( 'STOCK_ENABLED', __( 'Gestion de stock', 'nexo' ) );
		$crud->display_as( 'BARCODE_TYPE', __( 'Type de code barre', 'nexo' ) );
		$crud->display_as( 'AUTO_BARCODE', __( 'Générer une étiquette automatiquement', 'nexo' ) );

		$crud->field_description( 'AUTO_BARCODE', tendoo_info( __( 'Lorsque cette option est activée, Après la création/mise à jour de cet article, une étiquette sera générée en fonction du type de code barre. Si cette option est désactivée, alors le champ "Code barre" sera utilsiée pour générer l\'étiquette de l\'article. Assurez-vous de définir une valeur unique.', 'nexo' ) ) );

		$crud->field_description( 'BARCODE_TYPE', tendoo_info( __( 'Si la valeur de ce champ est vide et que l\'option "Générer une étiquette" est activée, alors le type de code barre utilisé sera celui des réglages des articles. Si aucun réglage n\'est défini, la génération de l\'étiquette sera ignorée.', 'nexo' ) ) );

		$crud->field_description( 'CODEBAR', tendoo_info( __( 'Si la valeur de ce champ est vide et que l\'option "Générer un étiquette" est activée, la génération d\'une étiquette sera ignorée.', 'nexo' ) ) );

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->required_fields( $this->events->apply_filters( 'product_required_fields', array(
			'DESIGN',
			'SKU',
			'REF_CATEGORIE',
			'REF_SHIPPING',
			// 'TAUX_DE_MARGE',
			// 'FRAIS_ACCESSOIRE',
			'PRIX_DE_VENTE',
			'DEFECTUEUX',
			'QUANTITY',
			'PRIX_DACHAT',
			'STATUS',
			'TYPE',
			'STOCK_ENABLED'
		) ) );

        $crud->set_field_upload('APERCU', get_store_upload_path() . '/items-images/' );

        $crud->set_rules('QUANTITY', __('Quantité Totale', 'nexo'), 'is_natural_no_zero');
        $crud->set_rules('DEFECTUEUX', __('Quantité Defectueuse', 'nexo'), 'numeric');
        $crud->set_rules('PRIX_DE_VENTE', __('Prix de vente', 'nexo'), 'numeric');
        $crud->set_rules('PRIX_DACHAT', __('Prix d\'achat', 'nexo'), 'numeric');
        $crud->set_rules('PRIX_PROMOTIONEL', __('Prix promotionnel', 'nexo'), 'numeric');
        $crud->set_rules('TAUX_DE_MARGE', __('Taux de marge', 'nexo'), 'numeric');
        $crud->set_rules('FRAIS_ACCESSOIRE', __('Frais Accessoires', 'nexo'), 'numeric');

        // Masquer le champ codebar
        // $crud->change_field_type('CODEBAR', 'invisible');
        $crud->change_field_type('COUT_DACHAT', 'invisible');
        $crud->change_field_type('QUANTITE_RESTANTE', 'invisible');
        $crud->change_field_type('QUANTITE_VENDU', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');
		// $crud->change_field_type( 'BARCODE_TYPE', 'invisible' );

		// @since 2.8.2
		$crud->field_type( 'TYPE', 'dropdown', $this->config->item('nexo_item_type'));
		$crud->field_type( 'STATUS', 'dropdown', $this->config->item('nexo_item_status'));
		$crud->field_type( 'STOCK_ENABLED', 'dropdown', $this->config->item('nexo_item_stock'));
		$crud->field_type( 'AUTO_BARCODE', 'dropdown', $this->config->item('nexo_yes_no' ) );
		$crud->field_type( 'BARCODE_TYPE', 'dropdown', $this->config->item( 'nexo_barcode_supported' ) );

        // Callback Before Render
        $crud->callback_before_insert(array( 	$this->Nexo_Products, 'product_save' ) );
		$crud->callback_after_insert( array( 	$this->Nexo_Products, 'product_after_save' ) );
		$crud->callback_before_update( array( 	$this->Nexo_Products, 'product_update' ) );
		$crud->callback_after_update( array( 	$this->Nexo_Products, 'product_after_update' ) );
		$crud->callback_before_delete( array( 	$this->Nexo_Products, 'product_before_delete' ) );

		$crud		=	$this->events->apply_filters( 'load_product_crud', $crud );

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
		global $PageNow;
		$PageNow			=	'nexo/produits/list';

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
		global $PageNow;
		$PageNow			=	'nexo/produits/add';

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

    /**
     *  Add Items V2
     *  @param
     *  @return
    **/

    public function create()
    {
        $this->load->model( 'Nexo_Shipping' );
        $this->load->model( 'Nexo_Categories' );

        $data                   =   [];
        $data[ 'shippings' ]    =   $this->Nexo_Shipping->get_shipping();
        $data[ 'categories' ]   =   $this->Nexo_Categories->get();
        $data[ 'providers' ]    =   $this->Nexo_Shipping->get_providers();

        // Load Script
        $this->events->add_action( 'dashboard_footer', function() use ( $data ){
            get_instance()->load->module_view( 'nexo', 'items/add_angular', $data );
        });

        // Wrapper Attributes
        $this->events->add_filter( 'gui_wrapper_attrs', function( $attrs ){
            $attrs      .=   ' ng-controller="nexoItems" ng-cloak';
            return $attrs;
        });

        // After Gui Cols
        $this->events->add_filter( 'gui_after_cols', function( $str ) {
            return '<loader class="ng-hide"></loader>';
        });

        // Dashboard Header
        $this->events->add_action( 'dashboard_header', function(){
            echo '<base href="' . site_url([ 'dashboard', 'nexo', 'produits', 'create' ]) . '"/>';
        });

        // Set title
        $this->Gui->set_title( store_title( __( 'Créer un nouveau produit', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'items/add_gui', $data );
    }

    /**
     *  Template
     *  @param string template name
     *  @return string template view
    **/

    public function template( $name )
    {
        return $this->load->module_view( 'nexo', 'items/templates/' . $name );
    }
}
new Nexo_Produits($this->args);
