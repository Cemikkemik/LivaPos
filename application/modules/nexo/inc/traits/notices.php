<?php
trait Nexo_Notices
{
    /** 
     * @param int id
     * @param string filter
     * @return json
    **/

    public function notices_get( $id, $as = 'REF_USER' )
    {
        $this->db->where( $as, $id );

        if( $this->get( 'limit' ) ) {
            $this->db->limit( $this->get( 'limit' ) );
        }

        $this->db->order_by( 'DATE_CREATION', 'desc' );

        $query      =   $this->db->get( store_prefix() . 'nexo_notices' )->result();

        if( count( $query ) > 0 ) {
            return $this->response( $query, 200 );
        } else {
            return $this->__empty();
        }
    }

    /**
     * Delete Notice
     * @param int notice id
     * @return json
    **/

    public function notices_delete( $notice_id )
    {
        $this->cache            =   new CI_Cache( array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_notices_') );        
        $notice                 =   $this->db->where( 'ID', $notice_id )->get( store_prefix() . 'nexo_notices' )->result_array();
        $notice_namespace       =   substr( md5( $notice[0][ 'REF_USER' ] . $notice[0][ 'TYPE' ] . $notice[0][ 'MESSAGE' ] ), 0, 10 );
        
        // Delete notice to allow a new add
        $this->cache->delete( $notice_namespace );

        return $this->db->where( 'ID', $notice_id )
        ->delete( store_prefix() . 'nexo_notices' );

        $this->db->where( 'ID', $notice_id )->delete( store_prefix() . 'nexo_notices' );
        return $this->__success();
    }
}