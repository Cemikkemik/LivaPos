<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'namespace'    =>    'coupons',
    'type'        =>    'unwrapped',
    'col_id'    =>    1,
    'title'        =>    __('Liste des coupons', 'nexo')
));

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'coupons', 1);

$this->Gui->output();
