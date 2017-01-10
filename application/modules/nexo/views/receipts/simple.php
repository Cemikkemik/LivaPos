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
<style media="screen">
/*@font-face {
    font-family: "My Custom Font";
    src: url(<?php echo module_url( 'nexo' ) . 'fonts/OCRAEXT.ttf';?>) format("truetype");
}
body {
    font-family: "My Custom Font", Verdana, Tahoma;
    p {font-size: 4vw;}
}*/
</style>


<body>
<?php endif;?>


<?php global $Options;?>
<?php if (@$order[ 'order' ][0][ 'CODE' ] != null):?>
<div class="container-fluid">
    <div class="row">
        <div class="well col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row order-details">
                <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
                    <h2 class="text-center"><?php echo @$Options[ store_prefix() . 'site_name' ];?></h2>
                </div>
                <?php ob_start();?>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?php echo xss_clean( @$Options[ store_prefix() . 'receipt_col_1' ] );?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                    <?php echo xss_clean( @$Options[ store_prefix() . 'receipt_col_2' ] );?>
                </div>
            </div>
            <?php
            $string_to_parse	=	ob_get_clean();
            echo $this->parser->parse_string( $string_to_parse, $template , true );
            ?>
            <div class="row">
                <div class="text-center">
                    <h3><?php _e('Ticket de caisse', 'nexo');?></h3>
                </div>
                </span>
                <table class="table">
                    <!-- <thead>
                        <tr>
                            <th class="col-md-6 "><?php _e('Produits', 'nexo');?></th>
                            <th class="col-md-2 text-right"><?php _e('Prix', 'nexo');?></th>

                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <th class="col-md-1 text-right"><?php _e('Remise', 'nexo');?></th>
                            <?php endif;?>
                            <th class="col-md-1 text-right"><?php _e('Total', 'nexo');?></th>
                        </tr>
                    </thead> -->
                    <tbody>
                    	<?php
                        $total_global    =    0;
                        $total_unitaire    =    0;
                        $total_quantite    =    0;
						$total_discount		=	0;

                        foreach ($order[ 'products' ] as $_produit) {
                            // $total_global        +=    __floatval($_produit[ 'PRIX_TOTAL' ]);
                            $total_unitaire      	+=    __floatval($_produit[ 'PRIX' ]);
                            $total_quantite   	 	+=    __floatval($_produit[ 'QUANTITE' ]);
							$total_global        	+=    ( __floatval($_produit[ 'PRIX_TOTAL' ]) );
                            ?>
                        <tr>
                            <td class="text-left"><?php echo $_produit[ 'QUANTITE' ];
                            ?> X <?php echo $_produit[ 'DESIGN' ];
                            ?><br><?php echo $_produit[ 'CODEBAR' ];?></td>
                            <td class="text-right" width="150">
							<?php echo $this->Nexo_Misc->cmoney_format( __floatval($_produit[ 'PRIX' ]) );
                            ?>
                            </td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class"text-right" width="150">
							<?php
							$discount_amount			=	0;
							if( $_produit[ 'DISCOUNT_TYPE' ] == 'percentage' ) {
								$discount_amount		=	__floatval( ( ( floatval( $_produit[ 'PRIX' ] ) * intval( $_produit[ 'QUANTITE' ] ) ) * floatval( $_produit[ 'DISCOUNT_PERCENT' ] ) ) / 100 );
								echo $this->Nexo_Misc->cmoney_format( __floatval( $discount_amount ) );
							} else if( $_produit[ 'DISCOUNT_TYPE' ] == 'flat' ) {
								$discount_amount		=	floatval( $_produit[ 'DISCOUNT_AMOUNT' ] );
								echo $this->Nexo_Misc->cmoney_format( __floatval( $discount_amount ) );
							}

							$total_discount			+=	$discount_amount;

							?> </td>
                            <?php endif;?>
                            <td class="text-right" width="150">
								<?php echo $this->Nexo_Misc->cmoney_format( __floatval( $_produit[ 'PRIX_TOTAL' ] ) );?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td class="text-left"><?php echo $total_quantite;?></td>

                            <td class="text-right">
                            <?php /*echo sprintf(
                                __( '%s %s %s', 'nexo' ),
                                $this->Nexo_Misc->display_currency( 'before' ),
                                __floatval( $total_unitaire ),
                                $this->Nexo_Misc->display_currency( 'after' )
                            )*/;?>
                            </td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="" style="text-align: right"><?php echo $this->Nexo_Misc->cmoney_format( __floatval( $total_discount ) );?></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($total_global)
                            );?>
                            </td>
                        </tr>
                        <?php if (__floatval($_produit[ 'RISTOURNE' ])):?>
                        <tr>
                            <td class=""><?php _e('Remise automatique', 'nexo');?></td>
                            <td class="text-right">(-)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
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
                            <td class="text-right">(-)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($_produit[ 'REMISE' ])
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( $_produit[ 'REMISE_TYPE' ] == 'percentage' ):?>
                        <tr>
                            <td class=""><?php _e('Remise (%)', 'nexo');?></td>
                            <td class="text-right">(-)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                nexoCartPercentageDiscount( $order[ 'products' ], $_produit )
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( $order[ 'order' ][0][ 'GROUP_DISCOUNT' ] != '0' ):?>
                        <tr>
                            <td class=""><?php _e('Remise de groupe', 'nexo');?></td>
                            <td class="text-right">(-)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval( $order[ 'order' ][0][ 'GROUP_DISCOUNT' ] )
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (@$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui'):?>
                        <tr>
                            <td class=""><?php _e('Net Hors Taxe', 'nexo');?></td>
                            <td class="" style="text-align: right">(=)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
							bcsub(
								__floatval($total_global),
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
                            <td class="" style="text-align: right">(+)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                $_produit[ 'TVA' ]
                            );?>
                            </td>
                        </tr>
                        <tr>
                            <td class=""><strong><?php _e('TTC', 'nexo');?></strong></td>
                            <td class="" style="text-align: right">(=)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                bcsub(
                                    __floatval($total_global) + __floatval($_produit[ 'TVA' ]),
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
                            <td class="" style="text-align: right">(=)</td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                bcsub(
                                    __floatval($total_global) + __floatval($_produit[ 'TVA' ]),
                                    (
                                        __floatval(@$_produit[ 'RISTOURNE' ]) +
                                        __floatval(@$_produit[ 'RABAIS' ]) +
                                        __floatval(@$_produit[ 'REMISE' ]) +
										__floatval(@$_produit[ 'GROUP_DISCOUNT' ])
                                    ), 2
                                ),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php
                        $order_payments         =   $this->Nexo_Misc->order_payments( $order[ 'order' ][0][ 'CODE' ] );
                        $payment_types          =   $this->config->item( 'nexo_payments_types' );
                        foreach( $order_payments as $payment ) {
                            ?>
                            <tr>
                                <td class="">
                                    <?php echo @$payment_types[ $payment[ 'PAYMENT_TYPE' ] ] == null ? __( 'Type de paiement inconnu', 'nexo' ) : @$payment_types[ $payment[ 'PAYMENT_TYPE' ] ]; ?></td>
                                <td class="text-right"></td>
                                <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                                <td class="text-right"></td>
                                <?php endif;?>
                                <td class="text-right">
                                <?php echo $this->Nexo_Misc->cmoney_format( __floatval( $payment[ 'MONTANT' ] ) );?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                        <tr>
                            <td class=""><?php _e('Somme Total Perçue', 'nexo');?></td>
                            <td class="text-right"></td>
                            <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                            <td class="text-right"></td>
                            <?php endif;?>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format( __floatval( $_produit[ 'SOMME_PERCU' ] ) );?>
                            </td>
                        </tr>
                        <?php
                        $terme        =    'nexo_order_comptant'    == $order[ 'order' ][0][ 'TYPE' ] ? __('Solde :', 'nexo') : __('&Agrave; percevoir :', 'nexo');
                        ?>
                        <tr>
                            <td class="text-right" colspan="<?php echo @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ? 3 : 2;?>"><h4><strong><?php echo $terme;?></strong></h4></td>
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
				<p class="text-center"><?php echo xss_clean( $this->parser->parse_string( @$Options[ store_prefix() . 'nexo_bills_notices' ], $template , true ) );?></p>
                <div class="container-fluid hideOnPrint">
                    <div class="row hideOnPrint">
                        <div class="col-lg-12">
                            <a href="<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'lists' ));?>" class="btn btn-success btn-lg btn-block"><?php _e('Revenir à la liste des commandes', 'nexo');?></a>
                        </div>
                    </div>
                </div>
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
<style media="screen, print"    >
@media print {
	* {
		font-family:"Courier New", Courier, monospace;
        font-weight:800;
	}
	.hideOnPrint {
		display:none !important;
	}
	td, th {font-size: 3.5vw;}
	.order-details, p {
		font-size: 3.7vw;
        font-weight: 800;
	}
	.order-details h2 {
		font-size: 7vw;
        font-weight: 800;
	}
	h3 {
		font-size: 3.5vw;
        font-weight: 800;
	}
	h4 {
		font-size: 3.5vw;
        font-weight: 800;
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
