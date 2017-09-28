<?php
class Marvin_Module extends Tendoo_Module
{
     public function __construct()
     {
          parent::__construct();
          $this->events->add_filter( 'nexo_customers_basic_fields', [ $this, 'customer_basic_fields' ]);
          $this->events->add_filter( 'nexo_customers_show_billing_tab', '__return_false' );
          $this->events->add_filter( 'nexo_customers_show_shipping_tab', '__return_false' );
          $this->events->add_action( 'do_enable_module', [ $this, 'enable' ]);
          $this->events->add_filter( 'nexo_filters_customers_post_fields', [ $this, 'filter_fields' ], 10, 2 );
          $this->events->add_filter( 'nexo_filters_customers_put_fields', [ $this, 'filter_fields' ], 10, 2 );
          $this->events->add_filter( 'nexo_clients_columns', [ $this, 'filter_customer_columns' ]);
          $this->events->add_filter( 'customers_crud_loaded', [ $this, 'filter_customer_crud' ]);
          $this->events->add_filter( 'nexo_filter_invoice_dom_tag_list', [ $this, 'nexo_filter_invoice_dom_tag_list' ]);
          $this->events->add_filter( 'nexo_filter_receipt_template', [ $this, 'nexo_filter_receipt_template' ]);
     }

     /**
      * Filter Receipt Template
      * @param array order details
      * @return array filtered order details
     **/

     public function nexo_filter_receipt_template( $data )
     {
          $data[ 'template' ][ 'customer_nid' ]   =    $data[ 'order' ][ 'NIT' ];
          $data[ 'template' ][ 'customer_address' ]   =    $data[ 'order' ][ 'ADDRESS' ];
          return $data;
     }

     /**
      * Customer Crud 
      * @param object CRUD object
      * @return object
     **/

     public function filter_customer_crud( $crud )
     {
          $crud->display_as( 'NIT', __( 'NIT de cliente', 'marvin' ) );
          $crud->display_as( 'ADDRESS', __( 'Direccion', 'marvin' ) );
          return $crud;
     }

     /**
      * Display Customer Columns
      * @param array columns
      * @return filtred columns
     **/

     public function filter_customer_columns( $columns )
     {
          return [ 'NOM', 'TEL', 'NIT', 'ADDRESS', 'AUTHOR', 'DATE_CREATION', 'TOTAL_SEND' ];
     }

     /**
      * Field customer field during submiting
      * @param array fields
      * @return array updated fields
     **/

     public function filter_fields( $fields, $rest )
     {
          $fields[ 'NIT' ]         =    $rest->post( 'NIT' ) !== null ? $rest->post( 'NIT' ) : $rest->put( 'NIT' );
          $fields[ 'ADDRESS' ]     =    $rest->post( 'ADDRESS' ) !== null ? $rest->post( 'ADDRESS' ) : $rest->put( 'ADDRESS' );
          return $fields;
     }

     /**
      * Enable Module
      * @param string module namespae
      * @return void
     **/

     public function enable( $namespace )
     {
          if( $namespace == 'marvin' && get_option( 'marvin_installed' ) == null ) {
               
               $this->load->model( 'Nexo_Stores' );

               $stores         =   $this->Nexo_Stores->get();

               array_unshift( $stores, [
               'ID'        =>  0
               ]);

               foreach( $stores as $store ) {
                    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
                    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_clients` 
                    ADD `NIT` varchar(200) NOT NULL AFTER `DESCRIPTION`;' );

                    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_clients` 
                    ADD `ADDRESS` varchar(200) NOT NULL AFTER `DESCRIPTION`;' );
               }

               set_option( 'marvin_installed', 1 );
          }
     }

     /**
      * Basic Fields
      * @param array fields
      * @return array fields
     **/

     public function customer_basic_fields( $fields )
     {
          return [
               [
                    'key'     =>   'phone',
                    'title'    =>  __( 'Téléphone', 'nexo' )
               ], [
                    'key'     =>   'NIT',
                    'title'   =>   __( 'NIT de cliente', 'marvin' ),
                    'type'    =>   'text'
               ], [
                    'key'     =>   'ADDRESS',
                    'title'   =>   __( 'Direccion', 'marvin' ),
                    'type'    =>   'textarea'
               ]
          ];
     }

     /**
      * Filter Tag List 
      * @param string
      * @return string
     **/

     public function nexo_filter_invoice_dom_tag_list( $tags ) 
     {
          return  $tags  . __( '{customer_address} Para mostrar la dirección del cliente', 'marvin' ) . '<br>'
          . __( '{customer_nid} Para mostrar el NID de cliente.', 'marvin' );
     }
}
new Marvin_Module;