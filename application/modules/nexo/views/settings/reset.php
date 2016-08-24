<?php
$this->Gui->col_width(1, 2);

$this->Gui->col_width(1, 2);

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_reset',
    'title'            =>        __('Réinitialiser', 'nexo'),
    'col_id'        =>        1,
    'gui_saver'        =>        true,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo')
        )
    ),
    'use_namespace'    =>        false,
));

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $this->load->view('../modules/nexo/views/settings/reset-script', array(), true)
), 'Nexo_reset', 1);

$this->Gui->output();
