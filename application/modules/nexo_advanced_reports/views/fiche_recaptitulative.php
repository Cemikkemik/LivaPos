<?php

$this->Gui->col_width( 1, 4 );

$this->Gui->add_meta( array(
	'col_id'	=>	1, 
	'namespace'	=>	'control_darrivage', 
	'title'		=>	__( 'Fiche recapitulative', 'nexo_advanced_reports' ),
	'type'		=>	'unwrapped'
) );

$this->Gui->add_item( array(
	'type'		=>	'dom',
	'content'	=>	$this->load->module_view( 'nexo_advanced_reports', 'fiche_recap_dom', array(), true )
), 'control_darrivage', 1 );

$this->Gui->output();