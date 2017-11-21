<?php
! defined('APPPATH') ? die() : null;

/**
 * Nexo Premium Hooks
 *
 * @author Blair Jersyer
**/

class Nexo_Premium_Filters extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Nexo Daily Link
     *
     * @return String
    **/

    public function nexo_daily_details_link($string, $date)
    {
        return site_url(array( 'dashboard', store_slug(), 'nexo_premium', 'Controller_Rapport_Journalier_Detaille', $date . '?ref=' . urlencode(current_url()) )) ;
    }

    /**
     * Admin Menus
     *
     * @author Blair
     * @return Array
    **/

    public function admin_menus($menus)
    {
		global $Options;
		// @since 2.8
		// Adjust menu when multistore is enabled
		$uri			=	$this->uri->segment(2,false);
		$store_uri		=	'';

		if( $uri == 'stores' || ! multistore_enabled() ) {

			// Only When Multi Store is enabled
			// @since 2.8

			if( @$Options[ 'nexo_store' ] == 'enabled' ) {
				$store_uri	=	'stores/' . $this->uri->segment( 3, 0 ) . '/';
			}

			$menus[ 'rapports' ]    =    $this->events->apply_filters('nexo_reports_menu_array', array(
				array(
					'title'        =>    __('Rapports', 'nexo_premium'),
					'href'        =>    '#',
					'disable'    =>    true,
					'icon'        =>    'fa fa-bar-chart',
					'permission'	=>	[
						'nexo.read.detailed-report',
						'nexo.read.best-sales',
						'nexo.read.daily-sales',
						'nexo.read.incomes-losses',
						'nexo.read.expenses-listings',
						'nexo.read.cash-flow',
						'nexo.read.annual-sales',
						'nexo.read.cashier-performances',
						'nexo.read.customer-statistics',
						'nexo.read.inventory-tracking',
					]
				),

				array(
					'title'         =>      __( 'Rapport Détaillés', 'nexo_premium' ),
					'href'          =>      site_url( 'dashboard/' . $store_uri . 'nexo/reports/detailed-sales' ),
					'permission'	=>		'nexo.read.detailed-report'
				),

				array(
					'title'            =>    __('Les meilleurs', 'nexo_premium'),
					'href'            =>    site_url('dashboard/' . $store_uri . 'nexo/reports/best-sellers'),
					'permission'	=>		'nexo.read.best-sales'
				),
				array(
					'title'       =>    __('Journalier', 'nexo_premium'), // menu title
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/reports/daily-sales'), // url to the page,
					'permission'	=>		'nexo.read.daily-sales'
				),
				array(
					'title'       =>    __('Bénéfices et Pertes', 'nexo_premium'), // menu title
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/reports/profit-and-losses'), // url to the page,
					'permission'	=>		'nexo.read.incomes-losses'
				),
				array(
					'title'       =>    __('Listing des dépenses', 'nexo_premium'), // menu title
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/reports/expenses'), // url to the page,
					'permission'	=>		'nexo.read.expenses-listings'
				),
				array(
					'title'       =>    __('Flux de trésorerie', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'reports', 'cash-flow' )),
					'permission'	=>		'nexo.read.cash-flow'
				),

				array(
					'title'       =>    __('Ventes Annuelles', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'reports', 'sales-stats' )),
					'permission'	=>		'nexo.read.annual-sales'
				),

				array(
					'title'       =>    __('Performances des caissiers', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'reports', 'cashiers' )),
					'permission'	=>		'nexo.read.cashier-performances'
				),

				array(
					'title'       =>    __('Statistique des clients', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'reports', 'customers' )),
					'permission'	=>		'nexo.read.customer-statistics'
				),

				array(
					'title'       =>    __('Fiche de Suivi de Stocks', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'reports', 'stock-tracking' )), // site_url('dashboard/nexo/rapports/Controller_Fiche_De_Suivi_de_stock'), // url to the page,
					'permission'	=>		'nexo.read.inventory-tracking'
				),
			));

			$menus[ 'factures' ]    =    array(
                array(
                    'title'            =>    __('Dépenses', 'nexo_premium'),
                    'href'            =>    '#',
					'icon'			=>	'fa fa-sticky-note-o',
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Liste des dépenses', 'nexo_premium'),
                    'href'            =>    dashboard_url([ 'expenses' ]),
                ),
                array(
                    'title'            =>    __('Nouvelle dépense', 'nexo_premium'),
                    'href'            =>    dashboard_url([ 'expenses', 'add' ])
                ),
				// @since 2.6.6
				[
					'title'		=>	__( 'Categories', 'nexo_premium' ),
					'href'		=>	dashboard_url([ 'expenses-categories' ])
				], [
					'title'		=>	__( 'Ajouter une categorie', 'nexo_premium' ),
					'href'		=>	dashboard_url([ 'expenses-categories', 'add' ])
				]
            );
		}

        return $menus;
    }
}
