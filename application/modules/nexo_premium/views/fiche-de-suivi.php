<?php

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'col_id'        =>        1,
    'namespace'        =>        'fiche_de_suivi',
    'type'            =>        'unwrapped'
));

$this->Gui->add_item(array(
    'type'            =>    'dom',
    'content'        =>    $this->load->view('../modules/nexo_premium/views/fiche-de-suivi-dom', $data, true)
), 'fiche_de_suivi', 1);

$this->Gui->output();
