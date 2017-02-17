<?php
$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=>	1, 
	'namespace'	=>	'control_darrivage', 
	'title'		=>	__( 'Contrôle d\'arrivage', 'nexo_advanced_reports' ),
	'type'		=>	'box-primary'
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$this->load->module_view( 'nexo_advanced_reports', 'control_darrivage_dom', array(), true )
), 'control_darrivage', 1 );

$this->Gui->output();