<?php
class Nexo_Orders_Model extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($order_id = null)
    {
        $this->db->select( '*,' . 
            'aauth_users.name as AUTHOR_NAME'
        )
        ->from( store_prefix() . 'nexo_commandes' )
        ->join( 'aauth_users', 'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR' );

        if ($order_id != null && ! is_array($order_id)) {
            $this->db->where( store_prefix() . 'nexo_commandes.ID', $order_id);
        } elseif (is_array($order_id)) {
            foreach ($order_id as $mark => $value) {
                $this->db->where($mark, $value);
            }
        }

        $query    =    $this->db->get();

        if ($query->result_array()) {
            $result         =   $query->result_array();
            return $result[0];
        }
        return false;
    }
}