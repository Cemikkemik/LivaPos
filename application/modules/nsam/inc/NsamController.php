<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NsamController extends CI_Model{

    /**
     *  Content Management
     *  @return void
    **/

    public function content_management()
    {
        if( ! is_multistore() ) {
            return show_error( __( 'This feature is only available for multistore.', 'nsam' ) );
        }

        $this->load->model( 'Nexo_Stores' );
        $data[ 'stores' ]     =   $this->Nexo_Stores->get( get_store_id(), 'ID !=' );
        $this->Gui->set_title( __( 'Content Management &mdash; NexoPOS', 'nsam' ) );
        $this->load->module_view( 'nsam', 'content_management', $data );
    }

    /**
     *  module controller
     *  @param void
     *  @return void
    **/

    public function module_control()
    {
        echo 'Hello World';
    }

    /**
     *  Subscription CRUD
     *  @param
     *  @return
    **/

    public function subscriptions()
    {
        echo 'Subscription';
    }


}
