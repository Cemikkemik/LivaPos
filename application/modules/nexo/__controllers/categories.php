<?php
class Nexo_Categories extends CI_Model
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
            ! User::can('create_shop_categories')  &&
            ! User::can('edit_shop_categories') &&
            ! User::can('delete_shop_categories')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $crud = new grocery_CRUD();

        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Catégorie', 'nexo'));

        $crud->set_table($this->db->dbprefix('nexo_categories'));
        $crud->columns('NOM',  'PARENT_REF_ID', 'DESCRIPTION');
        $crud->fields('NOM', 'PARENT_REF_ID', 'DESCRIPTION');
        
        $state = $crud->getState();
        if ($state == 'add' || $state == 'edit' || $state == 'read') {
            $crud->set_relation('PARENT_REF_ID', 'nexo_categories', 'NOM');
        }
        
        $crud->display_as('NOM', __('Nom de la catégorie', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description de la catégorie', 'nexo'));
        $crud->display_as('PARENT_REF_ID', __('Catégorie parente', 'nexo'));
        
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields('NOM');
        
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
        $_var1                    =    'categories';
        $this->Gui->set_title(__('Liste des catégories &mdash; Nexo', 'nexo'));
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
        if (! User::can('create_shop_shippings')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'categories';
        $this->Gui->set_title(__('Créer une nouvelle catégorie &mdash; Nexo', 'nexo'));
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Categories($this->args);
