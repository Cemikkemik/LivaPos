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
		global $PageNow;		
		$PageNow 	=	'nexo/settings';
		
        if (
            User::can('create_options') &&
            User::can('edit_options') &&
            User::can('delete_options')
        ) {
            if ($page == 'home') {
                $this->Gui->set_title( store_title( __('Réglages Généraux', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'checkout') {
                $this->Gui->set_title( store_title( __('Réglages de la caisse', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'items') {
                $this->Gui->set_title( store_title( __('Réglages des produits', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'customers') {
                $this->Gui->set_title( store_title( __('Réglages des clients', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'email') {
                $this->Gui->set_title( store_title( __('Réglages sur les emails', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'payments-gateways') {
                $this->Gui->set_title( store_title( __('Réglages sur les passerelles de paiments', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'reset') {
                $this->Gui->set_title( store_title( __('Réglages de la reinitialisation', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
			} elseif ($page == 'invoices') { // @since 2.7.9
                $this->Gui->set_title( store_title( __('Réglages des factures/reçu de caisse', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            } elseif ($page == 'keyboard') { // @since 2.7.9
                $this->Gui->set_title( store_title( __('Réglages Clavier', 'nexo')));
                $this->load->view("../modules/nexo/views/settings/{$page}.php");
            }
			// Settings are now handled by another module
			 elseif ($page == 'stripe') {
                $this->Gui->set_title( store_title( __('Réglages Stripe', 'nexo')));
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
