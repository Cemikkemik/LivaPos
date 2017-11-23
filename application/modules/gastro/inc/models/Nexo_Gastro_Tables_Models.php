<?php
class Nexo_Gastro_Tables_Models extends CI_Model
{
    /**
     * Return Table used by a specific order
     * @param numeric order id
     * @return order
     */
    public function get_table_used( $order_id )
    {
        $tables     =   $this->db->select( '*' )
        ->from( store_prefix() . 'nexo_commandes' )
        ->join( store_prefix() . 'nexo_restaurant_tables_relation_orders', store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_ORDER = ' . store_prefix() . 'nexo_commandes.ID' )
        ->join( store_prefix() . 'nexo_restaurant_tables', store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_TABLE = ' . store_prefix() . 'nexo_restaurant_tables.ID' )
        ->where( store_prefix() . 'nexo_commandes.ID', $order_id )
        ->get()->result_array();
        return $tables;
    }

    /**
     * Get Item modifier
     * @param number commande_order id
     * @return void
     */
    public function get_modifiers( $item_id, $command_code ) 
    {
        $modifiers      =   $this->db
        ->where( 'REF_COMMAND_CODE', $command_code )
        ->where( 'REF_COMMAND_PRODUCT', $item_id )
        ->where( 'KEY', 'modifiers' )
        ->get( store_prefix() . 'nexo_commandes_produits_meta' )
        ->result_array();
        return $modifiers;
    }
}