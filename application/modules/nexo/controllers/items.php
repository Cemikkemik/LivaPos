<?php
class NexoItemsController extends CI_Model
{
    public function crud_header()
    {
        if( User::cannot( 'nexo.view.items' ) ) {
            return nexo_access_denied();
        }

        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'nexo', 'items.style' );
        });

        // @since 3.0.20
        /**
         * We can't allo stock modification from the item no more
        **/

        if( is_multistore() ) {
            $currentScreen      =   $this->uri->segment( 7 );
        } else {
            $currentScreen      =   $this->uri->segment( 5 );
        }

		/**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $this->load->model('Nexo_Products');
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Articles', 'nexo'));

		global $PageNow;
        $PageNow			=	'nexo/produits';
        
        $this->events->add_filter( 'grocery_crud_list_item_class', function( $class, $row ) {
            $item       =   get_instance()->db->where( 'ID', $row->ID )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

            if ( $item[0][ 'STATUS' ] == 2 ) {
                return 'not-available';
            }
            return $class;
        }, 10, 2 );


        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_articles'));

		$details_fields		=	$this->config->item( 'nexo_item_details_group' );

        if( $currentScreen == 'edit' ) {
            foreach( $details_fields as $key => $stock ) {
                if( in_array( $stock, [ 'REF_SHIPPING' ] ) ) {
                    unset( $details_fields[ $key ] );
                }
            }
        }

		$crud->add_group( 'details', __( 'Identification', 'nexo' ), $details_fields, 'fa-tag' );
        $item_stock     =   $this->config->item( 'nexo_item_stock_group' );
        
        if( $currentScreen == 'edit' ) {
            foreach( $item_stock as $key => $stock ) {
                if( in_array( $stock, [ 'QUANTITY', 'REF_PROVIDER' ] ) ) {
                    unset( $item_stock[ $key ] );
                }
            }
        }

		$crud->add_group( 'stock', __( 'Inventaire', 'nexo' ), $item_stock, 'fa-archive' );
        $price_group        =   $this->config->item( 'nexo_item_price_group' );

        if( $currentScreen == 'edit' ) {
            foreach( $price_group as $key => $stock ) {
                if( in_array( $stock, [ 'PRIX_DACHAT' ] ) ) {
                    unset( $price_group[ $key ] );
                }
            }
        }

		$crud->add_group( 'price', __( 'Prix', 'nexo' ), $price_group, 'fa-money' );

		$crud->add_group( 'spec', __( 'Caractéristiques', 'nexo' ), $this->config->item( 'nexo_item_spec_group' ), 'fa-paint-brush' );

        $columns        =   [
            'SKU',
			'DESIGN',
			'REF_CATEGORIE',
            'PRIX_DE_VENTE_TTC',
			// 'PRIX_DACHAT', 
            'REF_TAXE',
            'TAX_TYPE',
            'QUANTITE_RESTANTE',
			// 'QUANTITE_VENDU',
			// 'DEFECTUEUX',			
			'TYPE',
			'STATUS',
			// 'CODEBAR'
        ];

        $columns        =   $this->events->apply_filters( 'product_columns', $columns );

        $crud->columns(
			$columns 
		);

        $crud->callback_column( 'PRIX_DE_VENTE_TTC', function( $price ){
            return $this->Nexo_Misc->cmoney_format( $price, true );
        });

        $crud->callback_column( 'PRIX_DACHAT', function( $price ){
            return $this->Nexo_Misc->cmoney_format( $price, true );
        });
        
        $crud->callback_column( 'REF_TAXE', function( $tax ){
            return $tax == 0 ? __( 'Non défini' ) : $tax;
        });

		$crud->set_relation('REF_RAYON', store_prefix() . 'nexo_rayons', 'TITRE');
		$crud->set_relation('REF_CATEGORIE', store_prefix() . 'nexo_categories', 'NOM');
		$crud->set_relation('REF_SHIPPING', store_prefix() . 'nexo_arrivages', 'TITRE');
        $crud->set_relation('REF_PROVIDER', store_prefix() . 'nexo_fournisseurs', 'NOM');
        $crud->set_relation('REF_TAXE', store_prefix() . 'nexo_taxes', 'NAME');
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->display_as( 'REF_TAXE', __('Taxe', 'nexo'));
        $crud->display_as( 'PRIX_DE_VENTE_TTC', __('Prix de vente(*)', 'nexo') );
        $crud->display_as( 'DESIGN', __('Nom du produit', 'nexo'));
        $crud->display_as( 'SKU', __('UGS (Unité de gestion de stock)', 'nexo'));
        $crud->display_as( 'REF_RAYON', __('Département', 'nexo'));
        $crud->display_as( 'REF_CATEGORIE', __('Catégorie', 'nexo'));
        $crud->display_as( 'REF_SHIPPING', __('Arrivage', 'nexo'));
        $crud->display_as( 'QUANTITY', __('Quantité', 'nexo'));
        $crud->display_as( 'DEFECTUEUX', __('Défectueux', 'nexo'));
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
        $crud->display_as( 'QUANTITE_RESTANTE', __('Restant', 'nexo'));
        $crud->display_as( 'QUANTITE_VENDU', __('Vendue', 'nexo'));
        $crud->display_as( 'PRIX_PROMOTIONEL', __('Prix promotionnel', 'nexo'));
        $crud->display_as( 'SPECIAL_PRICE_START_DATE', __('Début de la promotion', 'nexo'));
        $crud->display_as( 'SPECIAL_PRICE_END_DATE', __('Fin de la promotion', 'nexo'));
		$crud->display_as( 'PRIX_DACHAT', __( 'Prix d\'achat', 'nexo' ) );
		$crud->display_as( 'TYPE', __( 'Type d\'article', 'nexo' ) );
		$crud->display_as( 'STATUS', __( 'Etat', 'nexo' ) );
		$crud->display_as( 'STOCK_ENABLED', __( 'Gestion de stock', 'nexo' ) );
		$crud->display_as( 'BARCODE_TYPE', __( 'Type de code barre', 'nexo' ) );
		$crud->display_as( 'AUTO_BARCODE', __( 'Générer une étiquette automatiquement', 'nexo' ) );
        // $crud->display_as( 'SHADOW_PRICE', __( 'Prix fictif', 'nexo' ) ); @deprecated
        $crud->display_as( 'REF_PROVIDER', __( 'Fournisseur', 'nexo' ) );
        $crud->display_as( 'TAX_TYPE', __('Type de taxe', 'nexo'));
        $crud->display_as( 'STOCK_ALERT', __('Activer les alertes pour le stock faible', 'nexo'));
        $crud->display_as( 'ALERT_QUANTITY', __('Seuil pour alerte', 'nexo'));
        $crud->display_as( 'ON_EXPIRE_ACTION', __('Action en cas d\'expiration', 'nexo'));
        $crud->display_as( 'ON_STOCK_LOW', __('Quand le stock est faible', 'nexo'));
        $crud->display_as( 'EXPIRATION_DATE', __('Date d\'expiration', 'nexo'));

		$crud->field_description( 'AUTO_BARCODE', __( 'Lorsque cette option est activée, Après la création/mise à jour de cet article, une étiquette sera générée en fonction du type de code barre. Si cette option est désactivée, alors le champ "Code barre" sera utilsiée pour générer l\'étiquette de l\'article. Assurez-vous de définir une valeur unique.', 'nexo' ) );
		$crud->field_description( 'BARCODE_TYPE', __( 'Si la valeur de ce champ est vide et que l\'option "Générer une étiquette" est activée, alors le type de code barre utilisé sera celui des réglages des articles. Si aucun réglage n\'est défini, la génération de l\'étiquette sera ignorée.', 'nexo' ) );
		$crud->field_description( 'CODEBAR', __( 'Si la valeur de ce champ est vide et que l\'option "Générer un étiquette" est activée, la génération d\'une étiquette sera ignorée.', 'nexo' ) );
        $crud->field_description( 'PRIX_DACHAT', __( 'Le prix d\'achat représente la valeur du produit à l\'achat. Cette valeur sera utile pour déterminé la marge des bénéfices.', 'nexo' ) );
        $crud->field_description( 'PRIX_DE_VENTE', __( 'Le prix de vente peut être différent du prix de vente affiché sur le point de vente. Sa valeur pourra varifier selon la taxe applicable sur le produit.', 'nexo' ) );
        // $crud->field_description( 'SHADOW_PRICE', __( 'Si vos clients ont la capacité de discuter les prix. Vous pouvez définir le prix fictif affiché. Le prix de vente sera considéré comme prix minimal du produit.', 'nexo' ) ); @deprecated
        $crud->field_description( 'PRIX_PROMOTIONEL', __( 'Le prix promotionnel est un prix de vente spécial applicable à un produit durant une période spécifique.', 'nexo' ) );
        $crud->field_description( 'QUANTITY', __( 'Il s\'agit ici de la quantité initiale qui sera considérée comme quantité d\'approvisionnement.', 'nexo' ) );
        $crud->field_description( 'REF_PROVIDER', __( 'Lorsque qu\'un produit est crée, il est nécessaire de définir son fournisseur, cette information sera utilisée pour identifier ce dernier sur l\'approvisionnement principal.', 'nexo' ) );
        $crud->field_description( 'TAX_TYPE', __( 'Permet de définir si la taxe est incluse ou excluse.', 'nexo' ) );
        $crud->field_description( 'ALERT_QUANTITY', __( 'Seuil a atteindre pour activer la notification du stock inférieure.', 'nexo' ) );
        $crud->field_description( 'STOCK_ALERT', __( 'Permet d\'activer l\'alerte lorsque le stock du produit atteint le seuil définit.', 'nexo' ) );
        $crud->field_description( 'EXPIRATION_DATE', __( 'Si le produit expire à un moment précis. Vous pouvez définir la date d\'expiration', 'nexo' ) );
        $crud->field_description( 'ON_EXPIRE_ACTION', __( 'Déterminer l\'action après l\'expiration d\'un produit', 'nexo' ) );
        $crud->field_description( 'ON_STOCK_LOW', __( 'Déterminer une action lorsque le stock d\'un produit est faible.', 'nexo' ) );
        $crud->field_description( 'STATUS', __( 'Définir si le produit est disponible pour la vente ou pas.', 'nexo' ) );
        $crud->field_description( 'STOCK_ENABLED', __( 'Les produits avec le stock activé, verront leur inventaire affecté par les ventes.', 'nexo' ) );
        
        $crud->field_description( 
            'REF_TAXE', 
            __( 'Si vous souhaitez appliquer une taxe sur le prix des produits, vous pouvez choisir cette taxe sur cette liste.', 'nexo' ) 
        );

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->add_action( __( 'Historique d\'approvisionnement', 'nexo' ), null, null, 'fa fa-truck', [ $this, 'stock_supply_link' ] );
        $crud->add_action( __( 'Historique du produit', 'nexo' ), null, null, 'fa fa-list', [ $this, 'item_history_link' ] );

        $required_fields    =   array(
            'DESIGN',
            'SKU',
            'REF_CATEGORIE',
            'PRIX_DE_VENTE',
            'DEFECTUEUX',
            'QUANTITY',
            'STATUS',
            'TYPE',
            'STOCK_ENABLED'
        );

        // this fiels is only required when creating an item
        // it won't be available for the edition'
        if( $currentScreen == 'add' ) {
            $required_fields[]      =   'REF_PROVIDER';
            $required_fields[]      =   'REF_SHIPPING';
        }

        $crud->required_fields( $this->events->apply_filters( 'product_required_fields', $required_fields ) );

        $crud->set_field_upload('APERCU', get_store_upload_path() . '/items-images/' );

        $crud->set_rules('QUANTITY', __('Quantité', 'nexo'), 'is_natural_no_zero');
        $crud->set_rules('DEFECTUEUX', __('Défectueux', 'nexo'), 'numeric');
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
        $crud->change_field_type('PRIX_DE_VENTE_TTC', 'invisible');
		// $crud->change_field_type( 'BARCODE_TYPE', 'invisible' );

		// @since 2.8.2
		$crud->field_type( 'TYPE', 'dropdown', $this->events->apply_filters( 'nexo_item_type', $this->config->item('nexo_item_type') ) );
		$crud->field_type( 'STATUS', 'dropdown', $this->config->item('nexo_item_status'));
		$crud->field_type( 'STOCK_ENABLED', 'dropdown', $this->config->item('nexo_item_stock'));
		$crud->field_type( 'AUTO_BARCODE', 'dropdown', $this->config->item('nexo_yes_no' ) );
        $crud->field_type( 'BARCODE_TYPE', 'dropdown', $this->config->item( 'nexo_barcode_supported' ) );

        $crud->field_type( 'ON_EXPIRE_ACTION', 'dropdown', [
            'lock_sales'  =>  __( 'Empêcher les ventes', 'nexo' ),
            'allow_sales'   =>  __( 'Autoriser les ventes', 'nexo' )
        ]);

        $crud->field_type( 'ON_STOCK_LOW', 'dropdown', [
            'do_nothing'            =>  __( 'Ne rien faire', 'nexo' ),
            'show_notification'     =>  __( 'Afficher des notifications', 'nexo' ),
            'send_email'            =>  __( 'Email aux administrateurs', 'nexo' ),
        ]);

        $crud->field_type( 'TAX_TYPE', 'dropdown', [
            'inclusive'  =>  __( 'Inclusive', 'nexo' ),
            'exclusive'   =>  __( 'Exclusive', 'nexo' )
        ]);

        $crud->field_type( 'STOCK_ALERT', 'dropdown', [
            'enabled'  =>  __( 'Activé', 'nexo' ),
            'disabled'   =>  __( 'Désactivé', 'nexo' )
        ]);

        // Callback Before Render
        $crud->callback_before_insert(array( 	$this->Nexo_Products, 'product_save' ) );
		$crud->callback_after_insert( array( 	$this->Nexo_Products, 'product_after_save' ) );
		$crud->callback_before_update( array( 	$this->Nexo_Products, 'product_update' ) );
		$crud->callback_after_update( array( 	$this->Nexo_Products, 'product_after_update' ) );
		$crud->callback_before_delete( array( 	$this->Nexo_Products, 'product_delete_related_component' ) );

        $this->events->add_filter( 'grocery_header_buttons', function( $actions ) {
            $actions[]      =   [
                'text'      =>  __( 'Faire un approvisionnement', 'nexo' ),
                'url'       =>  dashboard_url([ 'supplies', 'add' ])  
            ];

            return $actions;
        });

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

    /**
     * stock_supply_link
    **/

    public function stock_supply_link( $primary_key, $row ) 
    {
        return dashboard_url([ 'items', 'supply-history', $row->CODEBAR ] );
    }

    /**
     * item_history_link
     * @return string
    **/

    public function item_history_link( $primary_key, $row )
    {
        return dashboard_url([ 'items', 'history', $row->CODEBAR ] );
    }

    public function lists($page = 'index', $id = null)
    {
		global $PageNow;
		$PageNow			=	'nexo/produits/list';

        if ($page == 'index') {
            $this->Gui->set_title( store_title( __('Liste des articles', 'nexo') ) );
        } elseif ($page == 'delete') {

			nexo_permission_check( 'nexo.delete.items' );

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
        if ( ! User::can('nexo.create.items')) {
            return nexo_access_denied();
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

    /**
     * Stock Supply controller
     * @since 3.0.20
     * @return void
    **/

    public function stock_supply()
    {
        // if( User::cannot( 'nexo.view.supplies' ) ) {
        //     return nexo_access_denied();
        // }

        if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}
        
        // Angular Script
        $this->events->add_action( 'dashboard_footer', function() {
            get_instance()->load->module_view( 'nexo', 'items.stock-supply.script' );
        });

        // Header
        $this->Gui->set_title( store_title( __( 'Approvisonnement', 'nexo' ) ) );

        // Load View
        return $this->load->module_view( 'nexo', 'items.stock-supply.gui', null, true );
    }

    /**
     * Supply Header
    **/

    public function supply_header( $barcode = null, $as = null )
    {
        // Redirect if multistore is enabled but not in use

        if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Flux du stock', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_articles_stock_flow'));

        $fields				=	array( 'TYPE', 'REF_ARTICLE_BARCODE', 'QUANTITE', 'UNIT_PRICE', 'REF_PROVIDER', 'REF_SHIPPING' );
        $crud->fields( $fields );
        $crud->order_by( store_prefix() . 'nexo_articles_stock_flow.DATE_CREATION', 'desc' );

        // REF_PROVIDER and REF_SHIPPING is not available on each item supply history
		$crud->columns( 'REF_ARTICLE_BARCODE', 'TYPE', 'QUANTITE', 'UNIT_PRICE', 'TOTAL_PRICE', 'AUTHOR', 'DATE_CREATION' );

        $crud->field_type( 'TYPE', 'dropdown', [
            // 'supply'        =>  __( 'Approvisionnement', 'nexo' ),
            'adjustment'    =>  __( 'Correction du stock', 'nexo' ),
            'defective'     =>  __( 'Stock Défectueux', 'nexo' )
        ]);

        $crud->field_type( 'TOTAL_PRICE', 'hidden' );
        $crud->callback_before_update( [ $this, 'before_update_stock_supply' ] );
        $crud->callback_before_delete( [ $this, 'before_delete_stock_supply' ] );
        
        $crud->display_as('REF_ARTICLE_BARCODE', __('Produit', 'nexo'));
        $crud->display_as('QUANTITE', __('Quantité', 'nexo'));
        $crud->display_as('UNIT_PRICE', __('Prix', 'nexo'));
        $crud->display_as('TOTAL_PRICE', __('Total', 'nexo'));
        // $crud->display_as('REF_PROVIDER', __('Fournisseur', 'nexo')); // moved to shipping
        $crud->display_as('TYPE', __('Type', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Date', 'nexo'));
        // $crud->display_as('REF_SHIPPING', __('Livraison', 'nexo')); // moved to shipping
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));

        $crud->where( 'TYPE', 'supply' );

        if( $barcode != null && $as == 'BARCODE' ) {
            $crud->where( 'REF_ARTICLE_BARCODE', $barcode );
        } else if( $barcode != null && $as == 'ID' ) {
            $item   =   $this->db->where( 'ID', $barcode )->get( store_prefix() . 'nexo_articles' )->result_array();
            $crud->where( 'REF_ARTICLE_BARCODE', $item[0][ 'CODEBAR' ] );
        }

        // add a new button to allow quick access to item
        $this->events->add_filter( 'grocery_header_buttons', function( $buttons ) {
            $buttons[]      =   [
                'url'       =>  dashboard_url([ 'items' ] ),
                'text'      =>  __( 'Liste des produits', 'nexo' )
            ];

            return $buttons;
        });       

        $this->load->model( 'Nexo_Products' );
        // $crud->unset_edit();
        $crud->unset_add();

        $itemsRaw          =   $this->db->get( store_prefix() . 'nexo_articles' )->result_array();
        $items              =   [];
        foreach( $itemsRaw as $raw ) {
            $items[ $raw[ 'CODEBAR' ] ]     =   $raw[ 'DESIGN' ];
        }

        $crud->field_type( 'REF_ARTICLE_BARCODE', 'dropdown', $items );

        $crud->callback_column( 'UNIT_PRICE', function( $price ){
            return $this->Nexo_Misc->cmoney_format( $price, true );
        });

        $crud->callback_column( 'TOTAL_PRICE', function( $price ){
            return $this->Nexo_Misc->cmoney_format( $price, true );
        });

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        // $crud->set_relation('REF_SHIPPING', store_prefix() . 'nexo_arrivages', 'TITRE');
        // $crud->set_relation('REF_PROVIDER', store_prefix() . 'nexo_fournisseurs', 'NOM');

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
     * Callback before stock update
    **/

    public function before_update_stock_supply( $stock, $id )
    {
        $old_stock  =   $this->db->where( 'ID', $id )
        ->get( store_prefix() . 'nexo_articles_stock_flow' )
        ->result_array();

        $current_product    =   $this->db->where( 'CODEBAR', $old_stock[0][ 'REF_ARTICLE_BARCODE' ] )
        ->get( store_prefix() . 'nexo_articles' )
        ->result_array();

        // Restoring initial operation
        if( in_array( $old_stock[0][ 'TYPE' ], [ 'defective', 'adjustment' ] ) ) { // remove from stock
            $current_product[0][ 'QUANTITE_RESTANTE' ]      =   floatval( $current_product[0][ 'QUANTITE_RESTANTE' ] ) + floatval( $old_stock[0][ 'QUANTITE' ] );
        } else if( $old_stock[0][ 'TYPE' ] == 'supply' ) { // add to stock
            $current_product[0][ 'QUANTITE_RESTANTE' ]      =   floatval( $current_product[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $old_stock[0][ 'QUANTITE' ] );
        }

        // only when stock is being removed
        if( floatval( $current_product[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $stock[ 'QUANTITE' ] ) < 0 && in_array( $stock[ 'TYPE' ], [ 'defective', 'adjustment' ] ) ) {
            echo json_encode([
                'success'    =>  'false',
                'message'   =>  __( 'La quantité restante après cette opération est négative. Impossible d\'enregistrer l\'opération', 'nexo' )
            ]);
            return false;
        }

        // Now increase the current stock of the item
        if( in_array( $stock[ 'TYPE' ], [ 'defective', 'adjustment' ] ) ) {
            $remaining_qte      =   floatval( $current_product[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $stock[ 'QUANTITE' ] );
        } else if( in_array( $stock[ 'TYPE' ], [ 'supply' ] )) { // 'usable' is only used by the refund feature
            $remaining_qte      =   floatval( $current_product[0][ 'QUANTITE_RESTANTE' ] ) + floatval( $stock[ 'QUANTITE' ] );
            $stock[ 'TOTAL_PRICE' ]       =   $remaining_qte * floatval( $stock[ 'UNIT_PRICE' ] );
        }

        $this->db->where( 'CODEBAR', $current_product[0][ 'CODEBAR' ] )->update( store_prefix() . 'nexo_articles', [
            'QUANTITE_RESTANTE'     =>  $remaining_qte
        ]);

        return $stock;
    }

    /**
     * Before Delete Stock Supply
     * @param int stock id
     * @return void
     */
    public function before_delete_stock_supply( $id )
    {
        // get supply entry
        $supply     =   $this->db->where( 'ID', $id )
        ->get( store_prefix() . 'nexo_articles_stock_flow' )
        ->result_array();

        // -> reduce item included on the supply
        // -> reduce remaining quantity
        $shipping   =   $this->db->where( 'ID', $supply[0][ 'REF_SHIPPING' ] )
        ->get( store_prefix() . 'nexo_arrivages' )
        ->result_array();

        // retreive fresh item
        $item       =   $this->db->where( 'CODEBAR', $supply[0][ 'REF_ARTICLE_BARCODE' ] )
        ->get( store_prefix() . 'nexo_articles' )
        ->result_array();

        // if shipping value is not update, default one will be used.
        $new_shipping_price         =   $shipping[0][ 'VALUE' ];

        if( in_array( $supply[0][ 'TYPE' ], [ 'supply', 'usable' ] ) ) {
            if( $supply[0][ 'TYPE' ] == 'supply' ) {
                $new_shipping_items     =   floatval( $shipping[0][ 'ITEMS' ] ) - floatval( $supply[0][ 'QUANTITE' ] );
                $cost                   =   floatval( $supply[0][ 'QUANTITE' ]) * floatval( $supply[0][ 'UNIT_PRICE' ]);
                $new_shipping_price     =   floatval( $shipping[0][ 'VALUE' ] ) - $cost;
            }
            $new_remaining_qte      =   floatval( $item[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $supply[0][ 'QUANTITE' ] );
        } else {
            $new_shipping_items     =   intval( $shipping[0][ 'ITEMS' ] ) + intval( $supply[0][ 'QUANTITE' ] );
            $new_remaining_qte      =   floatval( $item[0][ 'QUANTITE_RESTANTE' ] ) + floatval( $supply[0][ 'QUANTITE' ] );
        }

        // block negative suppression
        // we can also delete all remaining quantity and set remaining quantity to 0;
        if( $new_remaining_qte < 0 ) {
            echo json_encode([
                'success'               =>      false,
                'error_message'         =>      __( 'Impossible de supprimer la transaction. Cette dernière a probablement déjà été consommé à moitié ou en totalité. Veuillez utiliser l\'ajustement du stock à la place.', 'nexo' )
            ]);
            die;  
        }

        // update shipping if it's found.
        if( $shipping ) {
            $this->db->where( 'ID', $supply[0][ 'ID' ] )->update( store_prefix() . 'nexo_arrivages', [
                'ITEMS'     =>  $new_shipping_items,
                'VALUE'     =>  $new_shipping_price
            ]);
        }

        // reduce remaining quantity
        $this->db->where( 'CODEBAR', $supply[0][ 'REF_ARTICLE_BARCODE' ] )->update( store_prefix() . 'nexo_articles', [
            'QUANTITE_RESTANTE'     =>  $new_remaining_qte
        ]);
    }

    /**
     * Supply Controller
    **/

    public function supply( $barcode = null, $as = 'BARCODE' )
    {
        if( User::cannot( 'nexo.create.stock-adjustment' ) ) {
            return nexo_access_denied();
        }

        $this->Gui->set_title( store_title( __( 'Flux du stock', 'nexo' ) ) );
        $data[ 'crud_content' ]    =    $this->supply_header(  $barcode, $as );
        $this->load->module_view( 'nexo', 'items.stock-supply.crud-gui', $data );
    }

    /**
     * New Supply UI
     * @param void
     * @return void
    **/

    public function add_supply()
    {
        // Redirect if multistore is enabled but not in use

        if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			return show_error( __( 'Cette fonctionnalité a été désactivée', 'nexo' ) );
		}

        $this->load->model( 'Nexo_Shipping' );
        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'nexo', 'items.stock-supply.new-ui-script' );
        });

        $this->Gui->set_title( store_title( __( 'Approvisonnement du stock', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'items.stock-supply.new-ui-gui' );
    }

    /**
     * Product History
     * 
    **/

    public function history( $barcode = null )
    {
        // Redirect if multistore is enabled but not in use

        if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}
        
        $this->events->add_action( 'dashboard_footer', function() use( $barcode )  {
            get_instance()->load->module_view( 'nexo', 'items.history.script', compact( 'barcode' ) );
        });

        $this->Gui->set_title( store_title( __( 'Historique du produit', 'nexo' ) ) );

        $this->load->module_view( 'nexo', 'items.history.gui' );
    }

    /**
     * Creating Grouped Items
     * @param void
     */
    public function grouped_items()
    {
        $this->enqueue->js('../plugins/bootstrap-select/dist/js/bootstrap-select.min');
        $this->enqueue->css('../plugins/bootstrap-select/dist/css/bootstrap-select.min');
        
        $this->events->add_action( 'dashboard_footer', function() {
            get_instance()->load->module_view( 'nexo', 'items.grouped-items-script' );
        });

        $this->Gui->set_title( store_title( __( 'Grouper des produits', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'items.grouped-items' );
    }
}