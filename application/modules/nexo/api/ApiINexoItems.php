<?php
class ApiNexoItems extends Tendoo_Api
{
    /**
     * Search items
     * @param void
     * @return json
     */
    public function physicals_and_digitals()
    {
        $this->db->or_like( 'CODEBAR', $this->post( 'search' ) );
        $this->db->or_like( 'DESIGN', $this->post( 'search' ) );
        $this->db->or_like( 'SKU', $this->post( 'search' ) );
        $query  =   $this->db->get( store_prefix() . 'nexo_articles' );
        return $this->response( $query->result_array() );
    }

    /**
     * Create Grouped Items
     * @incomplete
     * @param void
     * @return json
     */
    public function post_grouped()
    {
        $form       =   $this->post( 'form' );

        // search if the barcode and sku is already used
        $this->db->or_where( 'CODEBAR', $form[ 'barcode' ]);
        $this->db->or_where( 'SKU', $form[ 'sku' ]);
        $search     =   $this->db->get( store_prefix() . 'nexo_articles' )->result();

        if ( $search ) {
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  __( 'Le code barre est déjà en cours d\'utilisation', 'nexo' )
            ], 403 );
        }
        
        $tax        =   $this->db->where( 'ID', $form[ 'tax_id' ])
        ->get( store_prefix() . 'nexo_taxes' )
        ->result_array();

        if ( $form[ 'tax_type' ] == 'exclusive' ) {
            if ( $tax[0][ 'TYPE' ] == 'percentage' ) {
                $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $percent;
            } else {
                $flat            =   floatval( $tax[0][ 'FLAT' ]);
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $flat;
            }
        } else {
            if ( $tax[0][ 'TYPE' ] == 'percentage' ) {
                $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) - $percent;
            } else {
                $flat            =   floatval( $tax[0][ 'FLAT' ] );
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) - $flat;
            }
        }

        $item_details               =   [
            'DESIGN'                =>  $this->post( 'item_name' ),
            'REF_CATEGORIE'         =>  $form[ 'category_id' ],
            'SKU'                   =>  $form[ 'sku' ],
            'PRIX_DE_VENTE'         =>  $sale_price,
            'PRIX_DE_VENTE_TTC'     =>  $sale_price_ttc,
            'CODEBAR'               =>  $form[ 'barcode' ],
            'BARCODE_TYPE'          =>  $form[ 'barcode_type' ],
            'TAX_TYPE'              =>  $form[ 'tax_type' ],
            'REF_TAXE'              =>  $form[ 'tax_id' ],
            'TYPE'                  =>  3, // for grouped items
            'STATUS'                =>  $form[ 'status' ] == 'on_sale' ? 1 : 2,
            'STOCK_ENABLED'         =>  $form[ 'stock_enabled' ]    == 'enabled' ? 1 : 2  ,
            'APERCU'                =>  $form[ 'apercu' ]     
        ];

        $this->db->insert( store_prefix() . 'nexo_articles', $item_details );
        $item_id  =  $this->db->insert_id();

        // create items     
        $this->db->insert( store_prefix() . 'nexo_articles_meta', [
            'KEY'                   =>  'included_items',
            'VALUE'                 =>  json_encode( $this->post( 'items' ) ),
            'REF_ARTICLE'           =>  $item_id,
            'DATE_CREATION'         =>  date_now()
        ]);

        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été crée.', 'nexo' )
        ]);
    }

    /**
     * Create Grouped Items
     * @incomplete
     * @param void
     * @return json
     */
    public function put_grouped( $id )
    {
        $form       =   $this->post( 'form' );

        // search if the barcode and sku is already used
        $this->db->or_where( 'CODEBAR', $form[ 'barcode' ]);
        $this->db->or_where( 'SKU', $form[ 'sku' ]);
        $search     =   $this->db->get( store_prefix() . 'nexo_articles' )->result_array();

        if ( $search ) {
            if ( $search[0][ 'ID' ] != $id ) {
                return $this->response([
                    'status'    =>  'failed',
                    'message'   =>  __( 'Le code barre est déjà en cours d\'utilisation', 'nexo' )
                ], 403 );
            }
        }
        
        $tax        =   $this->db->where( 'ID', $form[ 'tax_id' ])
        ->get( store_prefix() . 'nexo_taxes' )
        ->result_array();

        if ( $form[ 'tax_type' ] == 'exclusive' ) {
            if ( $tax[0][ 'TYPE' ] == 'percentage' ) {
                $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $percent;
            } else {
                $flat            =   floatval( $tax[0][ 'FLAT' ]);
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $flat;
            }
        } else {
            if ( $tax[0][ 'TYPE' ] == 'percentage' ) {
                $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) - $percent;
            } else {
                $flat            =   floatval( $tax[0][ 'FLAT' ] );
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) - $flat;
            }
        }

        $item_details               =   [
            'DESIGN'                =>  $this->post( 'item_name' ),
            'REF_CATEGORIE'         =>  $form[ 'category_id' ],
            'SKU'                   =>  $form[ 'sku' ],
            'PRIX_DE_VENTE'         =>  $sale_price,
            'PRIX_DE_VENTE_TTC'     =>  $sale_price_ttc,
            'CODEBAR'               =>  $form[ 'barcode' ],
            'BARCODE_TYPE'          =>  $form[ 'barcode_type' ],
            'TAX_TYPE'              =>  $form[ 'tax_type' ],
            'REF_TAXE'              =>  $form[ 'tax_id' ],
            'TYPE'                  =>  3, // for grouped items
            'STATUS'                =>  $form[ 'status' ] == 'on_sale' ? 1 : 2,
            'STOCK_ENABLED'         =>  $form[ 'stock_enabled' ]    == 'enabled' ? 1 : 2,
            'APERCU'                =>  $form[ 'apercu' ]   
        ];

        $this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_articles', $item_details );

        // Update meta   
        $this->db->where( 'REF_ARTICLE', $id )
        ->where( 'KEY', 'included_items' )
        ->update( store_prefix() . 'nexo_articles_meta', [
            'KEY'                   =>  'included_items',
            'VALUE'                 =>  json_encode( $this->post( 'items' ) ),
            'DATE_MOD'               =>  date_now()
        ]);

        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été mis à jour.', 'nexo' )
        ]);
    }
}