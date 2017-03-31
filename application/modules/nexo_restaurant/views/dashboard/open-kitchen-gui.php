<?php
$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=> 1,
	'namespace'	=>	'restaurant',
	'gui_saver'	=>	false,
	'type'		=>	'unwrapped',
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$this->load->module_view( 'nexo_restaurant', 'dashboard/open-kitchen', array(), true )
), 'restaurant', 1 );


$this->Gui->output();