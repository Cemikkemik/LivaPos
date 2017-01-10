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

		if( $uri == 'stores' || in_array( @$Options[ 'nexo_store' ], array( null, 'disabled' ), true ) ) {

			// Only When Multi Store is enabled
			// @since 2.8

			if( @$Options[ 'nexo_store' ] == 'enabled' ) {
				$store_uri	=	'stores/' . $this->uri->segment( 3, 0 ) . '/';
			}

			if (User::can('read_shop_reports')) {
				$menus[ 'rapports' ]    =    $this->events->apply_filters('nexo_reports_menu_array', array(
				array(
					'title'        =>    __('Rapports', 'nexo_premium'),
					'href'        =>    '#',
					'disable'    =>    true,
					'icon'        =>    'fa fa-bar-chart'
				),

                array(
                    'title'         =>      __( 'Rapport Détaillés', 'nexo_premium' ),
                    'href'          =>      site_url( 'dashboard/' . $store_uri . 'nexo_premium/detailed_sales' )
                ),

				array(
					'title'            =>    __('Les meilleurs', 'nexo_premium'),
					'href'            =>    site_url('dashboard/' . $store_uri . 'nexo_premium/Controller_Best_of'),
				),
				array(
					'title'       =>    __('Journalier', 'nexo_premium'), // menu title
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rapports/journalier'), // url to the page,
				),
                array(
                    'title'       =>    __('Bénéfices et Pertes', 'nexo_premium'), // menu title
                    'href'        =>    site_url('dashboard/' . $store_uri . 'nexo_premium/rapports/profits_and_losses'), // url to the page,
                ),
                array(
                    'title'       =>    __('Listing des dépenses', 'nexo_premium'), // menu title
                    'href'        =>    site_url('dashboard/' . $store_uri . 'nexo_premium/rapports/expenses_listing'), // url to the page,
                ),
				array(
					'title'       =>    __('Flux de trésorerie', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Mouvement_Annuel_Tresorerie' )),
				),

				array(
					'title'       =>    __('Ventes Annuelles', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Stats_Des_Ventes' )),
				),

				array(
					'title'       =>    __('Performances des caissiers', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Stats_Caissier' )),
				),

				array(
					'title'       =>    __('Statistique des clients', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Stats_Clients' )),
				),

				array(
					'title'       =>    __('Fiche de Suivi de Stocks', 'nexo_premium'), // menu title
					'href'        =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Fiche_De_Suivi' )), // site_url('dashboard/nexo/rapports/Controller_Fiche_De_Suivi_de_stock'), // url to the page,
				),

			));
			}

			/**
			 * 	Disabled
			 *	User::can('create_shop_backup') ||
			 *	User::can('edit_shop_backup') ||
			 *	User::can('delete_shop_backup') ||
			 *	User::can('read_shop_user_tracker') ||
			 *	User::can('delete_shop_user_tracker')
			**/

			if (
				true == false
			) {
				if (
					User::can('read_shop_user_tracker') ||
					User::can('delete_shop_user_tracker')
				) {
					$menus[ 'activite' ]    =    array(
						array(
							'title'            =>    __('Maintenance & Historique', 'nexo_premium'),
							'icon'            =>    'fa fa-shield',
							'disable'        =>    true
						),
						array(
							'title'            =>    __('Historique des activités', 'nexo_premium'),
							'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Historique' )),
						)
					);
				}

				if (
					User::can('create_shop_backup') ||
					User::can('edit_shop_backup') ||
					User::can('delete_shop_backup')
				) {
					$menus[ 'activite' ][]    =    array(
						'title'            =>    __('Sauvegardes', 'nexo_premium'),
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Backup' )),
					);
					$menus[ 'activite' ][]    =    array(
						'title'            =>    __('Restauration', 'nexo_premium'),
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo_premium', 'Controller_Restore' )),
					);
				}
			}

		}

        return $menus;
    }
}
