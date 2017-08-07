<?php
/**
 * NexoPOS helper
 * ---------------
 *
 * All useful function to help build faster
**/

use Carbon\Carbon;

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
			return sprintf( __( '%s &rsaquo; %s &mdash; %s', 'nexo' ), xss_clean( @$CurrentStore[0][ 'NAME' ] ), $title, store_option( 'site_name', __( 'NexoPOS' ) ) );
		} else {
            global $Options;
			return sprintf( __( '%s &rsaquo; %s', 'nexo' ), @$Options[ 'site_name' ] != null ? $Options[ 'site_name' ] : 'NexoPOS', $title );
		}
	}
}

/**
 * Store Prefix
 * @return string store prefix
**/

if( ! function_exists( 'store_prefix' ) ) {
	function store_prefix( $store_id = null ) {
        if( $store_id == null ) {
            global $store_id;
        }
		$prefix		=	$store_id != null ? 'store_' . $store_id . '_' : '';
		$prefix		=	( $prefix == '' && intval( get_instance()->input->get( 'store_id' ) ) > 0 ) ? 'store_' . get_instance()->input->get( 'store_id' ) . '_' : $prefix;
        $prefix     =   get_instance()->uri->segment( 2 ) == 'stores' && get_instance()->uri->segment( 3 ) != null ? 'store_' . get_instance()->uri->segment( 3 ) . '_' : $prefix;
		return $prefix;
	}
}

/**
 * Store Slug
**/

if( ! function_exists( 'store_slug' ) ) {
	function store_slug( $store_id = null ) {
        if( $store_id == null ) {
            global $store_id;
        }
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
		return $prefix;
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

/**
 *  Cart Gross Value.
 *
 *  @param  object order
 *  @param  object order items
 *  @return float/int
**/

if( ! function_exists( 'nexoCartValue' ) ) {
    function nexoCartGrossValue( $items ) {
        $value      =      0;
        foreach( $items as $item ) {
            if( $item[ 'DISCOUNT_TYPE' ] == 'percentage' ) {
                $percent    =   floatval( $item[ 'DISCOUNT_PERCENT' ] ) * floatval( $item[ 'PRIX' ] ) / 100;
                $discount   =   floatval( $item[ 'PRIX' ] ) - $percent;
                $value      +=  $discount;
            } else if( $item[ 'DISCOUNT_TYPE' ] == 'flat' ) {
                $discount   =   floatval( $item[ 'PRIX' ] ) -  floatval( $item[ 'DISCOUNT_AMOUNT' ] ) ;
                $value      +=  $discount;
            } else {
                $value      +=  floatval( $item[ 'PRIX' ] );
            }
        }
        return $value;
    }
}

/**
 *  Percentage Discount
 *  @param  array  order
 *  @return int/float
**/

if( ! function_exists( 'nexoCartPercentageDiscount' ) ) {
    function nexoCartPercentageDiscount( $items, $order ) {
        return ( nexoCartGrossValue( $items ) * floatval( $order[ 'REMISE_PERCENT' ] ) ) / 100;
    }
}

/**
 * Store Option
 * @param string option
 * @return array/bool/string
**/

if( ! function_exists( 'store_option' ) ) {
    function store_option( $option, $default = null ) {
        return get_option( store_prefix() . $option, $default );
    }
}

/**
 * Date format
 * @param string date
 * @param string format
 * @return string formated date
**/

if( ! function_exists( 'nexo_date_format' ) ) {
    function nexo_date_format( $date, $format = null ) {
        if( $format == null ) {
            $format     =   store_option( 'nexo_date_format', 'Y-m-d' );
        }

        return Carbon::parse( $date )->format( $format );
    }
}

/**
 * UI Notices
**/

if( ! function_exists( 'nexo_notices' ) ) {
    function nexo_notices( $data ) {
        get_instance()->Nexo_Notices->add( $data );
    }
}