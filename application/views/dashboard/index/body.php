<?php
/**
 * 	File Name 	: 	body.php
 *	Description :	header file for each admin page. include <html> tag and ends at </head> closing tag
 *	Since		:	1.4
**/

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	    =>    'dashboard_index'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->load->view( 'dashboard/index/grid', null, true )
), 'dashboard_index', 1 );


$this->Gui->output();