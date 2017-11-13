<?php 
class AreasController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  tables Area CRud
     *  @param
     *  @return
    **/

    private function __areas_crud()
    {
        if (
            ! User::can('create_restaurant_areas')  &&
            ! User::can('edit_restaurant_areas') &&
            ! User::can('delete_restaurant_areas')
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
        $crud->set_subject(__('Areas', 'nexo'));
		$crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_restaurant_areas' ) );

        $fields					=	array( 'NAME', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR', 'DESCRIPTION' );

		$crud->columns( 'NAME', 'DATE_CREATION', 'DATE_MODIFICATION', 'AUTHOR' );
        $crud->fields( $fields );

        $crud->display_as('NAME', __('Name', 'gastro'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('AUTHOR', __('Author', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Created On', 'nexo'));
        $crud->display_as('DATE_MODIFICATION', __('Edited On', 'nexo'));
        $crud->display_as('REF_ROOM', __('Room', 'nexo'));

        $crud->field_type( 'DATE_MODIFICATION', 'hidden' );
        $crud->field_type( 'DATE_CREATION', 'hidden' );
        $crud->field_type( 'AUTHOR', 'invisible' );
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->required_fields( 'NAME' );

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
     *  Areas
     *  @param
     *  @return
    **/

    public function areas()
    {
        $this->Gui->set_title( store_title( __( 'Restaurant Areas', 'gastro' ) ) );
        $data[ 'crud_content' ]    =    $this->__areas_crud();
        $this->load->module_view( 'gastro', 'areas-list-gui', $data );
    }
}