<?php
class Nexo_Products extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create codebar image
     * @param string code
     * @return void
    **/

    public function create_codebar($code, $code_type = null )
    {
        $this->load->model('Nexo_Misc');

        global $Options;

        $width        =    empty($Options[ store_prefix() . 'nexo_codebar_width' ]) ? 300 : intval($Options[ store_prefix() . 'nexo_codebar_width' ]);
        $height        =    empty($Options[ store_prefix() . 'nexo_codebar_height' ]) ? 300 : intval($Options[ store_prefix() . 'nexo_codebar_height' ]);
        $code_type    =    $code_type != null ? $code_type : empty($Options[ store_prefix() . 'nexo_product_codebar' ]) ? "code_128" : @$Options[ store_prefix() . 'nexo_product_codebar' ];
        $barwidth    =    empty($Options[ store_prefix() . 'nexo_bar_width' ]) ? 3 : intval(@$Options[ store_prefix() . 'nexo_bar_width' ]);

        $generator =    new Picqer\Barcode\BarcodeGeneratorJPG();
        $code_type      =   strtolower( $code_type );

        if ( $code_type == 'ean8') {
            $generator_type    =    $generator::TYPE_EAN_8;
        } elseif ($code_type == 'ean13') {
            $generator_type    =    $generator::TYPE_EAN_13;
        } elseif ($code_type == 'code_128') {
            $generator_type    =    $generator::TYPE_CODE_128;
        } elseif ($code_type == 'codabar') {
            $generator_type    =    $generator::TYPE_CODABAR;
        }

        $barcode_path        =    NEXO_CODEBAR_PATH . $code . $this->Nexo_Misc->ean_checkdigit($code, $code_type);
        file_put_contents($barcode_path . '.jpg', $generator->getBarcode($code, $generator_type, $barwidth, $height));
    }

    /**
     * Generate Bar code
     * @return void
    **/

    public function generate_barcode( $code = null, $barcode_type = null )
    {
        $this->load->model('Nexo_Misc');
        global $Options;

        if( $code == null ) {
            function random($start = true)
            {
                $start_int    =    $start ? 1 : 0;
                return rand($start_int, 9);
            }

            $saved_barcode    =    $this->options->get( store_prefix() . 'nexo_saved_barcode');
            $code            =    '';
            $limit            =    ! empty($Options[ store_prefix() . 'nexo_codebar_limit_nbr' ]) ? intval(@$Options[ store_prefix() . 'nexo_codebar_limit_nbr' ]) : 6;

            $barcode_type        =   $barcode_type == null ? @$Options[ store_prefix() . 'nexo_product_codebar' ] : $barcode_type;

            if ($saved_barcode) {
                do {
                    if ( $barcode_type == 'ean8') {
                        for ($i = 0; $i < 7; $i++) {
                            $start = ($i == 0) ? true : false;
                            $code .= random($start);
                        }
                        $code        =    $code . $this->Nexo_Misc->ean_checkdigit( $code,  $barcode_type );
                    } elseif ( $barcode_type == 'ean13') {
                        for ($i = 0; $i < 12; $i++) {
                            $start    = ($i == 0) ? true : false;
                            $code    .= random($start);
                        }
                        $code        .=    $this->Nexo_Misc->ean_checkdigit($code, $barcode_type );
                    } else {
                        for ($i = 0; $i < $limit ; $i++) {
                            $start = ($i == 0) ? true : false;
                            $code .= random($start);
                        }
                    }
                } while (in_array($code, $saved_barcode));
            } else {
                if ( $barcode_type == 'ean8') {
                    for ($i = 0; $i < 7; $i++) {
                        $start    = ($i == 0) ? true : false;
                        $code    .= random($start);
                    }
                    $code        .=    $this->Nexo_Misc->ean_checkdigit($code, $barcode_type );
                } elseif ( $barcode_type == 'ean13') {
                    for ($i = 0; $i < 12; $i++) {
                        $start = ($i == 0) ? true : false;
                        $code .= random($start);
                    }
                    $code        .= $this->Nexo_Misc->ean_checkdigit($code, $barcode_type );
                } else {
                    for ($i = 0; $i < $limit ; $i++) {
                        $start = ($i == 0) ? true : false;
                        $code .= random($start);
                    }
                }
            }

            $saved_barcode[]    =    $code;

            $this->options->set( store_prefix() . 'nexo_saved_barcode', $saved_barcode, true);

            if (in_array( $barcode_type, array( 'ean8', 'ean13' ))) {
                $this->create_codebar(substr($code, 0, -1));
            } else {
                $this->create_codebar($code);
            }
        } else {

            if( empty( $barcode_type ) ) {
                $barcode_type       =   @$Options[ store_prefix() . 'nexo_product_codebar' ];
            }

            $this->create_codebar( $code, $barcode_type );
        }

        return $code;
    }

    /**
     * Reset saved Barcode
     * @return void
     *
    **/

    public function reset_barcode()
    {
        $this->options->delete( store_prefix() . 'nexo_saved_barcode');

        /**
         * @source http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
        **/
        $files = glob( NEXO_BARCODE_PATH . '*'); // get all file names
        foreach ($files as $file) { // iterate files
          if (is_file($file)) {
              unlink($file); // delete file
          }
        }
    }

	/**
	 * Delete barcode image
	 *
	 * @param string barcode
	 * @return void
	**/

	public function delete_barcode_image( $barcode )
	{
		@unlink( NEXO_CODEBAR_PATH . $barcode . '.jpg' ); // delete file
	}

    /**
     * Resample barcode
     *
     * @param int product id
     * @return string json
    **/

    public function resample_codebar($product_id, $old_barcode, $barcode_type = null )
    {
        // Get a new barcode based on current settings
        $barcode        =    $this->generate_barcode( null, $barcode_type );

        // Update Order Barcodes
        $this->db->where('REF_PRODUCT_CODEBAR', $old_barcode)->update( store_prefix() . 'nexo_commandes_produits', array(
            'REF_PRODUCT_CODEBAR'    =>    $barcode
        ));

        // Update Barcode
        $this->db->where('ID', $product_id)->update( store_prefix() . 'nexo_articles', array(
            'CODEBAR'    =>    $barcode
        ));

        return json_encode(array(
            'type'    =>    'success'
        ));
    }

    /**
     * Product Save
     *
     * @param array
     * @return array
    **/

    public function product_save($param)
    {
        // Protecting
        if (! User::can('create_shop_items')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        global $Options;
        $param[ 'AUTHOR' ]                	=    intval(User::id());
        $param[ 'QUANTITE_RESTANTE' ]    	=    intval($param[ 'QUANTITY' ]); // - intval($param[ 'DEFECTUEUX' ]);
        $param[ 'QUANTITE_VENDU' ]       	=    0;
        // $param[ 'COUT_DACHAT' ]            	=    intval($param[ 'PRIX_DACHAT' ]) + intval($param[ 'FRAIS_ACCESSOIRE' ]);
        $param[ 'DATE_CREATION' ]        	=    date_now();

		// @since 2.9
		// Generate barcode
		if( $param[ 'AUTO_BARCODE' ] == '1' && ! empty( $param[ 'CODEBAR' ] ) && ! empty( $param[ 'BARCODE_TYPE' ] ) ) {
			$this->create_codebar( $param[ 'CODEBAR' ], $param[ 'BARCODE_TYPE' ] );
		} else { // if barcode is note generated automatically
			$param[ 'CODEBAR' ]               	=    $this->generate_barcode();
		}

		// If Multi store is enabled
		// @since 2.8
		global $store_id;
		if( $store_id != null ) {
			$param[ 'REF_STORE' ]		=	$store_id;
		}

		$param		=	$this->events->apply_filters( 'nexo_save_product', $param );

        return $param;
    }

    /**
     * Product Update
     *
     * @param array
     * @return array
    **/

    public function product_update($param)
    {
        // Protecting
        if (! User::can('edit_shop_items')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }

        global $Options;
        $segments                			=    $this->uri->segment_array();
        $item_id                    		=    end($segments) ;
        $article                        	=    $this->get_product( $item_id );

        $quantite                        	=    intval($article[0][ 'QUANTITY' ]);
        $old_defectueux                    	=    intval($article[0][ 'DEFECTUEUX' ]);

		// $param[ 'QUANTITE_RESTANTE' ]    =    ((intval($param[ 'QUANTITY' ]) - intval($param[ 'DEFECTUEUX' ])) - intval($article[0][ 'QUANTITE_VENDU' ]));
        $param[ 'QUANTITE_RESTANTE' ]    	=    ( intval($param[ 'QUANTITY' ] ) - intval( $article[0][ 'QUANTITE_VENDU' ] ) );
        $param[ 'DATE_MOD' ]            	=    date_now();
        $param[ 'AUTHOR' ]                	=    intval(User::id());
        // $param[ 'COUT_DACHAT' ]           	=    intval($param[ 'PRIX_DACHAT' ]) + intval($param[ 'FRAIS_ACCESSOIRE' ]);

		// @since 2.9
		// Generate barcode
		if( $param[ 'AUTO_BARCODE' ] == '1' && ! empty( $param[ 'CODEBAR' ] ) && ! empty( $param[ 'BARCODE_TYPE' ] ) ) {
			// Delete fist old barcode
			$this->delete_barcode_image( $article[0][ 'CODEBAR' ] );

			// Do generate barcode
			$this->create_codebar( $param[ 'CODEBAR' ], $param[ 'BARCODE_TYPE' ] );
		}

		// If Multi store is enabled
		// @since 2.8
		global $store_id;
		if( $store_id != null ) {
			$param[ 'REF_STORE' ]		=	$store_id;
		}

		$param		=	$this->events->apply_filters( 'nexo_update_product', $param );

        return $param;
    }

	/**
	 * Delete Item related object
	 * @param int item id
	 * @return void
	**/

	public function product_delete_related_component( $item_id )
	{
		$this->where( 'REF_ARTICLE', $item_id )->delete( store_prefix() . 'nexo_articles_meta' );
		$this->where( 'REF_ARTICLE', $item_id )->delete( store_prefix() . 'nexo_articles_variations' );
	}

	/**
	 * After Insert Item
	**/

	public function product_after_save( $array, $id )
	{
		$this->events->do_action( 'nexo_after_save_product', $array, $id );
	}

	/**
	 * After Update Item
	**/

	public function product_after_update( $array, $id )
	{
		$this->events->do_action( 'nexo_after_update_product', $array, $id );
	}

    // Deprecated

    public function get($element, $key, $as = 'ID')
    {
        $query    =    $this->db->where($as, $key)->get( store_prefix() . $element);
        return $query->result_array();
    }

    /**
     * get products linked to a shipping
     *
     * @param int shipping id
     * @return Array
    **/

    public function get_products_by_shipping($shipping_id)
    {
        $query    =    $this->db->where('REF_SHIPPING', $shipping_id)->get( store_prefix() . 'nexo_articles');
        return $query->result_array();
    }

    /**
     * Get product
     * @param int product id
     * @returns Array
    **/

    public function get_product($product_id)
    {
        $query    =    $this->db->where('ID', $product_id)->get( store_prefix() . 'nexo_articles');
        return $query->result_array();
    }
}
