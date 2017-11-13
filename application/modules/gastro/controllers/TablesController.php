<?php
class TablesController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

        /**
     *  Tables Header
     *  @param  void
     *  @return void
    **/

    private function __tables_crud()
    {
        if (
            ! User::can('create_restaurant_tables')  &&
            ! User::can('edit_restaurant_tables') &&
            ! User::can('delete_restaurant_tables')
        ) {
            // redirect(array( 'dashboard', 'access-denied' ));
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
        $crud->set_subject(__('Tables', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_restaurant_tables' ) );

        $fields					=	array( 'NAME', 'MAX_SEATS', 'STATUS',  'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR', 'DESCRIPTION' );
        $required_fields        =   [ 'NAME', 'STATUS', 'REF_AREA' ];

        if( store_option( 'disable_area_rooms' ) != 'yes' ) {
            array_splice( $fields, 1, 0, 'REF_AREA' );
            $required_fields[]  =   'MAX_SEATS';
        }

		$crud->columns( 'NAME', 'MAX_SEATS', 'STATUS', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR' );
        
        $crud->fields( $fields );

        $crud->display_as('NAME', __('Name', 'gastro'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('STATUS', __('Status', 'nexo'));
        $crud->display_as('AUTHOR', __('Author', 'nexo'));
        $crud->display_as('MAX_SEATS', __('Maximum Seats', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Created On', 'nexo'));
        $crud->display_as('DATE_MODIFICATION', __('Edited On', 'nexo'));
        $crud->display_as('REF_AREA', __('Area', 'nexo'));

        $crud->field_type( 'STATUS', 'dropdown', $this->config->item( 'gastro-table-status-for-crud' ) );

        $crud->field_type( 'DATE_MODIFICATION', 'hidden' );
        $crud->field_type( 'DATE_CREATION', 'hidden' );
        $crud->field_type( 'AUTHOR', 'invisible' );

        $crud->set_relation('REF_AREA', store_prefix() . 'nexo_restaurant_areas', 'NAME');
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->required_fields( $required_fields );

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
     *  Tables
     *  @param
     *  @return
    **/

    public function tables()
    {
        $this->Gui->set_title( store_title( __( 'Tables Lists', 'gastro' ) ) );
        $data[ 'crud_content' ]    =    $this->__tables_crud();
        $this->load->module_view( 'gastro', 'table-list-gui', $data );
    }
}