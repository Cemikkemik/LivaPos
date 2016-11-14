<?php
/**
 * NexoPOS helper
 * ---------------
 *
 * All useful function to help build faster
**/

if (! function_exists('nexo_permission_check')) {
    /**
     * Permission Tester
     *
     * Check whether for Ajax action an user can perform requested action
     *
     * @param string permission
     * @return void
    **/

    function nexo_permission_check($permission)
    {
        if (! User::can($permission)) {
            echo json_encode(array(
                'error_message'    =>   get_instance()->lang->line('permission-denied'),
                'success'        =>    false
            ));
            die;
        }
    }
}

if (! function_exists('nexo_availability_check')) {

    /**
     * Check Availability of item
     * Item in use can't be deleted
     *
     * @param string/int item filter
     * @param Array table where to check availability with this for array( array( 'col'=> 'id', 'table'	=> 'users' ) );
    **/

    function nexo_availability_check($item, $tables)
    {
        if (is_array($tables)) {
            foreach ($tables as $table) {
                $query    =    get_instance()->db->where(@$table[ 'col' ], $item)->get(@$table[ 'table' ]);
                if ($query->result_array()) {
                    echo json_encode(array(
                        'error_message'    =>   get_instance()->lang->line('cant-delete-used-item'),
                        'success'        =>    false
                    ));
                    die;
                }
            }
        }
    }
}

/**
 * Compare Two value and print arrow
 *
 * @param int
 * @param int
 * @param bool invert ?
 * @return string
**/

if (! function_exists('nexo_compare_card_values')) {
    function nexo_compare_card_values($start, $end, $invert = false)
    {
        if (intval($start) < intval($end)):
            return '<span class="ar-' . ($invert == true ? 'invert-up' : 'down') . '"></span>'; elseif (intval($start) > intval($end)):
            return '<span class="ar-' . ($invert == true ? 'invert-down' : 'up') . '"></span>';
        endif;
        return '';
    }
}

/**
 * Float val for NexoPOS numeric values
 * @param float/int
 * @return float/int
**/

if (! function_exists('__floatval')) {
    function __floatval($val)
    {
        return round(floatval($val), 2);
    }
}

/**
 * Store Name helper
 * @param string page title
 * @return string
**/

if( ! function_exists( 'store_title' ) ) {
	function store_title( $title ) {
		global $CurrentStore;

		if( $CurrentStore != null ) {
			return sprintf( __( '%s &rsaquo; %s &mdash; NexoPOS', 'nexo' ), xss_clean( @$CurrentStore[0][ 'NAME' ] ), $title );
		} else {
			return sprintf( __( 'NexoPOS &rsaquo; %s', 'nexo' ), $title );
		}
	}
}

/**
 * Store Prefix
 * @return string store prefix
**/

if( ! function_exists( 'store_prefix' ) ) {
	function store_prefix() {
		global $store_id;
		$prefix		=	$store_id != null ? 'store_' . $store_id . '_' : '';
		$prefix		=	( $prefix == '' && intval( get_instance()->input->get( 'store_id' ) ) > 0 ) ? 'store_' . get_instance()->input->get( 'store_id' ) . '_' : $prefix;
		return $prefix;
	}
}

/**
 * Store Slug
**/

if( ! function_exists( 'store_slug' ) ) {
	function store_slug() {
		global $store_id;
		return	$store_id != null ? 'stores/' . $store_id : '';
	}
}

/**
 * Get Store Id
**/

if( ! function_exists( 'get_store_id' ) ) {
	function get_store_id() {
		global $store_id;

		if( $store_id != null ) {
			return $store_id;
		} else if( intval( get_instance()->input->get( 'store_id' ) ) > 0 ) {
			return intval( get_instance()->input->get( 'store_id' ) );
		} else {
			return 0;
		}
	}
}

/**
 * Store Upload Path
**/

if( ! function_exists( 'get_store_upload_path' ) ) {
	function get_store_upload_path( $id = null ) {

		global $store_id;

		if( $id != null ) {
			return 'public/upload/store_' . $id;
		}

		if( $store_id != null ) {
			return 'public/upload/store_' . $store_id;
		}

		return 'public/upload';

	}
}

/**
 * Store URL
 * @param int store id
 * @return string store URL
**/

if( ! function_exists( 'get_store_upload_url' ) ) {
	function get_store_upload_url( $id = null ) {

		global $store_id;

		if( $id != null ) {
			return base_url() . 'public/upload/store_' . $id . '/';
		}

		if( $store_id != null ) {
			return base_url() . 'public/upload/store_' . $store_id . '/';
		}

		return base_url() . 'public/upload/';

	}
}

/**
 * Store Get param
**/

if( ! function_exists( 'store_get_param' ) ) {
	function store_get_param( $prefix = '?' ) {

		if( store_prefix() != '' ) {
			return $prefix . 'store_id=' . get_store_id();
		}
		return;
	}
}

/**
 * Is MultiStore
**/

if( ! function_exists( 'is_multistore' ) ) {
	function is_multistore() {

		if( store_prefix() != '' ) {
			return true;
		}
		return false;
	}
}

/**
 * Check Whether a multistore is enabled
**/

if( ! function_exists( 'multistore_enabled' ) ) {
	function multistore_enabled() {

		global $Options;

		if( @$Options[ 'nexo_store' ] == 'enabled' ) {
			return true;
		}
		return false;

	}
}

/**
 * Zero Fill
**/

if( ! function_exists( 'zero_fill' ) ) {
    function zero_fill( $int, $zeros = 3 ) {
        $pr_id = sprintf("%0". $zeros . "d", $int);
        return $pr_id;
    }
}
