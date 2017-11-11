<?php
class NexoSettingsController extends CI_Model
{
    public function home()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/home';
        $this->Gui->set_title( store_title( __('Réglages Généraux', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/home.php");
    }

    public function checkout()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/checkout';
        $this->Gui->set_title( store_title( __('Réglages de la caisse', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/checkout.php");
    }

    public function customers()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/checkout';
        $this->Gui->set_title( store_title( __('Réglages des clients', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/customers.php");
    }

    public function items()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/items';
        $this->Gui->set_title( store_title( __('Réglages des produits', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/items.php");
    }

    public function email()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/email';
        $this->Gui->set_title( store_title( __('Réglages des emails', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/email.php");
    }

    public function payments()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/gateways';
        $this->Gui->set_title( store_title( __('Réglages des paiements', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/payments-gateways.php");
    }

    public function reset()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/reset';
        $this->Gui->set_title( store_title( __('Réinitialisation & Démonstration', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/reset.php");
    }

    public function invoices()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/invoices';
        $this->Gui->set_title( store_title( __('Réglages des factures', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/invoices.php");
    }

    public function keyboard()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/keyboard';
        $this->Gui->set_title( store_title( __('Réglages du clavier', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/keyboard.php");
    }

    public function providers()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/providers';
        $this->Gui->set_title( store_title( __('Réglages des fournisseurs', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/providers.php");
    }

    public function orders()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/orders';
        $this->Gui->set_title( store_title( __('Réglages des commandes', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/orders.php");
    }

    public function stores()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/stores';
        $this->Gui->set_title( store_title( __('Réglages des boutiques', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/stores.php");
    }

    public function stripe()
    {
        global $PageNow;		
		$PageNow 	=	'nexo/stripe';
        $this->Gui->set_title( store_title( __('Réglages de Stripe', 'nexo')));
        $this->load->view("../modules/nexo/views/settings/stripe.php");
    }
}
