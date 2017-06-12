<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'type'		=>    'unwrapped',
    'col_id'	=>    1,
    'namespace'	=>    'expense_category'
) );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $crud->output
), 'expense_category', 1 );

$this->Gui->output();