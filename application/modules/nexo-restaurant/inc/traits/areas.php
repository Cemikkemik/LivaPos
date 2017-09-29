<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait nexo_restaurant_areas
{
    /**
     *  get Rooms
     *  @param string int
     *  @return json
    **/

    public function areas_get( $id = null )
    {
        if( $id != null ) {
            $this->db->where( 'ID', $id );
        }

        $this->response(
            $this->db->get( store_prefix() . 'nexo_restaurant_areas' )
            ->result(),
            200
        );
    }

    /**
     *  Get Area from Rooms
     *  @param int room id
     *  @return json
    **/

    public function areas_from_room_get( $roomID )
    {
        $this->db->select(
            store_prefix() . 'nexo_restaurant_areas.NAME as AREA_NAME,' .
            store_prefix() . 'nexo_restaurant_areas.ID as AREA_ID,' .
            store_prefix() . 'nexo_restaurant_rooms.ID as ROOM_ID'
        )->from( store_prefix() . 'nexo_restaurant_areas' )
        ->join( store_prefix() . 'nexo_restaurant_rooms', store_prefix() . 'nexo_restaurant_areas.REF_ROOM = ' . store_prefix() . 'nexo_restaurant_rooms.ID' )
        ->where( store_prefix() . 'nexo_restaurant_rooms.ID', $roomID );

        $query  =   $this->db->get();

        $this->response( $query->result(), 200 );
    }
}
