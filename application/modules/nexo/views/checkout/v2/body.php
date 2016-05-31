<?php
$this->Gui->col_width( 1, 2.25 );

$this->Gui->col_width( 2, 1.5 );

$this->Gui->add_meta( array(
	'type'	=>	'unwrapped',
	'col_id'	=>	1,
	'namespace'	=>	'checkout_v2_col1'
) );

$this->Gui->add_meta( array(
	'type'	=>	'unwrapped',
	'col_id'	=>	2,
	'namespace'	=>	'checkout_v2_col2'
) );

$this->Gui->add_item( array(
	'type'	=>	'dom',
	'content' => $this->load->module_view( 'nexo', 'checkout/v2/col_2', array(), true )
), 'checkout_v2_col1', 1 );

$this->Gui->add_item( array(
	'type'	=>	'dom',
	'content' => $this->load->module_view( 'nexo', 'checkout/v2/col_1', array(), true )
), 'checkout_v2_col2', 2 );

$this->Gui->add_item( array(
	'type'	=>	'dom',
	'content' => $this->load->module_view( 'nexo', 'checkout/v2/script', array(), true )
), 'checkout_v2_col2', 2 );

$this->Gui->output();