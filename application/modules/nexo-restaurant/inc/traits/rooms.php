<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait nexo_restaurant_rooms
{
    /**
     *  get Rooms
     *  @param string int
     *  @return json
    **/

    public function rooms_get( $id = null )
    {
        if( $id != null ) {
            $this->db->where( 'ID', $id );
        }

        $this->response(
            $this->db->get( store_prefix() . 'nexo_restaurant_rooms' )
            ->result(),
            200
        );
    }
}
