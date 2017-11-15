<?php
global $Route;

$Route->match([ 'get', 'post' ], 'users/{page_id?}', 'UsersController@list_users' )
->where([ 'page_id' => '[0-9]+' ]);

$Route->match([ 'get', 'post' ], 'users/create', 'UsersController@create' );
$Route->match([ 'get', 'post' ], 'users/profile', 'UsersController@profile' );
$Route->match([ 'get', 'post' ], 'users/delete/{id}', 'UsersController@delete' );
$Route->match([ 'get', 'post' ], 'users/edit/{id}', 'UsersController@edit' );
$Route->match([ 'get', 'post' ], 'users/groups', 'UsersController@groups' );