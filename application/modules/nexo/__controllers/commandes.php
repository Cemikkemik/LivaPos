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
        if (
            ! User::can('edit_shop_orders')    &&
            ! User::can('create_shop_orders')    &&
            ! User::can('delete_shop_orders')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
            
        global $Options;
        $this->load->model('Nexo_Checkout');
        $this->load->model('Nexo_Misc');
        $this->load->config('nexo');
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Vente', 'nexo'));

        $crud->set_table($this->db->dbprefix('nexo_commandes'));
        $cols            =    array( 'CODE', 'REF_CLIENT', 'TOTAL', 'PAYMENT_TYPE', 'TYPE', 'DATE_CREATION', 'AUTHOR' );
        
        if (@$Options[ 'nexo_enable_vat' ] == 'oui') {
            array_splice($cols, 5, 0, 'TVA');
        }
        
        $crud->columns($cols);
        
        // $fields            =    array( 'RABAIS', 'RISTOURNE', 'TYPE', 'CODE', 'DATE_CREATION', 'DATE_MOD', 'TOTAL', 'AUTHOR', 'DISCOUNT_TYPE' );

        // Add custom Actions
        $crud->add_action(__('Imprimer le ticket de caisse', 'nexo'), '', site_url(array( 'dashboard', 'nexo', 'print', 'order_receipt' )) . '/', 'btn btn-info fa fa-file');
        
        // call_user_func_array(array( $crud, 'fields' ), $fields);

        $crud->display_as('CODE', __('Code', 'nexo'));
        $crud->display_as('REF_CLIENT', __('Client', 'nexo'));
        $crud->display_as('REMISE', __('Remise Expresse', 'nexo'));
        $crud->display_as('SOMME_PERCU', __('Somme perçu', 'nexo'));
        $crud->display_as('AUTHOR', __('Par', 'nexo'));
        $crud->display_as('PAYMENT_TYPE', __('Paiement', 'nexo'));
        $crud->display_as('TYPE', __('Statut', 'nexo'));
        $crud->display_as('TVA', __('TVA', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Date', 'nexo'));
        $crud->display_as('DATE_MOD', __('Date de modification', 'nexo'));
        $crud->display_as('TOTAL', __('Total', 'nexo'));
        
        $crud->set_relation('REF_CLIENT', 'nexo_clients', 'NOM');

        $crud->field_type('TYPE', 'dropdown', $this->config->item('nexo_order_types'));
        $crud->field_type('PAYMENT_TYPE', 'dropdown', $this->config->item('nexo_payment_types'));
        
        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        // $crud->set_relation('PAYMENT_TYPE', 'nexo_paiements', 'DESIGN');

        $crud->change_field_type('RABAIS', 'invisible');
        $crud->change_field_type('RISTOURNE', 'invisible');
        $crud->change_field_type('CODE', 'invisible');
        $crud->change_field_type('TOTAL', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');
        $crud->change_field_type('DISCOUNT_TYPE', 'invisible');
        $crud->change_field_type('TVA', 'invisible');
		
        
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        // Filter Class
        $this->events->add_filter('grocery_crud_list_item_class', array( $this, 'filter_grocery_list_item_class' ), 10, 2);
        $this->events->add_filter('grocery_filter_edit_button', array( $this, 'filter_edit_button' ), 10, 4);
        $this->events->add_filter('grocery_filter_actions', array( $this, 'filter_grocery_actions' ), 10, 3);

        
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
        global $NexoEditScreen, $NexoAddScreen, $Options;
        $NexoEditScreen    =    ( bool ) preg_match('#dashboard\/nexo/commandes\/lists\/edit#', uri_string());
        $NexoAddScreen    =    ( bool ) preg_match('#dashboard\/nexo/commandes\/lists\/add#', uri_string());
        
        $this->events->add_action('dashboard_header', function () use ($NexoAddScreen, $NexoEditScreen) {
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
            redirect(array( 'dashboard', 'nexo', 'commandes', 'lists' ));
            
            /**
             * Deprecated
            **/
        } elseif ($page == 'edit') {
            redirect(array( 'dashboard', 'nexo', 'commandes', 'lists' ));
            
            /**
             * Deprecated
            **/
        } elseif ($page == 'v2_checkout') {
            if (! User::can('create_shop_orders')) {
                redirect(array( 'dashboard', 'access-denied' ));
            }
            
            $data        =    array();
            // Prefetch order
            if ($id != null) {
                $this->load->model('Nexo_Checkout');
                
                $order        =    $this->Nexo_Checkout->get_order_products($id, true);
                
                if ($order) {
                    if (! User::can('edit_shop_orders')) {
                        redirect(array( 'dashboard', 'access-denied' ));
                    }
                    
                    
                    if (in_array($order[ 'order' ][0][ 'TYPE' ], array( 'nexo_order_comptant', 'nexo_order_advance' ))) {
                        redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=order_edit_not_allowed' ));
                    }
                
                    $data[ 'order' ]    =    $order;
                } else {
                    redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=order_not_found' ));
                }
            }
            
            if (@$Options[ 'default_compte_client' ] == null) {
                redirect(array( 'dashboard', 'nexo', 'settings', 'customers?notice=default-customer-required' ));
            }
            
            // Before Cols
            $this->events->add_filter('gui_before_rows', function ($content) {
                return $content . get_instance()->load->module_view('nexo', 'checkout/v2/options', array(), true);
            });
            
            $this->load->model('Nexo_Checkout');
            
            $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
            $this->enqueue->js('../plugins/bootstrap-select/dist/js/bootstrap-select.min');
            
            $this->enqueue->css('../modules/nexo/css/animate');
            $this->enqueue->css('../plugins/bootstrap-select/dist/css/bootstrap-select.min');

            if ($id == null) {
                $this->Gui->set_title(__('Effectuer un vente &mdash; NexoPOS', 'nexo'));
            } else {
                $this->Gui->set_title(__('Modifier une commande &mdash; NexoPOS', 'nexo'));
            }
            
            $this->load->view('../modules/nexo/views/checkout/v2/body.php', $data);
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_orders');
            
            $data[ 'crud_content' ]    =    $this->crud_header();
            $_var1                    =    'commandes';
            $this->Gui->set_title(__('Modifier une commande existante &mdash; Nexo', 'nexo'));
            $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
        } else {
            
            // Change add url
            $this->events->add_filter('grocery_add_url', function ($url) {
                return site_url(array( 'dashboard', 'nexo', 'commandes', 'lists', 'v2_checkout' ));
            });
            
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
    
    /**
     * Proceed order
     * @params int order int
     * @return void
    **/
    
    public function proceed($order_id)
    {
        $this->load->model('Nexo_Checkout');
        
        if ($this->Nexo_Checkout->proceed_order($order_id)) {
            redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=order_proceeded' ));
        }
        
        redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=advanced_required' ));
    }
    
    /**
     * filter_grocery_list_item_class
     * 
     * @params string
     * @params object Row Item
     * @return string
    **/
    
    public function filter_grocery_list_item_class($class, $row)
    {
        $Advance        =    'nexo_order_advance';
        $Cash            =   'nexo_order_comptant';
        $Estimate        =   'nexo_order_devis';
        
        $nexo_order_types    =    array_flip($this->config->item('nexo_order_types'));
        
        if (@$nexo_order_types[ $row->TYPE ]    == $Advance) {
            return 'info';
        } elseif (@$nexo_order_types[ $row->TYPE ] == $Cash) {
            return 'success';
        } elseif (@$nexo_order_types[ $row->TYPE ] == $Estimate) {
            return 'warning';
        } else {
            return $class;
        }
        return $class;
    }
    
    /**
     * Filter Edit button
     * Hide edit button for cash orders
    **/
    
    public function filter_edit_button($string, $row, $edit_text, $subject)
    {
        $Advance        =    'nexo_order_advance';
        $Cash            =   'nexo_order_comptant';
        $Estimate        =   'nexo_order_devis';
        
        $nexo_order_types    =    array_flip($this->config->item('nexo_order_types'));
        
        if (in_array(@$nexo_order_types[ $row->TYPE ], array( $Cash ))) {
            return;
        } elseif (in_array(@$nexo_order_types[ $row->TYPE ], array( $Estimate ))) {
            ob_start();
            ?>
            <a href='<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists', 'v2_checkout', $row->ID ));
            ?>' title='<?php echo $edit_text?> <?php echo $subject?>'>
                <span class='edit-icon fa fa-edit btn-default btn'></span>
            </a>
            <?php
            return ob_get_clean();
        } elseif (@$nexo_order_types[ $row->TYPE ] ==  $Advance) {
            ob_start();
            ?>
            <a href='<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'proceed', $row->ID ));
            ?>' title='<?php _e('Payer un commande', 'nexo');
            ?>'>
                <span class='edit-icon fa fa-money btn-success btn'></span>
            </a>
            <?php
            return ob_get_clean();
        }
        return $string;
    }
    
    /**
     * Filter Grocery Actions
     * Allow printing only on Complete orders
     * @params Array grocery actions
     * @return Array
    **/
    
    public function filter_grocery_actions($grocery_actions_obj, $actions, $row)
    {
        // var_dump( $actions );
        // return $grocery_actions_obj;
        foreach ($actions as $key => $action) {
            $order_type        =    array_flip($this->config->item('nexo_order_types'));
            if ($order_type[ $row->TYPE ] != 'nexo_order_comptant' && $action->css_class == 'btn btn-info fa fa-file') {
                unset($grocery_actions_obj[ $key ]);
            }
        }
        return $grocery_actions_obj;
    }
}
new Nexo_Commandes($this->args);
