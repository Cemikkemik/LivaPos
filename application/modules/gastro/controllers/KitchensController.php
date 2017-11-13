<?php
class KitchensController extends Tendoo_Module
{
    public function __construt()
    {
        parent::__construct();
    }

    public function kitchens( $page = 'lists', $arg2 = null )
	{
		$data						=	array();

		if( in_array( $page, [ 'lists', 'edit' ] ) ) {

			$data[ 'crud_content' ]    =    $this->kitchen_crud_header();

			if( $page == 'lists' && $arg2 != 'add' ) {
				$this->Gui->set_title( sprintf( __( 'Kitchen &mdash; %s', 'gastro'), get('core_signature')));
			} elseif( $page == 'lists' && $arg2 == 'add' ) {
				$this->Gui->set_title( sprintf( __( 'Create a new kitchen &mdash; %s', 'gastro'), get('core_signature')));
			} else {
                $this->Gui->set_title( __( 'Edit Kitchen', 'gastro')  );
            }

			$this->load->module_view( 'gastro', 'kitchens', $data );

		} elseif( $page == 'open' ) {

			$this->load->model( 'Nexo_Restaurantç' );

			$data[ 'kitchen' ]		=	$this->Nexo_Restaurant->get_kitchen( $arg2 );

			if( ! $data[ 'kitchen' ] ) {
				redirect( array( 'dashboard', 'unable-to-find-item' ) );
			}

			$this->Gui->set_title( sprintf( __( 'Open Kitchen : %s &mdash; %s', 'nexo_restaurant'), $data[ 'kitchen' ][0][ 'NAME' ], get('core_signature' ) ) );

			$this->load->module_view( 'gastro', 'open-kitchen-gui', $data );

		} elseif( $page == 'watch' ) {
            // angular dependencies
            $this->events->add_filter( 'dashboard_dependencies', function( $array ) {
                $array[]    =   'angularMoment';
                return $array;
            });

            // enqueue new style
            $this->enqueue->js( 'bower_components/angular-moment/angular-moment.min', module_url( 'gastro' ) );

            // Save Footer
            $this->events->add_action( 'dashboard_footer', function() {
                get_instance()->load->module_view( 'gastro', 'watch-kitchen-script' );
            });

            $this->load->module_model( 'gastro', 'Nexo_Restaurant_Kitchens' );            
            $data[ 'kitchen' ]      =   $this->Nexo_Restaurant_Kitchens->get( $arg2 );

            if( @$data[ 'kitchen' ][0][ 'NAME' ] ) {
                $this->Gui->set_title( store_title( sprintf( __( 'Watch Kitchen : %s', 'gastro' ), $data[ 'kitchen' ][0][ 'NAME' ] ) ) );
            } else {
                $this->Gui->set_title( store_title( __( 'Watch Kitchen', 'gastro' ) ) );
            }

            $this->load->module_view( 'gastro', 'watch-kitchen', $data );
        } else if( $page == 'waiter' ) {
            // angular dependencies
            $this->events->add_filter( 'dashboard_dependencies', function( $array ) {
                $array[]    =   'angularMoment';
                return $array;
            });

            // enqueue new style
            $this->enqueue->js( 'bower_components/angular-moment/angular-moment.min', module_url( 'gastro' ) );

            // Save Footer
            $this->events->add_action( 'dashboard_footer', function() {
                get_instance()->load->module_view( 'gastro', 'waiters-screen.script' );
            });

            $this->Gui->set_title( store_title( __( 'Ready Orders', 'gastro' ) ) );
            $this->load->module_model( 'gastro', 'Nexo_Restaurant_Kitchens' );

            $data[ 'kitchen' ]      =   $this->Nexo_Restaurant_Kitchens->get( $arg2 );

            $this->load->module_view( 'gastro', 'waiters-screen.gui', $data );
        } else {
            $data[ 'crud_content' ]    =    $this->kitchen_crud_header();
            $this->load->module_view( 'gastro', 'kitchens', $data );
        }
	}

	/**
	 * Kitchen controller CRUD header
	**/

	private function kitchen_crud_header()
	{
        if( store_option( 'disable_kitchen_screen' ) == 'yes' ) { 
            redirect([ 'dashboard', 'feature-disabled' ]);
        }

		// if (
        //     ! User::can('create_restaurant_kitchens')  &&
        //     ! User::can('edit_restaurant_kitchens') &&
        //     ! User::can('delete_restaurant_kitchens')
        // ) {
        //     redirect(array( 'dashboard', 'access-denied' ));
        // }

		$this->load->module_model( 'gastro', 'Nexo_Restaurant_Kitchens', 'Nexo_Restaurant' );    

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Restaurant Kitchen', 'nexo_restaurant'));

        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_restaurant_kitchens'));
        $crud->columns( 'NAME', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        $crud->fields( 'NAME', 'REF_CATEGORY', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );

        $crud->order_by( 'DATE_CREATION', 'asc');

        $crud->display_as('NAME', __('Name', 'nexo_restaurant'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo_restaurant'));
        $crud->display_as('REF_CATEGORY', __('Category', 'nexo_restaurant'));
		$crud->display_as('AUTHOR', __('Author', 'nexo_restaurant'));
		$crud->display_as('DATE_CREATION', __('Created on', 'nexo_restaurant'));
        $crud->display_as('DATE_MOD', __('Edited on', 'nexo_restaurant'));

        // $crud->field_description( 'REF_ROOM', __( 'All order proceeded from that room will be send to that kitchen (even to that kitchen printer).', 'gastro' ) );
        $crud->field_description( 'REF_CATEGORY', __( 'All items belonging to these selected category will be shown on this kitchen. If this kitchen don\'t have any category assigned, all order will be displayed on that kitchen.', 'nexo' ) );

		$crud->set_relation('AUTHOR', 'aauth_users', 'name');

		$crud->field_type( 'DATE_CREATION', 'hidden' );
		$crud->field_type( 'DATE_MOD', 'hidden' );
        $crud->field_type( 'AUTHOR', 'invisible' );
        
        // multiselect for categories
        $raw_categories         =   $this->db->get( store_prefix() . 'nexo_categories' )->result_array();
        $categories             =   [];
        foreach( $raw_categories as $category ) {
            $categories[ $category[ 'ID' ] ]    =   $category[ 'NOM' ];
        }
        $crud->field_type( 'REF_CATEGORY', 'multiselect', $categories );

		// Callback Before Render
        $crud->callback_before_insert(array( $this, 'callback_creating_kitchen' ));
        $crud->callback_before_update(array( $this, 'callback_editing_kitchen' ));

        // Liste des produits
        $crud->add_action(__('Open Kitchen', 'nexo_restaurant'), '', site_url(array( 'dashboard', store_slug(), 'gastro', 'kitchens', 'watch' )) . '/', 'btn btn-success fa fa-sign-in');

        // $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        // $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

		$crud->required_fields( 'NAME', 'REF_ROOM' );

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
}