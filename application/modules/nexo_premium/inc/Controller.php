<?php
! defined('APPPATH') ? die() : null;

use Carbon\Carbon;

/**
 * Nexo Premium UI
 *
 * @author Blair Jersyer
 * @version 1.0
**/

class Nexo_Premium_Controller extends CI_Model
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
     * Index Page
     * @param string
     * @deprecated
     * @return void
    **/

    public function index($page)
    {
        if (method_exists($this, $page)) {
            call_user_func_array(array( $this, $page ), array_slice(func_get_args(), 1));
        } else {
            show_error(__('Cette page est introuvable', 'nexo_premium'));
        }
    }

    /**
     * Rapport Journalier Detaillé
     * @param string date
    **/

    public function Controller_Rapport_Journalier_Detaille($report_date)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        // if repport date is sup than current day
        $CarbonCurrent                =    Carbon::parse(date_now());
        $CarbonReportDate            =    Carbon::parse($report_date);

        if (! $CarbonCurrent->gte($CarbonReportDate)) {
            $this->Gui->set_title(__('Erreur', 'nexo_premium'));
            $this->notice->push_notice(tendoo_error(__('La date mentionnée est invalide. Le rapport sollicité ne peut se faire pour les jours à venir.', 'nexo_premium')));
            $this->Gui->output();
            return;
        }

        $data[ 'report_date' ]            =    $report_date;
        $data[ 'report_slug_prefix' ]    =    'nexo_detailed_daily_report_for_';
        $data[ 'report_slug' ]            =    $data[ 'report_date' ];
        $data[ 'CarbonCurrent' ]        =    $CarbonCurrent;
        $data[ 'CarbonReportDate' ]        =    $CarbonReportDate;

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
     * Mouvement Annuel de la trésorerie
     * @param string/NULL year
    **/

    public function Controller_Mouvement_Annuel_Tresorerie($year = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
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
     * Sales statistics
     *
     * @author  Blair Jersyer
     * @param string/NULL year
    **/

    public function Controller_Stats_Des_Ventes($year = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
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
     * Fiche de suivi
     *
     * @param int Shipping Int
     * @param int Shipping Int
     * @return void
    **/

    public function Controller_Fiche_De_Suivi($shipping_one = null, $shipping_two = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $this->load->model('Nexo_Shipping');
        $data                            =    array();
        $data[ 'data' ]                    =    array();
        $data[ 'data' ][ 'shippings' ]    =    $this->Nexo_Shipping->get_shipping();

        $this->Gui->set_title( store_title( __('Fiche de suivi de stock &mdash; Nexo POS', 'nexo_premium') ) );
        $this->load->view('../modules/nexo_premium/views/fiche-de-suivi', $data);
    }

    /**
     * Controller Factures
     *
     * @return void
    **/

    private function Controller_Header()
    {
        if (
         ! User::can('create_shop_purchases_invoices ') &&
         ! User::can('edit_shop_purchases_invoices') &&
         ! User::can('delete_shop_purchases_invoices')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $crud = new grocery_CRUD();

        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Factures', 'nexo_premium'));
        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_premium_factures'));

        $crud->columns('INTITULE', 'REF_CATEGORY', 'MONTANT', 'REF', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION', 'IMAGE' );
        $crud->fields('INTITULE', 'MONTANT', 'REF_CATEGORY', 'REF', 'DESCRIPTION', 'AUTHOR', 'IMAGE', 'DATE_CREATION', 'DATE_MODIFICATION');

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

    public function Controller_Factures($page = 'lists' )
    {
        if ($page == 'list') {
            $this->Gui->set_title( store_title( __('Liste des factures', 'nexo_premium') ));
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_purchases_invoices');
        } else {
            if (! User::can('create_shop_purchases_invoices')) {
                redirect(array( 'dashboard', 'access-denied' ));
            }

            $this->Gui->set_title( store_title( __('Ajouter/modifier une facture', 'nexo_premium') ) );
        }

        $data[ 'crud_content' ]    =    $this->Controller_Header();
        $this->load->view('../modules/nexo_premium/views/factures.php', $data);
    }

    /**
     * Clear cache
     * @param string cache id
     * @return void
    **/

    public function Controller_Clear_Cache($id)
    {
        if ($id == 'dashboard_card') {
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

    public function Controller_Stats_Caissier($start_date = null, $end_date = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $data[ 'start_date' ]    =    $start_date == null ? Carbon::parse(date_now()) : $start_date;
        $data[ 'end_date' ]        =    $end_date    == null ? Carbon::parse(date_now())->addMonths(1): $end_date;
        $data[ 'cashiers' ]        =    $this->auth->list_users('shop_cashier');

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
     * Statistique des clients
     *
     * @param string start date
     * @param end date
     * @return void
    **/

    public function Controller_Stats_Clients($start_date = null, $end_date = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
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

    public function Controller_Historique($page = 1)
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
            redirect(array( 'dashboard', 'access-denied' ));
        }
    }

    /**
     * Clear History
     *
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
            redirect(array( 'dashboard', 'access-denied' ));
        }
    }

    /**
     * Backup Controller Header
    **/

    private function Backup_Controller_Header()
    {
        if (
            ! User::can('create_shop_backup') &&
            ! User::can('edit_shop_backup') &&
            ! User::can('delete_shop_backup')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        $crud = new grocery_CRUD();

        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Sauvegardes', 'nexo_premium'));
        $crud->set_table($this->db->dbprefix('nexo_premium_backups'));

        $crud->columns('NAME', 'FILE_LOCATION', 'AUTHOR', 'DATE_CREATION');
        $crud->fields('NAME', 'FILE_LOCATION', 'AUTHOR', 'DATE_CREATION', 'DATE_MODIFICATION');

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->display_as('NAME', __('Titre de la sauvegarde', 'nexo_premium'));
        $crud->display_as('FILE_LOCATION', __('Emplacement du fichier', 'nexo_premium'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo_premium'));
        $crud->display_as('DATE_CREATION', __('Date de création', 'nexo_premium'));
        $crud->display_as('DATE_MODIFICATION', __('Date de modification', 'nexo_premium'));

        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));

        $crud->change_field_type('FILE_LOCATION', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MODIFICATION', 'invisible');

        $crud->callback_before_insert(array( $this, '__Callback_Backup_Create' ));
        $crud->callback_before_update(array( $this, '__Callback_Backup_Update' ));
        $crud->callback_before_delete(array( $this, '__Callback_Backup_Delete' ));

        $crud->add_action(__('Télécharger la sauvegarde', 'nexo_premium'), '', site_url(array( 'dashboard', 'nexo_premium', 'Controller_Download_Backup' )) . '/', 'btn btn-success fa fa-archive');

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
     * Backup
    **/

    public function Controller_Backup($page = 'list')
    {
        if (in_array($page, array( 'list', 'success' ))) {
            $this->Gui->set_title( store_title( __('Liste des sauvegardes', 'nexo_premium') ) );
        } elseif ($page == 'add') {
            if (! User::can('create_shop_backup')) {
                redirect(array( 'dashboard', 'access-denied' ));
            }
            $this->Gui->set_title( store_title( __('Ajouter une sauvegarde', 'nexo_premium') ) );
        } elseif ($page == 'delete') {
            nexo_permission_check('delete_shop_backup');
        } else {
            if (! User::can('edit_shop_backup')) {
                redirect(array( 'dashboard', 'access-denied' ));
            }
            $this->Gui->set_title( store_title( __('Edition une sauvegarde', 'nexo_premium') ) );
        }
        $data[ 'crud_content' ]    =    $this->Backup_Controller_Header();
        $this->load->view('../modules/nexo_premium/views/backups.php', $data);
    }

    /**
     * Callback for Backup creation
     *
     * @param Array post data
     * @return Array
    **/

    public function __Callback_Backup_Create($data)
    {
        $data[ 'NAME' ]                =    empty($data[ 'NAME' ]) ? __('Sauvegarde Base de donnée Nexo POS', 'nexo_premium') : $data[ 'NAME' ];
        $data[ 'AUTHOR'    ]            =    User::id();
        $data[ 'DATE_CREATION' ]    =    date_now();
        $data[ 'FILE_LOCATION' ]    =    $this->__backupName();

        $this->__doBackup($data);

        return $data;
    }

    /**
     * Callback for Backup update
     *
     * @param Array post data
     * @return Array
    **/

    public function __Callback_Backup_Update($data)
    {
        $segments    =    $this->uri->segment_array();
        $query        =    $this->db->where('ID', end($segments))->get('nexo_premium_backups');
        $result        =    $query->result_array();

        $data[ 'NAME' ]                    =    empty($data[ 'NAME' ]) ? __('Sauvegarde Base de donnée Nexo POS', 'nexo_premium') : $data[ 'NAME' ];
        $data[ 'AUTHOR'    ]                =    User::id();
        $data[ 'DATE_MODIFICATION' ]    =    date_now();
        $data[ 'FILE_LOCATION' ]        =    $result[0][ 'FILE_LOCATION' ];

        @unlink(PUBLICPATH . '/upload/nexo_premium_backups/' . $data[ 'FILE_LOCATION' ]);

        $this->__doBackup($data);

        return $data;
    }

    /**
     * Callback for Backup deletion
     *
     * @param Array post data
     * @return Array
    **/

    public function __Callback_Backup_Delete($data)
    {
        $query    =    $this->db->where('ID', $data)->get('nexo_premium_backups');
        $result    =    $query->result_array();

        if ($result) {
            @unlink(PUBLICPATH . '/upload/nexo_premium_backups/' . $result[0][ 'FILE_LOCATION' ] . '.zip');
        }

        return $data;
    }

    /**
     * get backup names
     *
     * @return string
    **/

    private function __backupName()
    {
        $date        =    Carbon::parse(date_now());
        return 'nexo_premium_backup_' . $date->year . $date->month . $date->day . $date->hour . $date->minute . $date->second . '-' . $date->micro;
    }

    /**
     * Do backup
    **/

    private function __doBackup($data)
    {
        $this->config->load('nexo_premium');
        $this->load->dbutil();
        $tables_tobackup        =    $this->config->item('tables_to_backup');
        if ($tables_tobackup) {
            foreach ($tables_tobackup as &$table) {
                $table            =    $this->db->dbprefix($table);
            }
            // Complete backup
            $backup = $this->dbutil->backup(array(
                    'tables'        => $tables_tobackup,   // Array of tables to backup.
                    // 'ignore'        => array(),                     // List of tables to omit from the backup
                    'format'        => 'zip',                       // gzip, zip, txt
                    'filename'      => $data[ 'FILE_LOCATION' ] . '.sql',              // File name - NEEDED ONLY WITH ZIP FILES
                    'add_drop'      => true,                        // Whether to add DROP TABLE statements to backup file
                    'add_insert'    => true,                        // Whether to add INSERT data to backup file
                    'newline'       => "\n"                         // Newline character used in backup file
            ));

            $this->load->helper('file');

            write_file(PUBLICPATH . 'upload/nexo_premium_backups/' . $data[ 'FILE_LOCATION' ] . '.zip', $backup);
        }
    }

    /**
     * Download Backup
    **/

    public function Controller_Download_Backup($id)
    {
        $query    =    $this->db->where('ID', $id)->get('nexo_premium_backups');
        $result    =    $query->result_array();
        if ($result) {
            $this->load->helper('download');
            force_download($result[0][ 'FILE_LOCATION' ] . '.zip', file_get_contents(PUBLICPATH . '/upload/nexo_premium_backups/' . $result[0][ 'FILE_LOCATION' ] . '.zip'));
        } else {
            redirect(array( 'dashboard', 'nexo_premium', 'Controller_Backup?notice=nexo-premium-unable-to-locate-backup' ));
        }
    }

    /**
     * Restaure
     *
    **/

    public function Controller_Restore()
    {
        $this->load->model('Nexo_Misc');

        $config['upload_path']            =    PUBLICPATH . '/upload/nexo_premium_backups/temp';
        $config['allowed_types']        =     'zip';

        $this->load->library('upload', $config);

        $data                            =    array();

        if ($this->upload->do_upload('restore_file')) {
            $data    =    $this->upload->data();
            if ($queries_nbr            =    $this->Nexo_Misc->do_restore($data)) {
                $data[ 'queries_nbr' ]    =    $queries_nbr;
                $data[ 'table_prefix' ]    =    $this->input->post('db_prefix');
            }
        }

        $this->Gui->set_title( store_title( __('Restauration', 'nexo_premium') ) );

        $this->load->module_view('nexo_premium', 'restore', $data);
    }

    /**
     * Best Of Controller
    **/

    public function Controller_Best_Of($filter = 'items', $start_date = null, $end_date = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
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

    public function Controller_Quote_Cleaner()
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
    *
    * Profit and Lost
    *
    * @param string date (optional) 4
    * @param string date end date (optional)
    * @return void
    */

    public function rapports( $rapport_namespace = '', $start = null, $end = null )
    {
        global $Options;
        if( $rapport_namespace == 'profits_and_losses' ){

            $this->enqueue->css( 'datepicker3', base_url() . 'public/plugins/datepicker/' );
            $this->enqueue->js( 'bootstrap-datepicker', base_url() . 'public/plugins/datepicker/' );

            $this->Gui->set_title( sprintf( __( 'Bénéfices et Pertes &mdash; %s', 'nexo_premium' ), @$Options[ store_prefix() . 'site_name' ] ) );

            $this->events->add_action( 'dashboard_footer', function(){
                get_instance()->load->module_view( 'nexo_premium', 'profit_and_lost_script' );
            });

            $this->load->module_view( 'nexo_premium', 'profit_and_lost' );

        } else if( $rapport_namespace == 'expenses_listing' ) {
            $this->enqueue->css( 'datepicker3', base_url() . 'public/plugins/datepicker/' );
            $this->enqueue->js( 'bootstrap-datepicker', base_url() . 'public/plugins/datepicker/' );

            $this->Gui->set_title( sprintf( __( 'Liste des dépenses &mdash; %s', 'nexo_premium' ), @$Options[ store_prefix() . 'site_name' ] ) );

            $this->events->add_action( 'dashboard_footer', function(){
                get_instance()->load->module_view( 'nexo_premium', 'expenses_listing_script' );
            });

            $this->load->module_view( 'nexo_premium', 'expenses_listing' );
        }
    }

    /**
     *  Detailed Sales Report Controller
     *  @param
     *  @return
    **/

    public function detailed_sales( $start = null, $end = null )
    {
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
    **/

    private function expenses_list_crud()
    {
        if (
         ! User::can('create_shop_purchases_invoices ') &&
         ! User::can('edit_shop_purchases_invoices') &&
         ! User::can('delete_shop_purchases_invoices')
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
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
     * Expense Category Insert
    **/

    public function expenses_category_insert( $data ) 
    {
        $data[ 'DATE_CREATION' ]    =   date_now();
        $data[ 'AUTHOR' ]           =   User::id();
        return $data;
    }

    /**
     * Expense Category Update
    **/

    public function expenses_category_update( $data )
    {
        $data[ 'DATE_MODIFICATION' ]    =   date_now();
        $data[ 'AUTHOR' ]               =   User::id();
        return $data;
    }


}
