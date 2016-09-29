<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'namespace'    =>    'registers',
    'type'        =>    'unwrapped',
    'col_id'    =>    1,
    'title'        =>    __('Caisses Enregistreuse', 'nexo')
));

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'registers', 1);

$this->Gui->output();
