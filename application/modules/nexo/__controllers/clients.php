<?php
class Nexo_Clients extends CI_Model
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
            ! User::can('create_shop_customers')  &&
            ! User::can('edit_shop_customers') &&
            ! User::can('delete_shop_customers')
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
        $crud->set_subject(__('Clients', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_clients'));

		// If Multi store is enabled
		// @since 2.8
		$fields					=	array(
			'REF_GROUP',
            'NOM',
            'PRENOM',
            'EMAIL',
            'TEL',
            'CITY',
            'STATE',
            'COUNTRY',
            'POST_CODE',
            'COMPANY_NAME',
            'DATE_NAISSANCE',
            'AVATAR',
            'ADRESSE',
            'DESCRIPTION',
            'AUTHOR',
            'DATE_CREATION',
            'DATE_MOD'
		);

        $fields         =   $this->events->apply_filters( 'nexo_clients_fields', $fields );

        $customer_columns   =   $this->events->apply_filters( 'nexo_clients_columns', [ 'NOM', 'EMAIL', 'TEL', 'OVERALL_COMMANDES', 'TOTAL_SEND', 'REF_GROUP', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' ] );

		$crud->set_theme('bootstrap');
        $crud->columns( $customer_columns );
        $crud->fields( $fields );

        $crud->display_as('NOM', __('Nom', 'nexo'));
        $crud->display_as('EMAIL', __('Email', 'nexo'));
        $crud->display_as('OVERALL_COMMANDES', __('Achats effectués', 'nexo'));
        $crud->display_as('NBR_COMMANDES', __('Nbr Commandes (sess courante)', 'nexo'));
        $crud->display_as('TEL', __('Téléphone', 'nexo'));
        $crud->display_as('PRENOM', __('Prénom', 'nexo'));
        $crud->display_as('DATE_NAISSANCE', __('Date de naissance', 'nexo'));
        $crud->display_as('ADRESSE', __('Adresse', 'nexo'));
        $crud->display_as('TOTAL_SEND', __('Dépense effectué', 'nexo'));
        $crud->display_as('LAST_ORDER', __('Dernière commande', 'nexo'));
        $crud->display_as('AVATAR', __('Avatar', 'nexo'));
        $crud->display_as('STATE', __('Pays', 'nexo'));
        $crud->display_as('CITY', __('Ville', 'nexo'));
        $crud->display_as('POST_CODE', __('Code postale', 'nexo'));
        $crud->display_as('COUNTRY', __('Continent', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Crée', 'nexo'));
        $crud->display_as('DATE_MOD', __('Modifié le', 'nexo'));
        $crud->display_as('AUTHOR', __('Par', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('REF_GROUP', __('Groupe', 'nexo'));
        $crud->display_as( 'COMPANY_NAME', __( 'Nom de la compagnie', 'nexo' ) );

        $crud->change_field_type('AUTHOR', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');

        $crud->callback_before_update(array( $this, '__update' ));
        $crud->callback_before_insert(array( $this, '__insert' ));

        $crud->callback_column( 'TOTAL_SEND', function( $data ){
            return $this->Nexo_Misc->cmoney_format( $data, true );
        });

        $crud->callback_column( 'EMAIL', function( $data ){
            return empty( $data ) ? __( 'Non Défini', 'nexo' ) : $data;
        });

        $crud->callback_column( 'TEL', function( $data ){
            return empty( $data ) ? __( 'Non Défini', 'nexo' ) : $data;
        });

        $crud->set_field_upload('AVATAR', get_store_upload_path() . '/customers/');

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $crud->required_fields('NOM', 'REF_GROUP');

		$crud->set_relation('REF_GROUP', store_prefix() . 'nexo_clients_groups', 'NAME');
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $crud->set_rules('EMAIL', __('Email', 'nexo'), 'valid_email');

        $crud->unset_jquery();

        // @since 3.1
        $crud->unset_add();
        $crud->unset_edit();

        // add a custom action on header
        $this->events->add_filter( 'grocery_header_buttons', function( $menus ) {
            $menus[]        =   [
                'text'      =>  __( 'Ajouter un client', 'nexo' ),
                'url'       =>  site_url([ 'dashboard', store_slug(), 'nexo', 'clients', 'add' ])
            ];
            return $menus;
        });

        $crud->add_action( __( 'Modifier', 'nexo' ), null, site_url([ 'dashboard', store_slug(), 'nexo', 'clients', 'edit/' ] ), 'fa fa-edit btn btn-default' );

        // Load Nexo Customer Clients Crud
        $crud   =   $this->events->apply_filters( 'customers_crud_loaded', $crud );

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
     * Create Customer
    **/

    public function __update($post)
    {
        $post[ 'DATE_MOD' ]            =    date_now();
        $post[ 'AUTHOR' ]            =    User::id();
        return $post;
    }

    /**
     * Callback before insert
    **/

    public function __insert($post)
    {
        $post[ 'DATE_CREATION' ]    =    date_now();
        $post[ 'AUTHOR' ]            =    User::id();
        return $post;
    }

    public function lists($page = 'index', $id = null)
    {
		global $PageNow;
		$PageNow			=	'nexo/clients/list';

        if ($page == 'index') {
            $this->Gui->set_title( store_title( __('Liste des clients', 'nexo')) );
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_customers');

            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_CLIENT', 'table'    =>    store_prefix() . 'nexo_commandes' )
            ));
        } else {
            $this->Gui->set_title( store_title( __('Liste des clients', 'nexo') ));
        }

        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'clients';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }

    public function add()
    {
		global $PageNow;
		$PageNow			=	'nexo/clients/add';

        if (! User::can('create_shop_customers')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $data                   =   [];        
        $data[ 'clients' ]      =   [];
        $data[ 'client_id' ]    =   0;
        $data[ 'groups' ]       =   $this->Nexo_Misc->customers_groups();

        // @since 3.1.0
        $this->events->add_action( 'dashboard_footer', function() use ( $data ) {
            get_instance()->load->module_view( 'nexo', 'customers.script', $data );
        });

        $this->Gui->set_title( store_title( __( 'Add a new customer', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'customers.gui' );
    }
    
    /**
     * Edit customer
     * @param int customer id
     * @return void
    **/

    public function edit( $customer_id ) 
    {
        global $PageNow;
		$PageNow			=	'nexo/clients/add';

        if (! User::can('create_shop_customers')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $this->load->module_model( 'nexo', 'nexo_customers' );
        $data[ 'clients' ]      =   $this->nexo_customers->get_customers( $customer_id );
        $data[ 'client_id' ]    =   $customer_id;
        $data[ 'groups' ]       =   $this->Nexo_Misc->customers_groups();

        // @since 3.1.0
        $this->events->add_action( 'dashboard_footer', function() use ( $data ) {
            get_instance()->load->module_view( 'nexo', 'customers.script', $data );
        });

        $this->Gui->set_title( store_title( __( 'Add a new customer', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'customers.gui' );
    }

    /**
     * User Groups header
     *
    **/

    public function groups_header()
    {
        if (
            ! User::can('create_shop_customers_groups')  &&
            ! User::can('edit_shop_customers_groups') &&
            ! User::can('delete_shop_customers_groups')
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
        $crud->set_subject(__('Groupes d\'utilisateurs', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_clients_groups'));

		$fields				=	array( 'NAME', 'DISCOUNT_TYPE', 'DISCOUNT_PERCENT', 'DISCOUNT_AMOUNT', 'DISCOUNT_ENABLE_SCHEDULE', 'DISCOUNT_START', 'DISCOUNT_END', 'DESCRIPTION',  'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );

		$crud->set_theme('bootstrap');

        $crud->columns('NAME', 'AUTHOR', 'DISCOUNT_TYPE', 'DISCOUNT_PERCENT', 'DISCOUNT_AMOUNT', 'DATE_CREATION', 'DATE_MODIFICATION');
        $crud->fields( $fields );

        $crud->display_as('NAME', __('Nom', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Date de création', 'nexo'));
        $crud->display_as('DISCOUNT_TYPE', __('Type de remise', 'nexo'));
        $crud->display_as('DISCOUNT_PERCENT', __('Pourcentage de remise (Sans "%")', 'nexo'));
        $crud->display_as('DISCOUNT_AMOUNT', __('Montant de la remise', 'nexo'));
        $crud->display_as('DISCOUNT_ENABLE_SCHEDULE', __('Activer la planification', 'nexo'));
        $crud->display_as('DISCOUNT_START', __('Début de la planification', 'nexo'));
        $crud->display_as('DISCOUNT_END', __('Fin de la planification', 'nexo'));
        $crud->display_as('DATE_MODIFICATION', __('Date de modification', 'nexo'));

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');

        // Load Field Type
        $crud->field_type('DISCOUNT_TYPE', 'dropdown', $this->config->item('nexo_discount_type'));
        $crud->field_type('DISCOUNT_ENABLE_SCHEDULE', 'dropdown', $this->config->item('nexo_true_false'));

        // Callback avant l'insertion
        $crud->callback_before_insert(array( $this, '__group_insert' ));
        $crud->callback_before_update(array( $this, '__group_update' ));

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        // Field Visibility
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MODIFICATION', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');

        $crud->required_fields('NAME', 'DISCOUNT_TYPE');

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
     * Groups
    **/

    public function groups($page = 'index', $id = null)
    {
		global $PageNow;
		$PageNow			=	'nexo/clients_groups/list';

        if ($page == 'index') {
            $this->Gui->set_title( store_title( __('Groupes', 'nexo')) );
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_customers_groups');

            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_GROUP', 'table'    =>    store_prefix() . 'nexo_clients' )
            ));
        } else {
            $this->Gui->set_title( store_title( __('Ajouter/Modifier un groupe de clients', 'nexo') ) );
        }

        $data[ 'crud_content' ]    =    $this->groups_header();
        $this->load->view('../modules/nexo/views/user-groups.php', $data);
    }

    /**
     * Callback
    **/

    public function __group_insert($data)
    {
        $data[ 'DATE_CREATION' ]    =    date_now();
        $data[ 'AUTHOR' ]            =    User::id();
        return $data;
    }

    public function __group_update($data)
    {
        $data[ 'DATE_MODIFICATION' ]    =    date_now();
        $data[ 'AUTHOR' ]                =    User::id();
        return $data;
    }

    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Clients($this->args);
