<?php
$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=> 1,
	'namespace'	=>	'kitchens',
	'gui_saver'	=>	false,
	'type'		=>	'unwrapped',
	'title'		=>	__( 'Restaurant Kitchens', 'nexo_restaurant' )
) );

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'kitchens', 1);

$this->Gui->output();