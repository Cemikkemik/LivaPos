<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'		=>    'unwrapped',
    'col_id'	=>    1,
    'namespace'	=>    'grouped_items'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->load->module_view( 'nexo', 'items.grouped-items-dom', null, true )
), 'grouped_items', 1 );

$this->Gui->output();