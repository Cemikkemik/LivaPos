<?php
$getRoutes          =   [];
$getRoutes[]        =   [ 'nexo/about', 'NexoAboutController@index' ];
$getRoutes[]        =   [ 'nexo/customers', 'NexoCustomersController@lists' ];
$getRoutes[]        =   [ 'nexo/customers/add', 'NexoCustomersController@add' ];
$getRoutes[]        =   [ 'nexo/customers/edit/{id}', 'NexoCustomersController@edit' ];
$getRoutes[]        =   [ 'nexo/customers/delete/{id}', 'NexoCustomersController@lists' ];
$getRoutes[]        =   [ 'nexo/items/import/', 'NexoImportController@items' ];
$getRoutes[]        =   [ 'nexo/items/history/{barcode}', 'NexoItemsController@history' ];
$getRoutes[]        =   [ 'nexo/items/supply-history/{barcode}', 'NexoItemsController@supply' ];
$getRoutes[]        =   [ 'nexo/items-stock-adjustment/', 'NexoItemsController@stock_supply' ];
$getRoutes[]        =   [ 'nexo/grouped-items/add', 'NexoItemsController@grouped_items' ];
$getRoutes[]        =   [ 'nexo/grouped-items/edit/{id}', 'NexoItemsController@grouped_items' ];
$getRoutes[]        =   [ 'nexo/settings/home', 'NexoSettingsController@home' ];
$getRoutes[]        =   [ 'nexo/settings/checkout', 'NexoSettingsController@checkout' ];
$getRoutes[]        =   [ 'nexo/settings/items', 'NexoSettingsController@items' ];
$getRoutes[]        =   [ 'nexo/settings/customers', 'NexoSettingsController@customers' ];
$getRoutes[]        =   [ 'nexo/settings/email', 'NexoSettingsController@email' ];
$getRoutes[]        =   [ 'nexo/settings/payments-gateways', 'NexoSettingsController@payments' ];
$getRoutes[]        =   [ 'nexo/settings/reset', 'NexoSettingsController@reset' ];
$getRoutes[]        =   [ 'nexo/settings/invoices', 'NexoSettingsController@invoices' ];
$getRoutes[]        =   [ 'nexo/settings/keyboard', 'NexoSettingsController@keyboard' ];
$getRoutes[]        =   [ 'nexo/settings/providers', 'NexoSettingsController@providers' ];
$getRoutes[]        =   [ 'nexo/settings/orders', 'NexoSettingsController@orders' ];
$getRoutes[]        =   [ 'nexo/settings/stores', 'NexoSettingsController@stores' ];
$getRoutes[]        =   [ 'nexo/settings/stripe', 'NexoSettingsController@stripe' ];
$getRoutes[]        =   [ 'nexo/settings', 'NexoSettingsController@home' ];
$getRoutes[]        =   [ 'nexo/stores', 'NexoStoreController@lists' ];
$getRoutes[]        =   [ 'nexo/stores/add', 'NexoStoreController@add' ];
$getRoutes[]        =   [ 'nexo/stores/all', 'NexoStoreController@all' ];
$getRoutes[]        =   [ 'nexo/stores/{param}/{id?}', 'NexoStoreController@lists' ];
$getRoutes[]        =   [ 'nexo/supplies/', 'NexoSuppliesController@lists' ];
$getRoutes[]        =   [ 'nexo/supplies/add', 'NexoItemsController@add_supply' ];
$getRoutes[]        =   [ 'nexo/supplies/invoice/{shipping_id}', 'NexoSuppliesController@delivery_invoice' ];
$getRoutes[]        =   [ 'nexo/supplies/labels/{shipping_id}', 'NexoPrintController@shipping_item_codebar' ];
$getRoutes[]        =   [ 'nexo/supplies/detailed-worth/{shipping_id}', 'NexoSuppliesController@detailed_worth' ];
$getRoutes[]        =   [ 'nexo/supplies/items/{shipping_id}', 'NexoSuppliesController@delivery_items' ];
$getRoutes[]        =   [ 'nexo/providers', 'NexoProvidersController@lists' ];
$getRoutes[]        =   [ 'nexo/providers/add', 'NexoProvidersController@add' ];
$getRoutes[]        =   [ 'nexo/providers_history/{provider_id}', 'NexoProvidersController@history' ];
$getRoutes[]        =   [ 'nexo/orders', 'NexoOrdersController@lists' ];
$getRoutes[]        =   [ 'nexo/orders/receipt/{order_id}', 'NexoPrintController@order_receipt' ];
$getRoutes[]        =   [ 'nexo/orders/invoice/{order_id}', 'NexoPrintController@invoice' ];
$getRoutes[]        =   [ 'nexo/orders/delete/{order_id}', 'NexoOrdersController@lists' ];
$getRoutes[]        =   [ 'nexo/pos', 'NexoRegistersController@__use' ];
$getRoutes[]        =   [ 'nexo/use/register/{register_id}/{order_id?}', 'NexoRegistersController@__use' ];
$getRoutes[]        =   [ 'nexo/close/register/{register_id}', 'NexoRegistersController@__use' ];
$getRoutes[]        =   [ 'nexo/open/register/{register_id}', 'NexoRegistersController@__use' ];
$getRoutes[]        =   [ 'nexo/register-history/{register_id}', 'NexoRegistersController@__use' ];
$getRoutes[]        =   [ 'nexo/reports/monthly-sales', 'NexoReportsController@journalier' ];
$getRoutes[]        =   [ 'nexo/reset-barcode', 'NexoItemsController@reset_barcode' ];
$getRoutes[]        =   [ 'nexo/generate-barcode/{barcode}/{type?}', 'NexoItemsController@generate_barcode' ];
$getRoutes[]        =   [ 'nexo/resample-barcode/{id}/{barcode}/{type?}', 'NexoItemsController@resample_barcode' ];

$getPost            =   [];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/registers/{action?}/{id?}', 'NexoRegistersController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/providers/{action?}/{id?}', 'NexoProvidersController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/coupons/{action?}/{id?}', 'NexoCouponsController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/supplies/{action?}/{id?}', 'NexoSuppliesController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/supplies/items/{shipping_id}/{action?}/{id?}', 'NexoSuppliesController@delivery_items' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/items/{action?}/{id?}', 'NexoItemsController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/items/supply-history/{barcode}/{action?}/{id?}', 'NexoItemsController@supply' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/categories/{action?}/{id?}', 'NexoCategoriesController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/taxes/{param?}/{id?}', 'NexoTaxesController@index' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/suppliers/{action?}/{id?}', 'NexoProvidersController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/customers/groups/{param?}/{id?}', 'NexoCustomersController@groups' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/customers/{param?}/{id?}', 'NexoCustomersController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/stores/{param}', 'NexoStoreController@lists' ];
$getPost[]          =   [ [ 'get', 'post' ], 'nexo/orders/{action?}/{id?}', 'NexoOrdersController@lists' ];

$postRoutes         =   [];
$postRoutes[]       =   [ 'nexo/customers/{param}', 'NexoCustomersController@lists' ];
$postRoutes[]       =   [ 'nexo/reset', 'NexoResetController@index' ];
$postRoutes[]       =   [ 'nexo/upload_images', 'NexoItemsController@uploadImages' ];