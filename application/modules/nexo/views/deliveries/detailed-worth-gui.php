<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'		=>    'unwrapped',
    'col_id'	=>    1,
    'namespace'	=>    'detailed_worth'
));

$this->Gui->add_item([
    'type'      =>  'dom',
    'content'   =>  $this->load->module_view( 'nexo', 'deliveries.detailed-worth-dom', null, true )
], 'detailed_worth', 1 );

$this->Gui->output();