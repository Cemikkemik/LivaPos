<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait nexo_restaurant_tables
{
    /**
     *  get Rooms
     *  @param string int
     *  @return json
    **/

    public function tables_get( $id = null )
    {
        if( $id != null ) {
            $this->db->where( 'ID', $id );
        }

        $this->response(
            $this->db->get( store_prefix() . 'nexo_restaurant_tables' )
            ->result(),
            200
        );
    }

    /**
     *  Get Area from Rooms
     *  @param int room id
     *  @return json
    **/

    public function tables_from_area_get( $areaID )
    {
        $this->db->select(
            store_prefix() . 'nexo_restaurant_tables.NAME as TABLE_NAME,' .
            store_prefix() . 'nexo_restaurant_tables.STATUS as STATUS,' .
            store_prefix() . 'nexo_restaurant_tables.MAX_SEATS as MAX_SEATS,' .
            store_prefix() . 'nexo_restaurant_tables.CURRENT_SEATS_USED as CURRENT_SEATS_USED,' .
            store_prefix() . 'nexo_restaurant_tables.ID as TABLE_ID,' .
            store_prefix() . 'nexo_restaurant_areas.ID as AREA_ID'
        )->from( store_prefix() . 'nexo_restaurant_tables' )
        ->join( store_prefix() . 'nexo_restaurant_areas', store_prefix() . 'nexo_restaurant_tables.REF_AREA = ' . store_prefix() . 'nexo_restaurant_areas.ID' )
        ->where( store_prefix() . 'nexo_restaurant_areas.ID', $areaID );

        $query  =   $this->db->get();

        $this->response( $query->result(), 200 );
    }

    /**
     *  Edit Table
     *  @param
     *  @return
    **/

    public function table_usage_put( $table_id )
    {
        $this->db->where( 'ID', $table_id )->update( store_prefix() . 'nexo_restaurant_tables', [
            'CURRENT_SEATS_USED'    =>  $this->put( 'CURRENT_SEATS_USED' ),
            'STATUS'                =>  $this->put( 'STATUS' )
        ]);
    }

}
