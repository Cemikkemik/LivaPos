// get table usage
<?php
$this->load->module_model( 'gastro', 'Nexo_Gastro_Tables_Models', 'table_model' );
$table      =   get_instance()->table_model->get_table_used( $order[ 'ORDER_ID' ] );
// var_dump( '' );die;
?>
this.CartGastroType             =   '<?php echo $order[ 'RESTAURANT_ORDER_TYPE' ];?>';
this.CartGastroStatus           =   '<?php echo $order[ 'RESTAURANT_ORDER_STATUS' ];?>'; 
this.CartGastroTable            =   <?php echo json_encode( $table );?>;