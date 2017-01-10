<?php

use SimpleExcel\SimpleExcel;

trait Nexo_items
{

    /**
     *  Create Bulk Items
     *  @param
     *  @return
    **/

    public function create_bulk_items_post()
    {
        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo_articles`' );
        $items      =   $this->post( 'items' );
        $items      =   unique_multidim_array( $items, 'SKU' );
        $items      =   unique_multidim_array( $items, 'CODEBAR' );
        $this->db->insert_batch( store_prefix() . 'nexo_articles', $items );
    }

    /**
     *  Create Shipping Categories
     *  @param POST shippings
     *  @param POST categories
     *  @return json
    **/

    public function create_shipping_categories_post()
    {
        $shippings  =   [];

        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo_arrivages`' );

        if( $this->post( 'shippings' ) ) {

            // Create All available Shippings
            foreach( $this->post( 'shippings' ) as $shipping ) {
                $this->db->insert( store_prefix() . 'nexo_arrivages', [
                    'TITRE'     =>  ucwords( $shipping ),
                    'AUTHOR'    =>      $this->post( 'author' ),
                    'DATE_CREATION' =>  $this->post( 'date' )
                ]);
                $shippings[ url_title( $shipping, '_' ) ]   =   $this->db->insert_id();
            }
        } else {

            // Create default shipping
            $this->db->insert( store_prefix() . 'nexo_arrivages', [
                'TITRE'     =>      $this->post( 'default_shipping_title' ),
                'AUTHOR'    =>      $this->post( 'author' ),
                'DATE_CREATION' =>  $this->post( 'date' ),
                'ID'        =>      1
            ]);
        }

        $categories         =   array();
        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo_categories`' );

        if( $this->post( 'cats' ) ) {
            foreach( $this->post( 'cats' ) as $cat ) {
                $this->db->insert( store_prefix() . 'nexo_categories', [
                    'NOM'           =>  ucwords( $cat ),
                    'AUTHOR'        =>  $this->post( 'author' ),
                    'DATE_CREATION' =>  $this->post( 'date' )
                ]);
                $categories[ url_title( $cat, '_' ) ]   =   $this->db->insert_id();
            }
        } else {
            $this->db->insert( store_prefix() . 'nexo_categories', [
                'NOM'           =>      $this->post( 'default_cat_title' ),
                'AUTHOR'        =>      $this->post( 'author' ),
                'DATE_CREATION' =>  $this->post( 'date' )
            ]);
        }

        return $this->response( [
            'categories'    =>  $categories,
            'shippings'     =>  $shippings
        ], 200 );
    }

    /**
     * Get item
     *
    **/

    public function item_get($id = null, $filter = 'ID' )
    {
        if ($id != null && $filter != 'sku-barcode') {
            $result        =    $this->db->where($filter, $id)->get( store_prefix() . 'nexo_articles')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } elseif ($filter == 'sku-barcode') {
            $result        =    $this->db
                                ->where('CODEBAR', $id)
                                ->or_where('SKU', $id)
                                ->get( store_prefix() . 'nexo_articles')
                                ->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } else {
            $this->db->select('*,
			' . store_prefix() . 'nexo_articles.ID as ID,
			' . store_prefix() . 'nexo_categories.ID as CAT_ID
			')
            ->from( store_prefix() . 'nexo_articles')
            ->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_articles.REF_CATEGORIE = ' . store_prefix() . 'nexo_categories.ID');
            $this->response($this->db->get()->result());
        }
    }

    /**
     * Delete Item from Shop
     *
    **/

    public function item_delete($id = null)
    {
        if ($id == null) {
            $this->response(array(
                'status' => 'failed'
            ));
        } else {
            $this->db->where('ID', $id)->delete( store_prefix() . 'nexo_articles')->result();

            $this->response(array(
                'status' => 'failed'
            ));
        }
    }

    /**
     * PUt item
     *
    **/

    public function item_put()
    {
        $request    =    $this->db->where($this->put('id'))
        ->set('DESIGN', $this->put('design'))
        ->set('REF_RAYON', $this->put('ref_rayon'))
        ->set('REF_SHIPPING', $this->put('ref_shipping'))
        ->set('REF_CATEGORIE', $this->put('ref_categorie'))
        ->set('QUANTITY', $this->put('quantity'))
        ->set('SKU', $this->put('sku'))
        ->set('QUANTITE_RESTANTE', $this->put('quantite_restante'))
        ->set('QUANTITE_VENDUE', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
        ->update( store_prefix() . 'nexo_articles');

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
     * Item insert
    **/

    public function item_post()
    {
        $request    =    $this->db
        ->set('DESIGN', $this->put('design'))
        ->set('REF_RAYON', $this->put('ref_rayon'))
        ->set('REF_SHIPPING', $this->put('ref_shipping'))
        ->set('REF_CATEGORIE', $this->put('ref_categorie'))
        ->set('QUANTITY', $this->put('quantity'))
        ->set('SKU', $this->put('sku'))
        ->set('QUANTITE_RESTANTE', $this->put('quantite_restante'))
        ->set('QUANTITE_VENDUE', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
        ->insert( store_prefix() . 'nexo_articles');

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
	 * Item by collection
	 *
	 * @param int collection id
	 * @return json
	**/

	public function item_by_collection_get( $collection_id )
	{
		$this->response(
		$this->db->select( '*,
		' . store_prefix() . 'nexo_categories.NOM as CAT_NAME' )
		->from( store_prefix() . 'nexo_articles' )
		->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_articles.REF_CATEGORIE = ' . store_prefix() . 'nexo_categories.ID', 'inner' )
		->where( 'REF_SHIPPING', $collection_id )
		->get()->result() );
	}

    /**
     *  Import Item from CSV
     *  @return json
    **/

    public function import_csv_post()
    {
        if( $this->post( 'ext' ) == 'csv' ) {
            $inputFileName          =   APPPATH . 'temp/sample.csv';
            file_put_contents( $inputFileName, $this->post( 'csv' ) );

            $csv_reader         =   new SimpleExcel( 'csv' );
            $csv_reader->parser->loadFile( $inputFileName );

            $data   =   $csv_reader->parser->getField();
            $cols   =   $this->post( 'cols' );
            unset( $data[0] );
            // unset( $cols[0] );

            $finalData      =   array();
            $categories     =   array();
            $shippings       =   array();
            foreach( $data as $entry ) {
                $currentArray       =   array();
                foreach( $cols as $key => $col ) {
                    if( !empty( $col ) ) {
                        // Get Categorie
                        if( in_array( $col, array( 'REF_CATEGORIE', 'REF_SHIPPING' ) ) ) {
                            if( ! in_array( $entry[ $key ], $categories ) && $col == 'REF_CATEGORIE' ) {
                                $categories[] = $entry[ $key ];
                            }

                            if( ! in_array( $entry[ $key ], $shippings ) && $col == 'REF_SHIPPING' ) {
                                // Get Shipping
                                $shippings[]  = $entry[ $key ];
                            }
                        }

                        //
                        if( in_array( $col, array( 'REF_CATEGORIE', 'REF_SHIPPING', 'BARCODE' ) ) ) {
                            $currentArray[ $col ]   =   $entry[ $key ] == '' ?  null : $entry[ $key ];
                        } else {
                            $currentArray[ $col ]   =   $entry[ $key ];
                        }
                    }
                }

                // Shipping is required
                if( ! in_array( 'REF_SHIPPING', $cols ) ) {
                    $currentArray[ 'REF_SHIPPING' ] =   1; // default shipping
                }

                // Category is requried
                if( ! in_array( 'REF_CATEGORIE', $cols ) ) {
                    $currentArray[ 'REF_CATEGORIE' ] =   1; // default category
                }

                $finalData[]    =   $currentArray;
            }

            return $this->response( array(
                'shippings'     =>  $shippings,
                'categories'    =>  $categories,
                'items'          =>  $finalData
            ));
        }
    }




}
