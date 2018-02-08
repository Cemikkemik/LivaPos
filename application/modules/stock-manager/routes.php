<?php
global $Routes;

$Routes->get( '/nexo/transfert/add', 'Nexo_Stock_Manager_Controller@new_transfert' );
$Routes->get( '/nexo/transfert-invoice/{transfert_id}', 'Nexo_Stock_Manager_Controller@transfert_invoice' );
$Routes->match([ 'get', 'post' ], '/nexo/transfert/{params?}/{id?}', 'Nexo_Stock_Manager_Controller@transfert_history' );
$Routes->get( '/nexo/settings/stock', 'Nexo_Stock_Manager_Controller@settings' );
$Routes->get( '/nexo/stock/receive/{transfert_id}', 'Nexo_Stock_Manager_Controller@receive' );
$Routes->get( '/nexo/stock/cancel/{transfert_id}', 'Nexo_Stock_Manager_Controller@cancel' );
$Routes->get( '/nexo/stock/reject/{transfert_id}', 'Nexo_Stock_Manager_Controller@reject' );