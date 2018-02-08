<?php
global $StoreRoutes;

$StoreRoutes->get( '/nexo/transfert/add', 'Nexo_Stock_Manager_Controller@new_transfert' );
$StoreRoutes->get( '/nexo/transfert/invoice/{id}', 'Nexo_Stock_Manager_Controller@transfert_invoice' );
$StoreRoutes->get( '/nexo/transfert/receive/{id}', 'Nexo_Stock_Manager_Controller@receive' );
$StoreRoutes->get( '/nexo/transfert/cancel/{id}', 'Nexo_Stock_Manager_Controller@cancel' );
$StoreRoutes->get( '/nexo/transfert/reject/{id}', 'Nexo_Stock_Manager_Controller@reject' );
$StoreRoutes->match([ 'get', 'post' ], '/nexo/transfert/{params?}/{id?}', 'Nexo_Stock_Manager_Controller@transfert_history' );
$StoreRoutes->get( '/nexo/settings/stock', 'Nexo_Stock_Manager_Controller@settings' );