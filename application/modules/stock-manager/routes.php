<?php
global $Routes;

$Routes->match([ 'get', 'post' ], '/nexo/stock-manager/{params?}/{id?}', 'Nexo_Stock_Manager_Controller@history' );
$Routes->get( '/nexo/stock-manager/settings', 'Nexo_Stock_Manager_Controller@settings' );
$Routes->get( '/nexo/stock-manager/receive/{transfert_id}', 'Nexo_Stock_Manager_Controller@receive' );
$Routes->get( '/nexo/stock-manager/cancel/{transfert_id}', 'Nexo_Stock_Manager_Controller@cancel' );
$Routes->get( '/nexo/stock-manager/reject/{transfert_id}', 'Nexo_Stock_Manager_Controller@reject' );