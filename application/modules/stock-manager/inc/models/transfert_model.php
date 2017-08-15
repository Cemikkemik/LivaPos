<?php
class transfert_model extends Tendoo_Module
{
    /**
     * Stock Transfert
     * @param int stock id
     * @return array transfert array
    **/

    public function get( $id = null )
    {
        if( $id != null ) {
            $this->db->where( 'ID', $id );
        } 
        $stock  =   $this->db->get( 'nexo_stock_transfert' )->result_array();
        return $stock;
    }

    /** 
     * Get Transfert Items
     * @param int transfert id
     * @return array
    **/

    public function get_with_items( $transfert_id ) 
    {
        return $this->db->where( 'REF_TRANSFER', $transfert_id )
        ->get( 'nexo_stock_transfert_items' )
        ->result_array();
    }

    /**
     * Update Transfert Status
     * @param int transfert id
     * @param int status 0: pending, 1: approuved, 2: rejected
     * @return void
    **/

    public function status( $transfert_id, $status ) 
    {
        $this->db->where( 'ID', $transfert_id )->update( 'nexo_stock_transfert', [
            'APPROUVED'     =>  $status,
            'APPROUVED_BY'  =>  User::id()
        ]);
    }
}