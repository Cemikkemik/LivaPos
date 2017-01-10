<?php
defined('BASEPATH') OR exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php'); // Include Rest Controller

use Carbon\Carbon;

class Angular extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('nexopos');
        $this->load->library('session');
        $this->load->model('Options');
        $this->load->database();
        $this->systemColumn     =   [ '$AnguCrudActions' ];

        if (! $this->oauthlibrary->checkScope( 'core' )) {
            $this->__forbidden();
        }
    }

    /**
     *  Table Get Entries
     *  @param
     *  @return
    **/

    public function table_get( $table_name, $id = null )
    {
        $order          =   $this->get( 'order' );
        $order_type     =   substr( $order, 0, 1 ) == '-' ? 'ASC' : 'DESC';
        $order_column   =   ( $order_type == 'ASC' ) ? substr( $order, 1 ) : $order;
        $search         =   $this->get( 'filter' );
        $relations      =   json_decode( $this->get( 'relations' ), true );
        $reservedCols   =   [ '$AnguCrudActions' ];

        if( $id != null ) {
            return $this->response([
                'entries'     =>   $this->db->where( $this->get( '__primaryCol' ), $id )
                ->get( $table_name )->result()
            ]);
        }

        $selectSql      =   '';
        $aliases       =   [];

        if( $relations ) {
            foreach( ( Array ) $relations as $field   => $relation ) {
                $selectSql  .=  $relation[ 'table' ] . '.`' . $relation[ 'col' ] . '` as ' . $relation[ 'alias' ] . ',';
                $aliases[]     =   $relation[ 'alias' ];
            }
        }

        $selectSql      =   substr( $selectSql, 0, strlen( $selectSql ) - 1); // to remove the final coma (',' );

        // Selecting table field prefixed by the table name
        if( $this->get( 'columns' ) ) {

            $selectSql  .=  ',';

            foreach( $this->get( 'columns' ) as $col ) {
                if( ! in_array( $col, $aliases ) && ! in_array( $col, $reservedCols ) ) {
                    $selectSql      .=  $table_name . '.`' . $col . '`,';
                }
            }

            $selectSql      =   substr( $selectSql, 0, strlen( $selectSql ) - 1); // to remove the final coma (',' );
        }

        $this->db->select( $selectSql )
        ->from( $table_name );

        if( $relations ) {
            foreach( ( Array ) $relations as $field   => $relation ) {
                $this->db->join(
                    $relation[ 'table' ],
                    '`' . $this->db->dbprefix . $table_name . '`.`' . $field . '` = `' . $this->db->dbprefix . $relation[ 'table' ] . '`.`' . $relation[ 'comparison' ] . '`',
                    'left'
                );
            }
        }

        $this->db->limit(
            $this->get( 'limit' ),
            ( intval( $this->get( 'page' ) ) - 1 ) * intval( $this->get( 'limit' ) )
        );

        if( $this->get( 'columns' ) && $this->get( 'filter' ) ) {
            foreach( $this->get( 'columns' ) as $column ) {
                if( ! in_array( $column, $this->systemColumn ) ) {
                    $this->db->or_like( $column, $this->get( 'filter' ) );
                }
            }
        }

        $order_by   =   in_array( $order_column, $aliases ) ? $order_column : $table_name . '.`' . $order_column . '`';

        $entries            =   $this->db
        ->order_by( $order_by, $order_type )
        ->get()->result();

        if( $this->get( 'columns' ) && $this->get( 'filter' ) ) {
            foreach( $this->get( 'columns' ) as $column ) {
                if( ! in_array( $column, $this->systemColumn ) ) {
                    $this->db->or_like( $column, $this->get( 'filter' ) );
                }
            }
            $totalRows      =   $this->db->get( $table_name )->num_rows();
        } else {
            $totalRows      =   $this->db->get( $table_name )->num_rows();
        }

        return $this->response([
            'entries'   =>  $entries,
            'total'     =>  $totalRows
        ], 200 );
    }

    /**
     *  Table POST Entry
     *  @param string table name
     *  @return json
    **/

    public function table_post( $table_name )
    {
        $data       =   [];
        $matching   =   [];

        foreach( $this->post( '__relations' ) as $key => $relation ) {
            $matching[ $relation[ 'alias' ] ]  =   $key;
        }

        /**
         * Replace joined table key by original table column key
         * all other key which are'nt part of joined table are ignored
        **/

        foreach( $this->post( '__geniunes' ) as $field ) {
            $post_field         =
                is_array( $this->post( $field ) ) ?
                join( ',', $this->post( $field ) ) : $this->post( $field );

            if( in_array( $field, array_keys( $matching ) ) ) {
                $data[ $matching[ $field ] ]     =   $post_field;
            } else {
                $data[ $field ]     =   $post_field;
            }
        }

        if( $this->db->insert( $table_name, $data ) ) {
            return $this->__success();
        }
        return $this->__failed();
    }

    /**
     *  Table UPDATE Entry
     *  @param string table name
     *  @param id entry id
     *  @return void
    **/

    public function table_put( $table_name, $id )
    {
        $data       =   [];
        $matching   =   [];

        foreach( $this->put( '__relations' ) as $key => $relation ) {
            $matching[ $relation[ 'alias' ] ]  =   $key;
        }

        /**
         * Replace joined table key by original table column key
         * all other key which are'nt part of joined table are ignored
        **/

        foreach( $this->put( '__geniunes' ) as $field ) {
            $post_field         =
                is_array( $this->put( $field ) ) ?
                join( ',', $this->put( $field ) ) : $this->put( $field );

            if( in_array( $field, array_keys( $matching ) ) ) {
                $data[ $matching[ $field ] ]     =   $post_field;
            } else {
                $data[ $field ]     =   $post_field;
            }
        }

        if( $this->db->where( $this->put( '__primaryCol' ), $id )->update( $table_name, $data ) ) {
            return $this->__success();
        }
        return $this->__failed();
    }

    /**
     *  Table delete Entry
     *  @param string table name
     *  @param id entry id
     *  @return json
    **/

    public function table_delete( $table_name, $id = null )
    {
        if( $_GET[ 'entry_id' ] ) {
            if( is_array( $_GET[ 'entry_id' ] ) ) {
                $this->db->where_in( $_GET[ '__primaryCol' ], $_GET[ 'entry_id' ] )->delete( $table_name );
                return $this->__success();
            }
        } else {
            if( $this->db->where( @$_GET[ '__primaryCol' ], $id )->delete( $table_name ) ) {
                return $this->__success();
            }
        }
        return $this->__failed();
    }

    /**
     *  When everything is ok
     *  @param
     *  @return
    **/

    private function __success()
    {
        $this->response(array(
            'status'        =>    'success'
        ), 200);
    }

    /**
     * Display a error json status
     *
     * @return json status
    **/

    private function __failed()
    {
        $this->response(array(
            'status'        =>    'failed'
        ), 403);
    }

    /**
     * Return Empty
     *
    **/

    private function __empty()
    {
        $this->response(array(
        ), 200);
    }

    /**
     * Not found
     *
     *
    **/

    private function __404()
    {
        $this->response(array(
            'status'        =>    '404'
        ), 404);
    }

    /**
     * Forbidden
    **/

    private function __forbidden()
    {
        $this->response(array(
            'status'        =>    'forbidden'
        ), 403);
    }
}
