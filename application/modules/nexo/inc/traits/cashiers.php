<?php
use Carbon\Carbon;

trait Nexo_cashiers
{
    /**
     * Get Cashier sales numbers
     * @param string start date
     * @param string end date
     * @return json
    **/
    
    public function cashier_sales_post()
    {
        $start_date        =    $this->post('start_of_day');
        $end_date        =    $this->post('end_of_day');
        $cashier_id        =    $this->post('cashier_id');
        $this->load->helper('nexopos');
        
        // Load Cache
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ));
        $this->load->config('nexo');
        
        if (! $Cache->get('profile_widget_cashier_sales_' . $cashier_id) || @$_GET[ 'refresh' ] == 'true') {
			
            $query        =    $this->db
                            //->where( 'DATE_CREATION >=', $start_date )
                            //->where( 'DATE_CREATION <=', $end_date )
                            ->where('AUTHOR', $cashier_id)
                            ->get( store_prefix() . 'nexo_commandes');
            
            $result        =    $query->result_array();
            $total        =    0;
            $month_income    =    0;
            $month_sales    =    0;
            
            if ($result) {
                foreach ($result as $_result) {
                    // Get only cash order
                    if (in_array($_result[ 'TYPE' ], array( 'nexo_order_comptant' ))) {
                        $total            +=    __floatval($_result[ 'TOTAL' ]);
                        
                        // This month
                        // It use the end date month as current month
                        $current_month_start        =    Carbon::parse($end_date)->startOfMonth();
                        $current_month_end            =    Carbon::parse($end_date)->endOfMonth();
                        if (Carbon::parse($_result[ 'DATE_CREATION' ])->between($current_month_start, $current_month_end)) {
                            $month_sales++;
                            $month_income            +=    __floatval($_result[ 'TOTAL' ]);
                        }
                    }
                }
            }
            
            $Cache->save('profile_widget_cashier_sales_' . $cashier_id, array(
                'sales_numbers'                =>    count($result),
                'sales_income'                =>    $total,
                'sales_income_this_month'    =>    $month_income,
                'sales_numbers_this_month'    =>    $month_sales
            ), $this->config->item('profile_widget_cashier_sales_lifetime'));
        }
        
        $this->response($Cache->get('profile_widget_cashier_sales_' . $cashier_id));
    }
}
