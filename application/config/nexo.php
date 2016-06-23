<?php

/**
 * Host Nexo Configs
**/

$config[ 'discounted_item_background' ]        =    '#DFF0D8';

/**
 * Order Types
**/

$config[ 'nexo_order_types' ]    =    array(
    'nexo_order_comptant'            =>    get_instance()->lang->line('nexo_order_complete'),
    'nexo_order_advance'            =>    get_instance()->lang->line('nexo_order_advance'),
    'nexo_order_devis'                =>    get_instance()->lang->line('nexo_order_quote')
);

/**
 * Discount Type
**/

$config[ 'nexo_discount_type' ]    =    array(
    'disable'        =>    get_instance()->lang->line('disabled'),
    'amount'        =>    get_instance()->lang->line('nexo_flat_discount'),
    'percent'        =>    get_instance()->lang->line('nexo_percentage_discount')
);

/**
 * Nexo True or False dropdown menu
**/

$config[ 'nexo_true_false' ]    =    array(
    'false'                =>    get_instance()->lang->line('no'),
    'true'                =>    get_instance()->lang->line('yes')
);

/**
 * Payment Type
**/

$config[ 'nexo_payment_types' ]    =    array(
    'cash'            =>    get_instance()->lang->line('cash'),
    // 'cheque'		=>	get_instance()->lang->line( 'cheque' ),
    'bank'            =>    get_instance()->lang->line('bank_transfert'),
    'stripe'        =>    get_instance()->lang->line( 'stripe')
);

/**
 * Cart Animation
**/


/**
 * Currency with double 00
**/

$config[ 'nexo_currency_with_double_zero' ]        =    array( 'usd', 'eur' );

/**
 * Test Mode
**/

$config[ 'nexo_test_mode' ]            =    true;

/**
 * Sound Fx
 * Enable fx for 'success', 'info', 'warning', 'bootbox'
**/

$config[ 'nexo_sound_fx' ]		=	array( 'success', 'info', 'warning', 'bootbox' );

/**
 * Items Cache duration
**/

$config[ 'nexo_items_cache_lifetime' ]		=	10800; // 3 hours

/**
 * Widget Cache
**/

$config[ 'nexo_widget_cache_lifetime' ]		=	10800;

/**
 * Feed Max execution time
**/

$config[ 'feed_execution_time' ]  = 20; // seconds

/**
 * Dashboard Profile widget cashe lifetime
**/

$config[ 'profile_widget_cashier_sales_lifetime' ]	=	86400; // one day
