<?php
class Nexo_Payments_Means_Controller extends CI_Model
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
        if (
            ! User::can('edit_shop_payments_means') &&
            ! User::can('create_shop_payments_means') &&
            ! User::can('delete_shop_payments_means')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Moyen de paiement', 'nexo'));
        $crud->set_table($this->db->dbprefix('nexo_paiements'));
        $crud->columns('DESIGN', 'DESCRIPTION');
        $crud->fields('DESIGN', 'DESCRIPTION');
        
        $crud->display_as('DESIGN', __('Intitulé du moyen', 'nexo'));
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
    
    public function lists($page = 'index', $id = null)
    {
        if ($page == 'index') {
            $this->Gui->set_title(__('Liste des types de paiements &mdash; Nexo', 'nexo'));
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_payments_means');
            
            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'PAYMENT_TYPE', 'table'    =>    'nexo_commandes' )
            ));
        } else {
            $this->Gui->set_title(__('Liste des types de paiements &mdash; Nexo', 'nexo'));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'paiements';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
        if (! User::can('create_shop_payments_means')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'paiements';
        $this->Gui->set_title(__('Ajouter un nouveau type de paiement &mdash; Nexo', 'nexo'));
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Payments_Means_Controller($this->args);
