<?php
global $StoreRoutes;
$StoreRoutes->match([ 'get', 'post' ], '/nexo/users/{param?}/{id?}', 'NsamController@users' );
// $StoreRoutes->get( '/nexo/access-manager', 'NsamController@users_control' );
$StoreRoutes->get( '/nexo/content-manager', 'NsamController@content_management' );
$StoreRoutes->get( '/nexo/settings/access-manager', 'NsamController@users_control' );