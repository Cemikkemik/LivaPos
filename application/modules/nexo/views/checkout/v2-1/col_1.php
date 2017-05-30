<?php global $Options;?>
<ul class="nav nav-tabs tab-cart hidden-lg hidden-md"> <!--  -->
    <li ng-click="showPart( 'cart' );" class="{{ cartIsActive }}"><a href="#"><?php echo __( 'Panier', 'nexo' );?></a></li>
    <li ng-click="showPart( 'grid' );" class="{{ gridIsActive }}"><a href="#"><?php echo __( 'Produits', 'nexo' );?></a></a></li>
</ul>
<div class="box box-primary direct-chat direct-chat-primary" id="cart-details-wrapper"> <!-- style="visibility:hidden" -->
    <div class="box-header with-border" id="cart-header">
        <form action="#" method="post">

            <div class="input-group" ng-controller="cartToolBox">
                <select data-live-search="true" name="customer_id" title="<?php _e('Veuillez choisir un client', 'nexo' );?>" class="form-control customers-list dropdown-bootstrap">
                    <option value="">
                        <?php _e('Sélectionner un client', 'nexo');?>
                    </option>
                </select>
                <span class="input-group-btn">
                    <?php if( @$Options[ store_prefix() . 'disable_customer_creation' ] != 'yes' ):?>

                    <button type="button" class="btn btn-default cart-add-customer" title="<?php _e( 'Ajouter un client', 'nexo' );?>">
                        <i class="fa fa-user"></i>
                        <span class="hidden-sm hidden-xs"><?php _e('Ajouter un client', 'nexo');?></span>
                        <span class="hidden-lg hidden-md">+1</span>
                    </button>

                    <?php endif;?>
                    <?php foreach( $this->events->apply_filters( 'nexo_cart_buttons', [
                        // 'delivery'      =>
                        // '<button type="button" class="btn btn-default">
                        // 	<i class="fa fa-truck"></i>
                        // </button>',

                        'history'       =>
                        '<button type="button" ng-click="openHistoryBox()" class="btn btn-default" title="' . _e( 'Charger les commandes en attente', 'nexo' ) . '">
                        	<i class="fa fa-history"></i>
                        </button>'

                    ])  as $button ):;?>
                        <?php echo $button;?>
                    <?php endforeach;?>

                    <?php echo $this->events->apply_filters( 'pos_search_input_after', '' );?>

                </span>
			</div>
        </form>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table" id="cart-item-table-header">
            <thead>
                <tr class="active">
                    <td width="200" class="text-left"><?php _e('Article', 'nexo');?></td>
                    <td width="120" class="text-center"><?php _e('Prix Unitaire', 'nexo');?></td>
                    <td width="100" class="text-center"><?php _e('Quantité', 'nexo');?></td>
                    <?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
                    <td width="90" class="text-center"><?php _e('Remise', 'nexo');?></td>
                    <?php endif;?>
                    <td width="100" class="text-right"><?php _e('Prix Total', 'nexo');?></td>
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
                    <td width="200" class="text-left"><?php echo __( 'Nombre de produits', 'nexo' );?> ( <span class="items-number">0</span> )</td>
                    <td width="150" class="text-right"></td>
                    <td width="120" class="text-right"><?php
                        if (@$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui') {
                            _e('Net hors taxe', 'nexo');
                        } else {
                            _e('Sous Total', 'nexo');
                        }
                        ?></td>
                    <td width="90" class="text-right"><span id="cart-value"></span></td>
                </tr>
                <tr class="active">
                    <td colspan="2" class="text-right cart-discount-notice-area"></td>
                    <td class="text-right"><?php _e('Remise sur le panier', 'nexo');?></td>
                    <td class="text-right"><span id="cart-discount"></span></td>
                </tr>
                <?php
                if (@$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui' && ! empty($Options[ store_prefix() . 'nexo_vat_percent' ])) {
                    ?>
                <tr class="active">
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"><?php echo sprintf(__('TVA (%s%%)', 'nexo'), $Options[ store_prefix() . 'nexo_vat_percent' ]);
                    ?></td>
                    <td class="text-right"><span id="cart-vat"></span></td>
                </tr>
                <?php

                }
                ?>
                <tr class="success">
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"><strong>
                        <?php _e('Net à payer', 'nexo');?>
                        </strong></td>
                    <td class="text-right"><span id="cart-topay"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" id="cart-panel">
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
			<?php echo $this->events->apply_filters( 'before_cart_pay_button', '' );?>
            <div class="btn-group" role="group" ng-controller="payBox">
                <button type="button" class="btn btn-default btn-lg" ng-click="openPayBox()" style="margin-bottom:0px;"> <i class="fa fa-money"></i>
                    <span class="hidden-xs"><?php _e('Payer', 'nexo');?></span>
                </button>
            </div>
            <?php echo $this->events->apply_filters( 'before_cart_save_button', '' );?>
            <div class="btn-group" role="group" ng-controller="saveBox">
                <button type="button" class="hold_btn btn btn-default btn-lg" ng-click="openSaveBox()" style="margin-bottom:0px;"> <i class="fa fa-hand-stop-o"></i>
                    <span class="hidden-xs"><?php _e('En attente', 'nexo');?></span>
                </button>
            </div>
            <?php echo $this->events->apply_filters( 'before_cart_discount_button', '' );?>
            <?php if( @$Options[ store_prefix() . 'hide_discount_button' ] != 'yes' ):?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-lg" id="cart-discount-button"  style="margin-bottom:0px;"> <i class="fa fa-gift"></i>
                    <span class="hidden-xs"><?php _e('Remise', 'nexo');?></span>
                </button>
            </div>
            <?php endif;?>
            <?php echo $this->events->apply_filters( 'before_cart_cancel_button', '' );?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-lg" id="cart-return-to-order"  style="margin-bottom:0px;"> <!-- btn-app  -->
                <i class="fa fa-refresh"></i>
                    <span class="hidden-xs"><?php _e('Annuler', 'nexo');?></span>
                </button>
            </div>
        </div>
    </div>
    <!-- /.box-footer-->
</div>
<?php if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):?>
<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
	'use strict';
	// Close Checkout on page navigation:
	$(window).on('popstate', function() {
		v2Checkout.stripe.handler.close();
	});
</script>
<?php endif;?>
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
