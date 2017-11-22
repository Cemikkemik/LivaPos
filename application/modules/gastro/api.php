<?php

$Route->get( 'gastro/areas', 'ApiAreasController@areas_get' );
$Route->get( 'gastro/areas/tables', 'ApiAreasController@areas_get' );
$Route->get( 'gastro/modifiers/by-group/{id}', 'ApiModifiersController@modifiers_by_group_get' );

$Route->get( 'gastro/tables/orders', 'ApiTablesController@orders' );
$Route->get( 'gastro/tables/{id?}', 'ApiTablesController@tables_get' );
$Route->get( 'gastro/tables/area/{id}', 'ApiTablesController@tables_from_area_get' );
$Route->get( 'gastro/tables/history/{table_id}', 'ApiTablesController@table_order_history_get' );
$Route->post( 'gastro/tables/serve/', 'ApiTablesController@serve_post' );
$Route->post( 'gastro/tables/collect/', 'ApiTablesController@collect_meal_post' );

$Route->put( 'gastro/tables/status/{id}', 'ApiTablesController@table_usage_put' );
$Route->put( 'gastro/tables/pay-order/{id}', 'ApiTablesController@pay_order_put' );

$Route->get( 'gastro/kitchens/orders', 'ApiKitchensController@orders' );
$Route->get( 'gastro/kitchens/ready-orders', 'ApiKitchensController@ready_orders_get' );
$Route->get( 'gastro/kitchens/google-refresh', 'ApiKitchensController@start_cooking_post' );
$Route->get( 'gastro/kitchens/print/{order_id}', 'ApiKitchensController@print_to_kitchen_get' );
$Route->get( 'gastro/kitchens/split-print/{order_id}', 'ApiKitchensController@split_print_get' );
$Route->post( 'gastro/kitchens/cook', 'ApiKitchensController@start_cooking_post' );
$Route->post( 'gastro/kitchens/collected-orders', 'ApiKitchensController@order_collected_post' );
$Route->post( 'gastro/kitchens/food-status', 'ApiKitchensController@food_state_post' );
