<?php
! defined( 'APPPATH' ) ? die() : NULL;

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
	
	public function nexo_daily_details_link( $string, $date )
	{
		return site_url( array( 'dashboard', 'nexo_premium', 'Controller_Rapport_Journalier_Detaille', $date . '?ref=' . urlencode( current_url() ) ) ) ;
	}
	
	/**
	 * Admin Menus
	 *
	 * @author Blair
	 * @return Array
	**/
	
	public function admin_menus( $menus )
	{
		$menus[ 'rapports' ]	=	$this->events->apply_filters( 'nexo_reports_menu_array', array(
			array( 
				'title'		=>	__( 'Rapports & Statistiques', 'nexo_premium' ),
				'href'		=>	'#',
				'disable'	=>	true,
				'icon'		=>	'fa fa-bar-chart'
			),
			array(
				'title'			=>	__( 'Les meilleurs', 'nexo_premium' ),
				'href'			=>	site_url('dashboard/nexo_premium/Controller_Best_of'),
			),
			array(
                'title'       =>	__( 'Journalier', 'nexo_premium' ), // menu title
                'href'        =>	site_url('dashboard/nexo/rapports/journalier'), // url to the page,
            ),
			
				array(
                'title'       =>	__( 'Flux de trésorerie', 'nexo_premium' ), // menu title
                'href'        =>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Mouvement_Annuel_Tresorerie' ) ), 
            ),
			
			array(
                'title'       =>	__( 'Ventes Annuelles', 'nexo_premium' ), // menu title
                'href'        =>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Stats_Des_Ventes' ) ), 
            ),
			
			array(
                'title'       =>	__( 'Performances des caissiers', 'nexo_premium' ), // menu title
                'href'        =>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Stats_Caissier' ) ), 
            ),
			
			array(
                'title'       =>	__( 'Statistique des clients', 'nexo_premium' ), // menu title
                'href'        =>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Stats_Clients' ) ), 
            ),
			
			array(
                'title'       =>	__( 'Fiche de Suivi de Stocks', 'nexo_premium' ), // menu title
                'href'        =>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Fiche_De_Suivi' ) ), // site_url('dashboard/nexo/rapports/Controller_Fiche_De_Suivi_de_stock'), // url to the page,
            ),
			
		) );
		
		if( User::can( 'manage_shop' ) ) {	
		
		$menus[ 'activite' ]	=	array(
			array(
				'title'			=>	__( 'Maintenance & Historique', 'nexo_premium' ),
				'icon'			=>	'fa fa-shield',
				'disable'		=>	true
			),
			array(
				'title'			=>	__( 'Historique des activités', 'nexo_premium' ),
				'href'			=>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Historique' ) ),
			),
			array(
				'title'			=>	__( 'Sauvegardes', 'nexo_premium' ),
				'href'			=>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Backup' ) ),
			),
			array(
				'title'			=>	__( 'Restauration', 'nexo_premium' ),
				'href'			=>	site_url( array( 'dashboard', 'nexo_premium', 'Controller_Restore' ) ),
			),
		);
		
		}
		
		return $menus;
	}
}