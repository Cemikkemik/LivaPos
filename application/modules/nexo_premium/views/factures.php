<?php
! defined('APPPATH') ? die() : null;

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'col_id'        =>        1,
    'namespace'        =>        'nexo_premium_facture',
    'type'            =>        'unwrapped',
    'title'            =>    __('Factures', 'nexo_premium')
));

$this->Gui->add_item(array(
    'type'            =>    'dom',
    'content'        =>    $crud_content->output
), 'nexo_premium_facture', 1);

$this->Gui->output();
