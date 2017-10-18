<?php
class Nexo_Controller extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ), 20 );
		$this->events->add_filter( 'admin_menus', array( $this, 'menus' ), 15);
    }

    public function menus($final)
    {
		// @since 2.7.7
		global $Nexo_Menus, $Options;
		$Nexo_Menus    =    array();
		$this->events->do_action('nexo_before_checkout', $Nexo_Menus);

		/***
		 * Display Store Menu only when multi store is enabled
		 * @since 2.8
		**/

		if( store_option( 'nexo_store' ) == 'enabled' ) {
			$Nexo_Menus[ 'nexo_shop' ]        =    array(
				array(
					'title'		=>        __('Boutiques', 'nexo'), // menu title
					'icon'		=>        'fa fa-cubes', // menu icon
					'disable'	=>    true,
					'permission' 	=>	[ 
						'nexo.create.store', 
						'nexo.delete.store', 
						'nexo.delete.store', 
						'nexo.enter.store' 
					]
				)
			);

			// Create a new store
			$Nexo_Menus[ 'nexo_shop' ][]	=	array(
				'title'		=>        __('Liste des boutiques', 'nexo'), // menu title
				'href'		=>		site_url( array( 'dashboard', 'nexo', 'stores' ) ),
				'permission' 	=>		'nexo.view.stores'
			);

			$Nexo_Menus[ 'nexo_shop' ][]	=	array(
				'title'		=>        __('Ajouter une boutique', 'nexo'), // menu title
				'href'		=>		site_url( array( 'dashboard', 'nexo', 'stores', 'add' ) ),
				'permission' 	=>		'nexo.create.store'
			);

			$Nexo_Menus[ 'nexo_shop' ][]	=	array(
				'title'		=>        __('Boutiques', 'nexo'), // menu title
				'href'		=>		site_url( array( 'dashboard', 'nexo', 'stores', 'all' ) ),
				'permission'	=>		'nexo.enter.store'
			);
		}

		// @since 2.8
		// Adjust menu when multistore is enabled
		$uri			=	$this->uri->segment(2,false);
		$store_uri		=	'';

		if( $uri == 'stores' || in_array( @$Options[ 'nexo_store' ], array( null, 'disabled' ), true ) ) {

			// Only When Multi Store is enabled
			// @since 2.8

			if( @$Options[ 'nexo_store' ] == 'enabled' && $this->config->item( 'nexo_multi_store_enabled' ) ) {
				$store_uri	=	'nexo/stores/' . $this->uri->segment( 3, 0 ) . '/';
			}

			if( @$Options[ store_prefix() . 'nexo_enable_registers' ] == 'oui' ) {

				$Nexo_Menus[ 'caisse' ]        =    array(
					array(
						'title'		=> 	__('Caisses', 'nexo'), // menu title
						'icon'		=> 	'fa fa-inbox', // menu icon
						'disable'		=>    true,
						'permission'	=>	[ 
							'nexo.create.registers', 
							'nexo.edit.registers', 
							'nexo.delete.registers', 
							'nexo.use.registers',
							'nexo.view.registers'
						]
					)
				);

				$Nexo_Menus[ 'caisse' ][]		=	array(
					'title'       =>    __('Liste des caisses', 'nexo'), // menu title
					'icon'        =>    'fa fa-shopping-basket', // menu icon
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/registers/for_cashiers'), // url to the page,
					'permission'	=>	'nexo.view.registers'
				);

				$Nexo_Menus[ 'caisse' ][]		=	array(
					'title'       	=>    __('Liste des caisses', 'nexo'), // menu title
					'icon'        	=>    'fa fa-shopping-basket', // menu icon
					'href'        	=>    site_url('dashboard/' . $store_uri . 'nexo/registers/lists'), // url to the page,
					'permission'	=>	'nexo.view.registers'
				);

				$Nexo_Menus[ 'caisse' ][]		=	array(
					'title'       	=>    __('Ajouter une caisse', 'nexo'), // menu title
					'icon'        	=>    'fa fa-shopping-basket', // menu icon
					'href'        	=>    site_url('dashboard/' . $store_uri . 'nexo/registers/lists/add'), // url to the page,,
					'permission'	=>	'nexo.create.registers'
				);
			}


			if( in_array( store_option( 'nexo_enable_registers' ), array( null, 'non' ) ) ){
				$Nexo_Menus[ 'caisse' ][]		=	array(
					'title'       =>    __('Ouvrir le PDV', 'nexo'), // menu title
					'icon'        =>    'fa fa-shopping-cart', // menu icon
					'href'        =>    site_url('dashboard/' . $store_uri . 'pos'), // url to the page,
					'permission' 	=>	'nexo.use.registers'
				);
			}

			// @since 2.7.5

			$Nexo_Menus[ 'sales' ]			=	array(
				array(
					'title'       	=>    __('Ventes', 'nexo'), // menu title
					'icon'        	=>    'fa fa-shopping-basket', // menu icon
					'href'        	=>    site_url('dashboard/' . $store_uri . 'nexo/commandes/lists'), // url to the page,
					'permission'	=>	'nexo.view.registers'
				)
			);

		// Coupon Features
		// @since 3.0.1
		$this->events->do_action('nexo_before_coupons', $Nexo_Menus);

		if( store_option( 'disable_coupon' ) != 'yes' ) {
			$Nexo_Menus[ 'coupons' ]    =    $this->events->apply_filters('nexo_coupons_menu_array',[
				array(
					'title'            =>    __('Coupons', 'nexo'),
					'icon'            =>    'fa fa-ticket',
					'disable'           =>  true,
					'permission'		=>	[
						'nexo.create.coupons', 
						'nexo.delete.coupons', 
						'nexo.edit.coupons', 
						'nexo.view.coupons'
					]
				),
				array(
				    'title'            =>    __('Liste des coupons', 'nexo'),
					'href'            =>    site_url( array( 'dashboard', $store_uri . 'nexo_coupons', 'lists' )),
					'permission'		=>	'nexo.view.coupons'
				),
				[
					'title'            =>    __('Ajouter un coupon', 'nexo'),
					'href'            =>    site_url( array( 'dashboard', $store_uri . 'nexo_coupons', 'lists', 'add' )),
					'permission'		=>	'newo.create.coupons'
				]
			]);
		}

		$this->events->do_action('nexo_after_coupons', $Nexo_Menus);

		$this->events->do_action('nexo_before_shipping', $Nexo_Menus);

		$Nexo_Menus[ 'arrivages' ]    =    $this->events->apply_filters('nexo_shipping_menu_array', array(
			array(
				'title'        =>    __('Inventaire', 'nexo'),
				'href'        =>    '#',
				'disable'    =>    true,
				'icon'        =>    'fa fa-archive',
				'permission'	=>	[
					'nexo.create.items',
					'nexo.edit.items',
					'nexo.delete.items',
					'nexo.create.categories',
					'nexo.edit.categories',
					'nexo.delete.categories',
					'nexo.create.departments',
					'nexo.edit.departments',
					'nexo.delete.departments',
					'nexo.create.supplies',
					'nexo.edit.supplies',
					'nexo.delete.supplies',
					'nexo.create.taxes',
					'nexo.edit.taxes',
					'nexo.delete.taxes',
				]
			),
			array(
				'title'        =>    __('Approvisionnements', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/arrivages/lists'),
			),
			// @since 3.7
			array(
				'title'			=>	__( 'Nouvel Approvisionnement', 'nexo' ),
				'href'		 	=>	site_url([ 'dashboard', store_slug(), 'nexo', 'produits', 'add_supply' ])
			),
			// @since 3.0.20
			array(
				'title'		=>	__( 'Ajustement des quantités', 'nexo' ),
				'href'		=>	site_url([ 'dashboard', store_slug(), 'nexo', 'produits', 'stock_supply' ] )
			),
			array(
				'title'        =>    __('Liste des articles', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/produits/lists'),
			),
			array(
				'title'        =>    __('Ajouter un article', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/produits/lists/add'),
			),
			array(
				'title'         =>  __( 'Importer les articles', 'nexo' ),
				'href'          =>  site_url( array( 'dashboard', store_slug(), 'nexo_import', 'items' ) )
			),
			array(
				'title'        =>    __('Liste des taxes', 'nexo'),
				'route'		=>	 [ store_slug(), 'nexo', 'taxes' ],
				'controller'	=>	'Nexo_Taxes_Controller@index'
			),
			array(
				'title'        =>    __('Ajouter une taxe', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo_taxes/add'),
			),
			array(
				'title'        =>    __('Liste des départements', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rayons/lists'),
			),
			array(
				'title'        =>    __('Ajouter un département', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rayons/lists/add'),
			),
			array(
				'title'        =>    __('Liste des catégories', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/categories/lists'),
			),
			array(
				'title'        =>    __('Ajouter une catégorie', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/categories/lists/add'),
			)
		));

		$Nexo_Menus[ 'vendors' ]	=	array(
			array(
				'title'        =>    __('Fournisseurs', 'nexo'),
				'disable'        =>  true,
				'href'			=>	'#',
				'icon'			=>	'fa fa-truck',
				'permission'		=>	[
					'nexo.create.suppliers',
					'nexo.edit.suppliers',
					'nexo.delete.suppliers',
				]
			),
			array(
				'title'        =>    __('Liste des fournisseurs', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/fournisseurs/lists'),
				'permission'	=>	'nexo.view.suppliers'
			),
			array(
				'title'        =>    __('Ajouter un fournisseur', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/fournisseurs/lists/add'),
				'permission'	=>	'nexo.create.suppliers'
			),
		);
				

		$this->events->do_action('nexo_before_customers', $Nexo_Menus);

		$Nexo_Menus[ 'clients' ]        =    $this->events->apply_filters('nexo_customers_menu_array', array(
			array(
				'title'        =>    __('Clients', 'nexo'),
				'href'        =>    '#',
				'disable'    =>    true,
				'icon'        =>    'fa fa-users',
				'permission'	=>	[
					'nexo.create.customers',
					'nexo.edit.customers',
					'nexo.delete.customers',
					'nexo.create.customers-groups',
					'nexo.edit.customers-groups',
					'nexo.delete.customers-groups',
				]
			),
			array(
				'title'        =>    __('Liste des clients', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/lists'),
				'permission'	=>	'nexo.view.customers'
			),
			array(
				'title'        =>    __('Ajouter un client', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/add'),
				'permission'	=>	'nexo.create.customers'
			),
			array(
				'title'        =>    __('Groupes', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/groups/list'),
				'permission'	=>	'nexo.view.customers-groups'
			),
			array(
				'title'        =>    __('Ajouter un groupe', 'nexo'),
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/groups/list/add'),
				'permission'	=>	'nexo.create.customers-groups'
			)
		));

		$this->events->do_action('nexo_before_reports', $Nexo_Menus);

		$Nexo_Menus[ 'rapports' ]    =    $this->events->apply_filters('nexo_reports_menu_array', array(
			array(
				'title'        =>    __('Rapports', 'nexo'),
				'href'        =>    '#',
				'disable'    =>    true,
				'icon'        =>    'fa fa-bar-chart',
				'permission'	=>	'nexo.read.*'
			),
			array(
				'title'       =>    __('Ventes journalières', 'nexo'), // menu title
				'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rapports/journalier'), // url to the page,,
				'permission'	=>	'nexo.read.daily-sales'
			),			
		));

		$this->events->do_action('nexo_before_accounting', $Nexo_Menus);

		$this->events->do_action('nexo_before_history', $Nexo_Menus);

		$this->events->do_action('nexo_before_settings', $Nexo_Menus);

		$Nexo_Menus[ 'nexo_settings' ]    =    $this->events->apply_filters('nexo_settings_menu_array', array(
			array(
				'title'            =>    sprintf( __('Réglages %s', 'nexo'), @$Options[ 'site_name' ] == null ? 'Nexo' : @$Options[ 'site_name' ] ),
				'icon'            =>    'fa fa-gear',
				'href'            =>    '#',
				'disable'        =>    true,
				'permission'		=>	'nexo.manage.settings'
			),
			array(
				'title'            =>    __('Général', 'nexo'),
				'icon'            =>    'fa fa-gear',
				'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings' )),
				'permission'		=>	'nexo.manage.settings'
			),
			array(
				'title'            =>    __('Caisse', 'nexo'),
				'icon'            =>    'fa fa-gear',
				'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'checkout' )),
				'permission'		=>	'nexo.manage.settings'
			),
			array(
				'title'            =>    __('Articles', 'nexo'),
				'icon'            =>    'fa fa-gear',
				'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'items' )),
				'permission'		=>	'nexo.manage.settings'
			),
			[
				'title'	 		=>	__( 'Commandes', 'nexo' ),
				'href' 			=>	site_url([ 'dashboard', store_slug(), 'nexo', 'settings', 'orders' ]),
				'permission'		=>	'nexo.manage.settings'
			],
			[
				'title'	 		=>	__( 'Fournisseurs', 'nexo' ),
				'href' 			=>	site_url([ 'dashboard', store_slug(), 'nexo', 'settings', 'providers' ]),
				'permission'		=>	'nexo.manage.settings'
			],
			array(// @since 2.7.9
				'title'            =>    __('Factures & Reçus', 'nexo'),
				'icon'            =>    'fa fa-gear',
				'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'invoices' )),
				'permission'		=>	'nexo.manage.settings'
			),
			array(// @since 3.0.19
				'title'            =>    __('Raccourcis Claviers', 'nexo'),
				'icon'            =>    'fa fa-keyboard-o',
				'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'keyboard' )),
				'permission'		=>	'nexo.manage.settings'
			),
			array(
				'title'            	=>    __('Clients', 'nexo'),
				'icon'            	=>    'fa fa-gear',
				'href'            	=>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'customers' )),
				'permission'		=>	'nexo.manage.settings'
			),
			array(
				'title'            	=>    __('Réinitialisation', 'nexo'),
				'icon'            	=>    'fa fa-gear',
				'href'            	=>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'reset' )),
				'permission'		=>	'nexo.manage.settings'
			),
			array(
				'title'				=>	__( 'A propos', 'nexo' ),
				'icon' 				=>	'fa fa-help',
				'href'				=>	site_url([ 'dashboard', store_slug(), 'nexo', 'about' ]),
				'permission'			=>	'nexo.manage.settings',
				'controller'			=>	''
			)
		));
	}

		/**
		 * Store Settings
		 * @since 2.8
		**/

		if( @$Options[ 'nexo_store' ] == 'enabled' ) {

			$Nexo_Menus[ 'nexo_store_settings' ]	=	array(
				array(
					'title'			=>	__( 'Réglages des boutiques', 'nexo' ),
					'href'			=>	site_url( array( 'dashboard', 'nexo', 'stores-settings' ) ),
					'icon'			=>	'fa fa-wrench',
					'permission' 		=>	'nexo.manage.settings'
				)
			);

		} else { // in order to simplify Setting menu, we remove Store setting from admin menu add set it as Nexo Settings Sub menu

			if( User::can( 'create_shop' ) && User::can( 'create_shop' ) && User::can( 'create_shop' ) ) {
				$Nexo_Menus[ 'nexo_settings' ][]	=	array(
					'title'			=>	__( 'Réglages des boutiques', 'nexo' ),
					'href'			=>	site_url( array( 'dashboard', 'nexo', 'stores-settings' ) ),
					'icon'			=>	'fa fa-wrench',
					'permission'		=>	'nexo.manage.settings'
				);
			}

		}

		$start    	=    array_slice($final, 0, 1);
		$end    		=    array_slice($final, 1);
		$final    	=    array_merge($start, $Nexo_Menus, $end);

		/**
		 * Hide Main Site Menus
		 * @since 2.8.0
		**/

		if( $uri === 'stores' ) {
			foreach( $final as $key => $menu ) {
				if( ! in_array( $key, array( 'activite', 'rapports', 'clients', 'vendors', 'arrivages', 'factures', 'nexo_settings', 'sales', 'caisse', 'coupons' ) ) ) {
					unset( $final[ $key ] );
				}
			}

			// Create a dashboard menu for Sub shop
			// @since 2.8.0

			if( $this->uri->segment( 2 ) == 'stores' ){

				$final		=	array_insert_before( 'caisse', $final, 'store-dashboard', array(
					array(
						'title'		=>	__( 'Tableau de bord', 'nexo' ),
						'href'		=>	site_url( array( 'dashboard', 'stores', $this->uri->segment( 3 ) ) ),
						'icon'		=>	'fa fa-dashboard'
					)
				) );

				@$final[ 'nexo_settings' ][0]	=	array(
					'title'		=>	__( 'Réglages de la boutique', 'nexo' ),
					'disable'	=>	true,
					'icon'		=>	'fa fa-cogs',
					'href'		=>	'javascript:void()',
					'permissino'	=>	'nexo.manage.settings'
				);
			}
		}

		return $final;
    }

    public function load_dashboard()
    {
		// $this->load->model('Nexo_Misc');
		// include_once( dirname( __FILE__ ) . '/../__controllers/import.php' );
		// include_once( dirname( __FILE__ ) . '/../__controllers/coupons.php' );
		// include_once( dirname( __FILE__ ) . '/../__controllers/templates.php' ); // @since 3.1
		// include_once( dirname( __FILE__ ) . '/../__controllers/taxes.php' );

		// $this->Gui->register_page( 'nexo', array( $this, 'load_controller' ));
		// $this->Gui->register_page( 'stores', array( $this, 'stores' ) );
		// $this->Gui->register_page_object( 'nexo_import', 	new Import );
		// $this->Gui->register_page_object( 'nexo_coupons', 	new NexoCouponController );
		// $this->Gui->register_page_object( 'nexo_templates', new Nexo_Templates_Controller ); // @since 3.1
		// $this->Gui->register_page_object( 'nexo_taxes', 	new Nexo_Taxes_Controller ); // @since 3.3

		// // @since 2.10.1
		// $this->events->add_filter( 'stores_controller_callback', function( $action ) {
		// 	$action[ 'nexo_import' ]    	=   new Import;
		// 		$action[ 'nexo_coupons' ]    	=   new NexoCouponController;
		// 		$action[ 'nexo_templates' ]    	=   new Nexo_Templates_Controller; // @since 3.1
		// 		$action[ 'nexo_taxes' ] 		=	new Nexo_Taxes_Controller; // @since 3.3
		// 	return $action;
		// });

		// @since 3.0.16
		$store_menus    =   get_instance()->events->apply_filters( 'nexo_store_menus', $this->load->module_view( 'nexo', 'header/store-menus', null, true ) );

		$this->events->add_action( 'display_admin_header_menu', function( $action ) use ( $store_menus ) {
            echo $store_menus;
		});
    	}
	public function load_controller()
	{
		$this->args    =    func_get_args();
		if (is_array($this->args) && count($this->args) > 0) {
			$file    =    dirname(__FILE__) . '/../__controllers/' . $this->args[0] . '.php';
			if (is_file($file)) {
				include_once($file);
			} else {
				show_404();
			}
		}
    	}

	
}
new Nexo_Controller;
