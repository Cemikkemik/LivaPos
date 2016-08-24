<?php
$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=> 1,
	'namespace'	=>	'restaurant',
	'gui_saver'	=>	false,
	'type'		=>	'unwrapped',
	'title'		=>	__( 'Restaurant Tables', 'nexo_restaurant' )
) );

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'restaurant', 1);

$this->Gui->output();