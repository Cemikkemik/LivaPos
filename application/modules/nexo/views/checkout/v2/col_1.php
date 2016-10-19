<?php global $Options;?>

<div class="box box-primary direct-chat direct-chat-primary" id="cart-details-wrapper" style="visibility:hidden">
    <div class="box-header with-border" id="cart-header"> 
        <!--<h3 class="box-title">
            <?php _e('Caisse', 'nexo');?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-sm btn-primary cart-add-customer"><i class="fa fa-user"></i> <?php _e('Ajouter un client', 'nexo');?></button>
        </div>-->
        <form action="#" method="post">
            <div class="input-group">
            	<span class="input-group-addon" id="sizing-addon1"><?php echo __( 'Choisir un client', 'nexo' );?></span>
                <select data-live-search="true" name="customer_id" title="<?php _e('Veuillez choisir un client', 'nexo' );?>" class="form-control customers-list dropdown-bootstrap">
                    <option value="">
                    <?php _e('Sélectionner un client', 'nexo');?>
                    </option>
                </select>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default cart-add-customer"><i class="fa fa-user"></i>
                    <?php _e('Ajouter un client', 'nexo');?>
                    </button>
                    <!--<button type="button" class="btn btn-default">
                    	<i class="fa fa-truck"></i>
                    </button>-->
                </span> 
                
			</div>
        </form>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table" id="cart-item-table-header">
            <thead>
                <tr class="active">
                    <td width="210" class="text-left"><?php _e('Article', 'nexo');?></td>
                    <td width="130" class="text-center"><?php _e('Prix Unitaire', 'nexo');?></td>
                    <td width="145" class="text-center"><?php _e('Quantité', 'nexo');?></td>
                    <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                    <td width="80" class="text-center"><?php _e('Remise', 'nexo');?></td>
                    <?php endif;?>
                    <td width="110" class="text-right"><?php _e('Prix Total', 'nexo');?></td>
                </tr>
            </thead>
        </table>
        <div class="direct-chat-messages" id="cart-table-body" style="padding:0px;">
            <table class="table" style="margin-bottom:0;">
                <tbody>
                    <tr id="cart-table-notice">
                        <td colspan="4"><?php _e('Veuillez ajouter un produit...', 'nexo');?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table" id="cart-details">
            <tfoot>
                <tr class="active">
                    <td width="230" class="text-right"></td>
                    <td width="130" class="text-right"></td>
                    <td width="130" class="text-right"><?php 
                        if (@$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui') {
                            _e('Net hors taxe', 'nexo');
                        } else {
                            _e('Sous Total', 'nexo');
                        }
                        ?></td>
                    <td width="110" class="text-right"><span id="cart-value"></span></td>
                </tr>
                <tr class="active">
                    <td colspan="2" width="380" class="text-right cart-discount-notice-area"></td>
                    <td width="160" class="text-right"><?php _e('Remise sur le panier', 'nexo');?></td>
                    <td width="110" class="text-right"><span id="cart-discount"></span></td>
                </tr>
                <?php 
                if (@$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui' && ! empty($Options[ store_prefix() . 'nexo_vat_percent' ])) {
                    ?>
                <tr class="active">
                    <td width="230" class="text-right"></td>
                    <td width="130" class="text-right"></td>
                    <td width="130" class="text-right"><?php echo sprintf(__('TVA (%s%%)', 'nexo'), $Options[ store_prefix() . 'nexo_vat_percent' ]);
                    ?></td>
                    <td width="110" class="text-right"><span id="cart-vat"></span></td>
                </tr>
                <?php

                }
                ?>
                <tr class="success">
                    <td width="230" class="text-right"></td>
                    <td width="130" class="text-right"></td>
                    <td width="130" class="text-right"><strong>
                        <?php _e('Net à payer', 'nexo');?>
                        </strong></td>
                    <td width="110" class="text-right"><span id="cart-topay"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" id="cart-panel">
        <div class="btn-group btn-group-justified" role="group" aria-label="..."> <?php echo $this->events->apply_filters( 'before_cart_pay_button', '' );?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-lg" id="cart-pay-button" style="margin-bottom:0px;"> <i class="fa fa-money"></i>
                <?php _e('Payer', 'nexo');?>
                </button>
            </div>
            <?php echo $this->events->apply_filters( 'before_cart_discount_button', '' );?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-lg" id="cart-discount-button"  style="margin-bottom:0px;"> <i class="fa fa-gift"></i>
                <?php _e('Remise', 'nexo');?>
                </button>
            </div>
            <?php echo $this->events->apply_filters( 'before_cart_cancel_button', '' );?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-lg" id="cart-return-to-order"  style="margin-bottom:0px;"> <!-- btn-app  --> 
                <i class="fa fa-remove"></i>
                <?php _e('Annuler', 'nexo');?>
                </button>
            </div>
        </div>
    </div>
    <!-- /.box-footer--> 
</div>
<style type="text/css">
.expandable {
	width: 19%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    transition-property: width;
	transition-duration: 2s;
}
.item-grid-title {
	width: 19%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    transition-property: width;
	transition-duration: 2s;
}
.item-grid-price {
	width: 19%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    transition-property: width;
	transition-duration: 2s;
}
.expandable:hover{
	overflow: visible; 
    white-space: normal; 
    width: auto;
}
.shop-items:hover {
	background:#FFF;
	cursor:pointer;
	box-shadow:inset 5px 5px 100px #EEE;
}
.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
  -webkit-user-select: none;   /* Chrome/Safari/Opera */
  -khtml-user-select: none;    /* Konqueror */
  -moz-user-select: none;      /* Firefox */
  -ms-user-select: none;       /* Internet Explorer/Edge */
  user-select: none;           /* Non-prefixed version, currently
                                  not supported by any browser */
}
.img-responsive {
    margin: 0 auto;
}
.modal-dialog {
	margin: 10px auto !important;
}

/**
 NexoPOS 2.7.1
**/

#cart-table-body .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
    border-bottom: 1px solid #f4f4f4;
	margin-bottom:-1px;
}
.box {
	border-top: 0px solid #d2d6de;
}
</style>
