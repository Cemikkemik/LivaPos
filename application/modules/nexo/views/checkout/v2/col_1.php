<?php global $Options;?>
<div class="box box-primary direct-chat direct-chat-primary" id="cart-details-wrapper" style="visibility:hidden">
    <div class="box-header with-border" id="cart-header">
        <h3 class="box-title">
            <?php _e( 'Caisse', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-sm btn-primary cart-add-customer"><i class="fa fa-user"></i> <?php _e( 'Ajouter un client', 'nexo' );?></button>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" id="cart-search-wrapper">
        <form action="#" method="post">
            <select data-live-search="true" name="customer_id" placeholder="<?php _e( 'Codebarre ou UGS...' );?>" class="form-control customers-list dropdown-bootstrap">
                <option value=""><?php _e( 'Sélectionner un client', 'nexo' );?></option>
            </select>
        </form>
    </div>
    <!-- /.box-footer--> 
    <!-- /.box-header -->
    <div class="box-body">
    	<table class="table" id="cart-item-table-header">
        	<thead>
                <tr class="active">
                    <td width="240" class="text-left"><?php _e( 'Article', 'nexo' );?></td>
                    <td width="140" class="text-center"><?php _e( 'Prix Unitaire', 'nexo' );?></td>
                    <td width="120" class="text-center"><?php _e( 'Quantité', 'nexo' );?></td>
                    <td width="100" class="text-right"><?php _e( 'Prix Total', 'nexo' );?></td>
                </tr>
            </thead>
        </table>
        <div class="direct-chat-messages" id="cart-table-body" style="padding:0px;">
            <table class="table" style="margin-bottom:0;">                
                <tbody>
                	<tr id="cart-table-notice">
                    	<td colspan="4"><?php _e( 'Veuillez ajouter un produit...', 'nexo' );?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table" id="cart-details">
            <tfoot>
                <tr class="active">
                    <td width="240" class="text-right"></td>
                    <td width="140" class="text-right"></td>
                    <td width="120" class="text-right">
						<?php 
						if( @$Options[ 'nexo_enable_vat' ] == 'oui' ) {
							_e( 'Net hors taxe', 'nexo' );
						} else {
							_e( 'Total', 'nexo' );
						}
						?>
                    </td>
                    <td width="100" class="text-right"><span id="cart-value"></span></td>
                </tr>
                <?php 
				if( @$Options[ 'nexo_enable_vat' ] == 'oui' ) {
				?>
                <tr class="danger">
                    <td width="240" class="text-right"></td>
                    <td width="140" class="text-right"></td>
                    <td width="120" class="text-right"><?php _e( 'TVA', 'nexo' );?></td>
                    <td width="100" class="text-right"><span id="cart-vat"></span></td>
                </tr>
                <?php
				}
				?>
                <tr class="active">
                    <td colspan="2" width="400" class="text-right cart-discount-notice-area"></td>
                    <td width="100" class="text-right"><?php _e( 'Remise', 'nexo' );?></td>
                    <td width="100" class="text-right"><span id="cart-discount"></span></td>
                </tr>
                <tr class="success">
                    <td width="240" class="text-right"></td>
                    <td width="140" class="text-right"></td>
                    <td width="120" class="text-right"><strong><?php _e( 'Net à payer', 'nexo' );?></strong></td>
                    <td width="100" class="text-right"><span id="cart-topay"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" id="cart-panel">
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-app btn-default btn-lg" style="margin-bottom:0px;">
			<i class="fa fa-money"></i>
			<?php _e( 'Payer', 'nexo' );?>
            </button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-app btn-default btn-lg" id="cart-discount-button"  style="margin-bottom:0px;">
            	<i class="fa fa-gift"></i>
				<?php _e( 'Remise', 'nexo' );?>
			</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-app btn-default btn-lg"  style="margin-bottom:0px;">
            	<i class="fa fa-remove"></i>
				<?php _e( 'Annuler', 'nexo' );?>
			</button>
          </div>
        </div>
    </div>
    <!-- /.box-footer--> 
</div>
