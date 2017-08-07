<?php
include_once(APPPATH . '/modules/nexo/vendor/autoload.php'); // Include from Nexo module dir

use Curl\Curl;
use Carbon\Carbon;

trait Nexo_rest_misc
{
    /**
     * Item Sales
     * Get sale for dashboard home widget
    **/

    public function widget_sale_new_post()
    {
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ));
        $this->load->config('nexo');

        if ( ( ! $Cache->get('widget_sale_new_best_items') && ! $Cache->get('widget_sale_new_items')) || @$_GET[ 'refresh' ] == 'true') {
            // Get Latest item
            $start_date        =    $this->post('start_date');
            $end_date        =    $this->post('end_date');
            $limit_result    =    $this->post('limit');

            $query            =    $this->db
            ->select('*')
            ->from( store_prefix() . 'nexo_commandes_produits')
            ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR' )
            ->join( store_prefix() . 'nexo_commandes', store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . store_prefix() . 'nexo_commandes.CODE')
            ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $start_date)
            ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $end_date)
            ->limit($limit_result)
            // ->group_by( 'nexo_articles.ID' )
            ->get();
            $Cache->save('widget_sale_new_best_items', $query->result(), $this->config->item('nexo_widget_cache_lifetime'));

            // Get Items
            $query    =    $this->db->get('nexo_articles');
            $Cache->save('widget_sale_new_items', $query->result(), $this->config->item('nexo_widget_cache_lifetime'));
        }

        $final_array            =    array(
            'best_items'        =>    $Cache->get('widget_sale_new_best_items'),
            'items'                =>    $Cache->get('widget_sale_new_items')
        );

        $this->response($final_array, 200);
    }

    /**
     * Widget Income
    **/

    public function widget_income_post()
    {
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ));
        $this->load->config('nexo');
        $this->load->model('Nexo_Misc');
        $this->load->helper('nexopos');

        if (! $Cache->get('widget_income') || @$_GET[ 'refresh' ] == 'true') {
            $start_date            =    $this->post('start');
            $end_date            =    $this->post('end');
            $dates                =    $this->Nexo_Misc->dates_between_borders($start_date, $end_date);
            $data                =    array();
            $curl                =    new Curl;

            if (! empty($dates)) {
                $this->load->config('rest');
                $curl->setHeader($this->config->item('rest_key_name'), $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]);

				$store_get			=	store_prefix() != '' ? '?store_id=' . $this->input->get( 'store_id' ) : '';

                foreach ($dates as $date) {
                    // get orders
                    $orders                =    $curl->post(
					site_url(
						array( 'rest', 'nexo', 'order_by_dates', 'nexo_order_comptant' . $store_get )),
						array(
							'start'            =>    Carbon::parse($date)->startOfDay()->toDateTimeString(),
							'end'            	=>    Carbon::parse($date)->endOfDay()->toDateTimeString()
						)
					);

                    if (! empty($orders)) {
                        $total        =    0;
                        foreach ( ( array ) $orders  as $order) {
                            $total    +=    __floatval($order->TOTAL);
                        }
                        $data[]        =    $total;
                    } else {
                        $data[]        =    0;
                    }
                }
            }

            $Cache->save('widget_income', $data, $this->config->item('nexo_widget_cache_lifetime'));
        }

        $this->response($Cache->get('widget_income'), 200);
    }

    /**
     * Widget Sales Statistics
    **/

    public function widget_sales_stats_post()
    {
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ) );

        if (! $Cache->get('widget_sales_stats') || @$_GET[ 'refresh' ] == 'true') {
            $this->load->config('nexo');
            $this->load->model('Nexo_Misc');

            $start_date        =    $this->post('start');
            $end_date        =    $this->post('end');
            $dates            =    $this->Nexo_Misc->dates_between_borders($start_date, $end_date);
            $data            =    array();

            $curl            =    new Curl;

            if (! empty($dates)) {
                $this->load->config('rest');
                $curl->setHeader($this->config->item('rest_key_name'), $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]);

                foreach (array_keys($this->config->item('nexo_order_types')) as $order_types) {
                    $data[ $order_types ]    =    array();

					$store_get			=	store_prefix() != '' ? '?store_id=' . $this->input->get( 'store_id' ) : '';

                    foreach ($dates as $date) {
                        $data[ $order_types ][ $date ]    =    array();

                        // get orders
                        $orders            =    $curl->post(site_url(array( 'rest', 'nexo', 'order_by_dates', $order_types . $store_get )), array(
                            'start'        =>    Carbon::parse($date)->startOfDay()->toDateTimeString(),
                            'end'        =>    Carbon::parse($date)->endOfDay()->toDateTimeString()
                        ));

                        $data[ $order_types ][ $date ]    =    count($orders);
                    }
                }
            }

            //Save on cache
            $Cache->save('widget_sales_stats', $data, $this->config->item('nexo_widget_cache_lifetime'));
        }

        $this->response($Cache->get('widget_sales_stats'), 200);
    }

    /**
     *  Post Report Item
     *  @return json
    **/

    public function stock_report_post()
    {
        $cache  =   new CI_Cache( array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_') );
        $cache->delete( store_prefix() . 'items_out_of_stock' );
        $cache->save( store_prefix() . 'items_out_of_stock', $this->post( 'reported_items' ), 86400 );
        $this->__success();
    }

}
