<?php
global $Route;

$Route->match([ 'get', 'post' ], '/nexo/licence', 'NexoUpdaterLicence@index' )->name( 'nexo.licence' );
$Route->match([ 'get', 'post' ], '/nexo/licence/activated', 'NexoUpdaterLicence@activated' )->name( 'nexo.licence' );