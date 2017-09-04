<?php
class Nexo_Unit_Contoller extends Tendoo_Module
{
     public function __construct()
     {
          parent::__construct();
     }
     
     /**
     * Crud Header
     * @return object
     **/
     
     public function crud_header()
     {
          // if (
               //     ! User::can('create_unit_of_measure')  &&
               //     ! User::can('edit_unit_of_measure') &&
               //     ! User::can('delete_unit_of_measure') &&
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
          $crud->set_subject(__( 'Unités de mesure', 'nexo'));
          
          $crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_units'));
          
          // If Multi store is enabled
          // @since 2.8
          $fields					=	array( 'NAME', 'QUANTITY', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION' );
          $crud->columns( 'NAME', 'QUANTITY', 'AUTHOR', 'DATE_CREATION' );
          $crud->fields( $fields );
          
          $crud->set_relation('AUTHOR', 'aauth_users', 'name');
          
          $crud->order_by('DATE_CREATION', 'desc');
          $crud->field_description( 'NAME', __( 'Cette valeur vous permettra d\'identifier chaque différente unités de mesure', 'nexo' ) );
          $crud->field_description( 'QUANTITY', __( 'En considérant que la valeur de base vaut "1". Les quantités de cette unité de mesure se déterminent sur cette base. <br><strong>Par exemple</strong> : Une canette de bière contiendra 6 ou 8 bières, sachaque 1 bière est l\'unité de base.', 'nexo' ) );
          
          $crud->display_as('NAME', __('Nom de l\'unité de mesure', 'nexo'));
          $crud->display_as('QUANTITY', __('Quantity', 'nexo'));
          $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
          $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
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
          $post[ 'AUTHOR' ]             =    User::id();
          $post[ 'DATE_CREATION' ]      =    date_now();
          return $post;
     }

     /**
      * Edit
      * @return array
     **/

     public function __update( $post ) 
     {
          $post[ 'AUTHOR' ]             =    User::id();
          $post[ 'DATE_MOD' ]            =    date_now();
          return $post;
     }

     /**
      * index
      * @return void
     **/

     public function lists()
     {
          $this->Gui->set_title( store_title( __( 'Unités de mesure', 'nexo' ) ) );
          $this->load->module_view( 'nexo', 'units.gui', [ 'crud_content'   =>   $this->crud_header() ]);
     }
}
