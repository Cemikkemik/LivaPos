<?php

$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=>	1,
	'namespace'	=>	'register_for_cashiers',
	'type'		=>	'unwrapped'
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$this->load->module_view( 'nexo', 'registers/for_cashiers_dom', array(), true )
), 'register_for_cashiers', 1 );

$this->Gui->output();