<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NsamController extends
 Tendoo_Module{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Content Management
     *  @return void
    **/

    public function content_management()
    {
        if( ! is_multistore() ) {
            return show_error( __( 'This feature is only available for multistore.', 'nsam' ) );
        }

        $this->load->model( 'Nexo_Stores' );
        $data[ 'stores' ]     =   $this->Nexo_Stores->get( get_store_id(), 'ID !=' );
        $this->Gui->set_title( __( 'Content Management &mdash; NexoPOS', 'nsam' ) );
        $this->load->module_view( 'nsam', 'content_management', $data );
    }

    /**
     *  module controller
     *  @param void
     *  @return void
    **/

    public function module_control()
    {
        echo 'Hello World';
    }

    /**
     *  Subscription CRUD
     *  @param
     *  @return
    **/

    public function subscriptions()
    {
        echo 'Subscription';
    }

    /**
     *  Users Control
     *  @param
     *  @return
    **/

    public function users_control()
    {
        $this->Gui->set_title( __( 'Users Access Management', 'nsam' ) );
        $this->load->module_view( 'nsam', 'users_management' );
    }

    /**
     * Users Headers
     *
    **/

    public function crud_header( $page )
    {
        if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Users', 'nsam'));
        $crud->set_primary_key( 'user_id', 'aauth_users' );
        $crud->set_table( $this->db->dbprefix( 'aauth_users' ) );
        $this->load->model( 'grocery_CRUD_Model' );
        $this->load->module_model( 'nsam', 'Nsam_Users_model' );
        $crud->set_model( 'Nsam_Users_model' );

        $crud->fields( 'name', 'email', 'pass', 'pass_confirm' );
        $crud->field_description( 'name', __( 'This name will be used during the login by the cashier. This username should be unique.', 'nsam' ) );
        $crud->field_description( 'email', __( 'Alternatively, you can provide an email which will also be used for login and account recovery.', 'nsam' ) );
        $crud->field_description( 'pass', __( 'Make sure to fill a secure password for the cashier.', 'nsam' ) );
        $crud->field_description( 'pass_confirm', __( 'Should be similar to the password.', 'nsam' ) );
        
        $crud->callback_before_insert([ $this, 'before_insert' ]);
        $crud->callback_after_insert([ $this, 'after_insert' ]);

        $crud->callback_before_update([ $this, 'before_update' ]);
        $crud->callback_after_update([ $this, 'after_update' ]);
        $crud->field_type( 'pass', 'password' );
        $crud->field_type( 'pass_confirm', 'password' );
        $crud->required_fields( 'name', 'email', 'pass' );

        // 'USAGE_LIMIT_PER_USER'
		$fields				=	array( 'name', 'email', 'pass', 'pass_confirm' );
        $crud->columns( 'name', 'groups', 'email', 'banned', 'last_login' );
        
        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->unset_jquery();
        $output = $crud->render();
        
        foreach ($output->js_files as $files) {
            $this->enqueue->js(substr($files, 0, -3), '');
        }
        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }
        
        return $output;
    }

    /**
     * Users Lists
     * @return void
    **/

    public function users( $page = 'list' )
    {
        $crud   =   $this->crud_header( $page );
        $this->Gui->set_title( store_title( __( 'Users List', 'nsam' ) ) );
        $this->load->module_view( 'nsam', 'users.list-gui', compact( 'crud' ) );
    }

    /**
     * Before Insert
     * @return data
    **/

    public function before_insert( $post ) 
    {
        // fetch if email is already used
        $user  =   $this->db->where( 'email', $post[ 'email' ])
        ->or_where( 'aauth_users.name', $post[ 'name' ])
        ->get( 'aauth_users' )->result_array();

        if( $user ) {
            if( strlen( $user[0][ 'name' ] ) < 5 ) {
                echo json_encode([
                    'error_fields'     =>  [
                        'name'          =>  __( 'Unable to create user. the username should have more that 5 characters', 'nsam' ),
                    ],
                    'error_message'     =>  __( 'Unable to create user. This username should have more that 5 characters', 'nsam' ),
                    'success'           =>  false
                ]);
                die;
            }

            if( $user[0][ 'name' ] == $post[ 'name' ] ) {
                echo json_encode([
                    'error_fields'     =>  [
                        'name'          =>  __( 'Unable to create user. This username is already taken', 'nsam' ),
                    ],
                    'error_message'     =>  __( 'Unable to create user. This username is already taken', 'nsam' ),
                    'success'           =>  false
                ]);
                die;
            }
            
            echo json_encode([
                'error_fields'     =>  [
                    'email'          =>  __( 'Unable to create user. This email is already taken', 'nsam' ),
                ],
                'error_message'     =>  __( 'Unable to create user. This email is already taken', 'nsam' ),
                'success'           =>  false
            ]);
            die;
        }

        if( ! filter_var( $post[ 'email' ], FILTER_VALIDATE_EMAIL) ) {
            echo json_encode([
                'error_fields'     =>  [
                    'email'          =>  __( 'This email is not a valid email.', 'nsam' ),
                ],
                'success'           =>  false
            ]);
            die;
        }

        if( $post[ 'pass' ] != $this->input->post( 'pass_confirm' ) ) {
            echo json_encode([
                'error_fields'     =>  [
                    'pass'                  =>  __( 'This password don\'t match the confirm password.', 'nsam' ),
                    'pass_confirm'          =>  __( 'This password confirm don\'t match the password.', 'nsam' ),
                ],
                'error_message'     =>  __( 'Unable to create user. This username is already taken', 'nsam' ),
                'success'           =>  false
            ]);
            die;
        }

        // unset password confirm
        unset( $post[ 'pass_confirm' ]);
        return $post;
    }

    /**
     * Before Update
     * @return data
    **/

    public function before_update( $post, $index ) 
    {
        // fetch if email is already used
        $user  =   $this->db->where( 'email', $post[ 'email' ])
        ->where( 'aauth_users.id !=', $index )
        ->where( 'aauth_users.name !=', $post[ 'name' ] )
        ->get( 'aauth_users' )->result_array();

        if( $user ) {
            if( strlen( $user[0][ 'name' ] ) < 5 ) {
                echo json_encode([
                    'error_fields'     =>  [
                        'name'          =>  __( 'Unable to create user. the username should have more that 5 characters', 'nsam' ),
                    ],
                    'error_message'     =>  __( 'Unable to create user. This username should have more that 5 characters', 'nsam' ),
                    'success'           =>  false
                ]);
                die;
            }

            if( $user[0][ 'name' ] == $post[ 'name' ] ) {
                echo json_encode([
                    'error_fields'     =>  [
                        'name'          =>  __( 'Unable to create user. This username is already taken', 'nsam' ),
                    ],
                    'error_message'     =>  __( 'Unable to create user. This username is already taken', 'nsam' ),
                    'success'           =>  false
                ]);
                die;
            }
            
            echo json_encode([
                'error_fields'     =>  [
                    'email'          =>  __( 'Unable to create user. This email is already taken', 'nsam' ),
                ],
                'error_message'     =>  __( 'Unable to create user. This email is already taken', 'nsam' ),
                'success'           =>  false
            ]);
            die;
        }

        if( ! filter_var( $post[ 'email' ], FILTER_VALIDATE_EMAIL) ) {
            echo json_encode([
                'error_fields'     =>  [
                    'email'          =>  __( 'This email is not a valid email.', 'nsam' ),
                ],
                'success'           =>  false
            ]);
            die;
        }

        if( $post[ 'pass' ] != $this->input->post( 'pass_confirm' ) ) {
            echo json_encode([
                'error_fields'     =>  [
                    'pass'                  =>  __( 'This password don\'t match the confirm password.', 'nsam' ),
                    'pass_confirm'          =>  __( 'This password confirm don\'t match the password.', 'nsam' ),
                ],
                'error_message'     =>  __( 'Unable to create user. the password don\'t match the confirm password.', 'nsam' ),
                'success'           =>  false
            ]);
            die;
        }

        // unset password confirm
        unset( $post[ 'pass_confirm' ]);
        return $post;
    }


    public function after_insert( $data, $primary ) 
    {
        // update password
        $this->db->where( 'id', $primary )->update( 'aauth_users',[
            'pass'  =>  $this->hash_password( $data[ 'pass' ], $primary )
        ]);

        // add user to cashier
        $this->auth->add_member( $primary, 'shop_cashier' );

        // assign user to current store on options
        set_option( 'store_access_' . $primary . '_' . get_store_id(), 'yes', true );
    }

    public function after_update( $data, $primary ) 
    {
        // update password
        $this->db->where( 'id', $primary )->update( 'aauth_users',[
            'pass'  =>  $this->hash_password( $data[ 'pass' ], $primary )
        ]);
    }

    private function hash_password($pass, $userid)
    {
        $salt = md5($userid);
        return hash('sha256', $salt.$pass);
    }

}
