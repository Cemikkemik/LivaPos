<?php
global $StoreRoutes;

$StoreRoutes->get( '/nexo/transferts/add', 'Nexo_Stock_Manager_Controller@new_transfert' );
$StoreRoutes->match([ 'get', 'post' ], '/nexo/transfert/{params?}/{id?}', 'Nexo_Stock_Manager_Controller@transfert_history' );
$StoreRoutes->get( '/nexo/settings/stock', 'Nexo_Stock_Manager_Controller@settings' );
$StoreRoutes->get( '/nexo/stock/receive/{transfert_id}', 'Nexo_Stock_Manager_Controller@receive' );
$StoreRoutes->get( '/nexo/stock/cancel/{transfert_id}', 'Nexo_Stock_Manager_Controller@cancel' );
$StoreRoutes->get( '/nexo/stock/reject/{transfert_id}', 'Nexo_Stock_Manager_Controller@reject' );