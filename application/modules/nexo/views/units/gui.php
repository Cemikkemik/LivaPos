<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
'type'			=>    'unwrapped',
'col_id'		=>    1,
'namespace'	=>    'unit'
));

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	     =>	$crud_content->output
), 'unit', 1 );


$this->Gui->output();