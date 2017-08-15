<?php
trait Stock_Transfert_Trait
{
    
    /**
     * Stock Transfert
     * @return json response
    **/

    public function stock_transfert_post()
    {
        $store                      =   $this->post( 'store' );
        $this->db->insert( 'nexo_stock_transfert', [
            'TITLE'                 =>  $this->post( 'title' ),
            'FROM_STORE'            =>  get_store_id(),
            'DESTINATION_STORE'     =>  $store[ 'ID' ],
            'AUTHOR'                =>  User::id(),
            'TYPE'                  =>  'supply',
            'DATE_CREATION'         =>  date_now(),
            'APPROUVED'              =>  0, // 0: pending, 1: approuved, 2: refused
        ]);

        $transfert_id                =   $this->db->insert_id();

        foreach( $this->post( 'items' ) as  $item ) {
            $this->db->insert( 'nexo_stock_transfert_items', [
                'DESIGN'        =>  $item[ 'DESIGN' ],
                'QUANTITY'      =>  $item[ 'QTE_ADDED' ],
                'BARCODE'       =>  $item[ 'CODEBAR' ],
                'DATE_CREATION' =>  date_now(),
                'REF_TRANSFER'  =>  $transfert_id,
                'UNIT_PRICE'    =>  $item[ 'PRIX_DACHAT' ],
                'TOTAL_PRICE'   =>  floatval( $item[ 'PRIX_DACHAT' ] ) * floatval( $item[ 'QTE_ADDED' ] )
            ]);

            // reduce stock from main warehouse
            if( store_option( 'deduct_from_store', 'yes' ) == 'yes' ) {
                if( get_store_id() == 0 ) {
                    // reduce from quantity
                    $this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )->update( 'nexo_articles', [
                        'QUANTITE_RESTANTE'     =>      floatval( $item[ 'QUANTITE_RESTANTE' ] ) - floatval( $item[ 'QTE_ADDED' ] )
                    ]);

                    // Input in stock flow as transfer
                    $this->db->insert( 'nexo_articles_stock_flow', [
                        'QUANTITE'              =>      floatval( $item[ 'QTE_ADDED' ] ),
                        'TYPE'                  =>      'transfert_out',
                        'UNIT_PRICE'            =>      $item[ 'PRIX_DACHAT' ],
                        'TOTAL_PRICE'           =>      floatval( $item[ 'QTE_ADDED' ] ) * floatval( $item[ 'PRIX_DACHAT' ] ),
                        'REF_ARTICLE_BARCODE'   =>      $item[ 'CODEBAR' ],
                        'DATE_CREATION'         =>      date_now(),
                        'AUTHOR'                =>      User::id()
                    ]);
                } else {
                    // reduce from quantity
                    $this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )->update( store_prefix() . 'nexo_articles', [
                        'QUANTITE_RESTANTE'     =>      floatval( $item[ 'QUANTITE_RESTANTE' ] ) - floatval( $item[ 'QTE_ADDED' ] )
                    ]);

                    // Input in stock flow as transfer
                    $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                        'QUANTITE'              =>      floatval( $item[ 'QTE_ADDED' ] ),
                        'TYPE'                  =>      'transfert_out',
                        'UNIT_PRICE'            =>      $item[ 'PRIX_DACHAT' ],
                        'TOTAL_PRICE'           =>      floatval( $item[ 'QTE_ADDED' ] ) * floatval( $item[ 'PRIX_DACHAT' ] ),
                        'REF_ARTICLE_BARCODE'   =>      $item[ 'CODEBAR' ],
                        'DATE_CREATION'         =>      date_now(),
                        'AUTHOR'                =>      User::id()
                    ]);
                } 
            }            
        }    

        $this->response([
            'transfert_id'  =>  $transfert_id
        ], 200 );    
    }
}