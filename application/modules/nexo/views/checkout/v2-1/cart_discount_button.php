<?php global $Options;?>
<?php if( @$Options[ store_prefix() . 'hide_discount_button' ] != 'yes' ):?>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-default btn-lg" id="cart-discount-button"  style="margin-bottom:0px;"> <i class="fa fa-gift"></i>
        <span class="hidden-xs"><?php _e('Remise', 'nexo');?></span>
    </button>
</div>
<?php endif;?>