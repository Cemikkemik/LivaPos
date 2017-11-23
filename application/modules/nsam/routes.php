<?php
global $Routes;
$Routes->match([ 'get', 'post' ], '/nexo/users/{param?}/{id?}', 'NsamController@users' );
// $Routes->get( '/nexo/access-manager', 'NsamController@users_control' );
$Routes->get( '/nexo/content-manager', 'NsamController@content_management' );
$Routes->get( '/nexo/settings/access-manager', 'NsamController@users_control' );