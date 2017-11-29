<?php
use Carbon\Carbon;
class ApiNexoOrders extends Tendoo_Api
{
    public function full_order( $order_id )
    {
        $this->load->model( 'Nexo_Checkout' );
        $order        =    $this->events->apply_filters( 
            'loaded_order', 
            $this->Nexo_Checkout->get_order_products($order_id, true) 
        );

        if( $order ) {
            // load shippings
            /** 
             * get shippings linked to that order
             * @since 3.1
            **/

            foreach( ( array ) $order[ 'order' ] as &$_order ) {
                $shippings   =   $this->db->where( 'ref_order', $_order[ 'ID' ] )
                ->get( store_prefix() . 'nexo_commandes_shippings' )
                ->result_array();

                if( $shippings ) {
                    $_order[ 'shipping' ]   =   $shippings[0];
                }
            }
            $this->response( $order, 200 );
        }      

		$this->__empty();
    }
}