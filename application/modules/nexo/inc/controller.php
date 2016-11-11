<?php
class Nexo_Controller extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action('load_dashboard', array( $this, 'load_dashboard' ));
        $this->events->add_action('load_frontend', array( $this, 'frontend' ));
		$this->events->add_filter('admin_menus', array( $this, 'menus' ), 15);
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
		// @since 2.7.7
        global $Nexo_Menus, $Options;

        $Nexo_Menus    =    array();

		$this->events->do_action('nexo_before_checkout', $Nexo_Menus);

		/***
		 * Display Store Menu only when multi store is enabled
		 * @since 2.8
		**/

		if( @$Options[ 'nexo_store' ] == 'enabled' ) {

			if (
				User::can('create_shop') ||
				User::can('edit_shop') ||
				User::can('delete_shop') ||
				User::can( 'enter_shop' )
			) {

				$Nexo_Menus[ 'nexo_shop' ]        =    array(
					array(
						'title'		=>        __('Boutiques', 'nexo'), // menu title
						'icon'		=>        'fa fa-cubes', // menu icon
						'disable'	=>    true
					)
				);

				if( User::can( 'create_shop' ) || User::can( 'edit_shop' ) || User::can( 'delete_shop' ) ) {

					// Create a new store
					$Nexo_Menus[ 'nexo_shop' ][]	=	array(
						'title'		=>        __('Toutes les boutiques', 'nexo'), // menu title
						'href'		=>		site_url( array( 'dashboard', 'nexo', 'stores', 'lists' ) )
					);

					$Nexo_Menus[ 'nexo_shop' ][]	=	array(
						'title'		=>        __('Ajouter une boutique', 'nexo'), // menu title
						'href'		=>		site_url( array( 'dashboard', 'nexo', 'stores', 'lists', 'add' ) )
					);
				} else {

					$Nexo_Menus[ 'nexo_shop' ][]	=	array(
						'title'		=>        __('Toutes les boutiques', 'nexo'), // menu title
						'href'		=>		site_url( array( 'dashboard', 'nexo', 'stores', 'all' ) )
					);

				}

			}

		}

		// @since 2.8
		// Adjust menu when multistore is enabled
		$uri			=	$this->uri->segment(2,false);
		$store_uri		=	'';

		if( $uri == 'stores' || in_array( @$Options[ 'nexo_store' ], array( null, 'disabled' ), true ) ) {

			// Only When Multi Store is enabled
			// @since 2.8

			if( @$Options[ 'nexo_store' ] == 'enabled' && $this->config->item( 'nexo_multi_store_enabled' ) ) {
				$store_uri	=	'stores/' . $this->uri->segment( 3, 0 ) . '/';
			}

			if( @$Options[ store_prefix() . 'nexo_enable_registers' ] == 'oui' ) {

				if (
					User::can('create_shop_registers') ||
					User::can('edit_shop_registers') ||
					User::can('delete_shop_registers') ||
					User::can( 'view_shop_registers' )
				) {
					$Nexo_Menus[ 'caisse' ]        =    array(
						array(
							'title'		=>        __('Caisses', 'nexo'), // menu title
							'icon'		=>        'fa fa-inbox', // menu icon
							'disable'	=>    true
						)
					);

					if( User::in_group( 'shop_cashier' ) ):

					$Nexo_Menus[ 'caisse' ][]		=	array(
						'title'       =>    __('Liste des caisses', 'nexo'), // menu title
						'icon'        =>    'fa fa-shopping-basket', // menu icon
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/registers/for_cashiers'), // url to the page,
					);

					else :

					$Nexo_Menus[ 'caisse' ][]		=	array(
						'title'       =>    __('Liste des caisses', 'nexo'), // menu title
						'icon'        =>    'fa fa-shopping-basket', // menu icon
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/registers/lists'), // url to the page,
					);

					endif;

					if( User::can( 'create_shop_registers' ) ) {
						$Nexo_Menus[ 'caisse' ][]		=	array(
							'title'       =>    __('Ajouter une caisse', 'nexo'), // menu title
							'icon'        =>    'fa fa-shopping-basket', // menu icon
							'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/registers/lists/add'), // url to the page,
						);
					}
				}
			}


			if (
				User::can('create_shop_orders') ||
				User::can('edit_shop_orders') ||
				User::can('delete_shop_orders')
			) {

				if( in_array( @$Options[ store_prefix() . 'nexo_enable_registers' ], array( null, 'non' ) ) ){
					$Nexo_Menus[ 'caisse' ][]		=	array(
						'title'       =>    __('Ouvrir le PDV', 'nexo'), // menu title
						'icon'        =>    'fa fa-shopping-cart', // menu icon
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/registers/__use/default'), // url to the page,
					);
				}

				// @since 2.7.5

				$Nexo_Menus[ 'sales' ]			=	array(
					array(
						'title'       =>    __('Ventes', 'nexo'), // menu title
						'icon'        =>    'fa fa-shopping-basket', // menu icon
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/commandes/lists'), // url to the page,
					)
				);
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
					'title'        =>    __('Inventaire', 'nexo'),
					'href'        =>    '#',
					'disable'    =>    true,
					'icon'        =>    'fa fa-archive'
				),
				array(
					'title'        =>    __('Liste des livraisons', 'nexo'),
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/arrivages/lists'),
				),
				array(
					'title'        =>    __('Nouvelle livraison', 'nexo'),
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/arrivages/add'),
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
					'title'        =>    __('Liste des rayons', 'nexo'),
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rayons/lists'),
				),
				array(
					'title'        =>    __('Ajouter un rayon', 'nexo'),
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rayons/lists/add'),
				),
				array(
					'title'        =>    __('Liste des catégories', 'nexo'),
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/categories/lists'),
				),
				array(
					'title'        =>    __('Ajouter une catégorie', 'nexo'),
					'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/categories/lists/add'),
				),
			));

				$Nexo_Menus[ 'vendors' ]	=	array(
					array(
						'title'        =>    __('Fournisseurs', 'nexo'),
						'disable'        =>  true,
						'href'			=>	'#',
						'icon'			=>	'fa fa-truck'
					),
					array(
						'title'        =>    __('Liste des fournisseurs', 'nexo'),
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/fournisseurs/lists'),
					),
					array(
						'title'        =>    __('Ajouter un fournisseur', 'nexo'),
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/fournisseurs/lists/add'),
					),
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
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/lists'),
					),
					array(
						'title'        =>    __('Ajouter un client', 'nexo'),
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/lists/add'),
					),
					array(
						'title'        =>    __('Groupes', 'nexo'),
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/groups/list'),
					),
					array(
						'title'        =>    __('Ajouter un groupe', 'nexo'),
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/clients/groups/list/add'),
					)
				));
			}

			$this->events->do_action('nexo_before_reports', $Nexo_Menus);

			if (User::can('read_shop_reports')) {
				$Nexo_Menus[ 'rapports' ]    =    $this->events->apply_filters('nexo_reports_menu_array', array(
					array(
						'title'        =>    __('Rapports', 'nexo'),
						'href'        =>    '#',
						'disable'    =>    true,
						'icon'        =>    'fa fa-bar-chart'
					),
					array(
						'title'       =>    __('Meilleurs ventes', 'nexo'), // menu title
						'href'        =>    'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010', // site_url('dashboard/nexo/rapports/fiche_de_suivi_de_stock'), // url to the page,
					),
					array(
						'title'       =>    __('Ventes journalières', 'nexo'), // menu title
						'href'        =>    site_url('dashboard/' . $store_uri . 'nexo/rapports/journalier'), // url to the page,
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

			/**
			 * Disabled
				User::can('create_shop_backup') ||
				User::can('edit_shop_backup') ||
				User::can('delete_shop_backup') ||
				User::can('read_shop_user_tracker') ||
				User::can('delete_shop_user_tracker')
			**/

			if (
				true == false
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
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings' ))
					),
					array(
						'title'            =>    __('Caisse', 'nexo'),
						'icon'            =>    'fa fa-gear',
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'checkout' ))
					),
					array(
						'title'            =>    __('Articles', 'nexo'),
						'icon'            =>    'fa fa-gear',
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'items' ))
					),
					array(// @since 2.7.9
						'title'            =>    __('Factures & Reçus', 'nexo'),
						'icon'            =>    'fa fa-gear',
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'invoices' ))
					),
					array(
						'title'            =>    __('Clients', 'nexo'),
						'icon'            =>    'fa fa-gear',
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'customers' ))
					),
					array(
						'title'            =>    __('Réinitialisation', 'nexo'),
						'icon'            =>    'fa fa-gear',
						'href'            =>    site_url(array( 'dashboard', $store_uri . 'nexo', 'settings', 'reset' ))
					)

				));
			}

		}

		/**
		 * Store Settings
		 * @since 2.8
		**/

		if( @$Options[ 'nexo_store' ] == 'enabled' ) {

			if( User::can( 'create_shop' ) && User::can( 'create_shop' ) && User::can( 'create_shop' ) ) {
				$Nexo_Menus[ 'nexo_store_settings' ]	=	array(
					array(
						'title'			=>	__( 'Réglages des boutiques', 'nexo' ),
						'href'			=>	site_url( array( 'dashboard', 'nexo', 'stores-settings' ) ),
						'icon'			=>	'fa fa-wrench'
					)
				);
			}

		} else { // in order to simplify Setting menu, we remove Store setting from admin menu add set it as Nexo Settings Sub menu

			if( User::can( 'create_shop' ) && User::can( 'create_shop' ) && User::can( 'create_shop' ) ) {
				$Nexo_Menus[ 'nexo_settings' ][]	=	array(
					'title'			=>	__( 'Réglages des boutiques', 'nexo' ),
					'href'			=>	site_url( array( 'dashboard', 'nexo', 'stores-settings' ) ),
					'icon'			=>	'fa fa-wrench'
				);
			}

		}

        $start    	=    array_slice($final, 0, 1);
        $end    	=    array_slice($final, 1);
        $final    	=    array_merge($start, $Nexo_Menus, $end);

		/**
		 * Hide Main Site Menus
		 * @since 2.8.0
		**/

		if( $uri === 'stores' ) {
			foreach( $final as $key => $menu ) {
				if( ! in_array( $key, array( 'activite', 'rapports', 'clients', 'vendors', 'arrivages', 'factures', 'nexo_settings', 'sales', 'caisse' ) ) ) {
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
					'href'		=>	'javascript:void()'
				);

			}
		}

        return $final;
    }

    public function load_dashboard()
    {
        $this->load->model('Nexo_Misc');

        $this->Gui->register_page('nexo', array( $this, 'load_controller' ));
		$this->Gui->register_page( 'stores', array( $this, 'stores' ) );

		$this->events->add_action( 'display_admin_header_menu', function( $action ) {

			get_instance()->load->model( 'Nexo_Stores' );

			$stores		=	get_instance()->Nexo_Stores->get( 'opened', 'STATUS' );

			global $Options;

			if( @$Options[ 'nexo_store' ] == 'enabled' ) {
			?>
<li class="messages-menu">
<?php
if( User::in_group( 'shop_cashier' ) || User::in_group( 'shop_tester' ) ) {
	$store_url	=	site_url( array( 'dashboard', 'nexo', 'stores', 'all' ) );
} else {
	$store_url	=	site_url( array( 'dashboard', 'nexo', 'stores', 'lists' ) );
}
?>
    <a href="<?php echo $store_url;?>" title="<?php _e( 'Gérer les boutiques', 'nexo' );?>">
    	<i class="fa fa-home"></i>
    </a>
</li>
<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
    	<i class="fa fa-cubes"></i>
    	<?php _e( 'Boutiques', 'nexo' );?>
    </a>
    <ul class="dropdown-menu">
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">

            <?php if( $stores ):?>
			<?php foreach( $stores as $store ): ?>
                <?php if( $store[ 'STATUS' ] == 'opened' ):?>
                <li><!-- start message -->
                    <a href="<?php echo site_url( array( 'dashboard', 'stores', $store[ 'ID' ] ) );?>">
                      <div class="pull-left">
                          <?php if (  $store[ 'IMAGE' ] != '' ): ?>
                              <img src="<?php echo upload_url() . '/stores/' . $store[ 'IMAGE' ];?>" class="img-circle" alt="User Image">
                          <?php else: ?>
                                <img src="<?php echo module_url( 'nexo' ) . '/images/default.png';?>" class="img-circle" alt="User Image">
                          <?php endif; ?>
                      </div>
                      <h4>
                        <?php echo xss_clean( $store[ 'NAME' ] );?>
                        <!-- <small><i class="fa fa-clock-o"></i> 5 mins</small>-->
                      </h4>
                      <p><?php echo $store[ 'DESCRIPTION' ] != '' ? xss_clean( $store[ 'DESCRIPTION' ] ) : __( 'Aucune description disponible', 'nexo' );?></p>
                    </a>
                  </li>
				<?php endif;?>
            <?php endforeach;?>
            <?php else:?>
            <li>
            	<a href="<?php echo site_url( array( 'dashboard', 'nexo', 'stores', 'lists', 'add' ) );?>"><?php _e( '+1 Créer une boutique', 'nexo' );?></a>
			</li>
            <?php endif;?>
            </ul>
        </li>
    </ul>
</li>
<?php
			}
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

	/**
	 * Store
	**/

	public function stores()
	{
		global	$store_id,
				$CurrentStore,
				$Options;

		if( @$Options[ 'nexo_store' ] == 'enabled' ) {

			$urls		      =	func_get_args();
			$store_id	      =	@$urls[0];
            $slug_namespace   = @$urls[1];
			$urls		      =	array_splice( $urls, 2 );

			if( $CurrentStore ) {

				$this->args    =    $urls;

				if (is_array($this->args) && count($this->args) > 0) {
					$file_name		=	$this->args[0];
				} else {
					$file_name		=	'dashboard';
				}

				$file    =    dirname(__FILE__) . '/../__controllers/' . $file_name . '.php';

				if ( is_file( $file ) && in_array( $slug_namespace, array( 'nexo', null ) ) ) {

					include_once($file);

				} else {

					$callback			=	$this->events->apply_filters( 'stores_controller_callback', array() );

					if( $callback ) {

						/**
						 * Saved Callback
						**/

						$slug_namespace	=	@array_slice(func_get_args(), 1, 1);

						if( @$callback[ $slug_namespace[0] ] != null ) {
                            if( is_array( $callback[ $slug_namespace[0] ] ) ) {
                                $method                             =   array_slice(func_get_args(), 2, 1);
                                $callback[ $slug_namespace[0] ][]   =   str_replace( '-', '_', $method[0] );
                                if( method_exists( $callback[ $slug_namespace[0] ][0], $callback[ $slug_namespace[0] ][1] ) ) {
        							call_user_func_array( $callback[ $slug_namespace[0] ], array_slice(func_get_args(), 3));
                                } else {
                                    show_404();
                                }
                            } else {
                                $method             =   array_slice(func_get_args(), 2, 1);
                                $finalArray         =   array( $callback[ $slug_namespace[0] ] );
                                $finalArray[]       =   str_replace( '-', '_', $method[0] );
                                if( method_exists( @$finalArray[0], @$finalArray[1] ) ) {
        							call_user_func_array( $finalArray, array_slice(func_get_args(), 3));
                                } else {
                                    show_404();
                                }
                            }
						} else {
							show_404();
						}

					} else {
						show_404();
					}

				}
			} else {
				redirect( array( 'dashboard', 'unknow-store' ) );
			}
		} else {
			redirect( array( 'dashboard', 'nexo-feature-unavailable' ) );
		}
	}
}
new Nexo_Controller;
