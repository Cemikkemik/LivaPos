<?php
class Nexo_customers extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Customer
     * @param int customer id (optional)
     * @return array
    **/

    public function get_customers( $id = null )
    {
        $this->db->select( 
            store_prefix() . 'nexo_clients.ID as id, ' . 
            store_prefix() . 'nexo_clients.NOM as name, ' . 
            store_prefix() . 'nexo_clients.PRENOM as surname,' .
            store_prefix() . 'nexo_clients.EMAIL as email,' . 
            store_prefix() . 'nexo_clients.DATE_NAISSANCE as birth_date,' . 
            store_prefix() . 'nexo_clients.OVERALL_COMMANDES as overall_commandes,' .
            store_prefix() . 'nexo_clients.NBR_COMMANDES as total_orders,' . 
            store_prefix() . 'nexo_clients.TOTAL_SPEND as total_spend,' .                        
            store_prefix() . 'nexo_clients.LAST_ORDER as last_order,' .
            store_prefix() . 'nexo_clients.AVATAR as avatar,' .
            store_prefix() . 'nexo_clients.COUNTRY as country,' .
            store_prefix() . 'nexo_clients.STATE as state,' . 
            store_prefix() . 'nexo_clients.DATE_CREATION as created_on,' . 
            store_prefix() . 'nexo_clients.DATE_MOD as edited_on,' . 
            store_prefix() . 'nexo_clients.REF_GROUP as ref_group,' . 
            store_prefix() . 'nexo_clients.AUTHOR as author,' .     
            store_prefix() . 'nexo_clients.DISCOUNT_ACTIVE as discount_active,' .     
            store_prefix() . 'nexo_clients.DESCRIPTION as description,' .        
            store_prefix() . 'nexo_clients.TEL as phone'     
        );

        if( $id != null ) {
            $this->db->where( store_prefix() . 'nexo_clients.ID', $id );
        }

        $clients  =   $this->db->get( store_prefix() . 'nexo_clients' )
        ->result_array();

        foreach( $clients as &$client ) {
            $clients_addresses      =   $this->db->where( store_prefix() . 'nexo_clients_address.ref_client', $client[ 'id' ] )
            ->get( store_prefix() . 'nexo_clients_address' )
            ->result_array();

            if( $clients_addresses ) {
                foreach( $clients_addresses as $client_address ) {
                    foreach( $client_address as $key => $value ) {
                        if( $key != 'type') {
                            $client[ $client_address[ 'type' ] . '_' . $key ]   =   $value;
                        }
                    }
                }
            }
        }   

        return $clients;
    }
}