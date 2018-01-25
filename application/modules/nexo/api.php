<?php

$Routes->post( 'nexopos/reports/monthly-sales', 'ApiNexoReports@monthly_sales' );
$Routes->get( 'nexopos/full-order/{order_id}', 'ApiNexoOrders@full_order' );
$Routes->get( 'nexopos/orders', 'ApiNexoOrders@orders' );
$Routes->post( 'nexopos/physicals-and-digitals/', 'ApiNexoItems@physicals_and_digitals' );
$Routes->post( 'nexopos/post-grouped', 'ApiNexoItems@post_grouped' );
$Routes->post( 'nexopos/put-grouped/{id}', 'ApiNexoItems@put_grouped' );
