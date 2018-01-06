<?php

use Carbon\Carbon;

class NexoPremiumController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        
        /**
         * Create Backup Folder
        **/
    
        if (! is_dir(PUBLICPATH . '/upload/nexo_premium_backups')) {
            @mkdir(PUBLICPATH . '/upload/nexo_premium_backups');
        }
    
        if (! is_dir(PUBLICPATH . '/upload/nexo_premium_backups/temp')) {
            @mkdir(PUBLICPATH . '/upload/nexo_premium_backups/temp');
        }
    }
    
    /**
     * Daily Report
     * @param string date
     * @return void
     */
    public function daily( $report_date = null )
    {
        if( User::cannot( 'nexo.read.daily-sales' ) ) {
            return nexo_access_denied();
        }

        if(substr($report_date, -1) == '/') {
            $report_date = substr($report_date, 0, -1);
        }

        if( $report_date == null ) {
            $report_date    =   date_now();
        }
        // if repport date is sup than current day
        $CarbonCurrent                  =    Carbon::parse(date_now());
        $CarbonReportDate               =    Carbon::parse($report_date);

        if (! $CarbonCurrent->gte($CarbonReportDate)) {
            $this->Gui->set_title(__('Erreur', 'nexo_premium'));
            $this->notice->push_notice(tendoo_error(__('La date mentionnée est invalide. Le rapport sollicité ne peut se faire pour les jours à venir.', 'nexo_premium')));
            $this->Gui->output();
            return;
        }

        $data[ 'report_date' ]            =    $report_date;
        $data[ 'report_slug_prefix' ]    =    'nexo_detailed_daily_report_for_';
        $data[ 'report_slug' ]            =    $data[ 'report_date' ];
        $data[ 'CarbonCurrent' ]            =    $CarbonCurrent;
        $data[ 'CarbonReportDate' ]         =    $CarbonReportDate;

        $this->Gui->set_title( store_title( __('Rapport journalier détaillé', 'nexo_premium') ) );

        $this->Cache                    =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    $data[ 'report_slug_prefix' ] . store_prefix() ));
        $data[ 'Cache' ]                =    $this->Cache;
        $from                            =    isset($_GET[ 'ref' ]) ? '<a class="btn btn-default btn-sm" href="' . urldecode($_GET[ 'ref' ]) . '">' . __('Revenir en arrière', 'nexo_premium') . '</a>' : '';

        $this->events->add_filter('gui_page_title', function ($title) use ($from) {
            return '<section class="content-header"><h1>' . strip_tags($title) . ' <span class="pull-right"><a class="btn btn-primary btn-sm" href="' . current_url() . '?refresh=true">' . __('Vider le cache', 'nexo_premium') . '</a> ' . $from . '</span></h1></section>';
        });

        $this->load->view('../modules/nexo_premium/views/rapport-journalier-detaille', $data);
    }

    /**
     * Cash Flow
     * @param string year
     * @return void
     */
    public function cash_flow( $year = null ) 
    {
        if( User::cannot( 'nexo.read.cash-flow' ) ) {
            return nexo_access_denied();
        }

        $year                        =    $year == null ? Carbon::parse(date_now())->year : intval($year);

        $CarbonCurrent                =    Carbon::parse(date_now());
        $CarbonReportDate            =    Carbon::parse(date_now());
        $CarbonReportDate->year        =    $year;

        if ($CarbonCurrent->year    < $CarbonReportDate->year) {
            $this->Gui->set_title( store_title( __('Erreur', 'nexo_premium') ) );
            $this->notice->push_notice(tendoo_error(__('La date mentionnée est invalide. Le rapport sollicité ne peut se faire pour les jours à venir.', 'nexo_premium')));
            $this->Gui->output();
            return;
        }

        $data[ 'report_slug_prefix' ]    =    'nexo_flux_de_tresorerie_';
        $data[ 'report_slug' ]            =    $year;
        $data[ 'CarbonCurrent' ]        =    $CarbonCurrent;
        $data[ 'CarbonReportDate' ]        =    $CarbonReportDate;

        $this->Cache                    =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    $data[ 'report_slug_prefix' ] . store_prefix() ));
        $data[ 'Cache' ]                =    $this->Cache;

        $this->Gui->set_title( store_title( __('Flux de trésorerie', 'nexo_premium') ) );

        $this->load->view('../modules/nexo_premium/views/flux-de-la-tresorerie', $data);
    }

    /**
     * Sales Statistics
     * @param string year
     * @return void
     */
    public function sales_stats( $year = null ) 
    {
        if( User::cannot( 'nexo.read.annual-sales' ) ) {
            return nexo_access_denied();
        }

        $this->load->model('Nexo_Categories');
        $this->load->model('Nexo_Misc');

        $CarbonCurrent                    =    Carbon::parse(date_now());
        $CarbonReportDate                =    Carbon::parse(date_now());
        $CarbonReportDate->year            =    $year == null ? $CarbonCurrent->year : $year;

        $year                            =    $year == null ? Carbon::parse(date_now())->year : intval($year);
        $data                            =    array();
        $data[ 'report_slug_prefix' ]    =    'nexo_premium_';
        $this->Cache                    =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    $data[ 'report_slug_prefix' ] . store_prefix() ));
        $data[ 'report_slug' ]            =    'annual_sales_report_' . $year;
        $data[ 'CarbonCurrent' ]        =    $CarbonCurrent;
        $data[ 'CarbonReportDate' ]        =    $CarbonReportDate;
        $data[ 'Cache' ]                =    $this->Cache;

        // Save Cache
        if (@$_GET[ 'refresh' ] == 'true' || (! $this->Cache->get('categories_hierarchy') || ! $this->Cache->get('categories'))) {
            // Build content
            $data[ 'Categories' ]            =    $this->Nexo_Categories->get();
            $data[ 'Categories_Hierarchy' ]    =    $this->Nexo_Misc->build_category_hierarchy($data[ 'Categories'    ]);
            $data[ 'Categories_Depth' ]        =    $this->Nexo_Misc->array_depth($data[ 'Categories_Hierarchy' ]);
            // Save to cache
            $this->Cache->save('categories_hierarchy', $data[ 'Categories_Hierarchy' ]);
            $this->Cache->save('categories', $data[ 'Categories' ]);
            $this->Cache->save('categories_depth', $data[ 'Categories_Depth' ]);
        } else { // Get from Cache
            $data[ 'Categories'    ]            =    $this->Cache->get('categories');
            $data[ 'Categories_Hierarchy' ]    =    $this->Cache->get('categories_hierarchy');
            $data[ 'Categories_Depth' ]        =    $this->Cache->get('categories_depth');
        }

        $this->Gui->set_title( store_title( __('Rapport des Ventes Annuelles', 'nexo_premium') ) );

        $this->load->view('../modules/nexo_premium/views/stats-des-ventes', $data);
    }

    /**
     * Stock Tracking Sheet
     * @param string shipping 1
     * @param string shipping 2
     * @return void
     */
    public function stock_tracking( $shipping1 = null, $shipping2 = null ) 
    {
        if( User::cannot( 'nexo.read.inventory-tracking' ) ) {
            return nexo_access_denied();
        }

        $this->load->model('Nexo_Shipping');
        $data                            =    array();
        $data[ 'data' ]                    =    array();
        $data[ 'data' ][ 'shippings' ]    =    $this->Nexo_Shipping->get_shipping();

        $this->Gui->set_title( store_title( __('Fiche de suivi de stock &mdash; Nexo POS', 'nexo_premium') ) );
        $this->load->view('../modules/nexo_premium/views/fiche-de-suivi', $data);
    }

    private function invoice_header( $page, $index )
    {
        $crud = new grocery_CRUD();
        
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Factures', 'nexo_premium'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_premium_factures'));

        $columns        =   [ 'INTITULE', 'REF_CATEGORY', 'MONTANT', 'REF', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION', 'IMAGE' ];
        $fields         =   [ 'INTITULE', 'MONTANT', 'REF_CATEGORY', 'REF', 'DESCRIPTION', 'AUTHOR', 'IMAGE', 'DATE_CREATION', 'DATE_MODIFICATION' ];

        // only if the providers account is enabeld
        if( store_option( 'enable_providers_account', 'no' ) == 'yes' ) {
            array_splice( $columns, 2, 0, 'REF_PROVIDER' );
            array_splice( $fields, 2, 0, 'REF_PROVIDER' );
            $crud->set_relation( 'REF_PROVIDER', store_prefix() . 'nexo_fournisseurs', 'NOM' );
            $crud->field_description( 'REF_PROVIDER', sprintf( 
                __( 'Assigner une dépense à un fournisseur. Assurez-vous <a href="%s">d\'avoir assigné la bonne catégorie</a> pour les comptes créditeurs des fournisseurs.', 'nexo_premium' ) 
            , dashboard_url([ 'settings', 'providers' ]) ) );
        }

        $crud->columns( $columns );
        $crud->fields( $fields );

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $crud->set_relation('REF_CATEGORY', store_prefix() . 'nexo_premium_factures_categories', 'NAME' );

        $crud->display_as('INTITULE', __('Nom', 'nexo_premium'));
        $crud->display_as('REF_CATEGORY', __('Catégorie', 'nexo_premium'));
        $crud->display_as('MONTANT', __('Prix de la facture', 'nexo_premium'));
        $crud->display_as('REF', __('Référence', 'nexo_premium'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo_premium'));
        $crud->display_as('IMAGE', __('Image', 'nexo_premium'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo_premium'));
        $crud->display_as('DATE_CREATION', __('Date de création', 'nexo_premium'));
        $crud->display_as('DATE_MODIFICATION', __('Date de modification', 'nexo_premium'));
        $crud->display_as('REF_PROVIDER', __('Fournisseur', 'nexo_premium'));

        $crud->field_description( 'REF_CATEGORY', __( 'Assigner la dépense à une categorie.', 'nexo_premium' ) );
        $crud->field_description( 'MONTANT', __( 'Si la dépense à une valeur, vous pouvez le définir sur ce champ.', 'nexo_premium' ) );
        $crud->field_description( 'REF', __( 'La référence peut être le numéro d\'une facture ou toute information qui permettrait d\'identifier l\'opération.', 'nexo_premium' ) );

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->required_fields('INTITULE', 'MONTANT');

        $crud->change_field_type('AUTHOR', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MODIFICATION', 'invisible');

        $crud->set_field_upload('IMAGE', 'public/upload');

        $crud->callback_before_insert(array( $this, '__Facture_Create' ));
        $crud->callback_before_update(array( $this, '__Facture_Update' ));

        $crud->unset_jquery();
        $output = $crud->render();

        foreach ($output->js_files as $files) {
            $this->enqueue->js(substr($files, 0, -3), '');
        }
        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }
        return $output;
    }

    /**
     * Bill creation
     *
     * @param Array content array
     * @return Array
    **/

    public function __Facture_Create($data)
    {
        $data[ 'AUTHOR' ]               =    User::id();
        $data[ 'DATE_CREATION' ]        =    date_now();

        return $data;
    }

    /**
     * Callback when creating Bills
     *
     * @param Array content
     * @return Array
    **/

    public function __Facture_Update($data)
    {
        $data[ 'AUTHOR' ]                   =    User::id();
        $data[ 'DATE_MODIFICATION' ]        =    date_now();

        return $data;
    }

    /**
     * Bill controller
     *
     * @param string page string
     * @return void
    **/

    public function invoices($page = 'lists', $index = null )
    {
        if ($page == 'list') {
            $this->Gui->set_title( store_title( __('Liste des factures', 'nexo_premium') ));
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_purchases_invoices');
        } elseif( $page == 'provider' && $index != null ) {
            // if the payable account is not set
            // then redirect to the config page
            if( empty( store_option( 'providers_account_category' ) ) ) {
                return redirect([ 'dashboard', store_slug(), 'nexo', 'settings', 'providers?notice=provider_account_cateogry_missing' ]);
            }
            // if page is set to 'provider'
            // means we would like to pay a provider
            if( $page == 'provider' ) {
                $this->load->library('user_agent');
                $this->load->model( 'Nexo_Misc' );
                $provider       =   $this->db->where( 'ID', $index )->get( store_prefix() . 'nexo_fournisseurs' )
                ->result_array();

                if( ! $provider ) {
                    $returnLink             =   '';
                    if( $this->agent->is_referral() ) {
                        $returnLink =  sprintf( __( '<a href="%s">Return</a>', 'nexo_premium' ), $this->agent->referrer() );
                    }

                    return show_error( sprintf( 
                        __( 'Unable to locate the provider. %s', 'nexo_premium' ),
                        $returnLink
                    ) );
                }

                $this->load->library( 'form_validation' );
                $this->form_validation->set_rules( 'amount', __( 'Montant', 'nexo_premium' ), 'required|numeric' );

                if( $this->form_validation->run() ) {
                    $result             =   $this->Nexo_Misc->setPayment([
                        'provider_id'   =>  $index,
                        'amount'        =>  $this->input->post( 'amount' ),
                        'description'   =>  $this->input->post( 'description' ),
                        'ref_category'  =>  store_option( 'providers_account_category' )
                    ]);

                    if( $result == 'payment_made' ) {
                        return redirect([ 'dashboard', store_slug(), 'nexo', 'fournisseurs', 'lists' ]);
                    }
                }

                $this->Gui->set_title( store_title( 
                    sprintf( __( 'Paiement d\'un fournisseur : %s', 'nexo_premium' ), $provider[0][ 'NOM' ] ) 
                ) );
            }

            return $this->load->module_view( 'nexo_premium', 'providers.pay-gui' );
        } else {
            if (! User::can('nexo.view.invoices')) {
                return nexo_access_denied();
            }

            $this->Gui->set_title( store_title( __('Ajouter/modifier une facture', 'nexo_premium') ) );
        }

        $data[ 'crud_content' ]    =    $this->invoice_header( $page, $index );
        $this->load->view('../modules/nexo_premium/views/factures.php', $data);
    }

    /**
     * Clear cache
     * @param string cache id
     * @return void
    **/

    public function clear_cache($id)
    {
        if ($id == 'dashboard_cards') {
            foreach (glob(APPPATH . 'cache/app/nexo_premium_dashboard_card_' . store_prefix() . '*') as $filename) {
                unlink($filename);
            }

			/***
			 * Return to store dashboard
			**/

			if( get_store_id() ) {
				redirect( array( 'dashboard', 'stores', get_store_id() ) );
			} else {
            	redirect(array( 'dashboard' ));
			}
        }
    }

    /**
     * Controler Stats Cashier or Cashier performance
     *
    **/

    public function cashiers_report($start_date = null, $end_date = null)
    {
        if (! User::can('nexo.read.cashier-performances')) {
            return show_error( __( 'Vous n\'avez pas accès à ce rapport', 'nexo_premium' ) );
        }

        $data[ 'start_date' ]    =    $start_date == null ? Carbon::parse(date_now()) : $start_date;
        $data[ 'end_date' ]        =    $end_date    == null ? Carbon::parse(date_now())->addMonths(1): $end_date;
        $data[ 'cashiers' ]        =    $this->auth->list_users('shop.cashier');

        // $this->enqueue->js( '../modules/nexo/bower_components/Chart.js/Chart.min' );
        $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
        $this->enqueue->js('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min');
        $this->enqueue->js('../modules/nexo/bower_components/chosen/chosen.jquery');

        $this->enqueue->css('../modules/nexo/bower_components/chosen/chosen');
        $this->enqueue->css('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min');

        $this->Gui->set_title( store_title( __('Performances des caissiers', 'nexo_premium') ) );

        $this->load->module_view('nexo_premium', 'cashier-performances', $data);
    }

    /**
     * Customer Report
     * @param string start date
     * @param string end date
     * @return void
     */
    public function customers_report($start_date = null, $end_date = null)
    {
        if (! User::can('nexo.read.customer-statistics')) {
            return show_error( __( 'Vous n\'avez pas accès à ce rapport', 'nexo_premium' ) );
        }

        $this->load->model('Nexo_Misc');

        $data[ 'start_date' ]    =    $start_date == null ? Carbon::parse(date_now()) : $start_date;
        $data[ 'end_date' ]        =    $end_date    == null ? Carbon::parse(date_now())->addMonths(1): $end_date;
        $data[ 'customers' ]    =    $this->Nexo_Misc->get_customers();

        // $this->enqueue->js( '../modules/nexo/bower_components/Chart.js/Chart.min' );
        $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
        $this->enqueue->js('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min');
        $this->enqueue->js('../modules/nexo/bower_components/chosen/chosen.jquery');

        $this->enqueue->css('../modules/nexo/bower_components/chosen/chosen');
        $this->enqueue->css('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min');

        $this->Gui->set_title( store_title( __('Statistiques des clients', 'nexo_premium') ) );

        $this->load->module_view('nexo_premium', 'customers-statistics', $data);
    }

    /**
     * Controller_Historique
     *
    **/

    public function log($page = 1)
    {
        if (
            User::can('read_shop_user_tracker') ||
            User::can('delete_shop_user_tracker')
        ) {
            $this->load->model('Nexo_Misc');
            $this->load->library('pagination');

            $config['base_url']        =    site_url('dashboard/nexo_premium/Controller_Historique') . '/';
            $config['total_rows']        =    count($this->Nexo_Misc->history_get());
            $config['per_page']        =    5;
            $config['full_tag_open']    =    '<ul class="pagination">';
            $config['full_tag_close']    =    '</ul>';
            $config['next_tag_open']    =    $config['prev_tag_open']    =    $config['num_tag_open']        =    $config['first_tag_open']    =    $config['last_tag_open']    =    '<li>';
            $config['next_tag_close']    =    $config['prev_tag_close']    =    $config['num_tag_close']    =    $config['first_tag_close']      =    $config['last_tag_close']    =    '</li>';
            $config['cur_tag_open']         =    '<li class="active"><a href="#">';
            $config['cur_tag_close']            =    '</a></li>';


            $this->pagination->initialize($config);

            $this->events->add_filter('gui_page_title', function ($title) {
                return '<section class="content-header"><h1>' . strip_tags($title) . ' <span class="pull-right"><a class="btn btn-primary btn-sm" href="' . site_url(array( 'dashboard', 'nexo_premium', 'Controller_Clear_History' )) . '?refresh=true">' . __('Supprimer l\'historique', 'nexo_premium') . '</a></span></h1></section>';
            });

            $history                    =    $this->Nexo_Misc->history_get($page - 1, $config['per_page']);

            $this->Gui->set_title( store_title( __('Historique des activités', 'nexo_premium' ) ) );

			$this->load->module_view('nexo_premium', 'historique', array(
                'history'                =>    $history,
                'pagination'            =>    $this->pagination->create_links()
            ));

        } else {
            return show_error( __( 'Vous n\'avez pas accès à ce rapport', 'nexo_premium' ) );
        }
    }

    /**
     * Clear History
     * @unused
    **/

    public function Controller_Clear_History()
    {
        if (User::can('delete_shop_user_tracker')) {
            $this->load->model('Nexo_Misc');

            $this->Nexo_Misc->history_delete();

            $this->Nexo_Misc->history_add(
                __('Réinitialisation de l\'historique', 'nexo_premium'),
                sprintf(__('L\'utilisateur <strong>%s</strong> à supprimé le contenu de l\'historique des activités.', 'nexo_premium'), User::pseudo())
            );

            redirect(array( 'dashboard', 'nexo_premium', 'Controller_Historique' ));
        } else {
            return show_error( __( 'Vous n\'avez pas accès à cette page', 'nexo_premium' ) );
        }
    }

    /**
     * Best Of Controller
    **/

    public function best_sellers($filter = 'items', $start_date = null, $end_date = null)
    {
        if (! User::can('nexo.read.best-sales')) {
            return nexo_access_denied();
        }

        $data    =    array();

        /**
         * We're going to pass to view load, that's why we add params in a sub array
        **/

        $data[ 'params' ]    =    array();

        $data[ 'params' ][ 'start_date' ]    =    $start_date == null ? Carbon::parse(date_now())->subDays(7) : $start_date;
        $data[ 'params' ][ 'end_date' ]        =    $end_date    == null ? Carbon::parse(date_now()) : $end_date;

        $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
        $this->enqueue->js('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min');
        $this->enqueue->js('../modules/nexo/bower_components/chosen/chosen.jquery');

        $this->enqueue->css('../modules/nexo/bower_components/chosen/chosen');
        $this->enqueue->css('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min');

        $this->Gui->set_title( store_title( __('Les Meilleurs', 'nexo_premium') ) );

        $this->load->module_view('nexo_premium', 'best_of/home', $data);
    }

    /**
     * Quote Controller
     *
    **/

    public function quotes_cleaner()
    {
        $this->load->model('Nexo_Checkout');
        $this->load->model('Nexo_Misc');

        $Options   		=    $this->options->get();
        $Expiration    	=    @$Options[ 'nexo_devis_expiration' ];
        $QuoteID    	=    'nexo_order_devis';
        $LogEnabled    	=    @$Options[ 'nexo_premium_enable_history' ];

        $this->lang->load_lines(APPPATH . '/modules/nexo/language/nexo_lang.php');

        // Only valid expiration days are accepted
        if (! in_array(intval($Expiration), array( null, 0 )) && intval($Expiration) > 0) {
            $query        =    $this->db
                ->where('DATE_CREATION <=', Carbon::parse(date_now())->subDay($Expiration))
                ->where('TYPE', $QuoteID)
                ->get('nexo_commandes');
            $results    =    $query->result_array();
            $log        =    '<ul>';

            if ($results) {
                $Codes        =    @$Options[ 'order_code' ];
                if (! is_array($Codes)) {
                    json_decode($Codes, true);
                }

                foreach ($results as $result) {
                    foreach ($Codes as $key    =>    $Code) {
                        if ($Code == $result[ 'CODE' ]) {
                            unset($Codes[ $key ]);
                        }
                    }

                    // Clean code used from "order_code" option
                    $this->Nexo_Checkout->commandes_delete($result[ 'ID' ]);
                    // Since commandes deletes doesn't delete parent order
                    $this->db->where('ID', $result[ 'ID' ])->delete('nexo_commandes');

                    $log .= '<li>' . $result[ 'CODE' ] . '</li>';
                }

                $this->options->set('order_code', $Codes);

                $log        .=    '</ul>';

                // If Log is enabled
                if ($LogEnabled == 'yes') {
                    $this->Nexo_Misc->history_add(
                        $this->lang->line('deleted-quotes-title'),
                        sprintf($this->lang->line('deleted-quotes-msg'), $log)
                    );
                }

                echo json_encode(array(
                    'title'    =>    addslashes($this->lang->line('deleted-quotes-title')),
                    'msg'    =>    addslashes(sprintf($this->lang->line('deleted-quotes-msg'), $log)),
                    'orders'=>    $results
                ));
            } else {
                echo json_encode(array());
            }
        }
    }

    /**
     * Profit and losses
     * @param string start date
     * @param string end date
     * @return void
     */
    public function profit_and_losses( $start = null, $end = null )
    {
        if( User::cannot( 'nexo.read.incomes-losses' ) ) {
            return nexo_access_denied();
        }

        $this->enqueue->css( 'datepicker3', base_url() . 'public/plugins/datepicker/' );
        $this->enqueue->js( 'bootstrap-datepicker', base_url() . 'public/plugins/datepicker/' );

        $this->Gui->set_title( sprintf( __( 'Bénéfices et Pertes &mdash; %s', 'nexo_premium' ), @$Options[ store_prefix() . 'site_name' ] ) );

        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'nexo_premium', 'profit_and_lost_script' );
        });

        $this->load->module_view( 'nexo_premium', 'profit_and_lost' );
    }

    /**
     * Expenses Listing
     * @param string start
     * @param string end
     * @return void
     */
    public function expense_listing( $start = null, $end = null )
    {
        if( User::cannot( 'nexo.read.expenses-listings' ) ) {
            return nexo_access_denied();
        }

        $this->enqueue->css( 'datepicker3', base_url() . 'public/plugins/datepicker/' );
        $this->enqueue->js( 'bootstrap-datepicker', base_url() . 'public/plugins/datepicker/' );

        $this->Gui->set_title( sprintf( __( 'Liste des dépenses &mdash; %s', 'nexo_premium' ), @$Options[ store_prefix() . 'site_name' ] ) );

        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'nexo_premium', 'expenses_listing_script' );
        });

        $this->load->module_view( 'nexo_premium', 'expenses_listing' );
    }

    /**
     *  Detailed Sales Report Controller
     *  @param
     *  @return
    **/

    public function detailed_sales( $start = null, $end = null )
    {
        if( User::cannot( 'nexo.read.detailed-report' ) ) {
            return nexo_access_denied();
        }

        global $Options;
        $today          =   date_now();
        $start          =   $start == null ? $startOfDate   =   Carbon::parse( $today )->startOfDay() : $start;
        $end            =   $end == null ? $endOfToday      =   Carbon::parse( $today )->endOfDay() : $end;

        $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
        $this->enqueue->js('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min');
        $this->enqueue->js('../modules/nexo/bower_components/chosen/chosen.jquery');

        $this->enqueue->css('../modules/nexo/bower_components/chosen/chosen');
        $this->enqueue->css('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min');

        $this->Gui->set_title( sprintf( __( 'Rapport des ventes détaillés &mdash; %s', 'nexo_premium' ), @$Options[ store_prefix() . 'site_name' ] ) );

        // Load JS
        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'nexo_premium', 'sales_detailed_script' );
        });

        $this->load->module_view( 'nexo_premium', 'sales_detailed', array(
            'start_date'     =>      $start->toDateTimeString(),
            'end_date'       =>      $end->toDateTimeString()
        ) );
    }

    /**
     * Expense Category List
    **/

    public function expenses_list()
    {
        $data[ 'crud' ]     =   $this->expenses_list_crud();
        $this->Gui->set_title( store_title( __( 'Catégories des dépenses', 'nexo_premium') ) );
        $this->load->module_view( 'nexo_premium', 'expenses.categories', $data );
    }

    /**
     * Expense Category Header
     * @unused
    **/

    private function expenses_list_crud()
    {
        if (
            ! User::can( 'nexo.view.invoices' )
        ) {
            return nexo_access_denied();
        }

        $crud = new grocery_CRUD();

        $crud->set_theme('bootstrap');
        $crud->set_subject(__( 'Catégories des factures' , 'nexo_premium'));
        $crud->set_table( $this->db->dbprefix( store_prefix() . 'nexo_premium_factures_categories' ) );

        $crud->columns( 'NAME', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION' );
        $crud->fields( 'NAME', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION');

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->display_as('NAME', __( 'Nom', 'nexo_premium'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo_premium'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo_premium'));
        $crud->display_as('DATE_CREATION', __('Date de création', 'nexo_premium'));
        $crud->display_as('DATE_MODIFICATION', __('Date de modification', 'nexo_premium'));

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->change_field_type( 'AUTHOR', 'invisible');
        $crud->change_field_type( 'DATE_CREATION', 'invisible');
        $crud->change_field_type( 'DATE_MODIFICATION', 'invisible');

        $crud->callback_before_insert(array( $this, '__Callback_Backup_Create' ));
        $crud->callback_before_update(array( $this, '__Callback_Backup_Update' ));
        $crud->callback_before_delete(array( $this, '__Callback_Backup_Delete' ));

        $crud->callback_before_insert(array( $this, 'expenses_category_insert' ) );
        $crud->callback_before_update(array( $this, 'expenses_category_update' ) );

        /**
        * Filter for actions
        **/

        $this->events->add_filter('grocery_actions', array( $this, '__Filter_action_url' ), 10, 2);

        $crud->unset_jquery();
        $output = $crud->render();

        foreach ($output->js_files as $files) {
            $this->enqueue->js(substr($files, 0, -3), '');
        }

        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }

        return $output;
    }

    /**
     * Expense Category Insert7
     * @unused
    **/

    public function expenses_category_insert( $data ) 
    {
        $data[ 'DATE_CREATION' ]    =   date_now();
        $data[ 'AUTHOR' ]           =   User::id();
        return $data;
    }

    /**
     * Expense Category Update
     * @unused
    **/

    public function expenses_category_update( $data )
    {
        $data[ 'DATE_MODIFICATION' ]    =   date_now();
        $data[ 'AUTHOR' ]               =   User::id();
        return $data;
    }
}