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
<div class="container-fuild">
    <div class="row">
        <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
            <div class="row">
                <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
                    <h2 class="text-center"><?php echo $Options[ 'site_name' ];?></h2>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <address>
                    <?php echo @$Options[ 'nexo_shop_street' ];?> <br>
                    <?php echo @$Options[ 'nexo_shop_pobox' ];?> <br>
                    <abbr><?php _e('Téléphone:', 'nexo');?></abbr> <?php echo @$Options[ 'nexo_shop_phone' ];?>
                    </address>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                	<address>
                    <em><?php echo mdate(__('Date: %d/%m/%Y %h:%i:%s', 'nexo'), strtotime($order[ 'order' ][0][ 'DATE_CREATION' ]));?></em><br>
					<em><?php echo sprintf(__('ID : #%s'),
                    $order[ 'order' ][0][ 'CODE' ]);?></em><br>
					<em><?php echo sprintf(__('Statut de la commande: %s', 'nexo'), $this->Nexo_Checkout->get_order_type($order[ 'order' ][0][ 'TYPE' ]));?></em><br>
                    <em><?php echo sprintf(__('Caissier : %s', 'nexo'), User::pseudo($order[ 'order' ][0][ 'AUTHOR' ]));?></em>
                    </address>
                </div>
            </div>
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
                            $total_unitaire        +=    __floatval($_produit[ 'PRIX' ]);
                            $total_quantite    +=    __floatval($_produit[ 'QUANTITE' ]);
                            ?>
                        <tr>
                            <td class=""><em><?php echo $_produit[ 'DESIGN' ];
                            ?></em></td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->display_currency('before');
                            ?>
							<?php echo __floatval($_produit[ 'PRIX' ]);
                            ?>
                            <?php echo $this->Nexo_Misc->display_currency('after');
                            ?>
                            </td>
                            <td class="" style="text-align: right"> <?php echo $_produit[ 'QUANTITE' ];
                            ?> </td>
                            <td class="text-right">
                            <?php echo $this->Nexo_Misc->display_currency('before');
                            ?>
							<?php echo __floatval($_produit[ 'PRIX_TOTAL' ]);
                            ?>
                            <?php echo $this->Nexo_Misc->display_currency('after');
                            ?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <?php if (in_array(@$Options[ 'nexo_enable_vat' ], array( null, 'non' ))):?>
                        <tr>
                            <td class=""><em><?php _e('Total', 'nexo');?></em></td>

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
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                __floatval($total_global),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php else :?>
                        <tr>
                            <td class=""><em><?php _e('Hors Taxe (HT)', 'nexo');?></em></td>

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
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                __floatval($total_global),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (__floatval($_produit[ 'RISTOURNE' ])):?>
                        <tr>
                            <td class=""><em><?php _e('Remise automatique', 'nexo');?></em></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right">(-)</td>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                __floatval($_produit[ 'RISTOURNE' ]),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (__floatval($_produit[ 'REMISE' ])):?>
                        <tr>
                            <td class=""><em><?php _e('Remise expresse', 'nexo');?></em></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right">(-)</td>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                __floatval($_produit[ 'REMISE' ]),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (@$Options[ 'nexo_enable_vat' ] == 'oui'):?>
                        <tr>
                            <td class=""><em><?php _e('Net Hors Taxe', 'nexo');?></em></td>
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
                                        __floatval(@$_produit[ 'REMISE' ])
                                    ), 2
                                ),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <tr>
                            <td class=""><em><?php _e('TVA', 'nexo');?> (<?php echo @$Options[ 'nexo_vat_percent' ];?>%)</em></td>
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
                            <td class=""><em><strong><?php _e('TTC', 'nexo');?></strong></em></td>
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
                                        __floatval(@$_produit[ 'REMISE' ])
                                    ), 2
                                ),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <tr>
                            <td class=""><em><?php _e('Somme perçu', 'nexo');?></em></td>
                            <td class="" style="text-align: right"> </td>
                            <td class="text-right"></td>
                            <td class="text-right">
                            <?php echo sprintf(
                                __('%s %s %s', 'nexo'),
                                $this->Nexo_Misc->display_currency('before'),
                                __floatval($_produit[ 'SOMME_PERCU' ]),
                                $this->Nexo_Misc->display_currency('after')
                            );?>
                            </td>
                        </tr>
                        <?php
                        $terme        =    'nexo_order_comptant'    == $order[ 'order' ][0][ 'TYPE' ] ? __('Reste :', 'nexo') : __('&Agrave; percevoir :', 'nexo');
                        ?>
                        <tr>
                            <td class="text-right" colspan="3"><h4><strong><?php echo $terme;?></strong></h4></td>
                            <td class="text-right text-danger"><h4><strong>
                            <?php echo $this->Nexo_Misc->display_currency('before');?>

								<?php

                                echo abs(bcsub(
                                    __floatval($order[ 'order' ][0][ 'TOTAL' ]),
                                    __floatval($order[ 'order' ][0][ 'SOMME_PERCU' ]),
                                    2
                                ));

                                // __floatval( $_produit[ 'TVA' ] )
                                ;?>
                            </strong>
                            </h4></td>
                        </tr>
                    </tbody>
                </table>
                <div class="container-fluid hideOnPrint">
                    <div class="row hideOnPrint">
                        <div class="col-lg-12">
                            <a href="<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists' ));?>" class="btn btn-success btn-lg btn-block"><?php _e('Revenir à la liste des commandes', 'nexo');?></a>
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
	.hideOnPrint {
		display:none !important;
	}
}
</style>
</body>
</html>
<?php
if (! $cache->get($order[ 'order' ][0][ 'ID' ]) || @$_GET[ 'refresh' ] == 'true') {
    $cache->save($order[ 'order' ][0][ 'ID' ], ob_get_contents(), 999999999); // long time
}
