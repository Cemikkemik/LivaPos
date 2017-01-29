<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlanChuaMain extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        //Codeigniter : Write Less Do More*
        $this->events->add_filter( 'nexo_commandes_columns', function( $cols ) {
            $cols[0]    =   'ID';
            return $cols;
        });

        // filter columns
        $this->events->add_filter( 'nexo_commandes_loaded', function( $crud ) {
            $crud->callback_column( 'ID', function( $data ){
                return zero_fill( ( int )$data, 4 );
            });
            return $crud;
        });

        // add codebar columns
        $this->events->add_filter( 'np_profit_lost_report_thead_row', function( $data ) {
            $data       = array_insert_after( 'design', $data, 'barcode', array(
              'text'     =>    __( 'Barcode', 'ac' ),
            ));

            return $data;
        });

        // add codebar body
        $this->events->add_filter( 'np_profit_lost_report_tbody_row', function( $data ) {
            $data       = array_insert_after( 'design', $data, 'barcode', array(
              'text'     =>    '{{ item.CODEBAR }}',
              'csv_field'   =>  'item.CODEBAR'
            ) );

            return $data;
        });

        // add codebar body
        $this->events->add_filter( 'np_profit_lost_report_tfoot_row', function( $data ) {
            $data[ 'total' ][ 'colspan' ]   +=  1;
            return $data;
        });

        // Add filter Order Code
        $this->events->add_filter( 'detailed_sale_report_entry_code', function( $data ) {
            return '{{ entry.ID | padNumber:4 }}';
        });

        $this->events->add_action( 'dashboard_footer', function(){
            return get_instance()->load->module_view( 'alan_chua', 'dashboard_footer' );
        });
    }

}

new AlanChuaMain;
