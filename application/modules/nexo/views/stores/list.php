<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'namespace'    =>    'stores',
    'type'        =>    'unwrapped',
    'col_id'    =>    1,
    'title'        =>    __('Gestion des boutiques', 'nexo')
));

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'stores', 1);

$this->Gui->output();
