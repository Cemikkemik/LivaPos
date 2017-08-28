<?php
require_once(APPPATH . 'modules/nexo/vendor/autoload.php');

use Carbon\Carbon;
use Curl\Curl;

class Nexo_Misc extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * License validation
    *
    * @param string license
    * @return string
    **/

    public function validate_license($license)
    {
        $this->load->library('curl');
        $result    =    @$this->curl->security(false)->get('http://nexoapp.tendoo.org/index.php/nexo_license_manager/' . $license);
        if (is_array($json_result    =    json_decode($result, true))) {
            if (riake('is_valid', $json_result)) {
                return riake('license_duration', $json_result);
            } else {
                return 'license-has-expired';
            }
        } else {
            return 'unable-to-connect';
        }
    }

    /**
    * Check License
    *
    * @return void
    **/

    public function check_license()
    {
        global $Options, $CurrentMethod;
        $Nexo_license        =    riake('nexo_license', $Options);
        $old_license        =    riake('nexo_old_license', $Options);
        $Nexo_expiration    =    riake('nexo_expiration', $Options);
        $now                =    gmt_to_local(now(), riake('site_timezone', $Options, 'Etc/Greenwich'), true);

        // Nexo
        if (riake('nexo_demo_mode_enabled', $Options) != true && strlen(riake('nexo_license_name_to', $Options)) > 0) {
            $license            =    $this->license_key->codeGenerate(riake('nexo_license_name_to', $Options));
            $Nexo_expiration    =    $now + (3600 * 24) * 62;

            $this->options->set('nexo_license', $license, true);
            $this->options->set('nexo_old_license', $license, true);
            $this->options->set('nexo_expiration', $Nexo_expiration, true);
            $this->options->set('nexo_demo_mode_enabled', true, true);
            $this->options->set('nexo_license_to_saved_name', riake('nexo_license_name_to', $Options), true);
        } elseif (riake('nexo_demo_mode_enabled', $Options) == true) {
            // Si la license actuelle est différente de la license précédente
            if ($Nexo_license != $old_license) {
                // Si la licence est valide
                if ($this->license_key->codeValidate($Nexo_license, riake('nexo_license_name_to', $Options))) {
                    $code    =    $this->validate_license($Nexo_expiration);
                    if (in_array($code, array( 'unable-to-connect', 'license-has-expired' ))) {
                        $this->options->set('nexo_license', $old_license, true);
                        $this->notice->push_notice($this->lang->line($code));
                    } else {
                        $this->upgrade_duration_time($code, $Nexo_license);
                        redirect(array( 'dashboard?notice=license-activated' ));
                    }
                } else { // si la licence n'est pas valide
                    $this->options->set('nexo_license', $old_license, true);
                    redirect(array( 'dashboard', 'invalid-activation-key' ));
                }
            }
        }
        // reminder before one week
        if ($Nexo_expiration > $now) {
            if ($Nexo_expiration - $now  < (3600 * 24) * 7) {
                $this->notice->push_notice(tendoo_info(mdate(__('Votre licence expire le %d-%m-%Y à %h:%i', 'nexo_premium'), $Nexo_expiration)));
            } else {
                $this->events->add_filter('ui_notices', function ($notices) use ($Nexo_expiration) {
                    $notices[]    =    array(
                        'msg'       =>    mdate(__('Votre licence expire le %d-%m-%Y à %h:%i', 'nexo_premium'), $Nexo_expiration),
                        'type'    =>  'warning',
                        'icon'        =>  'fa fa-times',
                        'href'        =>    site_url(array( 'dashboard', 'settings' ))
                    );
                    return $notices;
                });
            }
        }

        // redirect if license has expired

        if ($Nexo_expiration < $now && $CurrentMethod == 'nexo') {
            redirect(array( 'dashboard', 'license-expired' ));
        }
    }

    /**
    * License checker
    *
    * @return string
    **/

    public function check_license_ajax()
    {
        global $Options;

        $license        =    riake('nexo_old_license', $Options);
        $Nexo_license    =    riake('nexo_license', $Options);
        $code            =    $this->validate_license($license);

        if (! in_array($code, array( 'unable-to-connect', 'license-has-expired' ))) {
            $this->upgrade_duration_time($code, $Nexo_license);
            return 'license-updated';
        }
        return $code;
    }

    /**
    * Duration Upgrate
    *
    * @param string
    * @param string license
    * @return void
    * @deprecated
    **/

    private function upgrade_duration_time($code, $Nexo_license)
    {
        global $Options;
        // redefinir la durée d'une licence
        $this->options->set('nexo_expiration', (gmt_to_local(now(), riake('site_timezone', $Options, 'Etc/Greenwich'), true) + $code), true);
        $this->options->set('nexo_old_license', $Nexo_license, true);
    }

    /**
    * Currency Position
    * Affiche la devise selon les réglages défini. Par défaut à droite
    *
    * @param String (before/after)
    * @return String
    **/

    public function display_currency($position)
    {
        global $Options;
        if (@$Options[ store_prefix() . 'nexo_currency_position' ] === $position) {
            return $Options[ store_prefix() . 'nexo_currency' ];
        }
    }

    /**
    * get Currency
    *
    * @return String/Null
    **/

    public function currency()
    {
        global $Options;
        return @$Options[ store_prefix() . 'nexo_currency' ];
    }

    /**
    * This function empty the shop
    *
    * @return void
    **/

    public function empty_shop()
    {
        $this->events->do_action( 'nexo_empty_shop' );
        $this->clear_cache();
        // $this->db->query( 'TRUNCATE `'.$this->db->dbprefix.'nexo_bon_davoir`;' );
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes_produits`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes_produits_meta`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes_paiements`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes_coupons`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes_meta`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_commandes_shippings`;');

        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_coupons`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_historique`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_notices`;');
        
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_articles`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_articles_variations`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_articles_stock_flow`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_articles_meta`;');

        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_categories`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_fournisseurs`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_arrivages`;');

        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_taxes`;');

        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_rayons`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_clients`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_clients_groups`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_clients_address`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_clients_meta`;');

        // @since 2.7.5
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_registers`;');
        $this->db->query('TRUNCATE `'.$this->db->dbprefix. store_prefix() . 'nexo_registers_activities`;');
    }

    /**
    * Clear cache
    *
    * @return void
    **/

    public function clear_cache()
    {
        foreach (glob(APPPATH . '/cache/app/nexo_*') as $filename) {
            unlink($filename);
        }
    }

    /**
    * Fill Demo Data
    * Empty shop first and create dummy data
    * @return void
    **/

    public function enable_demo( $demo = 'default' )
    {
        $this->empty_shop();
        
        $this->events->do_action( 'nexo_enable_demo', $demo );

        if( $demo == 'default' ) {
            $this->load->view('../modules/nexo/inc/demo');
        } else if( $demo == 'clothes' ) {
            $this->load->view('../modules/nexo/inc/clothes-demo');
        }
    }

    /**
    * Calculate Checksum digit
    * @return int
    * @source http://www.wordinn.com/solution/130/php-ean8-and-ean13-check-digit-calculation
    **/

    public function ean_checkdigit($digits, $type)
    {
        if ($type == 'ean13') {
            //first change digits to a string so that we can access individual numbers
            $digits =(string)$digits;
            // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
            $even_sum = $digits{1}
            + $digits{3}
            + $digits{5}
            + $digits{7}
            + $digits{9}
            + $digits{11};
            // 2. Multiply this result by 3.
            $even_sum_three = $even_sum * 3;
            // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
            $odd_sum = $digits{0}
            + $digits{2}
            + $digits{4}
            + $digits{6}
            + $digits{8}
            + $digits{10};
            // 4. Sum the results of steps 2 and 3.
            $total_sum = $even_sum_three + $odd_sum;
            // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
            $next_ten = (ceil($total_sum/10))*10;
            $check_digit = $next_ten - $total_sum;
            return $check_digit; // $digits .
        } elseif ($type == 'ean8') {
            $digits = str_pad($digits, 12, "0", STR_PAD_LEFT);
            $sum = 0;
            for ($i=(strlen($digits)-1);$i>=0;$i--) {
                $sum += (($i % 2) * 2 + 1) * $digits[$i];
            }
            return (10 - ($sum % 10));
        }
    }

    /**
    * Week of the month
    * @source http://stackoverflow.com/questions/5853380/php-get-number-of-week-for-month
    **/

    public function getWeeks($date, $rollover)
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $weeks = 1;

        for ($i = 1; $i < $elapsed; $i++) {
            $dayfind        = $cut . (strlen($i) < 0 ? '0' . $i : $i);
            $daytimestamp    = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));
            // var_dump( $daytimestamp );
            if ($day == strtolower($rollover)) {
                $weeks ++;
            }
        }

        return $weeks;
    }

    /**
    * Get Week
    * @param int timestamp
    * @return string
    **/

    public function getWeek($timestamp)
    {
        $week_year = date('W', $timestamp);
        $week = 0;//date('d',$timestamp)/7;
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        $prev_month = date('m', $timestamp) -1;
        if ($month != 1) {
            $last_day_prev = $year."-".$prev_month."-1";
            $last_day_prev = date('t', strtotime($last_day_prev));
            $week_year_last_mon = date('W', strtotime($year."-".$prev_month."-".$last_day_prev));
            $week_year_first_this = date('W', strtotime($year."-".$month."-1"));
            if ($week_year_first_this == $week_year_last_mon) {
                $week_diff = 0;
            } else {
                $week_diff = 1;
            }
            if ($week_year ==1 && $month == 12) {
                // to handle December's last two days coming in first week of January
                $week_year = 53;
            }

            $week = $week_year-$week_year_last_mon + 1 +$week_diff;
        } else {
            // to handle first three days January coming in last week of December.
            $week_year_first_this = date('W', strtotime($year."-01-1"));
            if ($week_year_first_this ==52 || $week_year_first_this ==53) {
                if ($week_year == 52 || $week_year == 53) {
                    $week =1;
                } else {
                    $week = $week_year + 1;
                }
            } else {
                $week = $week_year;
            }
        }
        return $week;
    }

    /**
    * Client Creation
    *
    * @param String customer name
    * @param String customer email
    * @return json
    **/

    public function create_customer($name, $email)
    {
        if ($this->db->insert( store_prefix() . 'nexo_clients', array(
            'EMAIL'        =>    xss_clean($email),
            'NOM'        =>    $name
        ))) {
            return json_encode(array(
                'msg'        =>    __('Le client a été correctement crée.', 'nexo_premium'),
                'type'        =>    'success'
            ));
        } else {
            return json_encode(array(
                'msg'        =>    __('Une erreur s\'est produite.', 'nexo_premium'),
                'type'        =>    'warning'
            ));
        }
    }

    /**
     * get Customer Group
     * @param customer group
    **/

    public function customers_groups( $id = null )
    {
        if( $id != null ) {
            $this->db->where( 'ID', $id );
        }
        
        return $this->db->get( store_prefix() . 'nexo_clients_groups' )
        ->result_array();
    }

    /**
    * Get Money Format
    *
    * @param int/string
    * @return string
    **/

    public function money_format($number, $fractional = false)
    {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }

        return $number;
    }

    /**
    * Get Money Full format with currency
    * @param int/string
    * @return string
    **/

    public function cmoney_format($number, $fractional = true)
    {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }

        return $this->display_currency('before') . ' ' . $number . ' ' . $this->display_currency('after');
    }

    /**
    * Get customer
    *
    * @param int/void customer id
    * @return customer
    **/

    public function get_customers($id = null)
    {
        if ($id != null) {
            $this->db->where('ID', $id) ;
        }
        $query        =    $this->db->get( store_prefix() .  'nexo_clients');
        return $query->result_array();
    }

    /**
    * Create category hierarchy
    * and create cache
    *
    * @param array
    * @param int
    * @param bool
    * @return array
    **/

    public function build_category_hierarchy(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element[ 'PARENT_REF_ID' ] == $parentId) {
                $children = $this->build_category_hierarchy($elements, $element[ 'ID' ]);
                if ($children) {
                    $element[ 'CHILDRENS' ] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
    * Get Maximum Array depthness
    *
    * @source : http://stackoverflow.com/questions/262891/is-there-a-way-to-find-out-how-deep-a-php-array-is
    * @param Array
    * @return int
    **/

    public function array_depth(array $array, $current_depth = 1)
    {
        global $depth;

        if ($array) {
            foreach ($array as $value) {
                if (is_array(@$value[ 'CHILDRENS' ])) {
                    $this->array_depth($value[ 'CHILDRENS' ], $current_depth + 1);
                } else {
                    if (intval($depth) < $current_depth) {
                        $depth    =    $current_depth;
                    }
                }
            }
        }

        return intval($depth);
    }

    /**
    * Build table
    * @param Array
    * @param int max depth
    * @param int current depth
    * @param string content
    * @param int colspan
    * @return string
    **/

    public function build_table(array $array, $max_depth = 5, $current_depth = 1, $content    =    '', $colspan = 0 )
    {
        global $depth_html;

        if ($array) {
            foreach ($array as $key => $value) {
                if (is_array(@$value[ 'CHILDRENS' ])) {
                    $line_output    =    $content . '<td data-id="' . $value[ 'ID' ] . '">' . $value[ 'NOM' ] . '</td>';
                    $this->build_table($value[ 'CHILDRENS' ], $max_depth, $current_depth + 1, $line_output, $colspan);
                } else {
                    // $depth_html 	.=	$content . '<td>' . $value[ 'NOM' ] . '</td></tr>';

                    if ($current_depth == $max_depth) {
                        $depth_html    .=    $content . '<td data-id="' . $value[ 'ID' ] . '">' . $value[ 'NOM' ] . '</td>';

                        for ($_i = 0; $_i < $colspan; $_i++) {
                            if ($_i == $colspan - 1) {
                                $depth_html .= '<td col-total class="text-right success"></td>';
                            } else {
                                $depth_html .= '<td month-id="' . ($_i + 1) . '" class="text-right"></td>';
                            }
                        }

                        $depth_html        .=    '</tr>';
                    } else {
                        for ($i = $current_depth ; $i <= $max_depth ; $i++) {
                            if ($i == $current_depth) {
                                @$depth_html    .= $content . '<td data-id="' . $value[ 'ID' ] . '">' . $value[ 'NOM' ] . '</td>';
                            } else {
                                $depth_html    .= '<td></td>';
                            }
                        }

                        for ($_i = 0; $_i < $colspan; $_i++) {
                            if ($_i == $colspan - 1) {
                                $depth_html .= '<td col-total class="text-right success"></td>';
                            } else {
                                $depth_html .= '<td month-id="' . ($_i + 1) . '" class="text-right"></td>';
                            }
                        }

                        $depth_html    .=    '</tr>';
                    }
                }
            }
            if ($current_depth == $max_depth) {
                $depth_html    .=    '';
            }
        }

        return $depth_html;
    }

    /**
    * History
    *
    * @param string title
    * @param string description
    * @return void
    **/

    public function history_add($title, $description)
    {
        $this->db->insert('nexo_historique', array(
            'TITRE'            =>    $title,
            'DETAILS'        =>    $description,
            'DATE_CREATION'    =>    date_now()
        ));
    }

    /**
    * Delete History
    *
    * @param int history id
    * @return void
    **/

    public function history_delete($id = null)
    {
        if ($id != null) {
            $this->db->where('ID', $id);
            $this->db->delete('nexo_historique');
        } else {
            $this->db->truncate('nexo_historique');
        }
    }

    /**
    * History Get
    *
    * @param int limit
    * @param int offset
    * @return Array
    **/

    public function history_get($limit = null, $offset = null)
    {
        $this->db->order_by('DATE_CREATION', 'DESC');

        if ($limit != null && $offset != null) {
            $this->db->limit($offset, $limit);
        }

        $query        =    $this->db->get('nexo_historique');
        return $query->result_array();
    }

    /**
    * Do Restore
    *
    * @param Array Upload Data
    * @return bool
    **/

    public function do_restore($data)
    {
        $this->load->library('Unzip');
        $this->load->library('SimpleFileManager', array(), 'SimpleFileManager');

        $file_path            =    $data[ 'full_path' ];
        $temp_folder        =    PUBLICPATH . 'upload/nexo_premium_backups/temp/' . $data[ 'raw_name' ];

        if (is_file($file_path)) {
            // Extract
            $this->unzip->extract($file_path, $temp_folder);
            $this->unzip->close();
            // Delete file
            @unlink($file_path);
            // Dir
            $CleanSQLQueries    =    array();
            foreach (glob($temp_folder . '/*.sql') as $filename) {
                $file        =    file($filename);
                $newLines    =    array();

                foreach ($file as $line) {
                    if (preg_match("/^(\#)/", $line) === 0) {
                        $newLines[] = rtrim(str_replace(array( '\r', '\n' ), '', $line));
                    }
                }

                $SQL_Joinded        =    implode($newLines);
                $SingleQueries        =    explode(';', $SQL_Joinded);

                foreach ($SingleQueries as $SingleQuery) {
                    $SingleQuery    =    trim($SingleQuery);
                    if (! empty($SingleQuery)) {
                        $CleanSQLQueries[]    =    $SingleQuery;
                    }
                }
            }

            $Cache            =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_' . store_prefix() ) );
            $Cache->save('restore_queries', $CleanSQLQueries, 300);

            $this->SimpleFileManager->drop($temp_folder);

            return count($CleanSQLQueries);
        }
        return false;
    }

    /**
    * Dates between two dates
    *
    * @param string first date
    * @param string second date
    * @return Array
    **/

    public function dates_between_borders($start, $end)
    {
        $carbon_start    =    carbon::parse($start);
        $carbon_end        =    carbon::parse($end);

        if ($carbon_end->gt($carbon_start)) {
            $dates        =    array();
            while ($carbon_start->toDateString() != $carbon_end->toDateString()) {
                $dates[]    =    $carbon_start->toDateString();
                $carbon_start->addDay();
            }

            // Since it's not included in the loop
            $dates[]        =    $carbon_end->toDateString();

            return $dates;
        }
        return false;
    }

    /**
    * Get initial balance for a specific date
    * @param string date
    * @return bool
    **/

    public function get_balance_for_date( $date )
    {
        $startOfDay			=	Carbon::parse( $date )->startOfDay();
        $endOfDay			=	Carbon::parse( $date )->endOfDay();
        $currentDay			=	Carbon::parse( date_now() );
        $cacheDurationTime	=	false;

        if( $currentDay->isSameDay( $endOfDay ) ) {
            $cacheDurationTime	=	24 - intval( $currentDay->hour );
        }

        $Cache				=	new CI_Cache( array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ) );

        if( ! $Cache->get( 'initial_balance' ) || $cacheDurationTime == false ) {


            $query	=	$this->db->where( 'DATE_CREATION >=', $startOfDay->toDateTimeString() )
            ->where( 'DATE_CREATION <=', $endOfDay->toDateTimeString() )
            ->get( 'nexo_checkout_money' );

            $result			=	$query->result_array();

            if( $cacheDurationTime != false ) {
                $Cache->save( 'initial_balance', ( bool ) $result, $cacheDurationTime * 3600 );
            } else {
                return ( bool ) $result;
            }
        }

        return $Cache->get( 'initial_balance' );
    }

    /**
    *
    * Get Order Payments
    *
    * @param string order code
    * @return array
    */

    public function order_payments( $order_code )
    {
        return $this->db->where( 'REF_COMMAND_CODE', $order_code )
        ->get( store_prefix() . 'nexo_commandes_paiements' )
        ->result_array();
    }
}
