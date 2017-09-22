<?php
/**
 * Starting Cache
 * Cache should be manually restarted
**/

use Carbon\Carbon;

if (! $order_cache = $cache->get($order[ 'order' ][0][ 'ID' ]) || @$_GET[ 'refresh' ] == 'true') {
    ob_start();
}
?>
<?php if( @$_GET[ 'ignore_header' ] != 'true' ):?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo sprintf(__('Order ID : %s &mdash; Nexo Shop Receipt', 'nexo'), $order[ 'order' ][0][ 'CODE' ]);?></title>
<link rel="stylesheet" media="all" href="<?php echo css_url('nexo') . '/bootstrap.min.css';?>" />
</head>

<body>
<?php endif;?>

<?php global $Options;?>
<?php if (@$order[ 'order' ][0][ 'CODE' ] != null):?>
<div class="container-fluid">
    <div class="row">
        <div class="well col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row order-details">
                <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
                    <?php if( store_option( 'url_to_logo' ) != null ):?>
                        <div class="text-center">
                            <img src="<?php echo store_option( 'url_to_logo' );?>" 
                            style="display:inline-block;<?php echo store_option( 'logo_height' ) != null ? 'height:' . store_option( 'logo_height' ) . 'px' : '';?>
                            ;<?php echo store_option( 'logo_width' ) != null ? 'width:' . store_option( 'logo_width' ) . 'px' : '';?>"/>
                        </div>
                    <?php else:?>
                        <h2 class="text-center"><?php echo @$Options[ store_prefix() . 'site_name' ];?></h2>
                    <?php endif;?>
                </div>
                <?php ob_start();?>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?php echo xss_clean( @$Options[ store_prefix() . 'receipt_col_1' ] );?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                    <?php echo xss_clean( @$Options[ store_prefix() . 'receipt_col_2' ] );?>
                </div>
            </div>
            <?php if( @$_GET[ 'is-pdf' ] ):?>
            <br>
            <br>
            <?php endif;?>
            <?php
            $string_to_parse	=	ob_get_clean();
            echo $this->parser->parse_string( $string_to_parse, $template , true );
            ?>
            <div class="row">
                <div class="text-center">
                    <h3><?php _e('Reçu de vente', 'nexo');?></h3>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="col-md-6"><?php _e('Produits', 'nexo');?></th>
                            <th class="col-md-1 text-right"><?php _e('Total', 'nexo');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
                        $total_global    =    0;
                        $total_unitaire    =    0;
                        $total_quantite    =    0;
						$total_discount		=	0;
                        global $current_item;
                        foreach ($order[ 'products' ] as $_produit) {
                            $current_item           =   $_produit;
                            // $total_global        +=    __floatval($_produit[ 'PRIX_TOTAL' ]);
                            $total_unitaire      	+=    __floatval($_produit[ 'PRIX' ]);
                            $total_quantite   	 	+=    __floatval($_produit[ 'QUANTITE' ]);
                            $total_global        	+=    ( __floatval($_produit[ 'PRIX_TOTAL' ]) );
                            $discount_amount			=	0;
                            $discount_formated          =   0;
                            if( $_produit[ 'DISCOUNT_TYPE' ] == 'percentage' ) {
                                $discount_amount		=	__floatval( ( ( floatval( $_produit[ 'PRIX' ] ) * intval( $_produit[ 'QUANTITE' ] ) ) * floatval( $_produit[ 'DISCOUNT_PERCENT' ] ) ) / 100 );
                                $discount_formated      =   $this->Nexo_Misc->cmoney_format( __floatval( $discount_amount ) );
                            } else if( $_produit[ 'DISCOUNT_TYPE' ] == 'flat' ) {
                                $discount_amount		=	floatval( $_produit[ 'DISCOUNT_AMOUNT' ] );
                                $discount_formated      =   $this->Nexo_Misc->cmoney_format( __floatval( $discount_amount ) );
                            }

                            $total_discount			+=	$discount_amount;

                            
                            ?>
                            <tr>
                                <td class="">
                                    <?php echo empty( $_produit[ 'DESIGN' ] ) ? $_produit[ 'NAME' ] : $_produit[ 'DESIGN' ];?><br>
                                    <?php $item_price   =   __floatval($_produit[ 'PRIX' ]);?> 
                                    <?php echo $this->Nexo_Misc->cmoney_format(
                                        $this->events->apply_filters( 'receipt_filter_item_price', $item_price )
                                    );?>
                                    <?php if( $discount_formated > 0 ):?> (-<?php $discount_formated;?>) <?php endif;?>
                                    x <?php echo $_produit[ 'QUANTITE' ];
                                ?><br>
                                <?php $after_item_name  =    $this->events->apply_filters( 'receipt_after_item_name', [ 
                                    'output'        =>  '',
                                    'item'          =>  $_produit
                                ]);

                                echo $after_item_name[ 'output' ];                                     
                                ?>
                                </td>
                                <td class="text-right">
                                    <br>
    								<?php echo $this->Nexo_Misc->cmoney_format( __floatval( $_produit[ 'PRIX_TOTAL' ] ) );?>
                                </td>
                            </tr>
                        
                        <?php
                        }

                        ?>
                        <tr>
                            <td><?php echo __( 'Nombre de Produits', 'nexo' );?></td>
                            <td class="text-right"><?php echo $total_quantite;?></td>
                        </tr>
                        <tr>
                            <td><?php echo __( 'Total Remises', 'nexo' );?></td>
                            <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format( __floatval( $total_discount ) );?></td>
                        </tr>
                        <tr>
                            
                            <td class=""><strong><?php _e('Sous Total', 'nexo');?></strong></td>

                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($total_global)
                            );?>
                            </td>
                        </tr>
                        
                        <?php
                        if( ! empty( $order[ 'order' ][0][ 'SHIPPING_AMOUNT' ] ) ):
                        ?>
                        
                        <tr>
                            <td><?php echo __( 'Livraison', 'nexo' );?></td>
                            <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval( $order[ 'order' ][0][ 'SHIPPING_AMOUNT' ] )
                            );?></td>
                        </tr>

                        <?php endif;?>
    
                        <?php if (__floatval($_produit[ 'RISTOURNE' ])):?>
                        <tr>
                            <td class=""><?php _e('Remise automatique', 'nexo');?></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($_produit[ 'RISTOURNE' ])
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( $_produit[ 'REMISE_TYPE' ] == 'flat' ):?>
                        <tr>
                            <td class=""><?php _e('Remise expresse', 'nexo');?></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($_produit[ 'REMISE' ])
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( $_produit[ 'REMISE_TYPE' ] == 'percentage' ):?>
                        <tr>
                            <td class=""><?php echo sprintf( __('Remise (%s%%)', 'nexo'), $_produit[ 'REMISE_PERCENT' ] );?></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                ( nexoCartGrossValue( $order[ 'products' ] ) * floatval( $_produit[ 'REMISE_PERCENT' ] ) ) / 100
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( $order[ 'order' ][0][ 'GROUP_DISCOUNT' ] != '0' ):?>
                        <tr>
                            <td class=""><?php _e('Remise de groupe', 'nexo');?></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval( $order[ 'order' ][0][ 'GROUP_DISCOUNT' ] )
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( @$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui'):?>
                        <tr>
                            <td class=""><?php _e('Net Hors Taxe', 'nexo');?></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
							bcsub(
								__floatval($total_global) + __floatval($order[ 'order'][0][ 'SHIPPING_AMOUNT' ]),
								(
									__floatval(@$_produit[ 'RISTOURNE' ]) +
									__floatval(@$_produit[ 'RABAIS' ]) +
									__floatval(@$_produit[ 'REMISE' ]) +
                                    nexoCartPercentageDiscount( $order[ 'products' ], $_produit ) +
									__floatval(@$_produit[ 'GROUP_DISCOUNT' ])
								), 2
							) );?>
                            </td>
                        </tr>
                        <tr>
                            <td class=""><?php _e('TVA', 'nexo');?> (<?php echo @$Options[ store_prefix() . 'nexo_vat_percent' ];?>%)</td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                $_produit[ 'TVA' ]
                            );?>
                            </td>
                        </tr>
                        <tr>
                            <td class=""><strong><?php _e('TTC', 'nexo');?></strong></td>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                bcsub(
                                    __floatval($total_global) + __floatval($_produit[ 'TVA' ]) + __floatval($order[ 'order'][0][ 'SHIPPING_AMOUNT' ]),
                                    (
                                        __floatval(@$_produit[ 'RISTOURNE' ]) +
                                        __floatval(@$_produit[ 'RABAIS' ]) +
                                        __floatval(@$_produit[ 'REMISE' ]) +
                                        nexoCartPercentageDiscount( $order[ 'products' ], $_produit ) +
										__floatval(@$_produit[ 'GROUP_DISCOUNT' ])
                                    ), 2
                                ),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php else:?>
                        <tr>
                            <td class=""><strong><?php _e('Net à Payer', 'nexo');?></strong></td>
                            <td class="text-right">
                            <?php
                            echo $this->Nexo_Misc->cmoney_format( bcsub(
                                __floatval($total_global) + __floatval($_produit[ 'TVA' ]) + __floatval($order[ 'order'][0][ 'SHIPPING_AMOUNT' ]),
                                (
                                    __floatval(@$_produit[ 'RISTOURNE' ]) +
                                    __floatval(@$_produit[ 'RABAIS' ]) +
                                    __floatval(@$_produit[ 'REMISE' ]) +
                                    ( ( __floatval( @$_produit[ 'REMISE_PERCENT' ] ) * $total_global ) / 100 ) +
                                    __floatval(@$_produit[ 'GROUP_DISCOUNT' ])
                                ), 2
                            ) )?>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php
                        $order_payments         =   $this->Nexo_Misc->order_payments( $order[ 'order' ][0][ 'CODE' ] );
                        $payment_types          =   $this->events->apply_filters( 'nexo_payments_types', $this->config->item( 'nexo_payments_types' ) );

                        foreach( $order_payments as $payment ) {
                            ?>
                            <tr>
                                <td class="">
                                    <?php echo @$payment_types[ $payment[ 'PAYMENT_TYPE' ] ] == null ? __( 'Type de paiement inconnu', 'nexo' ) : @$payment_types[ $payment[ 'PAYMENT_TYPE' ] ]; ?>
                                </td>
                                <td class="text-right">
                                <?php echo $this->Nexo_Misc->cmoney_format( __floatval( $payment[ 'MONTANT' ] ) );?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                        <tr>
                            <td class=""><strong><?php _e('Somme Total Perçue', 'nexo');?><strong></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format( __floatval( $_produit[ 'SOMME_PERCU' ] ) );?>
                            </td>
                        </tr>
                        <?php
                        $terme        =    __floatval( $_produit[ 'SOMME_PERCU' ] ) >= floatval( $order[ 'order' ][0][ 'TOTAL' ] ) ? __('à rendre :', 'nexo') : __('&Agrave; percevoir :', 'nexo');
                        ?>
                        <tr>
                            <td class="text-right"><h4><strong><?php echo $terme;?></strong></h4></td>
                            <td class="text-right text-danger"><h4><strong>
								<?php
                                echo $this->Nexo_Misc->cmoney_format( abs(bcsub(
                                    __floatval($order[ 'order' ][0][ 'TOTAL' ]),
                                    __floatval($order[ 'order' ][0][ 'SOMME_PERCU' ]),
                                    2
                                )) );
                                ;?>
                            </strong>
                            </h4></td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-center"><?php echo xss_clean( $order[ 'order' ][0][ 'DESCRIPTION' ] );?></p>
				<p class="text-center"><?php echo xss_clean( $this->parser->parse_string( @$Options[ store_prefix() . 'nexo_bills_notices' ], $template , true ) );?></p>
                <?php if( @$_GET[ 'is-pdf' ] == null ):?>
                <div class="container-fluid hideOnPrint">
                    <div class="row hideOnPrint">
                        <div class="col-lg-12">
                            <a href="<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'lists' ));?>" class="btn btn-success btn-lg btn-block"><?php _e('Revenir à la liste des commandes', 'nexo');?></a>
                        </div>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php else:?>
<div class="container-fluid"><?php echo tendoo_error(__('Une erreur s\'est produite durant l\'affichage de ce reçu. La commande concernée semble ne pas être valide ou ne dispose d\'aucun produit.', 'nexo'));?></div>
<div class="container-fluid hideOnPrint">
    <div class="row hideOnPrint">
        <div class="col-lg-12">
            <a href="<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists' ));?>" class="btn btn-success btn-lg btn-block"><?php _e('Revenir à la liste des commandes', 'nexo');?></a>
        </div>
    </div>
</div>
<?php endif;?>
<style>
@media print {
	* {
		font-family:Verdana, Geneva, sans-serif;
	}
	.hideOnPrint {
		display:none !important;
	}
	td, th {font-size: 2.8vw;}
	.order-details, p {
		font-size: 2.5vw;
	}
	.order-details h2 {
		font-size: 5.5vw;
	}
	h3 {
		font-size: 2.8vw;
	}
	h4 {
		font-size: 2.8vw;
	}
}
</style>

<?php if( @$_GET[ 'ignore_header' ] != 'true' ):?>
</body>
</html>
<?php endif;?>

<?php
if (! $cache->get($order[ 'order' ][0][ 'ID' ]) || @$_GET[ 'refresh' ] == 'true') {
    $cache->save($order[ 'order' ][0][ 'ID' ], ob_get_contents(), 999999999); // long time
}
