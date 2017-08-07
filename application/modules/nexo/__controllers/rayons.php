<?php
class Nexo_Rayons extends CI_Model
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
            ! User::can('edit_shop_radius') &&
            ! User::can('create_shop_radius') &&
            ! User::can('delete_shop_radius')
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
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Rayons', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_rayons'));

		$fields				=	array( 'TITRE', 'DESCRIPTION' );
		$crud->columns('TITRE', 'DESCRIPTION');
        $crud->fields( $fields );

        $crud->display_as('TITRE', __('Nom du rayon', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description du rayon', 'nexo'));

        // XSS Cleaner
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
		$PageNow			=	'nexo/rayons/list';

        if ($page == 'add') {
            $this->Gui->set_title( store_title( __('Créer un nouveau rayon', 'nexo') ));
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_radius');

            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_RAYON', 'table'    =>    store_prefix() . 'nexo_articles' )
            ));
        } else {
            $this->Gui->set_title( store_title( __('Liste des rayons', 'nexo')) );
        }

        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'rayons';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }

    public function add()
    {
		global $PageNow;
		$PageNow			=	'nexo/rayons/add';

        if (! User::can('create_shop_radius')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'rayons';
        $this->Gui->set_title( store_title( __( 'Créer un nouveau rayon', 'nexo' ) ) );
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }

    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Rayons($this->args);
