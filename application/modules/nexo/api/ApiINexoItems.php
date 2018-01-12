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
                'message'   =>  __( 'Le code barre est déjà en cours d\'utilisation', 'nexo'' )
            ], 403 );
        }

        $item_details       =   [
            'DESIGN'        =>  $this->post( 'item_name' ),
            'REF_CATEGORIE'     =>  $form[ 'ref_category' ],
            'SKU'           =>  $form[ 'sku' ],
            'PRIX_DE_VENTE' =>  
        ]
    }
}