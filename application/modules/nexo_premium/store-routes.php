<?php
global $StoreRoutes;

$StoreRoutes->get( '/nexo/reports/daily-sales/{date?}', 'NexoPremiumController@daily' )->where([
    'date'      =>  '(.+)'
]);
$StoreRoutes->get( '/nexo/reports/cashiers/{start_date?}/{end_date?}', 'NexoPremiumController@cashiers_report' );
$StoreRoutes->get( '/nexo/reports/customers/{start_date?}/{end_date?}', 'NexoPremiumController@customers_report' );
$StoreRoutes->get( '/nexo/reports/cash-flow/{date?}', 'NexoPremiumController@cash_flow' );
$StoreRoutes->get( '/nexo/reports/sales-stats/{date?}', 'NexoPremiumController@sales_stats' );
$StoreRoutes->get( '/nexo/reports/stock-tracking/{shipping?}/{shipping2?}', 'NexoPremiumController@stock_tracking' );
$StoreRoutes->get( '/nexo/reports/best-sellers/{items?}/{start_date?}/{end_date?}', 'NexoPremiumController@best_sellers' );
$StoreRoutes->get( '/nexo/reports/profit-and-losses/{start_date?}/{end_date?}', 'NexoPremiumController@profit_and_losses' );
$StoreRoutes->get( '/nexo/reports/expenses/{start_date?}/{end_date?}', 'NexoPremiumController@expense_listing' );
$StoreRoutes->get( '/nexo/reports/detailed-sales/{start_date?}/{end_date?}', 'NexoPremiumController@detailed_sales' );
$StoreRoutes->get( '/nexo/invoices', 'NexoPremiumController@invoices' );
$StoreRoutes->get( '/nexo/clear-cache', 'NexoPremiumController@clear_cache' );
$StoreRoutes->get( '/nexo/log', 'NexoPremiumController@log' );
$StoreRoutes->get( '/nexo/quotes-cleaner', 'NexoPremiumController@quotes_cleaner' );