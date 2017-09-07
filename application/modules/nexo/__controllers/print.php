<?php
use Dompdf\Dompdf;

class Nexo_Print extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ),  array_slice($args, 2));
            } else {
                return $this->defaults($args);
            }
        }
        return $this->defaults($args);
    }

    public function defaults()
    {
        show_404();
    }

    public function order_receipt($order_id = null)
    {
        if ($order_id != null) {
            $this->cache        =    new CI_Cache(array( 'adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    'nexo_order_' . store_prefix() ));

            if ($order_cache = $this->cache->get($order_id) && @$_GET[ 'refresh' ] != 'true') {
                echo $this->cache->get($order_id);
                return;
            }

			$this->load->library('parser');
            $this->load->model('Nexo_Checkout');

            global $Options;

            $data                		=   array();
            $data[ 'order' ]    		=   $this->Nexo_Checkout->get_order_products($order_id, true);
            $data[ 'cache' ]    		=   $this->cache;
			$allowed_order_for_print	=	$this->events->apply_filters( 'allowed_order_for_print', array( 'nexo_order_comptant' ) );

            // Allow only cash order to be printed
            if ( ! in_array( $data[ 'order' ]['order'][0][ 'TYPE' ], $allowed_order_for_print ) ) {
                redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=print_disabled' ));
            }

            if (count($data[ 'order' ]) == 0) {
                die(sprintf(__('Impossible d\'afficher le ticket de caisse. Cette commande ne possède aucun article &mdash; <a href="%s">Retour en arrière</a>', 'nexo'), $_SERVER['HTTP_REFERER']));
            }

			// @since 2.7.9
			$data[ 'template' ]						=	array();
            $data[ 'template' ][ 'order_date' ]		=	mdate( '%d/%m/%Y %g:%i %a', strtotime($data[ 'order' ][ 'order' ][0][ 'DATE_CREATION' ]));
            $data[ 'template' ][ 'order_updated' ]  =	mdate( '%d/%m/%Y %g:%i %a', strtotime($data[ 'order' ][ 'order' ][0][ 'DATE_MOD' ]));
			$data[ 'template' ][ 'order_code' ]		=	$data[ 'order' ][ 'order' ][0][ 'CODE' ];
            $data[ 'template' ][ 'order_id' ]       =   $data[ 'order' ][ 'order' ][0][ 'ORDER_ID' ];
			$data[ 'template' ][ 'order_status' ]	=	$this->Nexo_Checkout->get_order_type($data[ 'order' ][ 'order' ][0][ 'TYPE' ]);
            $data[ 'template' ][ 'order_note' ]     =   $data[ 'order' ][ 'order' ][0][ 'DESCRIPTION' ];

			$data[ 'template' ][ 'order_cashier' ]	=	User::pseudo( $data[ 'order' ][ 'order' ][0][ 'AUTHOR' ] );
			$data[ 'template' ][ 'shop_name' ]		=	@$Options[ store_prefix() . 'site_name' ];
			$data[ 'template' ][ 'shop_pobox' ]		=	@$Options[ store_prefix() . 'nexo_shop_pobox' ];
			$data[ 'template' ][ 'shop_fax' ]		=	@$Options[ store_prefix() . 'nexo_shop_fax' ];
			$data[ 'template' ][ 'shop_email' ]     =	@$Options[ store_prefix() . 'nexo_shop_email' ];
			$data[ 'template' ][ 'shop_street' ]    =	@$Options[ store_prefix() . 'nexo_shop_street' ];
			$data[ 'template' ][ 'shop_phone' ]     =	@$Options[ store_prefix() . 'nexo_shop_phone' ];
            $data[ 'template' ][ 'customer_name' ]  =   $data[ 'order' ][ 'order' ][0][ 'customer_name' ];
            $data[ 'template' ][ 'customer_phone' ]  =   $data[ 'order' ][ 'order' ][0][ 'customer_phone' ];

            $theme                                          =	@$Options[ store_prefix() . 'nexo_receipt_theme' ] ? @$Options[ store_prefix() . 'nexo_receipt_theme' ] : 'default';

            $path   =   '../modules/nexo/views/receipts/' . $theme . '.php';

            $this->load->view(
                $this->events->apply_filters( 'nexo_receipt_theme_path', $path ),
                $data,
                $theme
            );

        } else {
            die(__('Cette commande est introuvable.', 'nexo'));
        }
    }

    public function order_refund( $order_id = null )
    {
        // if ($order_cache = $this->cache->get($order_id) && @$_GET[ 'refresh' ] != 'true') {
        //     echo $this->cache->get($order_id);
        //     return;
        // }

        $this->load->library('parser');
        $this->load->model('Nexo_Checkout');

        global $Options;

        $data                		=   array();
        // $data[ 'order' ]    		=   $this->Nexo_Checkout->get_order_products($order_id, true);
        // $data[ 'stock' ]            =   $this->Nexo_Checkout->get_order_with_item_stock( $order_id );
        // $data[ 'cache' ]    		=   $this->cache;

        // if (count($data[ 'order' ]) == 0) {
        //     die(sprintf(__('Impossible d\'afficher le reçu de remboursement. Cette commande ne possède aucun article &mdash; <a href="%s">Retour en arrière</a>', 'nexo'), $_SERVER['HTTP_REFERER']));
        // }

        // @since 2.7.9
        $data[ 'template' ]						=	array();
        $data[ 'template' ][ 'order_date' ]		=	':orderDate'; // mdate( '%d/%m/%Y %g:%i %a', strtotime($data[ 'order' ][ 'order' ][0][ 'DATE_CREATION' ]));
        $data[ 'template' ][ 'order_updated' ]  =   ':orderUpdated'; // just to show the date when the order has been update
        $data[ 'template' ][ 'order_code' ]		=	':orderCode'; // $data[ 'order' ][ 'order' ][0][ 'CODE' ];
        $data[ 'template' ][ 'order_id' ]       =   ':orderId'; // $data[ 'order' ][ 'order' ][0][ 'ID' ];
        $data[ 'template' ][ 'order_status' ]	=	':orderStatus'; // $this->Nexo_Checkout->get_order_type($data[ 'order' ][ 'order' ][0][ 'TYPE' ]);
        $data[ 'template' ][ 'order_note' ]     =   ':orderNote'; // $data[ 'order' ][ 'order' ][0][ 'DESCRIPTION' ];            
        $data[ 'template' ][ 'order_cashier' ]	=	':orderCashier'; // User::pseudo( $data[ 'order' ][ 'order' ][0][ 'AUTHOR' ] );

        $data[ 'template' ][ 'shop_name' ]		=	@$Options[ store_prefix() . 'site_name' ];
        $data[ 'template' ][ 'shop_pobox' ]		=	@$Options[ store_prefix() . 'nexo_shop_pobox' ];
        $data[ 'template' ][ 'shop_fax' ]		=	@$Options[ store_prefix() . 'nexo_shop_fax' ];
        $data[ 'template' ][ 'shop_email' ]		=	@$Options[ store_prefix() . 'nexo_shop_email' ];
        $data[ 'template' ][ 'shop_street' ]    =	@$Options[ store_prefix() . 'nexo_shop_street' ];
        $data[ 'template' ][ 'shop_phone' ]	    =	@$Options[ store_prefix() . 'nexo_shop_phone' ];

        $theme                                  =	@$Options[ store_prefix() . 'nexo_refund_theme' ] ? @$Options[ store_prefix() . 'nexo_refund_theme' ] : 'default';

        $path   =   '../modules/nexo/views/refund/' . $theme . '.php';

        $this->load->view(
            $this->events->apply_filters( 'nexo_refund_theme_path', $path ),
            $data,
            $theme
        );
    }

    /**
     * Gestion des impressions des étiquettes des produits
    **/

    public function shipping_item_codebar($shipping_id = null)
    {
        if ($shipping_id  == null) {
            show_error(__('Arrivage non définie.', 'nexo'));
        }

        $this->cache        =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    'nexo_products_labels_' . store_prefix() ));

        if ($products_labels = $this->cache->get($shipping_id) && @$_GET[ 'refresh' ] != 'true') {
            echo $this->cache->get( $shipping_id );
            return;
        }

        $this->load->model('Nexo_Products');
        $this->load->model('Nexo_Shipping');

        global $Options;
        $pp_row                    =    ! empty($Options[ store_prefix() . 'nexo_products_labels' ]) ? @$Options[ store_prefix() . 'nexo_products_labels' ] : 4;

        $data                    =    array();
        $data[ 'shipping_id' ]    =    $shipping_id;
        $data[ 'pp_row'    ]        =    $pp_row;
        $data[ 'cache' ]    =    $this->cache;

        if (isset($_GET[ 'products_ids' ])) {
            $get        =    str_replace('%2C', ',', $_GET[ 'products_ids' ]);
            $ids        =    explode(',', $get);
            $products    =    array();
            foreach ($ids as $id) {
                // $unique_product        =    $this->Nexo_Products->get( store_prefix() . 'nexo_articles', $id, 'ID');
                $unique_product             =   $this->db->select( '*' )
                ->from( store_prefix() . 'nexo_arrivages' )
                ->join( store_prefix() . 'nexo_articles_stock_flow', store_prefix() . 'nexo_articles_stock_flow.REF_SHIPPING = ' . store_prefix() . 'nexo_arrivages.ID' )
                ->join( store_prefix() . 'nexo_fournisseurs', store_prefix() . 'nexo_fournisseurs.ID = ' . store_prefix() . 'nexo_articles_stock_flow.REF_PROVIDER' )
                ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE' )
                ->where( store_prefix() . 'nexo_arrivages.ID', $delivery_id )
                ->get()->result_array();
                
                // Si le produit existe
                if (count($unique_product) > 0) {
                    $products[]            =    $unique_product[0];
                }
            }
            // var_dump( $products );
            $data[ 'products' ]        =    $products;
        } else {
            $data[ 'products' ]        =    $this->Nexo_Products->get_products_by_shipping($shipping_id);
        }

        $this->load->view('../modules/nexo/views/products-labels/default.php', $data);
    }

    /**
     *  Return a PDF document with current order receipt
     *  @param int order id
     *  @return PDF document
    **/

    public function order_pdf( $order_id )
    {
        ob_start();
        $this->order_receipt( $order_id );
        $content    =   ob_get_clean();
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml( $content );

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();

    }
}
new Nexo_Print($this->args);
