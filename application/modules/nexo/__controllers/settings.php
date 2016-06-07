<?php
class Nexo_Settings_Controller extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 0) {
            if (method_exists($this, $args[0])) {
                return call_user_func_array(array( $this, $args[0] ), array_slice($args, 1));
            } else {
                return $this->index();
            }
        }
        return $this->index();
    }
    
    public function settings($page = 'home')
    {
        if (
            User::can('create_options') &&
            User::can('edit_options') &&
            User::can('delete_options')
        ) {
            if ($page == 'home') {
                $this->Gui->set_title(__('Réglages Généraux &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'checkout') {
                $this->Gui->set_title(__('Réglages de la caisse &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'items') {
                $this->Gui->set_title(__('Réglages des produits &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'customers') {
                $this->Gui->set_title(__('Réglages des clients &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'email') {
                $this->Gui->set_title(__('Réglages sur les emails &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'payments-gateways') {
                $this->Gui->set_title(__('Réglages sur les passerelles de paiments &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'reset') {
                $this->Gui->set_title(__('Réglages la reinitialisation &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'stripe') {
                $this->Gui->set_title(__('Réglages Stripe &mdash; Nexo', 'nexo'));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } else {
                show_404();
            }
        } else {
            redirect(array( 'dashboard', 'access-denied' ));
        }
    }
}
new Nexo_Settings_Controller($this->args);
