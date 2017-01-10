<?php

// Live time in seconds
$config[ 'dashboard_card_lifetime' ]    =    43200;

/**
 * Interval between each check to
 * quotes order validity
 * @since 2.5.6.3
**/

$config[ 'quotes_check_interval' ]        =    86400;

/**
 * tables to backup
**/

$config[ 'tables_to_backup' ]            =    array( 'nexo_arrivages', 'nexo_articles', 'nexo_categories', 'nexo_clients', 'nexo_clients_groups', 'nexo_commandes', 'nexo_commandes_produits', 'nexo_fournisseurs', 'nexo_historique', 'nexo_premium_factures', 'nexo_rayons' );

/**
 * How many product to display. Best of Items
**/

$config[ 'best_of_items_limit' ]        =    5; // don't increase if your database is huge.

/**
 * Best of Cache lifetime (in seconds)
**/

$config[ 'best_of_cache_lifetime' ]        =    86400; // use 86400  for one day;
