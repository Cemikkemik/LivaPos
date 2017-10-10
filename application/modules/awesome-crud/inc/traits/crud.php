<?php
trait Awesome_Crud_Trait
{
    public function select_get( $table, $id = null )
    {
        // primary key
        $primaryKey        =   null;

        $this->db->select( '*' );

        // Allow to filter the request        
        $this->events->apply_filters( 'ac_filter_get_request', [ 
            'object'        =>  $this, 
            'table'         =>  $table,
            'id'            =>  $id, 
            'primaryKey'    =>  $primaryKey
        ]);

        $this->db->from( $table );

        if( $id != null && $primary_key != null ) {
            $this->db->where( $primary_key, $id );
        }

        $query      =   $this->db->get();

        if( count( $query->result() ) == 1 && $id != null ) {
            $result     =   $query->result();
            return $this->response( $result[0], 200 );
        }
        return $this->response( $query->result(), 200 );
    }

    public function select_delete( $table, $id ) 
    {
        $primaryKey         =   null;
        // allow delete for this entry
        $request    =   $this->events->apply_filters( 'ac_delete_entry', [
            'object'        =>  $this, 
            'table'         =>  $table,
            'id'            =>  $id, 
            'primaryKey'    =>  $primaryKey,
            'proceed'       =>  true
        ]);

        // if the entry can be delete
        if( $request[ 'proceed' ] ) {
            $this->db->where( $request[ 'primaryKey' ], $id )->delete( $table );
            return $this->__success();
        }
        return $this->__failed();
    }
}