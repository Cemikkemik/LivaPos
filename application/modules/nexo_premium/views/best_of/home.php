<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'col_id'    =>    1,
    'namespace'    =>    'best_of'
));

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $this->load->module_view('nexo_premium', 'best_of/content', $params, true)
), 'best_of', 1);

$this->Gui->output();
