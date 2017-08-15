<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	=>    'transfert_history'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $crud->output
), 'transfert_history', 1 );

$this->Gui->output();