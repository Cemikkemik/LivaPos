<?php
class Nexo_Type_De_Commandes extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->defaults();
            }
        }
        return $this->defaults();
    }
    
    public function crud_header()
    {
        if (! User::can('manage_shop')) {
            redirect(array( 'dashboard?notice=access-denied' ));
        }
        
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Liste des types des commandes', 'nexo'));
        $crud->set_table($this->db->dbprefix('nexo_types_de_commandes'));
        $crud->columns('DESIGN', 'DESCRIPTION');
        $crud->fields('DESIGN', 'DESCRIPTION');
        
        $crud->display_as('DESIGN', __('Intitulé du type', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields('DESIGN');
        
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
    
    public function lists()
    {
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'paiements';
        $this->Gui->set_title(__('Liste des types de commandes &mdash; Nexo', 'nexo'));
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'paiements';
        $this->Gui->set_title(__('Créer un nouveau type de commande &mdash; Nexo', 'nexo'));
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Type_De_Commandes($this->args);
