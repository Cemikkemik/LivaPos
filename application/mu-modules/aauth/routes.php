<?php
global $Routes;

$Routes->match([ 'get', 'post' ], 'users/{page_id?}', 'UsersController@list_users' )
->where([ 'page_id' => '[0-9]+' ]);

$Routes->match([ 'get', 'post' ], 'users/create', 'UsersController@create' );
$Routes->match([ 'get', 'post' ], 'users/profile', 'UsersController@profile' );
$Routes->match([ 'get', 'post' ], 'users/delete/{id}', 'UsersController@delete' );
$Routes->match([ 'get', 'post' ], 'users/edit/{id}', 'UsersController@edit' );
$Routes->match([ 'get', 'post' ], 'users/groups', 'UsersController@groups' );