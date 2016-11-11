<?php
use Carbon\Carbon;

class Nexo_Reports extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->index();
            }
        }
        return $this->index();
    }

    public function index()
    {
        $this->journalier();
    }

    public function journalier($start_date = null, $end_date = null)
    {
        if (! User::can('read_shop_reports')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        global $Options;

        switch (@$Options[ 'site_language' ]) {
            case 'fr_FR'    :    $lang    = 'fr'; break;
            default        :    $lang    = 'en'; break;
        }

        Carbon::setLocale($lang);

        $this->cache        =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    'nexo_daily_reports_' . store_prefix() ));

        if ($start_date == null && $end_date == null) {

            // Start Date
            $CarbonStart    =    Carbon::parse(date_now())->startOfMonth();

            // End Date
            $CarbonEnd        =    Carbon::parse(date_now())->endOfMonth();

            // Is Date valid
            $DateIsValid    =    $CarbonStart->lt($CarbonEnd);

            // Default date
            $start_date        =    $CarbonStart->toDateString();
            $end_date        =    $CarbonEnd->toDateString();
        } else {

            // Start Date
            $CarbonStart    =    Carbon::parse($start_date);

            // End Date
            $CarbonEnd        =    Carbon::parse($end_date);

            // Is Date valid
            $DateIsValid    =    $CarbonStart->lt($CarbonEnd);
        }

        $data                =    array(
            'report_slug'    =>     'from-' . $start_date . '-to-' . $end_date
        );

        if (! $DateIsValid) {
            show_error(sprintf(__('Le rapport ne peut être affiché, la date spécifiée est incorrecte', 'nexo')));
        }

        if ($CarbonStart->diffInMonths($CarbonEnd) > 999) {
            show_error(sprintf(__('Le rapport ne peut être affiché, l\'intervale de date ne peut excéder 3 mois.', 'nexo')));
        }

        $this->load->model('Nexo_Misc');

        $this->enqueue->js('../modules/nexo/bower_components/Chart.js/Chart.min');
        $this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
        $this->enqueue->js('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min');
        $this->enqueue->css('../modules/nexo/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min');

        $this->Gui->set_title( store_title( __('Rapport des ventes journalières', 'nexo') ) );

        $data[ 'start_date' ]    =    $CarbonStart->toDateString();
        $data[ 'end_date' ]        =    $CarbonEnd->toDateString();
        $data[ 'CarbonStart' ]    =    $CarbonStart;
        $data[ 'CarbonEnd' ]    =    $CarbonEnd;
        $data[ 'Cache' ]        =    $this->cache;

        $this->load->view("../modules/nexo/views/reports/daily.php", $data);
    }
}

new Nexo_Reports($this->args);
