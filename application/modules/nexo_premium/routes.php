<?php
global $Routes;

$Routes->get( '/nexo/reports/daily-sales/{date?}', 'NexoPremiumController@daily' )->where([
    'date'      =>  '(.+)'
]);
$Routes->get( '/nexo/reports/cashiers/{start_date?}/{end_date?}', 'NexoPremiumController@cashiers_report' );
$Routes->get( '/nexo/reports/customers/{start_date?}/{end_date?}', 'NexoPremiumController@customers_report' );
$Routes->get( '/nexo/reports/cash-flow/{date?}', 'NexoPremiumController@cash_flow' );
$Routes->get( '/nexo/reports/sales-stats/{date?}', 'NexoPremiumController@sales_stats' );
$Routes->get( '/nexo/reports/stock-tracking/{shipping?}/{shipping2?}', 'NexoPremiumController@stock_tracking' );
$Routes->get( '/nexo/reports/best-sellers/{items?}/{start_date?}/{end_date?}', 'NexoPremiumController@best_sellers' );
$Routes->get( '/nexo/reports/profit-and-losses/{start_date?}/{end_date?}', 'NexoPremiumController@profit_and_losses' );
$Routes->get( '/nexo/reports/expenses/{start_date?}/{end_date?}', 'NexoPremiumController@expense_listing' );
$Routes->get( '/nexo/reports/detailed-sales/{start_date?}/{end_date?}', 'NexoPremiumController@detailed_sales' );
$Routes->get( '/nexo/invoices', 'NexoPremiumController@invoices' );
$Routes->get( '/nexo/cache-clear/dashboard-card', 'NexoPremiumController@clear_cache' );
$Routes->get( '/nexo/log', 'NexoPremiumController@log' );
$Routes->get( '/nexo/quotes-cleaner', 'NexoPremiumController@quotes_cleaner' );

$Routes->match([ 'get', 'post' ], '/nexo/expenses-categories/{action?}/{id?}', 'NexoPremiumController@expenses_list' );
$Routes->match([ 'get', 'post' ], '/nexo/expenses/{action?}/{id?}', 'NexoPremiumController@invoices' );