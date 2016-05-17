<?php

global $Options;

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'namespace'    =>    'foo',
    'type'        =>    'box',
    'title'        =>    __('Tableau des activités des utilisateurs', 'nexo_premium'),
    'col_id'    =>    1
));

$complete_history    =    array();
// adding user to complete_users array
foreach ($history as $h) {
    $complete_history[]    =    array(
        $h[ 'TITRE' ] ,
        $h[ 'DETAILS' ],
        $h[ 'DATE_CREATION' ]
    );
}

global $Options;

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    @$Options[ 'nexo_premium_enable_history' ] != 'yes' ? tendoo_warning(sprintf(__('L\'historique des activités n\'est pas <a href="%s">activée</a>', 'nexo_premium'), site_url(array( 'dashboard', 'nexo', 'settings' )))) : ''
), 'foo', 1);

$this->Gui->add_item(array(
    'type'        =>    'table',
    'cols'        =>    array( __('Title', 'nexo_premium'), __('Description', 'nexo_premium'), __('Effectué le', 'nexo_premium') ),
    'rows'        =>    $complete_history
), 'foo', 1);



$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $pagination
), 'foo', 1);

$this->Gui->output();
