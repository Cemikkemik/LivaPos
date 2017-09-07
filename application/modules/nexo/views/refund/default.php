<div class="wper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="row order-details">
                    <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
                        <h2 class="text-center"><?php echo store_option( 'site_name' );?></h2>
                    </div>
                    <?php ob_start();?>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <?php echo xss_clean( store_option( 'receipt_col_1' ) );?>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                        <?php echo xss_clean( store_option( 'receipt_col_2' ) );?>
                    </div>
                </div>
                <?php
                $string_to_parse	=	ob_get_clean();
                echo $this->parser->parse_string( $string_to_parse, $template , true );
                ?>
                <div class="row">
                    <div class="text-center">
                        <h3><?php _e('Reçu de remboursement', 'nexo');?></h3>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo __( 'Nom de l\'article', 'nexo' );?></th>
                                <th><?php echo __( 'Etat', 'nexo' );?></th>
                                <th><?php echo __( 'Prix unitaire', 'nexo' );?></th>
                                <th><?php echo __( 'Quantité', 'nexo' );?></th>
                                <th><?php echo __( 'Total', 'nexo' );?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="item in order_items">
                                <td>{{ item.DESIGN }}</td>
                                <td>{{ item.TYPE == "defective" ? "<?php echo _s( 'Défectueux', 'nexo' );?>" : "<?php echo _s( 'En bon état', 'nexo' );?>" }}</td>
                                <td>{{ item.PRIX | moneyFormat  }}</td>
                                <td>{{ item.QUANTITE }}</td>
                                <td>{{ item.PRIX * item.QUANTITE | moneyFormat }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><?php echo __( 'Total', 'nexo' );?></td>
                                <td></td>
                                <td></td>
                                <td>{{ totalQuantity() }}</td>
                                <td>{{ totalAmount() | moneyFormat }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <p class="text-center"><?php echo xss_clean( $this->parser->parse_string( store_option( 'nexo_bills_notices' ), $template , true ) );?></p>
                </div>
                <style>
                * {
                    font-size: 0.96em;
                }
                </style>
            </div>
        </div>
    </div>
</div>
<?php return;?>
<?php
/**
 * Starting Cache
 * Cache should be manually restarted
**/

use Carbon\Carbon;

// if (! $order_cache = $cache->get($order[ 'order' ][0][ 'ID' ]) || @$_GET[ 'refresh' ] == 'true') {
//     ob_start();
// }
?>
<?php if( @$_GET[ 'ignore_header' ] != 'true' ):?>
<!doctype html>
<html ng-app="tendooApp">
<head>
<meta charset="utf-8">
<title><?php echo sprintf(__('Order ID : %s &mdash; Nexo Shop Receipt', 'nexo'), $order[ 'order' ][0][ 'CODE' ]);?></title>
<link rel="stylesheet" media="all" href="<?php echo css_url('nexo') . '/bootstrap.min.css';?>" />

<script type="text/javascript" src="<?php echo js_url() . '../bower_components/angular/angular.min.js';?>"></script>
<script type="text/javascript" src="<?php echo js_url( 'nexo' ) . '../bower_components/numeral/min/numeral.min.js';?>"></script>
<script type="text/javascript" src="<?php echo js_url( 'nexo' ) . 'nexo-api.js';?>"></script>

<script type="text/javascript">
    NexoAPI.CurrencyPosition	=	function( amount ) {
        return '<?php echo addslashes($this->Nexo_Misc->display_currency('before'));
    ?> ' + amount + ' <?php echo addslashes($this->Nexo_Misc->display_currency('after'));
    ?>';
    }

    /**
     * Currency Position + Money Format
    **/

    NexoAPI.DisplayMoney		=	function( amount ) {
        return NexoAPI.CurrencyPosition( NexoAPI.Format( parseFloat( amount ) ) );
    }
</script>
</head>

<body>
<?php endif;?>

<?php global $Options;?>
<?php if (@$order[ 'order' ][0][ 'CODE' ] != null):?>
<div class="container-fluid" ng-controller="refundCTRL">
    <div class="row">
        <div class="well col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row order-details">
                <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
                    <h2 class="text-center"><?php echo store_option( 'site_name' );?></h2>
                </div>
                <?php ob_start();?>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?php echo xss_clean( store_option( 'receipt_col_1' ) );?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                    <?php echo xss_clean( store_option( 'receipt_col_2' ) );?>
                </div>
            </div>
            <?php if( @_GET[ 'is-pdf' ] ):?>
            <br>
            <br>
            <?php endif;?>
            <?php
            $string_to_parse	=	ob_get_clean();
            echo $this->parser->parse_string( $string_to_parse, $template , true );
            ?>
            <div class="row">
                <div class="text-center">
                    <h3><?php _e('Reçu de remboursement', 'nexo');?></h3>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo __( 'Nom de l\'article', 'nexo' );?></th>
                            <th><?php echo __( 'Etat', 'nexo' );?></th>
                            <th><?php echo __( 'Prix unitaire', 'nexo' );?></th>
                            <th><?php echo __( 'Quantité', 'nexo' );?></th>
                            <th><?php echo __( 'Total', 'nexo' );?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="item in items">
                            <td>{{ item.DESIGN }}</td>
                            <td>{{ item.TYPE == "defective" ? "<?php echo _s( 'Défectueux', 'nexo' );?>" : "<?php echo _s( 'En bon état', 'nexo' );?>" }}</td>
                            <td>{{ item.PRIX | moneyFormat }}</td>
                            <td>{{ item.QUANTITE }}</td>
                            <td>{{ item.PRIX * item.QUANTITE | moneyFormat }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><?php echo __( 'Total', 'nexo' );?></td>
                            <td></td>
                            <td></td>
                            <td>{{ totalQuantity() }}</td>
                            <td>{{ totalAmount() | moneyFormat }}</td>
                        </tr>
                    </tfoot>
                </table>
				<p class="text-center"><?php echo xss_clean( $this->parser->parse_string( store_option( 'nexo_bills_notices' ), $template , true ) );?></p>
                <?php if( @_GET[ 'is-pdf' ] == null ):?>
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
	td, th {font-size: 2.0vw;}
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

<?php if( @$_GET[ 'ignore_header' ] != 'true' ):?>
</body>
</html>
<?php endif;?>
<script type="text/javascript">
    var tendooApp   =   angular.module( 'tendooApp', [] );
</script>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script type="text/javascript">
    tendooApp.controller( 'refundCTRL', [ '$scope', function( $scope ){
        $scope.items    =   <?php echo json_encode( $stock );?>;
        $scope.totalQuantity    =   function() {
            var total   =   0;
            angular.forEach( $scope.items, function( value ) {
                total   +=  parseInt( value.QUANTITE );
            });
            return total;
        }

        $scope.totalAmount      =   function(){
            var total   =   0;
            angular.forEach( $scope.items, function( value ) {
                total   +=  ( parseInt( value.QUANTITE ) * parseFloat( value.PRIX ) );
            });
            return total;
        }
    }]);
</script>

<?php
if (! $cache->get($order[ 'order' ][0][ 'ID' ]) || @$_GET[ 'refresh' ] == 'true') {
    $cache->save($order[ 'order' ][0][ 'ID' ], ob_get_contents(), 999999999); // long time
}
