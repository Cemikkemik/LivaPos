<?php
class Api extends Tendoo_Controller
{
     public function __construct()
     {
          parent::__construct();
     }

     public function __call( $method, $arguments ) 
     {

     }

     /**
      * Index Method to load module Rest routes
      * @return void
      * @since 3.6
     **/

     public function index( $slug, $index = null )
     { 
          $this->load->config( 'rest' );
          $rest          =    $this->db->where( 'key', @$_SERVER[ 'HTTP_' . $this->config->item( 'rest_header_key' ) ])
          ->get( 'restapi_keys' )
          ->result_array();

          if( ! $rest ) {
               http_response_code( 403 );
               echo json_encode([
                    'message'      =>   'Unable to authenticate rest keys',
                    'status'       =>   'forbidden'
               ]);
               return;
          }
                
          $routes       =    $this->events->apply_filters( 'rest_routes', []);

          if( in_array( $slug, array_keys( $routes ) ) ) {
               // get index
               $primary              =    @$routes[ $slug ][ 'primary' ] ? @$routes[ $slug ][ 'primary' ] : 'id';

               if ( $_SERVER['REQUEST_METHOD'] === 'GET') {

                    if( empty( $routes[ $slug ][ 'table' ] ) ) {
                         http_response_code( 403 );
                         echo json_encode([
                              'message'      =>   'the resource is not correctly defined. The table is missing.',
                              'status'       =>   'failed'
                         ]);
                         return;
                    }

                    // if a custom select is added
                    if( @$routes[ $slug ][ 'select' ] ) {
                         $this->db->select( implode( ', ', $routes[ $slug ][ 'select' ] ) );
                    }

                    if( @$routes[ $slug ][ 'join' ] ) {
                         foreach( $routes[ $slug ][ 'join' ] as $_joined_table => $statement ) {
                              $statement[0]  =    str_replace( ':primary', $primary, $statement[0] );
                              $statement[1]  =    str_replace( ':index', $index, $statement[1] );
                              $this->db->join( $_joined_table, $statement );
                         }
                    }

                    if( @$routes[ $slug ][ 'where' ] ) {
                         foreach( $routes[ $slug ][ 'where' ] as $statement ) {
                              $statement[0]  =    str_replace( ':primary', $primary, $statement[0] );
                              $statement[1]  =    str_replace( ':index', $index, $statement[1] );
                              call_user_func_array([ $this->db, 'where' ], $statement );
                         }
                    }

                    if( @$routes[ $slug ][ 'or_where' ] ) {
                         foreach( $routes[ $slug ][ 'or_where' ] as $statement ) {
                              $statement[0]  =    str_replace( ':primary', $primary, $statement[0] );
                              $statement[1]  =    str_replace( ':index', $index, $statement[1] );
                              call_user_func_array([ $this->db, 'or_where' ], $statement );
                         }
                    }

                    if( @$routes[ $slug ][ 'like' ] ) {
                         foreach( $routes[ $slug ][ 'like' ] as $statement ) {
                              $statement[0]  =    str_replace( ':primary', $primary, $statement[0] );
                              $statement[1]  =    str_replace( ':index', $index, $statement[1] );
                              call_user_func_array([ $this->db, 'like' ], $statement );
                         }
                    }

                    if( @$routes[ $slug ][ 'or_like' ] ) {
                         foreach( $routes[ $slug ][ 'or_like' ] as $statement ) {
                              $statement[0]  =    str_replace( ':primary', $primary, $statement[0] );
                              $statement[1]  =    str_replace( ':index', $index, $statement[1] );
                              call_user_func_array([ $this->db, 'or_like' ], $statement );
                         }
                    }

                    $hasSpecialQuery    =    false;
                    
                    foreach( [ 'or_link', 'like', 'where', 'or_where' ] as $query ) {
                         if( @$routes[ $slug ][ $query ] ) {
                              $hasSpecialQuery    =    true;
                         }
                    }

                    if( $index != null && ! $hasSpecialQuery ) {

                         $result   =    $this->db->where( $primary, $index )
                         ->get( $routes[ $slug ][ 'table' ] )->result();

                         echo json_encode( $result[0] );
                         return;
                    }

                    // listing all result
                    $data     =    $this->db->get( $routes[ $slug ][ 'table' ] )
                    ->result();
                    
                    // Record Message
                    echo json_encode( $data );
               } else if( $_SERVER['REQUEST_METHOD'] === 'POST' && @$routes[ $slug ][ 'fillable' ] ) {
                    $data          =    [];
                    foreach( $routes[ $slug ][ 'fillable' ] as $column ) {
                         $data[ $column ]    =    $this->input->post( $column );
                    }

                    $this->db->insert( $routes[ $slug ][ 'table' ], $data );

                    // success message
                    echo json_encode([
                         'message'      =>   'record has been saved',
                         'status'       =>   'success'
                    ]);
               } else if( $_SERVER['REQUEST_METHOD'] === 'PUT' && @$routes[ $slug ][ 'fillable' ] ) {
                    $data               =    [];

                    foreach( $routes[ $slug ][ 'fillable' ] as $column ) {
                         $data[ $column ]    =    $this->input->post( $column );
                    }

                    $this->db->where( $primary, $index )
                    ->update( $routes[ $slug ][ 'table' ], $data );
                    
                    // success message
                    echo json_encode([
                         'message'      =>   'record has been updated',
                         'status'       =>   'success'
                    ]);
               }  else if( $_SERVER['REQUEST_METHOD'] === 'DELETE' && @$routes[ $slug ][ 'fillable' ] ) {
                    $data               =    [];

                    $this->db->where( $primary, $index )
                    ->delete( $routes[ $slug ][ 'table' ] );
                    
                    // success message
                    echo json_encode([
                         'message'      =>   'record has been deleted',
                         'status'       =>   'success'
                    ]);
               }
          } else {
               http_response_code( 404 );
               echo json_encode([
                    'message'      =>   'Unable to find the resource.',
                    'status'       =>   'forbidden'
               ]);
               return;
          }
     }
}