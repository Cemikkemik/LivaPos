<?php
$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'namespace'	=>	'nr_package',
	'type'		=>	'unwrapped',
	'col_id'	=>	1
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$this->load->module_view( 'nexo-playground-manager', 'package-management-dom', array(), true )
), 'nr_package', 1 );

$this->Gui->output();
