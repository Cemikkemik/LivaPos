<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NexoCustomerDisplayController extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        //Codeigniter : Write Less Do More
    }

    /**
    * Index Page
    * @return void
    **/

    public function index()
    {
        show_404();
    }

    /**
    *
    * Cd list controller
    *
    * @return void
    */

    public function cd_list()
    {
        $this->load->model( 'NexoCustomerDisplayModel' );
        $this->Gui->set_title( __( 'Register List', 'nexo_customer_display' ) );
        $this->load->module_view( 'nexo_customer_display', 'register_list' );
    }

    /**
    * Open Display
    * @return void
    **/

    public function cd_open()
    {
        $array      =   array();
        $this->load->model( 'NexoCustomerDisplayModel' );
        $array[ 'sliders' ]     =   $this->NexoCustomerDisplayModel->get_sliders();
        // get Sliders
        $this->load->module_view( 'nexo_customer_display', 'customer_display', $array );
    }

    /**
    * Setup
    *
    **/

    public function cd_settings()
    {
        $this->Gui->set_title( __( 'Nexo Customer Display Settings', 'nexo_customer_display' ) );
        $this->load->module_view( 'nexo_customer_display', 'settings' );
    }

    /**
    * slider
    * Open slider
    **/

    public function cd_sliders()
    {
        $output     =     $this->sliders_header();

        $this->Gui->set_title( __( 'Sliders', 'nexo_customer_display' ) );
        $this->load->module_view( 'nexo_customer_display', 'slider-gui', array(
          'crud_content'    =>  $output
        ) );
    }

    /**
    * slider
    * Open slider
    **/

    public function cd_slides()
    {
        $output     =     $this->slides_header();
    }



    /**
    * Slider header
    **/

    private function sliders_header()
    {
        /**
        * This feature is not more accessible on main site when
        * multistore is enabled
        **/

        if( multistore_enabled() && ! is_multistore() ) {
          redirect( array( 'dashboard', 'feature-disabled' ) );
        }

        global $Options;

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Sliders', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_sliders'));

        $fields				=	array( 'TITLE', 'DESCRIPTION' );
        $crud->columns('TITLE', 'DESCRIPTION' ); // 'AUTHOR', 'DATE_CREATION'
        $crud->fields( $fields );

        $crud->display_as('TITLE', __('Slider Title', 'nexo_customer_display'));
        $crud->required_fields('TITLE');

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
    * Slides
    **/

    private function slides_header()
    {
        /**
        * This feature is not more accessible on main site when
        * multistore is enabled
        **/

        if( multistore_enabled() && ! is_multistore() ) {
          redirect( array( 'dashboard', 'feature-disabled' ) );
        }

        global $Options;

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Slides', 'nexo'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_slides'));

        $fields				=	array( 'TITLE', 'DESCRIPTION' );
        $crud->columns('TITRE', 'DESCRIPTION');
        $crud->fields( $fields );
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

}
