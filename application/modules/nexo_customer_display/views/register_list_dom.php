<?php
defined('BASEPATH') OR exit('No direct script access allowed');
global $Options;

if( @$Options[ store_prefix() . 'nexo_enable_registers' ] == 'oui' ):
$registers  =   $this->NexoCustomerDisplayModel->registers_list();
?>
<ul class="list-group">
    <?php if ( count( $registers ) > 0 ): ?>
        <?php foreach ($registers as $key => $value): ?>
            <li class="list-group-item"><a href="<?php echo site_url( array( 'dashboard', store_slug(), 'customer-display', 'cd_open', '?register_id=' . $value[ 'ID' ] ) );?>"><?php echo $value[ 'NAME' ]; ?></li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="list-group-item"><?php echo __( 'No register is available or in use', 'nexo_customer_display' );?></li>
    <?php endif; ?>
</ul>
<?php else: ?>
    <ul class="list-group">
      <li class="list-group-item"><a href="<?php echo site_url( array( 'dashboard', store_slug(), 'customer-display', 'cd_open' ) );?>"><?php echo __( 'Default Register', 'nexo_customer_display' );?></a></li>
    </ul>
<?php endif;?>
