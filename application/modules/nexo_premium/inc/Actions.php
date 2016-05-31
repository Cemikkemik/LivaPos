<?php
! defined('APPPATH') ? die() : null;

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
                    'title'            =>    __('Factures', 'nexo_premium'),
                    'href'            =>    '#',
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Liste des factures', 'nexo_premium'),
                    'href'            =>    site_url(array( 'dashboard', 'nexo_premium', 'Controller_Factures', 'list' )),
                    'disable'        =>    true
                ),
                array(
                    'title'            =>    __('Nouvelle facture', 'nexo_premium'),
                    'href'            =>    site_url(array( 'dashboard', 'nexo_premium', 'Controller_Factures', 'add' )),
                    'disable'        =>    true
                )
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
        $this->events->add_filter('gui_before_cols', array( $this, 'create_cards' ));
        $this->events->add_filter('gui_page_title', function ($title) {
            return '<section class="content-header"><h1>' . strip_tags($title) . ' <a class="btn btn-primary btn-sm pull-right" href="' . site_url(array( 'dashboard', 'nexo_premium', 'Controller_Clear_Cache', 'dashboard_card' )) . '">' . __('Clear cache') . '</a></h1></section>';
        });
    }
    
    /**
     * Create Cards
     *
     * @return String
    **/
    
    public function create_cards($content)
    {
        $this->load->model('Nexo_Checkout');
        $this->load->model('Nexo_Misc');
        
        $this->config->load('nexo_premium', true);
        
        $Nexo_Config        =    $this->config->item('nexo_premium');
        $this->Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_dashboard_card_'));
        
        global $Options;

        
        if (! $this->Cache->get('sales_number')) {
            
            // Count Sale Number
            $Sales_Number        =    ($Orders    =    $this->Nexo_Checkout->get_order()) == false ? 0 : count($Orders);
            $this->Cache->save('sales_number', $Sales_Number, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
        }
        
        if (! $this->Cache->get('net_sales')) {
                    
            // Count Sale Number
            $Sales                =    $this->Nexo_Checkout->get_order();
            $net_sales            =    0;
            
            if ($Sales) {
                foreach ($Sales as $sale) {
                    // Uniquement les commandes comptant et avance	
                    if ( $sale[ 'TYPE' ] == 'nexo_order_comptant' ) {
                        $CA        		=    intval($sale[ 'TOTAL' ]) - (intval($sale[ 'RISTOURNE' ]) + intval($sale[ 'RABAIS' ]) + intval($sale[ 'REMISE' ]));
                        $net_sales    +=    $CA;
                    } elseif( $sale[ 'TYPE' ] == 'nexo_order_advance' ) {
                        $CA       	 	=    intval($sale[ 'SOMME_PERCU' ]) - (intval($sale[ 'RISTOURNE' ]) + intval($sale[ 'RABAIS' ]) + intval($sale[ 'REMISE' ]));
                        $net_sales    +=    $CA;
					}
                }
            }
            
            $this->Cache->save('net_sales', $net_sales, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
        }
        
        if (! $this->Cache->get('customers_number')) {
            $Customers            =    $this->Nexo_Misc->get_customers();
            $this->Cache->save('customers_number', is_array($Customers) ? count($Customers) : 0, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
        }
        
        if (! $this->Cache->get('creances')) {
            $creances            =    0;
            $Sales                =    $this->Nexo_Checkout->get_order();
            
            if ($Sales) {
                foreach ($Sales as $sale) {
                    // Uniquement les commandes comptant et avance	
                    if (in_array($sale[ 'TYPE' ], array( 'nexo_order_devis' ) ) ) {
                        $CA            =    intval($sale[ 'TOTAL' ]) - (intval($sale[ 'RISTOURNE' ]) + intval($sale[ 'RABAIS' ]) + intval($sale[ 'REMISE' ]));
                        $creances    +=    $CA;
                    }
                    if (in_array($sale[ 'TYPE' ], array( 'nexo_order_advance' ) ) ) {
                        $CA            =    (intval($sale[ 'TOTAL' ]) - (intval($sale[ 'RISTOURNE' ]) + intval($sale[ 'RABAIS' ]) + intval($sale[ 'REMISE' ]))) - intval(intval($sale[ 'SOMME_PERCU' ]));
                        $creances    +=    $CA;
                    }
                }
            }
            
            $this->Cache->save('creances', $creances, @$Nexo_Config[ 'dashboard_card_lifetime' ]);
        }
            
        $before        =    $this->load->view('../modules/nexo_premium/views/dashboard-content', array(
            'Cache'        =>        $this->Cache
        ), true);
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
                'yes'        =>    __('Oui', 'nexo'),
                'no'        =>    __('Non', 'nexo')
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
        
        $Cache            =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    $this->general_interval_cache_namespace ));
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
