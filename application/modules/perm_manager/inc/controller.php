<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class PermManagerController extends Tendoo_Module 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function mainboard( $notify = NULL)
    {
        if( isset( $notify )){
            if( $notify[ 'type' ] == 'success'){
                $this->notice->push_notice( tendoo_success( __( $notify['message'] ) ) );
            } else if( $notify[ 'type' ] == 'info'){
                $this->notice->push_notice( tendoo_info( __( $notify['message'] ) ) );
            } else if( $notify[ 'type' ] == 'error'){
                $this->notice->push_notice( tendoo_error( __( $notify['message'] ) ) );
            }
        }

        $groups = $this->users->auth->list_groups();
        
        $this->events->add_action( 'dashboard_footer', 
        function() {
            get_instance()->load->module_view( 'perm_manager', 'mainboard_footer' );
        });
        $this->Gui->set_title ( 'Permission Manager' );
        $this->load->module_view('perm_manager', 'mainboard_view', array( 
                'groups'          => $groups
            )
        );
    }
    
    public function add(){
        
        // setting a permission array for select
        $sel_permission = array();
        $permissions = $this->users->auth->list_perms();
        foreach ( force_array ( $permissions ) as $permission ) {
            $sel_permission[ $permission->name ] = $permission->name;
        }
        $groups = $this->users->auth->list_groups();

        // setting a group array for select

        $sel_group = array();
        foreach ( force_array ( $groups ) as $group) {
            $sel_group[ $group->name ] = $group->name;
        }

        $this->Gui->set_title ( 'Add permission' );

        $this->load->module_view('perm_manager', 'add_view', array( 
                'sel_group'       => $sel_group,
                'sel_permission'  => $sel_permission
            )
        );
    }

    public function delete($group, $perm){
        if($this->users->auth->deny_group( $group, $perm )){
            $notify = array( 'type' => 'success','message' => 'Permission retired');
            $this->mainboard( $notify );
        } else {
            $notify = array( 'type' => 'error','message' => 'An unattended event happened');
            $this->mainboard( $notify );
        }
    }

    public function add_permission (){

        exit( $this->input->post());
        
        $group       = $this->input->post('group_par');
        $permission  = $this->input->post('perms_par');
        if( $this->users->auth->is_group_allowed( $permission, $group) ){
            $notify = array( 'type' => 'info','message' => 'The group already have this permission');
            $this->mainboard( $notify );
        } else if($this->users->auth->allow_group( $group, $permission )){
            $notify = array( 'type' => 'success','message' => 'Permission added');
            $this->mainboard( $notify );
        } else {
            $notify = array( 'type' => 'error','message' => 'An unattended event happened');
            $this->mainboard( $notify );
        }
    }

    public function get(){
        
        $data = $this->users->auth->list_groups();
        foreach ( $data as &$d){
            $perms = $this->users->auth->list_perms( $d->id );
            $d->permissions = $perms;
        }
        $data[ 'roles' ] = $data;
        $data[ 'permissions' ] =  $this->users->auth->list_perms();
        echo json_encode( $data );
    }
}