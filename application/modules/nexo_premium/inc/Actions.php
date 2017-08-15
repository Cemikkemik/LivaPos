<?php
! defined('APPPATH') ? die() : null;

use Carbon\Carbon;

/**
 * Nexo Premium Hooks
 *
 * @author Blair Jersyer
**/

class Nexo_Premium_Actions extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * New controller
     *
     * @return void
    **/

    public function Menu_Accounting()
    {
        if (
         User::can('create_shop_purchases_invoices ') ||
         User::can('edit_shop_purchases_invoices') ||
         User::can('delete_shop_purchases_invoices')
        ) {
            global $Nexo_Menus;

            $Nexo_Menus[ 'factures' ]    =    array(
                array(
                    'title'            =>    __('Dépenses', 'nexo_premium'),
                    'href'            =>    '#',
					'icon'			=>	'fa fa-sticky-note-o',
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Liste des dépenses', 'nexo_premium'),
                    'href'            =>    site_url(array( 'dashboard', store_slug(), 'nexo_premium', 'Controller_Factures', 'list' )),
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Nouvelle dépense', 'nexo_premium'),
                    'href'            =>    site_url(array( 'dashboard', store_slug(), 'nexo_premium', 'Controller_Factures', 'add' )),
                    'disable'        =>    true
                ),
				// @since 2.6.6
				[
					'title'		=>	__( 'Categories', 'nexo_premium' ),
					'href'		=>	site_url([ 'dashboard', store_slug(), 'nexo_premium', 'expenses_list' ] )
				], [
					'title'		=>	__( 'Ajouter une categorie', 'nexo_premium' ),
					'href'		=>	site_url([ 'dashboard', store_slug(), 'nexo_premium', 'expenses_list', 'add' ] )
				]
            );
        }
    }

    /**
     * Dashboard Home
     *
     * @return void
    **/

    public function dashboard_home()
    {
		if( ( is_multistore() && multistore_enabled() ) || ! multistore_enabled() ) {
			$this->events->add_filter('gui_before_cols', array( $this, 'create_cards' ));
			$this->events->add_filter('gui_page_title', function ($title) {

				return '<section class="content-header"><h1>' . strip_tags($title) . ' <a class="btn btn-primary btn-sm pull-right" href="' . site_url(array( 'dashboard', store_slug(), 'nexo_premium', 'Controller_Clear_Cache', 'dashboard_card' )) . '">' . __('Supprimer le cache', 'nexo_premium') . '</a></h1></section>';

			});
		}
    }

    /**
     * Create Cards
     *
     * @return String
    **/

    public function create_cards($content)
    {
		if( ( is_multistore() && multistore_enabled() ) || ! multistore_enabled() ) {
			$this->load->model('Nexo_Checkout');
			$this->load->model('Nexo_Misc');

			$this->config->load('nexo_premium', true);
			$this->load->helper('nexopos');

			$Nexo_Config        	=    $this->config->item('nexo_premium');
			$this->Cache        	=    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_dashboard_card_' . store_prefix() ));
			$startOfDay            	=    Carbon::parse(date_now())->startOfDay();
			$endOfDay            	=    Carbon::parse(date_now())->endOfDay();
			$startOfYesterday    	=    Carbon::parse(date_now())->subDay(1)->startOfDay();
			$endOfYesterday        	=    Carbon::parse(date_now())->subDay(1)->endOfDay();

			global $Options;


			if (! $this->Cache->get('sales_number')) {

				// Count Sale Number
				$Sales_Number   		=    ($Orders    =    $this->Nexo_Checkout->get_order()) == false ? 0 : count($Orders);
				$SalesToday            	=    0;
				$SalesYesterDay        	=    0;

				if (is_array($Orders)) {
					foreach ($Orders as $sale) {
						if ($startOfDay->lte(Carbon::parse($sale[ 'DATE_CREATION' ])) && $endOfDay->gte(Carbon::parse($sale[ 'DATE_CREATION' ]))) {
							$SalesToday++;
						}
						// Sales Yesterday
						if ($startOfYesterday->lte(Carbon::parse($sale[ 'DATE_CREATION' ])) && $endOfYesterday->gte(Carbon::parse($sale[ 'DATE_CREATION' ]))) {
							$SalesYesterDay++;
						}
					}
				}

				$this->Cache->save('sales_number', $Sales_Number, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('sales_number_today', $SalesToday, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('sales_number_yesterday', $SalesYesterDay, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
			}

			if (! $this->Cache->get('net_sales')) {

				// Count Sale Number
				$Sales                  =   $this->Nexo_Checkout->get_order();
				$net_sales              =   0;
				$net_sales_today        =    0;
				$net_sales_yesterday    =    0;
				$CA						=    0;

				if ($Sales) {
					foreach ($Sales as $sale) {
						// Default value
						$CA                        =    0;
						// Uniquement les commandes comptant et avance
						if ( in_array( $sale[ 'TYPE' ], $this->events->apply_filters( 'dashboard_card_supported_order_type', [ 'nexo_order_comptant' ] ) ) ) {
							$CA                =
							__floatval($sale[ 'TOTAL' ]) - (
								__floatval($sale[ 'RISTOURNE' ]) +
								__floatval($sale[ 'RABAIS' ]) +
								__floatval($sale[ 'REMISE' ])
							);
						} elseif ($sale[ 'TYPE' ] == 'nexo_order_advance') {
							$CA               =    __floatval($sale[ 'SOMME_PERCU' ]) - (
								__floatval($sale[ 'RISTOURNE' ]) +
								__floatval($sale[ 'RABAIS' ]) +
								__floatval($sale[ 'REMISE' ])
							);
						}

						$net_sales                +=    $CA;
						// Sale Today
						if ($startOfDay->lte(Carbon::parse($sale[ 'DATE_CREATION' ])) && $endOfDay->gte(Carbon::parse($sale[ 'DATE_CREATION' ]))) {
							$net_sales_today    +=    $CA;
						}
						// Sales Yesterday
						if ($startOfYesterday->lte(Carbon::parse($sale[ 'DATE_CREATION' ])) && $endOfYesterday->gte(Carbon::parse($sale[ 'DATE_CREATION' ]))) {
							$net_sales_yesterday+=    $CA;
						}
					}
				}

				$this->Cache->save('net_sales', $net_sales, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('net_sales_today', $net_sales_today, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('net_sales_yesterday', $net_sales_yesterday, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
			}

			if (! $this->Cache->get('customers_number')) {
				$Customers            =    $this->Nexo_Misc->get_customers();
				$CustomersToday            =    0;
				$CustomersYesterday        =    0;

				if (is_array($Customers)) {
					foreach ($Customers as $Customer) {
						// Sale Today
						if ($startOfDay->lte(Carbon::parse($Customer[ 'DATE_CREATION' ])) && $endOfDay->gte(Carbon::parse($Customer[ 'DATE_CREATION' ]))) {
							$CustomersToday++;
						}
						// Sales Yesterday
						if ($startOfYesterday->lte(Carbon::parse($Customer[ 'DATE_CREATION' ])) && $endOfYesterday->gte(Carbon::parse($Customer[ 'DATE_CREATION' ]))) {
							$CustomersYesterday++;
						}
					}
				}

				$this->Cache->save('customers_number', is_array($Customers) ? count($Customers) : 0, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('customers_number_today', $CustomersToday, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('customers_number_yesterday', $CustomersYesterday, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
			}

			if (! $this->Cache->get('creances')) {
				$creances                =    0;
				$Sales                    =    $this->Nexo_Checkout->get_order();
				$creancesToday            =    0;
				$creancesYesteday        =    0;
				$CA                        =    0;

				if ($Sales) {
					foreach ($Sales as $sale) {
						if( in_array( $sale[ 'TYPE' ], [ 'nexo_order_devis', 'nexo_order_advance' ] ) ) {
							if (in_array($sale[ 'TYPE' ], array( 'nexo_order_devis' ))) {
								$CA            =    __floatval($sale[ 'TOTAL' ]) - (__floatval($sale[ 'RISTOURNE' ]) + __floatval($sale[ 'RABAIS' ]) + __floatval($sale[ 'REMISE' ]));
							} else if (in_array($sale[ 'TYPE' ], array( 'nexo_order_advance' ))) {
								$CA            =    (__floatval($sale[ 'TOTAL' ]) - (__floatval($sale[ 'RISTOURNE' ]) + __floatval($sale[ 'RABAIS' ]) + __floatval($sale[ 'REMISE' ]))) - __floatval($sale[ 'SOMME_PERCU' ]);
							}

							$creances                +=    $CA;
							// Sale Today
							if ($startOfDay->lte(Carbon::parse($sale[ 'DATE_CREATION' ])) && $endOfDay->gte(Carbon::parse($sale[ 'DATE_CREATION' ]))) {
								$creancesToday        +=    $CA;
							}
							// Sales Yesterday
							if ($startOfYesterday->lte(Carbon::parse($sale[ 'DATE_CREATION' ])) && $endOfYesterday->gte(Carbon::parse($sale[ 'DATE_CREATION' ]))) {
								$creancesYesteday    +=    $CA;
							}
						}
					}
				}

				$this->Cache->save('creances', $creances, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('creances_today', $creancesToday, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
				$this->Cache->save('creances_yesterday', $creancesYesteday, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
			}

			$before        =    $this->load->view('../modules/nexo_premium/views/dashboard-content', array(
				'Cache'        =>        $this->Cache
			), true);


		} else {
			$before        =    $this->load->view('../modules/nexo_premium/views/store-main-content', array(
				'Cache'        =>        $this->Cache
			), true);
		}

		$content    =    $before . $content;
		return $content;
    }

    /**
     * Create order History
     *
    **/

    public function Create_Order_History($post)
    {
        $this->load->library('Nexo_Misc');

        $this->Nexo_Misc->history_add(
            __('Création d\'une nouvelle commande', 'nexo_premium'),
            sprintf(
                __('L\'utilisateur %s a crée une nouvelle commande avec pour code : %s', 'nexo_premium'),
                User::pseudo(),
                $post[ 'CODE' ]
            )
        );
    }

    /**
     * Edit Order
    **/

    public function Edit_Order_History($post)
    {
        $this->load->library('Nexo_Misc');

        $this->Nexo_Misc->history_add(
            __('Modification d\'une commande', 'nexo_premium'),
            sprintf(
                __('L\'utilisateur %s a modifié une commande avec pour code : %s', 'nexo_premium'),
                User::pseudo(),
                $post[ 'command_code' ]
            )
        );
    }

    /**
     * Delete Order
    **/

    public function Delete_Order_History($post)
    {
        if (uri_string() != 'dashboard/nexo_premium/Controller_Quote_Cleaner') {
            $this->load->library('Nexo_Misc');

            $this->Nexo_Misc->history_add(
                __('Suppréssion d\'une commande', 'nexo_premium'),
                sprintf(
                    __('L\'utilisateur %s a supprimé une commande avec pour identifiant : %s', 'nexo_premium'),
                    User::pseudo(),
                    $post
                )
            );
        }
    }

    /**
     * Settings
    **/

    public function Checkout_Settings($GUI)
    {
        $GUI->add_meta(array(
            'namespace'        =>        'history',
            'title'            =>        __('Historique des utilisateurs', 'nexo_premium'),
            'col_id'        =>        2,
            'gui_saver'        =>        true,
            'footer'        =>        array(
                'submit'    =>        array(
                    'label'    =>        __('Sauvegarder les réglages', 'nexo_premium')
                )
            ),
            'use_namespace'    =>        false,
        ));

        $GUI->add_item(array(
            'type'        =>    'dom',
            'content'        =>    '<br>'
        ), 'history', 2);

        $GUI->add_item(array(
            'type'        =>    'select',
            'name'        =>    'nexo_premium_enable_history',
            'label'        =>    __('Souhaitez-vous activer l\'historique des activités ?', 'nexo_premium'),
            'description'        =>    __('Ceci peut très légèrement ralentir l\'application, et prendre plus d\'espace dans votre base de données.', 'nexo_premium'),
            'options'    =>    array(
                ''            =>    __('Veuillez choisir une option', 'nexo_premium'),
                'yes'        =>    __('Oui', 'nexo_premium'),
                'no'        =>    __('Non', 'nexo_premium')
            )
        ), 'history', 2);
    }

    /**
     * Delete Quotes Orders
    **/

    private $general_interval_cache_namespace    =    'nexo_premium_';

    public function Clean_Quote_Orders()
    {
        $this->config->load('nexo_premium');

        $Cache            =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    $this->general_interval_cache_namespace . store_prefix() ));
        if (! $Cache->get('check_quote_orders')) {
            ?>
<script type="text/javascript">
"use strict";

$( document ).ready(function(e) {
	$.ajax( '<?php echo site_url(array( 'dashboard', 'nexo_premium', 'Controller_Quote_Cleaner' ));
            ?>', {
		success	:	function( e ){
			if( typeof e.title != 'undefined' ) {
				tendoo.notify.success(
					e.title,
					e.msg,
					'<?php echo site_url(array( 'dashboard', 'nexo_premium', 'Controller_Historique' ));
            ?>',
					true,
					86400
				);
			}
		},
		dataType:"json"
	});
});
</script>
            <?php
            $Cache->save('check_quote_orders', date_now(), $this->config->item('quotes_check_interval'));
        }
    }
}
