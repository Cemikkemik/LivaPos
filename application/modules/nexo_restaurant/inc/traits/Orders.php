<?php
use Carbon\Carbon;

trait Nexo_Restaurant_Orders
{
	/**
     * Get order using dates
     *
     * @params string datetime
     * @params string datetime
     * @return json
    **/
    
    public function order_by_dates_post($order_type = 'all')
    {
		$this->db->select( '*,
			' . store_prefix() . 'nexo_commandes.ID as ID' )
		->from( store_prefix() . 'nexo_commandes' )
		->join( store_prefix() . 'nexo_clients', store_prefix() . 'nexo_clients.ID = ' . store_prefix() . 'nexo_commandes.REF_CLIENT' );
		
        $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $this->post('start'));
        $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $this->post('end'));
        
        if ($order_type != 'all') {
            $this->db->where('TYPE', $order_type);
        }
        
        $query    =    $this->db->get();
        $this->response($query->result(), 200);
    }
}