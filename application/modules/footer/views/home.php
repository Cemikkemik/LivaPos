<?php

$this->Gui->col_width( 1, 3 );

$this->Gui->add_meta( array( 
	'col_id'	=>	1,
	'namespace'	=>	'footer_settings',
	'type'		=>	'box',
	'gui_saver'	=>	true,
	'footer'	=>	array(
		'submit'	=>	array(
			'label'	=>	__( 'Save Settings', 'footer' )
		)
	),
	'title'		=>	__( 'Footer Settings', 'footer' )
) );

$this->Gui->add_item( array(
	'type'		=>	'textarea',
	'name'		=>	'footer_content',
	'label'		=>	__( 'Content to display on footer script', 'footer' )
), 'footer_settings', 1 );

$this->Gui->output();