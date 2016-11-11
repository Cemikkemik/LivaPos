<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/directives/loading-spinner.php' );?>
<!-- PayBox Feature -->
<?php echo $this->events->do_action( 'load_register_content' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/register/services/serviceKeyboardHandler.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/register/services/serviceNumber.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/register/directives/default-payment.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/register/directives/paybox-content.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/register/directives/keyboard-directive.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/register/controllers/paybox.php' );?>
<!-- Save order feature -->
<?php include_once( MODULESPATH . '/nexo/inc/angular/saveorder/directives/saveorder-content.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/saveorder/controllers/saveorder.php' );?>
<!-- Open order history feature -->
<?php include_once( MODULESPATH . '/nexo/inc/angular/cart-tool-box/filters/history-title-filter.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/cart-tool-box/directives/history-order-list.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/cart-tool-box/directives/history-content.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/cart-tool-box/controllers/cart-tool-box.php' );?>
<!-- Tab feature -->
<?php include_once( MODULESPATH . '/nexo/inc/angular/pos-tabs/controllers/pos-tabs.php' );?>
