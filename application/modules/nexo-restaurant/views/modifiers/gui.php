<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'		=>    'unwrapped',
    'col_id'	=>    1,
    'namespace'	=>    'modifiers'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $crud->output
), 'modifiers', 1 );

$this->Gui->output();