<?php
global $StoreRoutes;

$StoreRoutes->get( '/nexo/settings/stripe', 'Gateway_Controller@stripe_settings' );
$StoreRoutes->get( '/nexo/settings/payments', 'Gateway_Controller@settings' );