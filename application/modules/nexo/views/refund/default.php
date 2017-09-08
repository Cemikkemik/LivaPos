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