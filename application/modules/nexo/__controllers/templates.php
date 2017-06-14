<?php
class Nexo_Templates_Controller extends Tendoo_Module
{
    /**
     * Load Template for Customers
     * @param string tab
     * @return string
    **/
    
    public function customers_form( )
    {
        return $this->load->module_view( 'nexo', 'customers.form-template' );
    }

    public function customers_main()
    {
        return $this->load->module_view( 'nexo', 'customers.main-template' );
    }

    public function load( $view )
    {
        return $this->load->module_view( 'nexo', $view );
    }

    /**
     * Shippings
     * @return string view
    **/

    public function shippings( $template = 'main-template' )
    {
        return $this->load->module_view( 'nexo', 'shippings.' . $template );
    }
}