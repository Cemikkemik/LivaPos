<?php
/**
 * Starting Cache
 * Cache should be manually restarted
**/

global $Options;

$pp_row_limit    =    $pp_row;

if (! $products_labels = $cache->get($shipping_id) || @$_GET[ 'refresh' ] == 'true') {
    ob_start();
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo sprintf(__('Etiquettes des produits : %s &mdash; Nexo POS', 'nexo'), $shipping_id);?></title>
<link rel="stylesheet" media="all" href="<?php echo module_url('nexo') . '/bower_components/bootstrap/dist/css/bootstrap.min.css';?>" />
</head>

<body>
	<div class="container-fluid">
    	<div class="hideOnPrint">
            <br>
            <form action="">
                <div class="input-group">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><?php _e('Circonscrire les articles', 'nexo');?></button>
                  </span>
                  <input name="products_ids" type="text" class="form-control" placeholder="<?php _e('Spécifier les identifiants des produits', 'nexo');?>">
                  <input type="hidden" name="refresh" value="true">
                </div><!-- /input-group -->
            </form>
            <br>
            <div class="btn-group" role="group" aria-label="...">
                <a href="<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'arrivages', 'lists' ));?>" class="btn btn-default">
	                <?php _e('Revenir à la liste des collections', 'nexo');?>
                </a>
                <a href="<?php echo current_url() . '?refresh=true';?>" class="btn btn-default">
	                <?php _e('Désactiver le cache', 'nexo');?>
                </a>
            </div>
            <br><br>
        </div>
		<table class="table table-bordered">
        	<thead>
				<tr>
                	<td colspan="<?php echo $pp_row_limit;?>"><?php _e('Etiquettes des produits', 'nexo');?></td>
                </tr>
            </thead>
            <tbody>
            <?php
            if (count($products) > 0) {

				$this->load->model( 'Nexo_Categories' );

                $start        =    1;
                foreach ($products as $product) {
                    $shipping        =    $this->Nexo_Shipping->get_shipping($shipping_id, 'as_id');
					$category		=	get_instance()->Nexo_Categories->get( $product[ 'REF_CATEGORIE' ], 'as_id' );

                    // Parcours des produits restants
                    for ($i = 0; $i < intval( $this->input->get( 'per_item' ) ? $this->input->get( 'per_item' ) : $product[ 'QUANTITE' ] ) ; $i++) { // $product[ 'QUANTITE_RESTANTE' ]
                        // Balise d'ouverture
                        if ($start == 0) {
                            echo '<tr>';
                        }
                        ?>
					<td class="text-center" style="width:<?php echo 100 / $pp_row_limit;?>%;float:left;padding:0;">
                    	<!-- <h4 class="text-center" style="margin:3px 0;"><?php echo $Options[ 'site_name' ];
                        ?></h4> -->
                    	<h4 class="text-center" style="margin:10px;padding:0px;"><?php echo $product[ 'DESIGN' ];?></h4>
                        <img style="display:inline-block;margin:2.5%;width:95%;height:50px;" src="<?php echo upload_url() . '/codebar/' . $product[ 'CODEBAR' ] . '.jpg';
                        ?>">

                        <p class="text-center" style="margin:0px;font-size:12px;"><?php echo $product[ 'CODEBAR' ];?></p>

                        <p style="border:solid 1px #CCC;margin-top:0px;padding:5px 10px;position:relative;right:0px;float:right;margin-bottom:0px;border-right:0px;border-bottom:0px;font-size:12px;">
                        <?php echo
						$this->Nexo_Misc->display_currency('before') . ' ' .
						$product[ 'PRIX_DE_VENTE_TTC' ] . ' ' .
						$this->Nexo_Misc->display_currency('after');
                        ?>
						</p>
                        <?php

						$ship_title	=	$shipping[0][ 'TITRE' ];
						$exploded	=	explode( ' ', trim( $ship_title ) );

						foreach( $exploded as $key => $string ) {
							if( $key < count( $exploded ) - 1 ) {
								$exploded[ $key ]	=	substr( $string, 0, 1 );
							}
						}

						$array_string		=	explode( ' ', $ship_title );
						$final_string		=	'';
						foreach( $array_string as $final ) {
                            if( ! empty( $final ) ) {
                                $final_string	.=	ucwords( $final[0] );
                            }
						}
						?>
                        <p style="padding:3px 10px;margin:0px;float:left;">
	                        <strong><?php echo $final_string . '-' . $product[ 'SKU' ];?></strong>
                        </p>
                    </td>
					<?php
                        // Inclusion ou non de la balise de fin
                        if ($start == $pp_row_limit) {
                            echo '</tr>';
                            $start = 1;
                        } else {
                            $start++;
                        }
                    }
                }
            } else {
                ?>
                <tr>
                	<td colspan="<?php echo $pp_row_limit;
                ?>"><?php _e('Aucun produit disponible', 'nexo');
                ?></td>
                </tr>
                <?php

            }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<style>
@media print {
	.hideOnPrint {
		display:none !important;
	}
}
	h3 {
		font-size: 0.8vw;
	}
	h4 {
		font-size: 1vw;
	}
	strong {
		font-size: 0.7vw;
	}
</style>
<?php
if (! $cache->get($shipping_id) || @$_GET[ 'refresh' ] == 'true') {
    $cache->save($shipping_id, ob_get_contents(), 999999999); // long time
}
