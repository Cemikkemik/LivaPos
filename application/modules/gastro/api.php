<?php

$Routes->get( 'gastro/areas', 'ApiAreasController@areas_get' );
$Routes->get( 'gastro/areas/tables', 'ApiAreasController@areas_get' );
$Routes->get( 'gastro/modifiers/by-group/{id}', 'ApiModifiersController@modifiers_by_group_get' );

$Routes->get( 'gastro/tables/orders', 'ApiTablesController@orders' );
$Routes->get( 'gastro/tables/{id?}', 'ApiTablesController@tables_get' );
$Routes->get( 'gastro/tables/area/{id}', 'ApiTablesController@tables_from_area_get' );
$Routes->get( 'gastro/tables/history/{table_id}', 'ApiTablesController@table_order_history_get' );
$Routes->post( 'gastro/tables/serve/', 'ApiTablesController@serve_post' );
$Routes->post( 'gastro/tables/collect/', 'ApiTablesController@collect_meal_post' );

$Routes->put( 'gastro/tables/status/{id}', 'ApiTablesController@table_usage_put' );
$Routes->put( 'gastro/tables/pay-order/{id}', 'ApiTablesController@pay_order_put' );

$Routes->get( 'gastro/kitchens/orders', 'ApiKitchensController@orders' );
$Routes->get( 'gastro/kitchens/ready-orders', 'ApiKitchensController@ready_orders_get' );
$Routes->get( 'gastro/kitchens/google-refresh', 'ApiKitchensController@start_cooking_post' );
$Routes->get( 'gastro/kitchens/print/{order_id}', 'ApiKitchensController@print_to_kitchen_get' );
$Routes->get( 'gastro/kitchens/split-print/{order_id}', 'ApiKitchensController@split_print_get' );
$Routes->post( 'gastro/kitchens/cook', 'ApiKitchensController@start_cooking_post' );
$Routes->post( 'gastro/kitchens/collected-orders', 'ApiKitchensController@order_collected_post' );
$Routes->post( 'gastro/kitchens/food-status', 'ApiKitchensController@food_state_post' );
