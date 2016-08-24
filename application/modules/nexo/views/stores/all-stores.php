<?php
$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'namespace'	=>	'store',
	'type'		=>	'unwrapped'
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$this->load->module_view( 'nexo', 'stores/all-stores-dom', $data, true )
), 'store', 1 );


$this->Gui->output();