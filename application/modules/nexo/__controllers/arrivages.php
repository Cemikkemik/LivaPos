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

		if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Approvisionnements', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_arrivages' ) );

        $crud->callback_column( 'VALUE', function( $price ){
            return $this->Nexo_Misc->cmoney_format( $price, true );
        });

        $crud->callback_column( 'PROVIDERS', function( $providers ){
            return empty( $providers ) ? __( 'N/D', 'nexo' ) : $providers;
        });

		// fields
		$fields			=	array( 'TITRE', 'DESCRIPTION' );
        $crud->columns( 'TITRE', 'PROVIDERS', 'VALUE', 'ITEMS', 'AUTHOR', 'DATE_CREATION' );
        $crud->fields( $fields );

        $crud->order_by('TITRE', 'asc');

        $crud->unset_add();
        $crud->unset_edit();

        $crud->display_as('TITRE', __('Nom de la livraison', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Crée le', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
        $crud->display_as('VALUE', __('Valeur', 'nexo'));
        $crud->display_as('ITEMS', __('Produits Inclus', 'nexo'));
        $crud->display_as('PROVIDERS', __('Fournisseurs', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('FOURNISSEUR_REF_ID', __('Fournisseur', 'nexo'));

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');

        // Liste des produits
        $crud->add_action(__('Liste des produits', 'nexo'), '', site_url(array( 'dashboard', store_slug(), 'nexo', 'arrivages', 'delivery_items' )) . '/', 'btn btn-info fa fa-list-ol');
        $crud->add_action(__('Etiquettes des articles', 'nexo'), '', site_url(array( 'dashboard', store_slug(), 'nexo', 'print', 'shipping_item_codebar' )) . '/', 'btn btn-success fa fa-tags');
        $crud->add_action(__('Facture de l\'arrivage', 'nexo'), '', site_url(array( 'dashboard', store_slug(), 'nexo', 'arrivages', 'delivery_invoice' )) . '/', 'btn btn-info fa fa-file');

        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->required_fields('TITRE');

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
            $this->Gui->set_title( store_title( __('Liste des approvisonnements', 'nexo')) );
        } elseif ($page == 'delete') { // Check Deletion permission

            nexo_permission_check('delete_shop_shippings');

            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_SHIPPING', 'table'    =>    store_prefix() . 'nexo_articles' )
            ));
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

    /** 
     * Delivery Invoice
     * @return void
    **/

    public function delivery_invoice( $delivery_id )
    {
        global $Options;
        $items      =   $this->db->select( '*' )
        ->from( store_prefix() . 'nexo_arrivages' )
        ->join( store_prefix() . 'nexo_articles_stock_flow', store_prefix() . 'nexo_articles_stock_flow.REF_SHIPPING = ' . store_prefix() . 'nexo_arrivages.ID' )
        ->join( store_prefix() . 'nexo_fournisseurs', store_prefix() . 'nexo_fournisseurs.ID = ' . store_prefix() . 'nexo_articles_stock_flow.REF_PROVIDER' )
        ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE' )
        ->where( store_prefix() . 'nexo_arrivages.ID', $delivery_id )
        ->get()->result_array();

        $this->load->library('parser');

        $data               =   [];
        $data[ 'items' ]    =   $items;
        $data[ 'template' ]						=	array();
        $data[ 'template' ][ 'shop_name' ]		=	@$Options[ store_prefix() . 'site_name' ];
        $data[ 'template' ][ 'shop_pobox' ]		=	@$Options[ store_prefix() . 'nexo_shop_pobox' ];
        $data[ 'template' ][ 'shop_fax' ]		=	@$Options[ store_prefix() . 'nexo_shop_fax' ];
        $data[ 'template' ][ 'shop_email' ]     =	@$Options[ store_prefix() . 'nexo_shop_email' ];
        $data[ 'template' ][ 'shop_street' ]    =	@$Options[ store_prefix() . 'nexo_shop_street' ];
        $data[ 'template' ][ 'shop_phone' ]     =	@$Options[ store_prefix() . 'nexo_shop_phone' ];

        $this->load->module_view( 'nexo', 'deliveries.invoice', $data );
    }

    /**
     * Delivery Items CRUD
     * @return object
    **/

    public function delivery_items_crud( $delivery_id )
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

		if( ( multistore_enabled() && ! is_multistore() ) && $this->events->apply_filters( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject( __('Produits de l\'approvisionnement', 'nexo') );
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_articles_stock_flow' ) );
        $crud->set_relation( 'REF_PROVIDER', store_prefix() . 'nexo_fournisseurs', 'NOM');
        $crud->set_relation( 'REF_SHIPPING', store_prefix() . 'nexo_arrivages', 'TITRE');
        $crud->set_relation( 'AUTHOR', 'aauth_users', 'name' );
        
        $crud->set_primary_key( 'CODEBAR', store_prefix() . 'nexo_articles' );
        $crud->set_relation( 'REF_ARTICLE_BARCODE', store_prefix() . 'nexo_articles', 'DESIGN');
        
        $crud->where( 
            '(' .
            store_prefix() . 'nexo_articles_stock_flow.TYPE = "supply" or ' .
            store_prefix() . 'nexo_articles_stock_flow.TYPE = "import"' .  
            ')' . 
            ' AND ' . store_prefix() . 'nexo_articles_stock_flow.REF_SHIPPING = ' . $delivery_id
        );
        // $crud->where_in( store_prefix() . 'nexo_articles_stock_flow.TYPE', 'import' );
        // $crud->where( store_prefix() . 'nexo_articles_stock_flow.REF_SHIPPING', $delivery_id );
        // $crud->or_where( store_prefix() . 'nexo_articles_stock_flow.TYPE', 'transfert_canceled' );
        // $crud->or_where( store_prefix() . 'nexo_articles_stock_flow.TYPE', 'transfert_rejected' );

        $crud->add_action( __('Historique d\'approvisionnement', 'nexo'), '', '', 'btn btn-default fa fa-eye', [ $this, 'supply_link' ]);

        $crud->columns( 'REF_ARTICLE_BARCODE', 'TYPE', 'QUANTITE', 'UNIT_PRICE', 'REF_PROVIDER', 'REF_SHIPPING', 'DATE_CREATION', 'AUTHOR' );

        $crud->callback_column( 'TYPE', function( $type ){
            $config     =   get_instance()->config->item( 'stock-operation' );
            return $config[ $type ];
        });

        $crud->callback_before_update([ $this, '__before_update_history' ]);

        $crud->fields( 'QUANTITE', 'UNIT_PRICE', 'TOTAL_PRICE' );

        $crud->callback_column( 'UNIT_PRICE', function( $type ){
            return get_instance()->Nexo_Misc->cmoney_format( $type );
        });

        $crud->unset_add();
        $crud->field_type( 'TOTAL_PRICE', 'hidden' );
        // $crud->unset_edit();

        $crud->display_as('REF_ARTICLE_BARCODE', __('Produit', 'nexo'));
        $crud->display_as('REF_SHIPPING', __('Approvisionnement', 'nexo'));
        $crud->display_as('QUANTITE', __('Quantité', 'nexo'));
        $crud->display_as('UNIT_PRICE', __('Prix Unitaire', 'nexo'));
        $crud->display_as('REF_PROVIDER', __('Fournisseur', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Crée le', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));

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
     * Before Update history
    **/

    public function __before_update_history( $post, $index )
    {
        $history        =   $this->db->where( 'ID', $index )
        ->get( store_prefix() . 'nexo_articles_stock_flow' )
        ->result_array();

        if( $history ) {
            $items       =   $this->db
            ->where( 'REF_SHIPPING', $history[0][ 'REF_SHIPPING' ] )
            ->get( store_prefix() . 'nexo_articles_stock_flow' )
            ->result_array();

            $total_amount       =   0;
            $total_quantity     =   0;
            $current_item       =   [];
            foreach( $items as $item ) {
                if( $item[ 'ID' ] != $index ) {
                    if( $item[ 'UNIT_PRICE' ] != '0' && $item[ 'TOTAL_PRICE' ] != '0' ) {
                        $total              =   floatval( $item[ 'UNIT_PRICE' ]) * floatval( $item[ 'QUANTITE' ] );
                        $total_amount       +=  $total;
                        $total_quantity     +=  floatval( $item[ 'QUANTITE' ]);
                    }
                } else {
                    $current_item   =   $item;
                }
            }

            // update current supply total
            $item_details       =   $this->db->where( 'CODEBAR', $history[0][ 'REF_ARTICLE_BARCODE' ])
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

            // remove previous remaning quantity
            $quantity               =   0;
            
            if( floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] > 0 ) && floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $history[0][ 'QUANTITE' ] ) >= 0 ) {
                $quantity           =   floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $history[0][ 'QUANTITE' ] );
            }

            // new quantity
            $quantity           +=  floatval( $post[ 'QUANTITE' ] );

            // update new remaning quantity
            $this->db->where( 'CODEBAR', $history[0][ 'REF_ARTICLE_BARCODE' ] )
            ->update( store_prefix() . 'nexo_articles', [
                'QUANTITE_RESTANTE'     =>  $quantity
            ]);

            $this->db->where( 'ID', $history[0][ 'REF_SHIPPING' ] )->update( store_prefix() . 'nexo_arrivages', [
                'VALUE'         =>  $total_amount + ( floatval( $post[ 'UNIT_PRICE' ] ) * floatval( $post[ 'QUANTITE' ] ) ),
                'ITEMS'         =>  $total_quantity + floatval( $post[ 'QUANTITE' ] )
            ]);

            $this->events->do_action_ref_array( 'update_supply_history', [ $post, $index ]);
        }
        return $post;
    }

    /** 
     * Supply LInk
     * @param int primary key
     * @param array row obbject
     * @return string
    **/

    public function supply_link( $primary_key, $row )
    {
        return site_url(array( 'dashboard', store_slug(), 'nexo', 'produits', 'supply' )) . '/' . $row->REF_ARTICLE_BARCODE;
    }

    /**
     * Delivery Items
     * @return void
    **/

    public function delivery_items( $delivery_id, $page = 'index', $id = 0 )
    {
        // only supply, import and transfert can be edited
        if( $page == 'edit' ) {
            $stock  =   $this->db->where( 'ID', $id )->get( store_prefix() . 'nexo_articles_stock_flow' )
            ->result_array();

            if( ! in_array( $stock[0][ 'TYPE' ], $this->events->apply_filters( 'editable_stock_type', [ 'supply', 'import' ] ) ) ) {
                show_error( __( 'Vous ne pouvez pas modifier cet element', 'nexo' ) );
            }
        }

        $crud       =   $this->delivery_items_crud( $delivery_id );
        $this->Gui->set_title( store_title( __( 'Produits de l\'approvisionnement', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'deliveries.supply-item-gui', compact( 'crud' ) );
    }

}
new Nexo_Arrivages($this->args);
