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
     * @params string permission
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
     * @params string/int item filter
     * @params Array table where to check availability with this for array( array( 'col'=> 'id', 'table'	=> 'users' ) );
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
