<?php
class Nexo_Taxes_Controller extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __call( $method, $arguments )
    {
        if( ! method_exists( $this, $method ) ) {
            $this->index();
        }
    }
    
    public function crud_header()
    {
        // if (
        //     ! User::can('create_shop_registers')  &&
        //     ! User::can('edit_shop_registers') &&
        //     ! User::can('delete_shop_registers') &&
		// 	! User::can( 'view_shop_registers' )
        // ) {
        //     redirect(array( 'dashboard', 'access-denied' ));
        // }

		/**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( ( multistore_enabled() && ! is_multistore() ) && $this->events->add_filter( 'force_show_inventory', false ) == false ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

		$crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__( 'Taxes', 'nexo'));

        $crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_taxes'));

		// If Multi store is enabled
		// @since 2.8
		$fields					=	array( 'NAME', 'RATE', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION' );
		$crud->columns('NAME', 'RATE', 'AUTHOR', 'DATE_CREATION' );
        $crud->fields( $fields );

		$crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->order_by('DATE_CREATION', 'desc');

        $crud->display_as('NAME', __('Nom de la taxe', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
		$crud->display_as('RATE', __('Taux d\'imposition', 'nexo'));
		$crud->display_as('DATE_CREATION', __('Crée', 'nexo'));

        $this->events->add_filter( 'grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter( 'grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->callback_before_insert(array( $this, '__create' ));
        $crud->callback_before_update(array( $this, '__update' ));
        // $crud->callback_before_delete(array( $this, '__delete_register' ));

		// if( in_array( $this->uri->segment( 5 ), array( 'add', 'edit' ) ) ) {
		// 	$crud->field_type('STATUS', 'dropdown', $this->config->item('nexo_registers_status_for_creating'));
		// } else {
		// 	$crud->field_type('STATUS', 'dropdown', $this->config->item('nexo_registers_status'));
		// }
        $crud->callback_column( 'RATE', function( $value ) {
            return $value . ' %';
        });

        $crud->required_fields('NAME', 'PERCENTAGE');
		$crud->change_field_type('DATE_CREATION', 'invisible');
		$crud->change_field_type('AUTHOR', 'invisible');

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
     * Create
     * @return array
    **/

    public function __create( $post )
    {
        $post[ 'AUTHOR' ]           =   User::id();
        $post[ 'DATE_CREATION' ]    =   date_now();
        return $post;
    }

    /**
     * Update
     * @return array
    **/

    public function __update( $post )
    {
        $post[ 'AUTHOR' ]           =   User::id();
        $post[ 'DATE_MOD' ]         =   date_now();
        return $post;
    }

    /**
     * Index
     * @return void
    **/

    public function index()
    {
        $this->Gui->set_title( store_title( __( 'Liste des taxes', 'nexo' ) ) );
        $data[ 'crud_content' ]    =    $this->crud_header();

		$this->load->module_view( 'nexo', 'taxes.gui', $data );
    }

    /**
     * Add tax form
     * @return void
    **/

    public function add()
    {
        $this->Gui->set_title( store_title( __( 'Ajouter une taxe', 'nexo' ) ) );
        $data[ 'crud_content' ]    =    $this->crud_header();

		$this->load->module_view( 'nexo', 'taxes.gui', $data );
    }

    /**
     * Insert Validation
     * @use index
     * @return void
    **/

    public function insert_validation()
    {
        $this->index();
    }

    /**
     * Insert
     * @use index
     * @return void
    **/

    public function insert()
    {
        $this->index();
    }

    /**
     * Success Page
     * @use index
     * @return void
    **/

    public function success()
    {
        $this->index();
    }

    /**
     * Delete
    **/

    public function delete()
    {
        $this->index();
    }
}