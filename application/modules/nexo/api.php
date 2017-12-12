<?php

$Routes->post( 'nexopos/reports/monthly-sales', 'ApiNexoReports@monthly_sales' );
$Routes->get( 'nexopos/full-order/{order_id}', 'ApiNexoOrders@full_order' );
$Routes->get( 'nexopos/orders', 'ApiNexoOrders@orders' );
