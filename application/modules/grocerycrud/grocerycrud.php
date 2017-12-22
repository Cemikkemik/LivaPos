<?php
class GroceryCrudModule extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action('load_dashboard', array( $this, 'init' ));
    }
    public function init()
    {
        global $Options;
        $language        =    'english';
        switch (@$Options[ 'site_language' ]) {
            case 'en_US' : $language    =    'english'; break;
            case 'fr_FR' : $language    =    'french'; break;
            case 'tr_TR' : $language    =    'turkish'; break;
            case 'de_DE' : $language    =    'dutch'; break;
            case 'es_ES' : $language    =    'spanish'; break;
        }
        
        $this->config->load('grocery_crud');
        $this->config->set_item('grocery_crud_default_language', $language);
        $this->config->set_item( 'options', __( 'Options', 'grocerycrud' ) );
        $this->load->library('Grocery_CRUD');
        $this->load->library('GroceryCrudCleaner');
    }
}
new GroceryCrudModule;
