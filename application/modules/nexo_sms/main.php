<?php
class Nexo_Sms extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        
        $this->events->add_action('dashboard_header', array( $this, 'footer' ));
        
        // Extends Nexo Settings pages
        $this->events->add_filter('nexo_settings_menu_array', array( $this, 'sms_settings' ));
        
        // Create Dashboard
        $this->events->add_action('load_dashboard', array( $this, 'load_dashboard' ));
    }
    
    /**
     * Load Dashboard
    **/
    
    public function load_dashboard()
    {
        // Load Languages Lines
        $this->lang->load_lines(dirname(__FILE__) . '/language/lines.php');
        
        // Load Config
        $this->load->config('nexo_sms');
        
        // Register Page
        $this->Gui->register_page('nexo_sms', array( $this, 'nexo_sms_settings_controller' ));
    }
    
    /**
     * Footer
     * Load Javascript on Dashboard footer
    **/
    
    public function footer()
    {
        // Only on order screen
        if (preg_match('#dashboard/nexo/commandes/lists/v2_checkout#', uri_string())) {
            $this->load->module_view('nexo_sms', 'script');
        }
    }
    
    /**
     * SMS settings
    **/
    
    public function sms_settings($array)
    {
        $array    =    array_insert_after(2, $array, count($array), array(
            'title'        =>    __('SMS', 'nexo'),
            'icon'      =>    'fa fa-gear',
            'href'        =>    site_url(array( 'dashboard', 'nexo_sms', 'settings' ))
        ));
        
        return $array;
    }
    
    /**
     * Nexo SMS Settings Controller
    **/
    
    public function nexo_sms_settings_controller($page)
    {
        $this->Gui->set_title(__('Réglages SMS &mdash; NexoPOS', 'nexo_sms'));
        $this->load->module_view('nexo_sms', 'home');
    }
}
new Nexo_Sms;
