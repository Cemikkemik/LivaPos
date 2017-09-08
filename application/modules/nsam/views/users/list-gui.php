<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'         =>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'nsam-users'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $crud->output
), 'nsam-users', 1 );


$this->Gui->output();