<?php global $Options, $NexoEditScreen;?>
<div class="row">
    <div class="col-lg-8">
        <div class="box box-primary">
            <div class="box-body" id="codebar-wrapper"> </div>
        </div>
        <div class="box box-primary" id="filter-wrapper" style="display:none;">
            <div class="box-header" style="border-bottom:solid 1px #DEDEDE;">
                <?php _e('Articles disponibles', 'nexo');?>
            </div>
            <div class="box-body" style="max-height:400px;overflow-y:scroll;overflow-x: hidden;padding:0px;">
                <div class="row" id="filter-list" style="padding-left:15px;padding-right:15px;"> </div>
            </div>
        </div>
        <div class="box box-primary" id="nexo-cart">
            <div class="box-header">
                <?php _e('Liste des articles', 'nexo');?>
            </div>
            <table class="table Nexo-cart-table" style="font-size:16px;">
                <thead>
                    <tr>
                        <td><?php _e('Nom du produit', 'nexo');?></td>
                        <td><?php _e('Prix Unitaire', 'nexo');?></td>
                        <td><?php _e('Quantité', 'nexo');?></td>
                        <td><?php _e('Prix Total', 'nexo');?></td>
                        <td><?php _e('Opération', 'nexo');?></td>
                    </tr>
                </thead>
                <tbody style="font-size:15px;">
                    <tr class="cart-empty">
                        <td colspan="5"><?php _e('Panier vide.', 'nexo');?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="box box-primary" id="nexo-checkout-details-guide">
            <div class="box-body no-padding">
                <table class="table table-striped" id="cart-price-list">
                    <thead>
                        <tr>
                            <td><h4>
                                    <?php _e('Caisse', 'nexo');?>
                                </h4></td>
                            <td width="150"><h5 id="discount-wrapper"></h5></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr  class="bg-success">
                            <td><h5>
                            <?php if (@$Options[ 'nexo_enable_vat' ] == 'oui'):?>
								<?php _e('Hors Taxe (HT) :', 'nexo');?>
                            <?php else:?>
                            	<?php _e('Total :', 'nexo');?>
                            <?php endif;?>
                                </h5></td>
                            <td><h5> 
                            	<span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('after');?> </span> 
                                <span class="Nexo-cart-total-price pull-right" style="margin-right:10px;">0</span> 
                                <span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('before');?> </span> 
                                </h5></td>
                        </tr>
                        
                        <tr class="bg-danger">
                            <td><h5>
                                    <?php _e('Remise :', 'nexo');?>
                                </h5></td>
                            <td><h5> 
                            	<span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('after');?> </span> 
                                <span class="Nexo-cart-global-charge pull-right" style="margin-right:10px;">0</span> 
                                <span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('before');?> </span> 
                                </h5></td>
                        </tr>
                        
						<?php if (@$Options[ 'nexo_enable_vat' ] == 'oui'):?>
						
                        <tr class="bg-danger">
                            <td><h5>
                                    <?php echo sprintf(__('TVA (%s%%):', 'nexo'), $Options[ 'nexo_vat_percent' ]);?>
                                </h5></td>
                            <td><h5> 
                            	<span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('after');?> </span> 
                                <span class="Nexo-cart-global-vat pull-right" style="margin-right:10px;">0</span> 
                                <span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('before');?> </span> 
                                </h5></td>
                        </tr>
                        
                        <tr class="bg-success">
                            <td><h5><strong>
                                    <?php _e('TTC :', 'nexo');?>
                                </strong></h5></td>
                            <td><h5> 
                            	<span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('after');?> </span> 
                                <span class="Nexo-cart-ttc  pull-right" style="margin-right:10px;">0</span> 
                                <span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('before');?> </span> 
                                </h5></td>
                        </tr>
                        
                        <?php endif;?>
                      
                        
                        <tr class="bg-success">
                            <td><h5>
                                    <?php _e('Perçu :', 'nexo');?>
                                </h5></td>
                            <td><h5> 
                            	<span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('after');?> </span> 
                                <span class="Nexo-cart-received  pull-right" style="margin-right:10px;">0</span> 
                                <span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('before');?> </span> 
                                </h5></td>
                        </tr>
                        
                        <tr class="bg-warning">
                            <td><h5>
                                    <?php _e('Reste :', 'nexo');?>
                                </h5></td>
                            <td><h5> 
                            	<span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('after');?> </span> 
                                <span class="Nexo-cart-left pull-right" style="margin-right:10px;">0</span> 
                                <span class="pull-right" style="margin-right:10px;"><?php echo $this->Nexo_Misc->display_currency('before');?> </span> 
                                </h5></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-primary" id="checkout">
                    	<?php _e('Payer', 'nexo');?>
                    </button>
                    <?php global $order_id;?>
                <?php if ($order_id) :?>
	                <a class="btn btn-info" href="<?php echo site_url(array( 'dashboard', 'nexo', 'print', 'order_receipt', $order_id . '?refresh=true' ));?>"><?php _e('Imprimer', 'nexo');?></a>
                <?php endif;?>
                <a class="btn btn-success" href="<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists' ));?>"><?php _e('Retour', 'nexo');?></a>
                </div>
            </div>
        </div>
    </div>
</div>
