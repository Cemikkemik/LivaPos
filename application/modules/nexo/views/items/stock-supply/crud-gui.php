<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'		=>    'unwrapped',
    'col_id'	=>    1,
    'namespace'	=>    'stock_flow'
));

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'stock_flow', 1);

$this->Gui->output();