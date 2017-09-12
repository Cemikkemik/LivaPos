<?php
trait Nexo_customers
{
	/**
     * Customers
    **/

    public function customer_get($id = null, $filter = 'ID')
    {
        if ($id != null) {
            $result        =    $this->db->where($filter, $id)->get( store_prefix() . 'nexo_clients')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 500);
        } else {
            $this->response($this->db->get( store_prefix() . 'nexo_clients')->result());
        }
    }

    /**
     * Customer Insert
     *
     * @param POST string name
     * @param POST string email
     * @param POST string tel
     * @param POST string prenom
    **/

    public function customer_post()
    {
        $request    =    $this->db
        ->set('NOM',    $this->post('nom'))
        ->set('EMAIL',    $this->post('email'))
        ->set('TEL',    $this->post('tel'))
        ->set('PRENOM',    $this->post('prenom'))
        ->set('REF_GROUP', $this->post('ref_group'))
        ->set('AUTHOR', $this->post('author'))
        ->set('DATE_CREATION', $this->post('date_creation'))
        ->insert( store_prefix() . 'nexo_clients');

        if ($request) {
            $this->response(array(
                'status'        =>        'success'
            ), 200);
        } else {
            $this->response(array(
                'status'        =>        'error'
            ), 404);
        }
    }
	
	/**
     * Customer Groups
     * @param int/string group par
     * @return json
    **/

    public function customers_groups_get($id = null, $filter = 'id')
    {		
        if ($id != null) {
            $this->db->where('ID', $id);
        }

        $query    =    $this->db->get( store_prefix() . 'nexo_clients_groups');
        $this->response($query->result(), 200);
    }

    /**
     * Customer Groups Post
     * @param String name
     * @param String Description
     * @param Int author
     * @return void
    **/

    public function customers_groups_post()
    {
		$data		=	array(
            'NAME'            =>    $this->post('name'),
            'DESCRIPTION'    =>    $this->post('descirption'),
            'DATE_CREATION'    =>    date_now(),
            'AUTHOR'        =>    $this->post('user_id')
        );
		
        $this->db->insert( store_prefix() . 'nexo_clients_groups', $data );

        $this->__success();
    }

    /**
     * Customer Groupe delete
     * @param Int group id
     * @return json
     *
    **/

    public function customers_groups_delete($id)
    {
        if ($this->db->where('ID', $id)->delete( store_prefix() . 'nexo_clients_groups')) {
            $this->__failed();
        } else {
            $this->__success();
        }
    }

    /**
     * Customer edit
     * @param Int group id
     * @return json
    **/

    public function customers_groups_update($group_id)
    {
		$data 		=	array(
            'NAME'                =>    $this->put('name'),
            'DESCRIPTION'        =>    $this->put('description'),
            'AUTHOR'            =>    $this->put('user_id'),
            'DATE_MODIFICATION'    =>    date_now()
        );
		
        if ($this->where('ID', $group_id)->update('nexo_clients_groups', $data )) {
            $this->__success();
        } else {
            $this->__failed();
        }
    }

    /**
     * Customers POST
     * @since 3.1
     * @return json
    **/

    public function customers_post()
    {
        $emailUsed  =   false;
        // we must avoid same user with same email
        if( $this->post( 'email' ) != '' ) {
            $query  =   $this->db->where( 'EMAIL', $this->post( 'email' ) )->get( store_prefix() . 'nexo_clients' )
            ->result();

            if( $query ) {
                $emailUsed  =   true;
            }
        }

        if( $emailUsed ) {
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  'email_used'
            ], 403 );
        }

        $this->db->insert( store_prefix() . 'nexo_clients', [
            'NOM'               =>  $this->post( 'name' ),
            'PRENOM'            =>  $this->post( 'surname' ),
            'COUNTRY'           =>  $this->post( 'country' ),
            'CITY'              =>  $this->post( 'city' ),
            'STATE'             =>  $this->post( 'state' ),
            'DESCRIPTION'       =>  $this->post( 'description' ),
            'DATE_NAISSANCE'    =>  $this->post( 'birth_date' ),
            'EMAIL'             =>  $this->post( 'email' ),
            'DATE_CREATION'     =>  date_now(),
            'DATE_MOD'          =>  date_now(),
            'AUTHOR'            =>  $this->post( 'author' ),
            'TEL'               =>  $this->post( 'phone' ),
            'REF_GROUP'         =>  $this->post( 'ref_group' )
        ]);

        $insert_id      =   $this->db->insert_id();

        $meta                   =   [];
        foreach( $this->post() as $key => $value ) {
            if( substr( $key, 0, 8 ) == 'shipping' || substr( $key, 0, 7 ) == 'billing' ) {

                if( substr( $key, 0, 8 ) == 'shipping' ) {
                    if( @$meta[ 'shipping' ] == null ) {
                        $meta[ 'shipping' ]     =   [];
                    }

                    $meta[ 'shipping' ][ substr( $key, 9 ) ]     =   $value;
                } else {
                    if( @$meta[ 'billing' ] == null ) {
                        $meta[ 'billing' ]     =   [];
                    }

                    $meta[ 'billing' ][ substr( $key, 8 ) ]     =   $value;
                }
            }
        }

        $meta[ 'billing' ][ 'ref_client' ]          =   $insert_id;
        $meta[ 'billing' ][ 'type' ]                =   'billing';
        $meta[ 'shipping' ][ 'ref_client' ]         =   $insert_id;
        $meta[ 'shipping' ][ 'type' ]               =   'shipping';

        $this->db->insert( store_prefix() . 'nexo_clients_address', $meta[ 'shipping' ] );
        $this->db->insert( store_prefix() . 'nexo_clients_address', $meta[ 'billing' ] );  

        return $this->__success();  
    }

    /**
     * Customers PUT
     * @since 3.1
     * @return json
    **/

    public function customers_put( $client_id = null )
    {
        if( $client_id == null ) {
            return $this->__failed();
        }

        $emailUsed  =   false;
        // we must avoid same user with same email
        if( $this->put( 'email' ) != '' ) {
            $query  =   $this->db->where( 'EMAIL', $this->put( 'email' ) )->get( store_prefix() . 'nexo_clients' )
            ->result();

            if( $query ) {
                if( intval( $query[0]->ID ) != intval( $client_id ) && ! empty( $query[0]->ID ) ) {
                    $emailUsed  =   true; 
                }
            }
        }

        if( $emailUsed ) {
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  'email_used'
            ], 403 );
        }

        $this->db->where( 'ID', $client_id )->update( store_prefix() . 'nexo_clients', [
            'NOM'               =>  $this->put( 'name' ),
            'PRENOM'            =>  $this->put( 'surname' ),
            'COUNTRY'           =>  $this->put( 'country' ),
            'CITY'              =>  $this->put( 'city' ),
            'STATE'             =>  $this->put( 'state' ),
            'DESCRIPTION'       =>  $this->put( 'description' ),
            'DATE_NAISSANCE'    =>  $this->put( 'birth_date' ),
            'EMAIL'             =>  $this->put( 'email' ),
            'DATE_MOD'          =>  date_now(),
            'AUTHOR'            =>  $this->put( 'author' ),
            'TEL'               =>  $this->put( 'phone' ),
            'REF_GROUP'         =>  $this->put( 'ref_group' )
        ]);

        $insert_id      =   $this->db->insert_id();

        $meta                   =   [];
        foreach( $this->put() as $key => $value ) {
            if( substr( $key, 0, 8 ) == 'shipping' || substr( $key, 0, 7 ) == 'billing' ) {

                if( substr( $key, 0, 8 ) == 'shipping' ) {
                    if( @$meta[ 'shipping' ] == null ) {
                        $meta[ 'shipping' ]     =   [];
                    }

                    $meta[ 'shipping' ][ substr( $key, 9 ) ]     =   $value;
                } else {
                    if( @$meta[ 'billing' ] == null ) {
                        $meta[ 'billing' ]     =   [];
                    }

                    $meta[ 'billing' ][ substr( $key, 8 ) ]     =   $value;
                }
            }
        }

        $meta[ 'billing' ][ 'ref_client' ]          =   $client_id;
        $meta[ 'billing' ][ 'type' ]                =   'billing';
        $meta[ 'shipping' ][ 'ref_client' ]         =   $client_id;
        $meta[ 'shipping' ][ 'type' ]               =   'shipping';

        // for editing, just make sure those information exist for that customer otherwise, we'll insert
        $address        =   $this->db->where( 'ref_client', $client_id )
        ->get( store_prefix() . 'nexo_clients_address' )
        ->result();

        if( $address ) {
            $this->db->where( 'ref_client', $client_id )
            ->where( 'type', 'billing' )
            ->update( 
                store_prefix() . 'nexo_clients_address', 
                $meta[ 'billing' ] 
            );

            $this->db->where( 'ref_client', $client_id )
            ->where( 'type', 'shipping' )->update( 
                store_prefix() . 'nexo_clients_address', 
                $meta[ 'shipping' ] 
            );
        } else { // this will pretty useful for those who'll make an update
            $this->db->insert( store_prefix() . 'nexo_clients_address', $meta[ 'shipping' ] );
            $this->db->insert( store_prefix() . 'nexo_clients_address', $meta[ 'billing' ] );
        }

        return $this->__success();  
    }

    /**
     * Customer Get
     * @param int customer id
     * @return json
    **/

    public function customers_get( $client_id = null ) 
    {
        if( $client_id != null ) {
            $this->db->where( store_prefix() . 'nexo_clients.ID', $client_id );
        }

        $customers      =   $this->db->select( '*' )->from( store_prefix() . 'nexo_clients' )
        ->get()
        ->result_array();

        if( $customers ) {
            foreach( $customers as &$customer ) {

                $addresses      =   $this->db->where( 'ref_client', $customer[ 'ID' ] )
                ->get( store_prefix() . 'nexo_clients_address' )
                ->result_array();

                if( $addresses ) {
                    foreach( $addresses as $address ) {
                        foreach( $address as $key => $value ) {
                            if( $key != 'type' ) {
                                $customer[ $address[ 'type' ] . '_' . $key ]        =   $value;
                            }
                        }
                    }
                }
            }
        }

        return $this->response( $customers, 200 );
    }
}