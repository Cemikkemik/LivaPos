<?php 
class Nexo_Controller extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_filter('admin_menus', array( $this, 'menus' ));
        $this->events->add_action('load_dashboard', array( $this, 'load_dashboard' ));
        $this->events->add_action('load_frontend', array( $this, 'frontend' ));
    }
    public function frontend()
    {
        global $CurrentScreen, $CurrentMethod;
        if ($CurrentScreen == 'validate_license') {
            echo json_encode(array(
                'is_valid'            =>    true,
                'license_duration'    =>    60
            ));
        }
    }
    public function menus($final)
    {
        global $Nexo_Menus;
        
        $Nexo_Menus    =    array();
        
        $this->events->do_action('nexo_before_checkout', $Nexo_Menus);
        
        if (
            User::can('create_shop_orders') ||
            User::can('edit_shop_orders') ||
            User::can('delete_shop_orders') ||
            User::can('create_shop_order_types') ||
            User::can('edit_shop_order_types') ||
            User::can('delete_shop_order_types') ||
            User::can('create_shop_payments_means') ||
            User::can('edit_shop_payments_means') ||
            User::can('delete_shop_payments_means')
        ) {
            $Nexo_Menus[ 'caisse' ]        =    array(
                array(
                    'title'            =>        __('Caisse', 'nexo'), // menu title
                    'icon'            =>        'fa fa-shopping-cart', // menu icon
                    'href'            =>        site_url('dashboard/foo'), // url to the page,
                    'disable'        =>    true
                ),
                array(
                    'title'       =>    __('Liste des commandes', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/commandes/lists'), // url to the page,
                ),
                array(
                    'title'       =>    __('Nouvelle commande', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/commandes/lists/add'), // url to the page,
                ),
				array(
                    'title'       =>    __('Effectuer une vente', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/commandes/lists/v2_add'), // url to the page,
                ),
                array(
                    'title'       =>    __('Liste des moyens de paiment', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/paiements/lists'), // url to the page,
                ),
                array(
                    'title'       =>    __('Ajouter un moyen de paiment', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/paiements/lists/add'), // url to the page,
                ),
				/**
                array(
                    'title'       =>    __('Liste des types de commandes', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/types_de_commandes/lists'), // url to the page,
                ),
                array(
                    'title'       =>    __('Ajouter un type de commande', 'nexo'), // menu title
                    'icon'        =>    'fa fa-star', // menu icon
                    'href'        =>    site_url('dashboard/nexo/types_de_commandes/lists/add'), // url to the page,
                ),
				**/
            );
        }
        
        $this->events->do_action('nexo_before_customers', $Nexo_Menus);
        
        if (
            User::can('create_shop_customers') ||
            User::can('edit_shop_customers') ||
            User::can('delete_shop_customers') ||
            User::can('create_shop_customers_groups') ||
            User::can('edit_shop_customers_groups') ||
            User::can('delete_shop_customers_groups')
        ) {
            $Nexo_Menus[ 'clients' ]        =    $this->events->apply_filters('nexo_customers_menu_array', array(
                array(
                    'title'        =>    __('Clients', 'nexo'),
                    'href'        =>    '#',
                    'disable'    =>    true,
                    'icon'        =>    'fa fa-users'
                ),
                array(
                    'title'        =>    __('Liste des clients', 'nexo'),
                    'href'        =>    site_url('dashboard/nexo/clients/lists'),
                ),
                array(
                    'title'        =>    __('Ajouter un client', 'nexo'),
                    'href'        =>    site_url('dashboard/nexo/clients/lists/add'),
                ),
                array(
                    'title'        =>    __('Groupes', 'nexo'),
                    'href'        =>    site_url('dashboard/nexo/clients/groups/list'),
                ),
                array(
                    'title'        =>    __('Ajouter un groupe', 'nexo'),
                    'href'        =>    site_url('dashboard/nexo/clients/groups/list/add'),
                )
            ));
        }
        
        $this->events->do_action('nexo_before_shipping', $Nexo_Menus);
        
        if (
            User::can('create_shop_items') ||
            User::can('edit_shop_items') ||
            User::can('delete_shop_items') ||
            User::can('create_shop_categories') ||
            User::can('edit_shop_categories') ||
            User::can('delete_shop_categories') ||
            User::can('create_shop_radius') ||
            User::can('edit_shop_radius') ||
            User::can('delete_shop_radius') ||
            User::can('create_shop_providers') ||
            User::can('edit_shop_providers') ||
            User::can('delete_shop_providers') ||
            User::can('create_shop_shippings') ||
            User::can('edit_shop_shippings') ||
            User::can('delete_shop_shippings')
        ) {
            $Nexo_Menus[ 'arrivages' ]    =    $this->events->apply_filters('nexo_shipping_menu_array', array(
            array(
                'title'        =>    __('Arrivages', 'nexo'),
                'href'        =>    '#',
                'disable'    =>    true,
                'icon'        =>    'fa fa-truck'
            ),
            array(
                'title'        =>    __('Liste des livraisons', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/arrivages/lists'),
            ),
            array(
                'title'        =>    __('Nouvelle livraison', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/arrivages/add'),
            ),
            array(
                'title'        =>    __('Liste des articles', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/produits/lists'),
            ),
            array(
                'title'        =>    __('Ajouter un article', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/produits/lists/add'),
            ),
            array(
                'title'        =>    __('Liste des fournisseurs', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/fournisseurs/lists'),
            ),
            array(
                'title'        =>    __('Ajouter un fournisseur', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/fournisseurs/lists/add'),
            ),
            array(
                'title'        =>    __('Liste des rayons', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/rayons/lists'),
            ),
            array(
                'title'        =>    __('Ajouter un rayon', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/rayons/lists/add'),
            ),
            array(
                'title'        =>    __('Liste des catégories', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/categories/lists'),
            ),
            array(
                'title'        =>    __('Ajouter une catégorie', 'nexo'),
                'href'        =>    site_url('dashboard/nexo/categories/lists/add'),
            ),
        ));
        }
        
        $this->events->do_action('nexo_before_reports', $Nexo_Menus);
        
        if (User::can('read_shop_reports')) {
            $Nexo_Menus[ 'rapports' ]    =    $this->events->apply_filters('nexo_reports_menu_array', array(
                array(
                    'title'        =>    __('Rapports & Statistiques', 'nexo'),
                    'href'        =>    '#',
                    'disable'    =>    true,
                    'icon'        =>    'fa fa-bar-chart'
                ),
				array(
                    'title'       =>    __('Les meilleurs', 'nexo'), // menu title
                    'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/fiche_de_suivi_de_stock'), // url to the page,
                ),
                array(
                    'title'       =>    __('Rapport Journalier', 'nexo'), // menu title
                    'href'        =>    site_url('dashboard/nexo/rapports/journalier'), // url to the page,
                ),
                array(
                    'title'       =>    __('Rendement Mensuel', 'nexo'), // menu title
                    'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/rendement_mensuel'), // url to the page,
                ),
                array(
                    'title'       =>    __('Statistiques des ventes', 'nexo'), // menu title
                    'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/statistique_des_ventes'), // url to the page,
                ),
                array(
                    'title'       =>    __('Fiche de suivi de stocks général', 'nexo'), // menu title
                    'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/fiche_de_suivi_de_stock'), // url to the page,
                ),
				array(
                    'title'       =>    __('Performances des caissiers', 'nexo'), // menu title
                    'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/fiche_de_suivi_de_stock'), // url to the page,
                ),
				array(
                    'title'       =>    __('Statistiques des clients', 'nexo'), // menu title
                    'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/fiche_de_suivi_de_stock'), // url to the page,
                ),
            ));
        }
        
        $this->events->do_action('nexo_before_accounting', $Nexo_Menus);
                
        $this->events->do_action('nexo_before_history', $Nexo_Menus);
        
        if (
            User::can('create_shop_backup') ||
            User::can('edit_shop_backup') ||
            User::can('delete_shop_backup') ||
            User::can('read_shop_user_tracker') ||
            User::can('delete_shop_user_tracker')
        ) {
            $Nexo_Menus[ 'activite' ]    =    $this->events->apply_filters('nexo_history_menu_array', array(
                array(
                    'title'            =>    __('Maintenance & Historique', 'nexo'),
                    'icon'            =>    'fa fa-shield',
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Historique des activités', 'nexo'),
                    'href'            =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url( array( 'dashboard', 'nexo', 'history' ) ),
                ),
                array(
                    'title'            =>    __('Importation / Exportation', 'nexo'),
                    'href'            =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url( array( 'dashboard', 'nexo', 'export_bdd' ) ),
                ),
            ));
        }
        
        $this->events->do_action('nexo_before_settings', $Nexo_Menus);
        
        if (
            User::can('create_options') ||
            User::can('edit_options') ||
            User::can('delete_options')
        ) {
            $Nexo_Menus[ 'nexo_settings' ]    =    $this->events->apply_filters('nexo_settings_menu_array', array(
                array(
                    'title'            =>    __('Réglages Nexo', 'nexo'),
                    'icon'            =>    'fa fa-gear',
                    'href'            =>    '#',
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Général', 'nexo'),
                    'icon'            =>    'fa fa-gear',
                    'href'            =>    site_url(array( 'dashboard', 'nexo', 'settings' ))
                ),
                array(
                    'title'            =>    __('Caisse', 'nexo'),
                    'icon'            =>    'fa fa-gear',
                    'href'            =>    site_url(array( 'dashboard', 'nexo', 'settings', 'checkout' ))
                ),
                array(
                    'title'            =>    __('Articles', 'nexo'),
                    'icon'            =>    'fa fa-gear',
                    'href'            =>    site_url(array( 'dashboard', 'nexo', 'settings', 'items' ))
                ),
                array(
                    'title'            =>    __('Clients', 'nexo'),
                    'icon'            =>    'fa fa-gear',
                    'href'            =>    site_url(array( 'dashboard', 'nexo', 'settings', 'customers' ))
                ),
                array(
                    'title'            =>    __('Réinitialisation', 'nexo'),
                    'icon'            =>    'fa fa-gear',
                    'href'            =>    site_url(array( 'dashboard', 'nexo', 'settings', 'reset' ))
                )
                
            ));
        }
        
        $start    =    array_slice($final, 0, 1);
        $end    =    array_slice($final, 1);
        $final    =    array_merge($start, $Nexo_Menus, $end);
        return $final;
    }
    
    public function load_dashboard()
    {
        $this->load->model('Nexo_Misc');
        $this->Gui->register_page('nexo', array( $this, 'load_controller' ));
    }
    public function load_controller()
    {
        $this->args    =    func_get_args();
        if (is_array($this->args) && count($this->args) > 0) {
            $file    =    dirname(__FILE__) . '/../__controllers/' . $this->args[0] . '.php';
            if (is_file($file)) {
                include_once($file);
            } else {
                show_error('Unable to find this file : ' . $file);
            }
        }
    }
}
new Nexo_Controller;
