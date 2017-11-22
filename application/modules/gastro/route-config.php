<?php
$getRoutes  =   [];
$crudRoutes     =   [];

$getRoutes[]    =   [ '/gastro/revoke', 'RestaurantController@revoke'];
$getRoutes[]    =   [ '/gastro/callback', 'RestaurantController@callback'];
$getRoutes[]    =   [ '/gastro/settings', 'RestaurantController@settings'];
$getRoutes[]    =   [ '/gastro/templates/{name}', 'RestaurantController@templates'];
$crudRoutes[]   =   [ '/gastro/kitchen-screen/{id?}', 'KitchensController@kitchenScreen' ];
$crudRoutes[]   =   [ '/gastro/kitchens/{param?}/{id?}', 'KitchensController@kitchens' ];
$crudRoutes[]   =   [ '/gastro/waiters-screen/{id?}', 'KitchensController@waiterScreen' ];
$crudRoutes[]   =   [ '/gastro/tables/{param?}/{id?}', 'TablesController@tables' ];
$crudRoutes[]   =   [ '/gastro/areas/{param?}/{id?}', 'AreasController@areas' ];
$crudRoutes[]   =   [ '/gastro/modifiers-groups/{param?}/{id?}', 'ModifiersController@modifiers_groups' ];
$crudRoutes[]   =   [ '/gastro/modifiers/{param?}/{id?}', 'ModifiersController@modifiers' ];