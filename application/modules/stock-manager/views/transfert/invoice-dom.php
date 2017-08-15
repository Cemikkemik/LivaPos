<div class="transfert-report">
    <div class="row">
        <div class="col-xs-12">
            {template}
            <div class="row">
                <div class="col-xs-6">
                    <address>
                    <?php echo store_option( 'transfert_column_1' );?>
                    </address>
                </div>
                <div class="col-xs-6 text-right">
                    <address>
                    <?php echo store_option( 'transfert_column_2' );?>
                    </address>
                </div>
            </div>
            {/template}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong><?php echo __( 'Transfert Summary', 'stock-manager' );?></strong>
                        <span class="pull-right"><?php echo sprintf( __( 'Type : %s', 'stock-manager' ), $transfert[0][ 'TYPE' ] );?></span>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <td><strong><?php echo __( 'Item Name', 'stock-manager' );?></strong></td>
                                    <td class="text-center"><strong><?php echo __( 'Price', 'stock-manager' );?></strong></td>
                                    <td class="text-center"><strong><?php echo __( 'Quantity', 'stock-manager' );?></strong></td>
                                    <td class="text-right"><strong><?php echo __( 'Total', 'stock-manager' );?></strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                <?php
                                $total_unit_price   =   0;
                                $total_quantity     =   0;
                                $total_price        =   0;
                                ?>
                                <?php foreach( ( array ) $items  as $item ):?>
                                <tr>
                                    <td><?php echo $item[ 'DESIGN' ];?></td>
                                    <td class="text-center"><?php echo $this->Nexo_Misc->cmoney_format( @$item[ 'UNIT_PRICE' ] );?></td>
                                    <td class="text-center"><?php echo $item[ 'QUANTITY' ];?></td>
                                    <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format( @$item[ 'TOTAL_PRICE' ] );?></td>
                                </tr>
                                <?php
                                $total_unit_price       +=  floatval( $item[ 'UNIT_PRICE' ] );
                                $total_quantity         +=  floatval( $item[ 'QUANTITY' ] );
                                $total_price            +=  floatval( $item[ 'TOTAL_PRICE' ]);
                                ?>
                                <?php endforeach;?>
                                <tr class="active">
                                    <td></td>
                                    <td class="text-center"><?php echo $this->Nexo_Misc->cmoney_format( $total_unit_price );?></td>
                                    <td class="text-center"><?php echo $total_quantity;?></td>
                                    <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format( $total_price );?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<button class="btn btn-primary hidden-print pull-right" print-item=".transfert-report"><?php echo __( 'Print', 'stock-manager' );?></button>