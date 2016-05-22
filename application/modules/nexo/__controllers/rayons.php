<?php
class Nexo_Rayons extends CI_Model
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
            ! User::can('edit_shop_radius') &&
            ! User::can('create_shop_radius') &&
            ! User::can('delete_shop_radius')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Rayons', 'nexo'));
        $crud->set_table($this->db->dbprefix('nexo_rayons'));
        $crud->columns('TITRE', 'DESCRIPTION');
        $crud->fields('TITRE', 'DESCRIPTION');
        
        $crud->display_as('TITRE', __('Nom du rayon', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description du rayon', 'nexo'));
        
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
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
    
    public function lists($page = 'index', $id = null)
    {
        if ($page == 'add') {
            $this->Gui->set_title(__('Créer un nouveau rayon &mdash; Nexo', 'nexo'));
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_radius');
            
            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_RAYON', 'table'    =>    'nexo_articles' )
            ));
        } else {
            $this->Gui->set_title(__('Liste des rayons &mdash; Nexo', 'nexo'));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1    =    'rayons';
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function add()
    {
        if (! User::can('create_shop_radius')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
        $_var1                    =    'rayons';
        $this->Gui->set_title(__('Créer une nouveau rayon &mdash; Nexo', 'nexo'));
        $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Rayons($this->args);
