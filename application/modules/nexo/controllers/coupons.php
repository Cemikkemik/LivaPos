<?php
class NexoCouponController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Crud Header
     *  @param void
     *  @return object crud
    **/

    public function crud_header()
    {
        if (
            ! User::can('create_coupons') &&
            ! User::can('edit_coupons') &&
            ! User::can('delete_coupons')
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
        $crud->set_subject(__('Coupons', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_coupons'));

        // 'USAGE_LIMIT_PER_USER'
		$fields				=	array( 'CODE', 'AMOUNT', 'DISCOUNT_TYPE', 'PRODUCTS_IDS', 'PRODUCT_CATEGORIES', 'USAGE_LIMIT', 'EXPIRY_DATE', 'REWARDED_CASHIER', 'DESCRIPTION' );
		$crud->columns( 'CODE', 'AMOUNT', 'PRODUCTS_IDS', 'DISCOUNT_TYPE', 'EXPIRY_DATE', 'REWARDED_CASHIER', 'USAGE_COUNT' );

        $this->load->model( 'Nexo_Products' );
        $items              =   $this->Nexo_Products->get_product();
        $data_array         =   [];
        foreach( ( $items ) as $item ) {
            $data_array[ $item[ 'ID' ] ]    =   $item[ 'DESIGN' ];
        }

        $this->load->model( 'Nexo_Categories' );
        $categories         =   $this->Nexo_Categories->get();
        $data_categories    =   [];
        foreach( $categories as $category ) {
            $data_categories[ $category[ 'ID' ] ]   =   $category[ 'NOM' ];
        }

        $crud->fields( $fields );

        $crud->field_type( 'PRODUCTS_IDS', 'multiselect', $data_array );
        $crud->field_type( 'PRODUCT_CATEGORIES', 'multiselect', $data_categories );
        $crud->field_type( 'DISCOUNT_TYPE', 'dropdown', $this->config->item( 'coupon_type' ));

        $crud->display_as('CODE', __('Code', 'nexo'));
        $crud->display_as('AMOUNT', __('Valeur', 'nexo'));
        $crud->display_as('DISCOUNT_TYPE', __('Type du coupon', 'nexo'));
        $crud->display_as('PRODUCTS_IDS', __('Produit requis', 'nexo'));
        $crud->display_as('PRODUCT_CATEGORIES', __('Catégories requises', 'nexo'));
        $crud->display_as('EXPIRY_DATE', __('Date d\'expiration', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('REWARDED_CASHIER', __('Caissier récompensé', 'nexo'));
        $crud->display_as( 'USAGE_LIMIT', __( 'Limite d\'utilisation', 'nexo' ) );
        // $crud->display_as( 'USAGE_LIMIT_PER_USER', __( 'Limite d\'utilisation par client', 'nexo' ) );
        $crud->display_as( 'USAGE_COUNT', __( 'Nombre d\'utilisation', 'nexo' ) );

        // description
        $crud->field_description( 'USAGE_LIMIT', __( 'Permet de restreindre l\'utilisation du coupon à un nombre déterminé de fois.' , 'nexo' ) );
        $crud->field_description( 'PRODUCT_CATEGORIES', __( 'Le coupon ne sera utilisable que si un produit appartenant à l\'une de ces catégories est ajouté au panier.', 'nexo' ) );
        $crud->field_description( 'REWARDED_CASHIER', __( 'L\'utilisation du coupon donnera le mérite au caissier sélectionné.', 'nexo' ) );
        $crud->field_description( 'PRODUCTS_IDS', __( 'Le coupon ne sera utilisable que si un des produits selectionné est ajouté au panier.', 'nexo' ) );
        $crud->field_description( 'DISCOUNT_TYPE', __( 'Vous pouvez déterminer quel type de réduction opèrera le coupon.', 'nexo' ) );
        $crud->field_description( 'AMOUNT', __( 'Il peut s\'agir du pourcentage ou du montant fixe.', 'nexo' ) );
        $crud->field_description( 'CODE', __( 'Cette valeur permettra d\'identifier le coupon.', 'nexo' ) );
        $crud->field_description( 'EXPIRY_DATE', __( 'Après la date d\'expiration, le coupon ne sera plus utilisable.', 'nexo' ) );
        
        $crud->set_relation('REWARDED_CASHIER', 'aauth_users', 'name');

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->required_fields( 'CODE', 'AMOUNT', 'DISCOUNT_TYPE' );

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
     *  index
     *  @param
     *  @return
    **/

    public function lists( $page = 'index', $id = null )
    {
        global $PageNow;
		$PageNow			=	'nexo/coupons/list';

        if( $page == 'index' ) {
            $this->Gui->set_title( store_title( __('Liste des coupons', 'nexo') ) );
        } else if( $page == 'delete' ) {
            nexo_permission_check('delete_coupons');
            nexo_availability_check( $id, array(
                array( 'col'    =>    'REF_COUPON', 'table'    =>   store_prefix() . 'nexo_commandes_coupons' )
            ));
        } else {
            $this->Gui->set_title( store_title( __( 'Ajouter un coupon', 'nexo' ) ) );
        }

        $data[ 'crud_content' ]    =    $this->crud_header();
        $this->load->module_view( 'nexo', 'coupons/crud', $data );
    }

    public function old()
    {
        $AnguCrud       =   new AngularCrudLibrary( store_prefix() . 'nexo_coupons' );

        $AnguCrud->setColumns([
            'ID'                =>  __( 'Id', 'nexo' ),
            'CODE'              =>  __( 'Code', 'nexo' ),
            'DISCOUNT_TYPE'     =>  __( 'Type', 'nexo' ),
            'AMOUNT'            =>  __( 'Valeur / Pourcentage', 'nexo' ),
            'EXPIRY_DATE'       =>  __( 'Date D\'expiration', 'nexo' ),
            'ITEM_IDS'          =>  __( 'Produits ciblés', 'nexo' ),
            'ITEMS_CATEGORIES'  =>  __( 'Catégorie ciblées', 'nexo' ),
            // 'USAGE_LIMIT'       =>  __( 'Limite d\'utilisation', 'nexo' ),
            // 'MINIMUM_AMOUNT'    =>  __( 'Montant minimal', 'nexo' ),
            'CASHIERS_IDS'      =>  __( 'Caissier Récompensé', 'nexo' )
        ]);

        $AnguCrud->setRelation([
            'PRODUCTS_IDS'      =>  [
                'table'         =>  store_prefix() . 'nexo_articles',
                'col'           =>  'DESIGN',
                'comparison'    =>  'ID',
                'alias'         =>  'ITEM_IDS'
            ],
            'PRODUCT_CATEGORIES'  =>  [
                'table'         =>  store_prefix() . 'nexo_categories',
                'col'           =>  'NOM',
                'comparison'    =>  'ID',
                'alias'         =>  'ITEMS_CATEGORIES'
            ],
            'REWARDED_CASHIER'  =>  [
                'table'         =>  'aauth_users',
                'col'           =>  'name',
                'comparison'    =>  'id',
                'alias'         =>  'CASHIERS_IDS'
            ]
        ]);

        $AnguCrud->config([
            'baseUrl'           =>  site_url( array( 'dashboard', store_slug(), 'nexo_coupons', 'index' ) ),
            'page'              =>  $page,
            'crudTitle'         =>  __( 'Coupons', 'nexo' ),
            'primaryCol'        =>  'ID',
            'fieldsType'        =>  [
                'AMOUNT'                    =>  'number',
                'MINIMUM_AMOUNT'            =>  'number',
                'USAGE_LIMIT'               =>  'number',
                'DISCOUNT_TYPE'             =>  'select_options',
                'ITEM_IDS'                  =>  'select_relation_multiple',
                'ITEMS_CATEGORIES'          =>  'select_relation_multiple',
                'CASHIERS_IDS'              =>  'select_relation',
                'EXPIRY_DATE'               =>  'datetime'
            ],
            'validations'       =>  [
                'AMOUNT'        =>  [ 'required' ],
                'CODE'          =>  [ 'required' ]
            ],
            'selectOptions'     =>  [
                'DISCOUNT_TYPE' =>  [
                    [ 'key' =>  'percentage', 'value' =>  __( 'Pourcentage', 'nexo' )],
                    [ 'key' =>  'fixed', 'value' =>  __( 'Montant Fixe', 'nexo' )],
                ]
            ],
            'fieldDescription'  =>  [
                'AMOUNT'        =>  __( 'Définissez la valeur du coupon, en pourcentage ou montant fixe.', 'nexo' ),
                'CODE'          =>  __( 'Ce champ représente l\'identifiant du coupon. Il ne peut être utilisé qu\'une seul fois.', 'nexo' ),
                'PRODUCTS_IDS'  =>  __( 'Ce coupon ne s\'appliquera au panier que si un des produits sélectionné n\'est ajouté au panier.', 'nexo' ),
                'DISCOUNT_TYPE' =>  __( 'Type du coupon : Pourcentage ou Montant Fixe.', 'nexo' ),
                'USAGE_LIMIT'   =>  __( 'Après un nombre définit d\'utilisation, ce coupon ne sera plus valable', 'nexo' ),
                'MINIMUM_AMOUNT'    =>  __( 'Montant minimal du panier afin que le coupon ne puisse s\'appliquer', 'nexo' ),
                'PRODUCT_CATEGORIES'    =>  __( 'Ce coupon ne s\'appliquera au panier que si une produit du panier appartient à une des catégories sélectionnée.', 'nexo' ),
                'REWARDED_CASHIER'  =>  __( 'Lorsque les récompenses des caissiers est activée, à chaque utilisation du coupon, le caissier sélectionné recevra des points.', 'nexo' ),
                'EXPIRY_DATE'       =>  __( 'Ce coupon ne sera plus utilisable après cette date.', 'nexo' )
            ]
        ]);


        // $AnguCrud->addDefaultButton([
        //     'text'  =>  __( 'Imprimer', 'nexo' ),
        //     'url'   =>  site_url( array( 'dashboard', 'nexo_coupons', 'print_all' ) )
        // ]);

        // $AnguCrud->addSelectingButton([
        //     'icon'  =>  'print',
        //     'allow_multiple'    =>  true, // only_multiple, only_unique
        //     'label' =>  __( 'Imprimer', 'nexo' ),
        //     'url'   =>  site_url( array( 'dashboard', 'nexo_coupons', 'print_selected' ) )
        // ]);

        return $AnguCrud->LoadView();
    }
}
