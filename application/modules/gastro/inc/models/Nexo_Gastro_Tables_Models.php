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
        $tables     =   $this->db->select( '*,
        ' . store_prefix() . 'nexo_restaurant_tables.ID as TABLE_ID,
        ' . store_prefix() . 'nexo_restaurant_tables.ID as ID,
        ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID' )
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
        return @$modifiers[0][ 'VALUE' ] ? $modifiers[0][ 'VALUE' ] : '[]';
    }

    /**
     * Get Table area
     * @param int table id
     * @return array
     */
    public function get_table_area( $table_id ) 
    {
        $table      =   $this->get_table( $table_id );
        $this->db->select( '*, ' . store_prefix() . 'nexo_restaurant_areas.ID as AREA_ID' );
        $area       =   $this->db->where( 'ID', $table[ 'REF_AREA' ] )
        ->get( store_prefix() . 'nexo_restaurant_areas' )
        ->result_array();

        return @$area[0] ? $area[0] : [];
    }

    /**
     * Get Table
     * @param int table_id
     * @return array
     */
    public function get_table( $table_id )
    {
        $table      =   $this->db->where( 'ID', $table_id )
        ->get( store_prefix() . 'nexo_restaurant_tables' )
        ->result_array();
        return @$table[0] ? $table[0] : [];
    }

    /**
     * Change Table Statuts
     */
    public function table_status( $options ) 
    {
        $order      =   $this->db->where( 'ID', $options[ 'ORDER_ID' ] )
        ->get( store_prefix() . 'nexo_commandes' )->result_array();

        $table_id       =   $options[ 'TABLE_ID' ];

        // current table
        $table      =   $this->db->where( 'ID', $table_id )
        ->get( store_prefix() . 'nexo_restaurant_tables' )->result_array();

        if( $table ) {
            $data       =   [
                'CURRENT_SEATS_USED'    =>  $options[ 'CURRENT_SEATS_USED' ],
                'STATUS'                =>  $options[ 'STATUS' ]
            ];
    
            if( $data[ 'STATUS' ] == 'in_use' ) {
                // if the current order status is in_use, we assume the table has been opened before
                if( $table[0][ 'STATUS' ]   == 'in_use' ) {
                    $current_session_id     =   $table[0][ 'CURRENT_SESSION_ID' ];
                } else {
                    // we're placing order for the first time
                    // create session
                    $this->db->insert( store_prefix() . 'nexo_restaurant_tables_sessions', [
                        'REF_TABLE'         =>  $table_id,
                        'SESSION_STARTS'    =>  date_now(),
                        'AUTHOR'            =>  User::id(),
                    ]);

                    // save last session id
                    $data[ 'CURRENT_SESSION_ID' ]       =   $this->db->insert_id();
                    // the table is placed for the first time
                    $data[ 'SINCE' ]        =   @$order[0][ 'DATE_MOD' ];
                    $current_session_id     =   $data[ 'CURRENT_SESSION_ID' ];
                }   
                
                // add table relation to order
                $this->db->insert( store_prefix() . 'nexo_restaurant_tables_relation_orders', [
                    'REF_ORDER'     =>  $options[ 'ORDER_ID' ],
                    'REF_TABLE'     =>  $table_id,
                    'REF_SESSION'   =>  $current_session_id
                ]);
            } else {
                // close table session
                $this->db->where( 'ID', $table[0][ 'CURRENT_SESSION_ID' ])
                ->update( store_prefix() . 'nexo_restaurant_tables_sessions', [
                    'SESSION_ENDS'      =>  date_now(),
                    'AUTHOR'            =>  User::id(),
                ]);

                // remove last session id
                $data[ 'CURRENT_SESSION_ID' ]       =   0; // reset last session id
                $data[ 'SINCE' ]                    =   '0000-00-00 00:00:00';
            }        
                
            $this->db->where( 'ID', $table_id )->update( store_prefix() . 'nexo_restaurant_tables', $data );
            // return new status
            $table      =   $this->db->where( 'ID', $table_id )->get( store_prefix() . 'nexo_restaurant_tables' )
            ->result_array();

            return $table[0];
        }
        return [];
    }

    /**
     * Remove Order from table history
     * @param int order id
     * @return Active Record Object
     */
    public function remove_from_history( $order_id, $table_id ) 
    {
        return $this->db->where( 'REF_TABLE', $table_id )
        ->where( 'REF_ORDER', $order_id )
        ->delete( store_prefix() . 'nexo_restaurant_tables_relation_orders' );
    }

    /**
     * Unbind order from any history
     * @param int order id
     * @return Active Record Object
     */
    public function unbind_order( $order_id, $table_id ) 
    {
        return $this->db
        ->where( 'REF_ORDER', $order_id )
        ->delete( store_prefix() . 'nexo_restaurant_tables_relation_orders' );
    }
}