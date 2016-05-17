<?php
class Nexo_Commandes extends CI_Model
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
        global $Options;
        $this->load->model('Nexo_Checkout');
        $this->load->model('Nexo_Misc');
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Commandes', 'nexo'));

        $crud->set_table($this->db->dbprefix('nexo_commandes'));
        $cols            =    array( 'CODE', 'REF_CLIENT', 'SOMME_PERCU', 'TOTAL', 'REMISE', 'PAYMENT_TYPE', 'TYPE', 'DATE_CREATION', 'DATE_MOD', 'AUTHOR' );
        
        if (@$Options[ 'nexo_enable_vat' ] == 'oui') {
            array_splice($cols, 5, 0, 'TVA');
        }
        
        $crud->columns($cols);
        
        $fields            =    array( 'RABAIS', 'RISTOURNE', 'TYPE', 'CODE', 'DATE_CREATION', 'DATE_MOD', 'TOTAL', 'AUTHOR', 'DISCOUNT_TYPE' );
        
        // Add custom Actions
        $crud->add_action(__('Imprimer le ticket de caisse', 'nexo'), '', site_url(array( 'dashboard', 'nexo', 'print', 'order_receipt' )) . '/', 'btn btn-success fa fa-file');
        
        if (@$Options[ 'nexo_display_select_client' ] === 'enable') {
            $fields[]    =    'REF_CLIENT';
        }
        
        if (@$Options[ 'nexo_display_payment_means' ] === 'enable') {
            $fields[]    =    'PAYMENT_TYPE';
        }
        
        if (@$Options[ 'nexo_display_amount_received' ] === 'enable') {
            $fields[]    =    'SOMME_PERCU';
        }
        
        if (@$Options[ 'nexo_display_discount' ] === 'enable') {
            $fields[]    =    'REMISE';
        }
        
        if (@$Options[ 'nexo_enable_vat' ] == 'oui') {
            $fields[]    =    'TVA';
        }
        
        call_user_func_array(array( $crud, 'fields' ), $fields);
        
        $crud->display_as('CODE', __('Code', 'nexo'));
        $crud->display_as('REF_CLIENT', __('Client', 'nexo'));
        $crud->display_as('REMISE', __('Remise Expresse', 'nexo'));
        $crud->display_as('SOMME_PERCU', __('Somme perçu', 'nexo'));
        $crud->display_as('AUTHOR', __('Opérateur', 'nexo'));
        $crud->display_as('PAYMENT_TYPE', __('Mode de paiment', 'nexo'));
        $crud->display_as('TYPE', __('Type de la commande', 'nexo'));
        $crud->display_as('TVA', __('TVA', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Date de création', 'nexo'));
        $crud->display_as('DATE_MOD', __('Date de modification', 'nexo'));
        $crud->display_as('TOTAL', __('Total', 'nexo'));
        
        $crud->set_relation('REF_CLIENT', 'nexo_clients', 'NOM');
        $crud->set_relation('TYPE', 'nexo_types_de_commandes', 'DESIGN');
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $crud->set_relation('PAYMENT_TYPE', 'nexo_paiements', 'DESIGN');
        
        $crud->change_field_type('TYPE', 'invisible');
        $crud->change_field_type('RABAIS', 'invisible');
        $crud->change_field_type('RISTOURNE', 'invisible');
        $crud->change_field_type('CODE', 'invisible');
        $crud->change_field_type('TOTAL', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('AUTHOR', 'readonly');
        $crud->change_field_type('DISCOUNT_TYPE', 'invisible');
        $crud->change_field_type('TVA', 'invisible');
		
		// XSS Cleaner
		$this->events->add_filter( 'grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ) );
		$this->events->add_filter( 'grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ) );
        
        // $crud->required_fields( 'PAYMENT_TYPE', 'SOMME_PERCU' );
        $crud->callback_before_insert(array( $this->Nexo_Checkout, 'commandes_save' ));
        $crud->callback_before_update(array( $this->Nexo_Checkout, 'commandes_update' ));
        $crud->callback_before_delete(array( $this->Nexo_Checkout, 'commandes_delete' ));
        
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
    
    public function lists($page = 'home', $id = null)
    {
        global $NexoEditScreen, $NexoAddScreen;
        $NexoEditScreen    =    ( bool ) preg_match('#dashboard\/nexo/commandes\/lists\/edit#', uri_string());
        $NexoAddScreen    =    ( bool ) preg_match('#dashboard\/nexo/commandes\/lists\/add#', uri_string());
        
        $this->events->add_action('dashboard_header', function () use ($NexoAddScreen,$NexoEditScreen) {
            /** 
             * We Want to make sure that nothing appear before checkout load
            **/
            if ($NexoAddScreen || $NexoEditScreen) {
                ?>
            <style type="text/css">
			#meta-produits, .content-wrapper .content, .content-header {
				display:none;
			}
			</style>
			<?php

            }
        });
        
        if ($page == 'add') {
            // Add Moment Library
            $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
            // $this->enqueue->js( '../modules/nexo/bower_components/bootstrap-tour/build/js/bootstrap-tour.min' );
            // $this->enqueue->css( '../modules/nexo/bower_components/bootstrap-tour/build/css/bootstrap-tour.min' );
            $this->enqueue->js('../modules/nexo/js/core/checkout-customer-creation');
            
            // Protecting
            if (! User::can('create_orders')) {
				redirect(array( 'dashboard', 'access-denied?from=Nexo_orders_conroller' ));
			}
            
            $data[ 'crud_content' ]    =    $this->crud_header();
            $_var1    =    'commandes';
            $this->Gui->set_title(__('Créer une nouvelle commande &mdash; Nexo', 'nexo'));
            $this->load->view('../modules/nexo/views/' . $_var1 . '-new.php', $data);
        } elseif ($page == 'edit') {
            // Add Moment Library
            $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
            
            global $order_id;
            $order_id    =    $id;
            // Protecting
            if (! User::can('edit_orders')){
				redirect(array( 'dashboard', 'access-denied?from=Nexo_orders_conroller' ));
			}
            
            $data[ 'crud_content' ]    =    $this->crud_header();
            $_var1    =    'commandes';
            $this->Gui->set_title(__('Modifier une commande existante &mdash; Nexo', 'nexo'));
            $this->load->view('../modules/nexo/views/' . $_var1 . '-edit.php', $data);
        } elseif ($page == 'ajax_list') {
            if ($id == 'delete_selection') {
                $id_array = array();
                $selection = $this->input->post("selection", true);
                $id_array = explode("|", $selection);
                
                foreach ($id_array as $item):
                    if ($item != ''):
                        //DELETE ROW
                        $this->db->where('ID', $item);
                $this->db->delete('nexo_commandes');
                endif;
                endforeach;
            } else {
                $data[ 'crud_content' ]    =    $this->crud_header();
                $_var1    =    'commandes';
                $this->Gui->set_title(__('Liste des commandes &mdash; Nexo', 'nexo'));
                $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
            }
        } else {
            
            // Protecting
            if (! User::can('create_orders')) {
				redirect(array( 'dashboard', 'access-denied?from=Nexo_orders_conroller' ));
			}
            
            $data[ 'crud_content' ]    =    $this->crud_header();
            $_var1    =    'commandes';
            $this->Gui->set_title(__('Liste des commandes &mdash; Nexo', 'nexo'));
            $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
        }
    }
    
    public function defaults()
    {
        $this->lists();
    }
}
new Nexo_Commandes($this->args);
