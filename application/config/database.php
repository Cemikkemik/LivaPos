<?php
/**
 * Database configuration for Tendoo CMS
 * -------------------------------------
 * Tendoo Version : 3
**/

defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'tendoo';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = 'tendoo2_';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = 'application/cache/database/';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

if(!defined('DB_PREFIX'))
{
	define('DB_PREFIX',$db['default']['dbprefix']);
}