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
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo sprintf(__('Order ID : %s &mdash; Nexo Shop Receipt', 'nexo'), $order[ 'order' ][0][ 'CODE' ]);?></title>
<link rel="stylesheet" media="all" href="<?php echo css_url('nexo') . '/bootstrap.min.css';?>" />
</head>

<body>
<?php global $Options;?>
<?php if (@$order[ 'order' ][0][ 'CODE' ] != null):?>
<div class="container-fluid">
    <div class="row">
        <div class="well col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row order-details">
                <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
                    <h2 class="text-center"><?php echo $Options[ store_prefix() . 'site_name' ];?></h2>
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="col-md-8"><?php _e('Produits', 'nexo');?></th>
                            <th class="col-md-2 text-right"><?php _e('Prix', 'nexo');?></th>
                            <th class="text-right">#</th>
                            <th class="col-md-2 text-right"><?php _e('Total', 'nexo');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php 
                        $total_global    =    0;
                        $total_unitaire    =    0;
                        $total_quantite    =    0;
                        
                        foreach ($order[ 'products' ] as $_produit) {
                            $total_global        +=    __floatval($_produit[ 'PRIX_TOTAL' ]);
                            $total_unitaire      +=    __floatval($_produit[ 'PRIX' ]);
                            $total_quantite   	 +=    __floatval($_produit[ 'QUANTITE' ]);
                            ?>
                        <tr>
                            <td class=""><?php echo $_produit[ 'DESIGN' ];
                            ?></td>
                            <td class="text-right">
							<?php echo $this->Nexo_Misc->cmoney_format( __floatval($_produit[ 'PRIX' ]) );
                            ?>
                            </td>
                            <td class="" style="text-align: right"> <?php echo $_produit[ 'QUANTITE' ];
                            ?> </td>
                            <td class="text-right">
							<?php echo $this->Nexo_Misc->cmoney_format( __floatval($_produit[ 'PRIX_TOTAL' ]) );
                            ?>
                            </td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr>
                            <td class=""><strong><?php _e('Total', 'nexo');?></strong></td>
                            
                            <td class="text-right">
                            <?php /*echo sprintf( 
                                __( '%s %s %s', 'nexo' ), 
                                $this->Nexo_Misc->display_currency( 'before' ), 
                                __floatval( $total_unitaire ),
                                $this->Nexo_Misc->display_currency( 'after' ) 
                            )*/;?>
                            </td>
                            <td class="" style="text-align: right"><?php echo $total_quantite;?></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($total_global)
                            );?>
                            </td>
                        </tr>
                        <?php if (__floatval($_produit[ 'RISTOURNE' ])):?>
                        <tr>
                            <td class=""><?php _e('Remise automatique', 'nexo');?></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right">(-)</td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($_produit[ 'RISTOURNE' ])
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (__floatval($_produit[ 'REMISE' ])):?>
                        <tr>
                            <td class=""><?php _e('Remise expresse', 'nexo');?></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right">(-)</td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval($_produit[ 'REMISE' ])
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if ( $order[ 'order' ][0][ 'GROUP_DISCOUNT' ] != '0' ):?>
                        <tr>
                            <td class=""><?php _e('Remise de groupe', 'nexo');?></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right">(-)</td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format(
                                __floatval( $order[ 'order' ][0][ 'GROUP_DISCOUNT' ] )
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (@$Options[ 'nexo_enable_vat' ] == 'oui'):?>
                        <tr>
                            <td class=""><?php _e('Net Hors Taxe', 'nexo');?></td>
                            <td class="text-right"></td>
                            <td class="" style="text-align: right">(=)</td>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                bcsub(
                                    __floatval($total_global),
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
                        <tr>
                            <td class=""><?php _e('TVA', 'nexo');?> (<?php echo @$Options[ store_prefix() . 'nexo_vat_percent' ];?>%)</td>
                            <td class="text-right"></td>
                            <td class="" style="text-align: right">(+)</td>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                $_produit[ 'TVA' ],
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <tr>
                            <td class=""><strong><?php _e('TTC', 'nexo');?></strong></td>
                            <td class="text-right"></td>
                            <td class="" style="text-align: right">(=)</td>
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
                        <?php else:?>
                        <tr>
                            <td class=""><strong><?php _e('Net à Payer', 'nexo');?></strong></td>
                            <td class="text-right"></td>
                            <td class="" style="text-align: right">(=)</td>
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
                        <tr>
                            <td class=""><?php _e('Perçu', 'nexo');?></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right"></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->cmoney_format( __floatval( $_produit[ 'SOMME_PERCU' ] ) );?>
                            </td>
                        </tr>
                        <?php
                        $terme        =    'nexo_order_comptant'    == $order[ 'order' ][0][ 'TYPE' ] ? __('Solde :', 'nexo') : __('&Agrave; percevoir :', 'nexo');
                        ?>
                        <tr>
                            <td class="text-right" colspan="3"><h4><strong><?php echo $terme;?></strong></h4></td>
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
				<p class="text-center"><?php echo xss_clean( @$Options[ store_prefix() . 'nexo_bills_notices' ] );?></p>
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
<style>
@media print {
	* {
		font-family:Verdana, Geneva, sans-serif;
	}
	.hideOnPrint {
		display:none !important;
	}	
	td, th {font-size: 3vw;}
	.order-details, p {
		font-size: 2.7vw;
	}
	.order-details h2 {
		font-size: 6vw;
	}
	h3 {
		font-size: 3vw;
	}
	h4 {
		font-size: 3vw;
	}
}
</style>
</body>
</html>
<?php
if (! $cache->get($order[ 'order' ][0][ 'ID' ]) || @$_GET[ 'refresh' ] == 'true') {
    $cache->save($order[ 'order' ][0][ 'ID' ], ob_get_contents(), 999999999); // long time
}
