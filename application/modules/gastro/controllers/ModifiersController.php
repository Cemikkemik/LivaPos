<?php
class ModifiersController extends Tendoo_Module
{
    /**
     * Modifiers Group header
    **/

    private function modifiers_header()
    {
        if (
            ! User::in_group( [ 'master', 'shop_manager', 'shop_tester', 'administrator' ] )
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_subject(__( 'Modifiers', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_modifiers'));

		$fields				=	array( 'NAME', 'REF_CATEGORY', 'DEFAULT', 'PRICE', 'IMAGE', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );

		$crud->set_theme('bootstrap');

        $crud->columns( 'NAME', 'REF_CATEGORY', 'PRICE', 'DEFAULT', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );
        $crud->fields( $fields );

        $crud->display_as( 'NAME', __( 'Name', 'gastro' ) );
        $crud->display_as( 'REF_CATEGORY', __( 'Group', 'gastro' ) );
        $crud->display_as( 'DEFAULT', __( 'Default', 'gastro' ) );
        $crud->field_description( 'DEFAULT', tendoo_info( __( 'That is the default modifier which will be selected by default. If there is already a default modifiers, setting this as "default" will replace the previous default modifier.', 'nexo' ) ) );
        $crud->display_as( 'AUTHOR', __( 'Author', 'gastro' ) );
        $crud->display_as( 'PRICE', __( 'Price', 'gastro' ) );
        $crud->display_as( 'DATE_CREATION', __( 'Created On', 'gastro' ) );
        $crud->display_as( 'DATE_MODIFICATION', __( 'Edited On', 'gastro' ) );
        $crud->display_as( 'DESCRIPTION', __( 'Description', 'gastro' ) );
        $crud->display_as( 'IMAGE', __( 'Thumb', 'gastro' ) );
        
        $crud->set_field_upload('IMAGE', get_store_upload_path() . '/items-images/' );
    
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $crud->set_relation('REF_CATEGORY', store_prefix() . 'nexo_restaurant_modifiers_categories', 'NAME' );
        
        $options    =   [
            0   =>  __( 'No', 'gastro' ),
            1   =>  __( 'Yes', 'gastro' )
        ];

        // Load Field Type
        $crud->field_type( 'DEFAULT', 'dropdown', $options );

        // Callback avant l'insertion
        $crud->callback_before_insert(array( $this, '__modifiers_insert' ));
        $crud->callback_before_update(array( $this, '__modifiers_update' ));

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        // Field Visibility
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MODIFICATION', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');

        $crud->required_fields( 'NAME', 'REF_CATEGORY' );

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
     * Modifiers
     * @param int modifiers
    **/

    public function modifiers()
    {
        $data[ 'crud' ]     =   $this->modifiers_header();
        $this->Gui->set_title( store_title( __( 'Modifiers', 'gastro' ) ) );
        $this->load->module_view( 'gastro', 'modifiers.gui', $data );
    }

    public function __modifiers_insert( $data ) 
    {
        $data[ 'AUTHOR' ]           =   User::id();
        $data[ 'DATE_CREATION' ]    =   date_now();

        // change default modifeirs
        $defaults       =   $this->db
        ->where( 'REF_CATEGORY', $data[ 'REF_CATEGORY' ] )
        ->where( 'DEFAULT', 1 )
        ->get( store_prefix() . 'nexo_restaurant_modifiers' )
        ->result_array();

        // change current default.
        if( $defaults ) {
            $this->db->where( 'ID', $defaults[0][ 'ID' ] )
            ->update( store_prefix() . 'nexo_restaurant_modifiers', [
                'DEFAULT'   =>  0
            ]);
        }

        return $data;
    }

    /**
     * Update modifers
    **/

    public function __modifiers_update( $data, $primary_key )
    {
        $data[ 'AUTHOR' ]               =   User::id();
        $data[ 'DATE_MODIFICATION' ]    =   date_now();

        // change default modifeirs
        $defaults       =   $this->db
        ->where( 'REF_CATEGORY', $data[ 'REF_CATEGORY' ] )
        ->where( 'ID !=', $primary_key )
        ->where( 'DEFAULT', 1 )
        ->get( store_prefix() . 'nexo_restaurant_modifiers' )
        ->result_array();

        // change current default.
        if( $defaults ) {
            $this->db->where( 'ID', $defaults[0][ 'ID' ] )
            ->update( store_prefix() . 'nexo_restaurant_modifiers', [
                'DEFAULT'   =>  0
            ]);
        }

        return $data;
    }

    /**
     * Modifiers Group header
    **/

    private function modifiers_groups_header()
    {
        if (
            ! User::in_group( [ 'master', 'shop_manager', 'shop_tester', 'administrator' ] )
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_subject(__( 'Modifiers Groups', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_modifiers_categories'));

		$fields				=	array( 'NAME', 'FORCED', 'MULTISELECT', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );

		$crud->set_theme('bootstrap');

        $crud->columns( 'NAME', 'AUTHOR', 'FORCED', 'MULTISELECT', 'DATE_CREATION', 'DATE_MODIFICATION');
        $crud->fields( $fields );

        $crud->display_as( 'NAME', __( 'Name', 'gastro' ) );
        $crud->display_as( 'FORCED', __( 'Forced Modifiers', 'gastro' ) );
        $crud->field_description( 'FORCED', __( 'If enabled, will force modifier selection.', 'gastro' ) );
        $crud->display_as( 'MULTISELECT', __( 'Multiselect', 'gastro' ) );
        $crud->field_description( 'MULTISELECT', __( 'if enabled this will allow more than 1 modifiers per item.', 'gastro' ) );
        $crud->display_as( 'AUTHOR', __( 'Author', 'gastro' ) );
        $crud->display_as( 'DATE_CREATION', __( 'Created On', 'gastro' ) );
        $crud->display_as( 'DATE_MODIFICATION', __( 'Edited On', 'gastro' ) );
        $crud->display_as( 'DESCRIPTION', __( 'Description', 'gastro' ) );
    
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $options    =   [
            0   =>  __( 'No', 'gastro' ),
            1   =>  __( 'Yes', 'gastro' )
        ];

        // Load Field Type
        $crud->field_type( 'MULTISELECT', 'dropdown', $options );
        $crud->field_type( 'FORCED', 'dropdown', $options );

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

        $crud->required_fields( 'NAME', 'FORCED', 'MULTISELECT' );

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
     * Modifiers Groups
     * @param int modifiers
    **/

    public function modifiers_groups()
    {
        $data[ 'crud' ]     =   $this->modifiers_groups_header();
        $this->Gui->set_title( store_title( __( 'Modifiers Groups', 'gastro' ) ) );
        $this->load->module_view( 'gastro', 'modifiers.groups-gui', $data );
    }

    /**
     * Group Insert
    **/

    public function __group_insert( $data ) 
    {
        $data[ 'AUTHOR' ]           =   User::id();
        $data[ 'DATE_CREATION' ]    =   date_now();

        return $data;
    }

    /**
     * Group update
    **/

    public function __group_update( $data ) 
    {
        $data[ 'AUTHOR' ]               =   User::id();
        $data[ 'DATE_MODIFICATION' ]    =   date_now();

        return $data;
    }
}