<?php
$migration_files            =   MODULESPATH . 'nexo/migrate/';
$files 				        =	[];
$versions       =   [];
if ($handle = opendir( $migration_files )) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			$base_name 	=	basename( $migration_files . $entry );
			$files[ substr( $base_name, 0, strlen( $base_name ) - 4 ) ]  	=	$migration_files . $entry;
		}
	}
    
    $keys   =   array_keys( $files );
    usort( $keys, 'version_compare' );
    
	foreach( $keys as $key ) {
	    $versions[ $key ]       =   $files[ $key ];
	}
	
	closedir($handle);
}

return $versions;