<?php
use Pecee\Http\Request;
class PermissionsController extends Tendoo_Module
{
    public function post()
    {
        $request_body   =   file_get_contents('php://input');
        $data           =   json_decode( $request_body, true );
        
        if( @$data[ 'permissions' ] ) {
            foreach( $data[ 'permissions' ] as $group_name => $permissions ) {
                foreach( $permissions as $permission => $allowed ) {
                    if( $allowed ) {
                        $this->auth->allow_group( $group_name, $permission );
                    } else {
                        $this->auth->deny_group( $group_name, $permission );
                    }
                }
            }
        }
        return response()->json([
            'status'        =>  'success',
            'message'       =>  'permissions updated'
        ]);
    }
}