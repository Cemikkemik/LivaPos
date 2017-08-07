<?php

$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=>	1,
	'namespace'	=>	'nexo_taxes',
	'type'		=>	'unwrapped'
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$crud_content->output
), 'nexo_taxes', 1 );

$this->Gui->output();