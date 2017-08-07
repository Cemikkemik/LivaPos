<?php
class Nexo_Notices_Model extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->cache    =   new CI_Cache( array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_notices_') );        
    }

    /**
     * Push Notice
     * @param int user id
     * @param string type (warning, success, info)
     * @param string message
     * @return void
    **/

    public function add( $user_id, $message = '', $link = '#', $type = 'text-warning', $icon = 'fa fa-warning', $duration = 3600 )
    {
        if( is_array( $user_id ) ) {
            extract( $user_id );
            return $this->add( $user_id, $message, $link, $type, $icon, $duration );
        }
        // if a namespace of that notice doesn't yet exist, then we can add a notice for that
        $notice_namespace       =   substr( md5( $user_id . $type . $message ), 0, 10 );
        if( ! $this->cache->get( $notice_namespace ) ) {
            $this->cache->save( $notice_namespace, $message, $duration );
            $this->db->insert( store_prefix() . 'nexo_notices', [
                'TYPE'              =>  $type,
                'MESSAGE'           =>  $message,
                'REF_USER'          =>  $user_id,
                'DATE_CREATION'     =>  date_now(),
                'ICON'              =>  $icon,
                'LINK'              =>  $link
            ]);
        }        
    }

    /**
     * Get Notices for a specific user
     * @param int user id
     * @return array
    **/

    public function getAll( $user_id, $limit = 10 )
    {
        return $this->db->where( 'REF_USER', $user_id )
        ->limit( $limit )
        ->get( store_prefix() . 'nexo_notices' )        
        ->result();
    }

    /**
     * Delete notice
     * @param int notice id
     * @return void
    **/

    public function delete( $notice_id )
    {
        $notice                 =   $this->get( $notice_id );
        $notice_namespace       =   substr( md5( $notice[0][ 'REF_USER' ] . $notice[0][ 'TYPE' ] . $notice[0][ 'MESSAGE' ] ), 0, 10 );
        
        // Delete notice to allow a new add
        $this->cache->delete( $notice_namespace );

        return $this->db->where( 'ID', $notice_id )
        ->delete( store_prefix() . 'nexo_notices' );
    }

    /**
     * Get Notice
     * @param int notice id
     * @return void
    **/

    public function get( $notice_id )
    {
        return $this->db->where( 'ID', $notice_id )
        ->get( store_prefix() . 'nexo_notices' )        
        ->result();
    }
}