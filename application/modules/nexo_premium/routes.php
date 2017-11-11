<?php
global $Route;

$Route->get( '/nexo/reports/daily-sales/{date?}', 'NexoPremiumController@daily' )->where([
    'date'      =>  '(.+)'
]);
$Route->get( '/nexo/reports/cashiers/{start_date?}/{end_date?}', 'NexoPremiumController@cashiers_report' );
$Route->get( '/nexo/reports/customers/{start_date?}/{end_date?}', 'NexoPremiumController@customers_report' );
$Route->get( '/nexo/reports/cash-flow/{date?}', 'NexoPremiumController@cash_flow' );
$Route->get( '/nexo/reports/sales-stats/{date?}', 'NexoPremiumController@sales_stats' );
$Route->get( '/nexo/reports/stock-tracking/{shipping?}/{shipping2?}', 'NexoPremiumController@stock_tracking' );
$Route->get( '/nexo/reports/best-sellers/{items?}/{start_date?}/{end_date?}', 'NexoPremiumController@best_sellers' );
$Route->get( '/nexo/reports/profit-and-losses/{start_date?}/{end_date?}', 'NexoPremiumController@profit_and_losses' );
$Route->get( '/nexo/reports/expenses/{start_date?}/{end_date?}', 'NexoPremiumController@expense_listing' );
$Route->get( '/nexo/reports/detailed-sales/{start_date?}/{end_date?}', 'NexoPremiumController@detailed_sales' );
$Route->get( '/nexo/invoices', 'NexoPremiumController@invoices' );
$Route->get( '/nexo/clear-cache', 'NexoPremiumController@clear_cache' );
$Route->get( '/nexo/log', 'NexoPremiumController@log' );
$Route->get( '/nexo/quotes-cleaner', 'NexoPremiumController@quotes_cleaner' );