<?php
class UsersController extends Tendoo_Module
{
    /**
     * Edit user
     * @param int user id
     * @return void
     */
    public function edit( $index )
    {
        // if current user matches user id
        if ($this->users->auth->get_user_id() == $index) {
            redirect(array( 'dashboard', 'users', 'profile' ));
        }

        if (! User::can('edit_users')) {
            return show_error( __( 'Access denied. You\'re not allowed to edit users', 'aauth' ) );
        }

        $user_group            =    farray($this->users->auth->get_user_groups($index));

        // validation rules
        $this->load->library('form_validation');

        $this->form_validation->set_rules('user_email', __('User Email', 'aauth'), 'required|valid_email');
        $this->form_validation->set_rules('password', __('Password', 'aauth'), 'min_length[6]');
        $this->form_validation->set_rules('confirm', __('Confirm', 'aauth'), 'matches[password]');
        $this->form_validation->set_rules('userprivilege', __('User Privilege', 'aauth'), 'required');

        // load custom rules
        $this->events->do_action('user_creation_rules');

        if ($this->form_validation->run()) {

            $exec    =    $this->users->edit(
                $index,
                $this->input->post('user_email'),
                $this->input->post('password'),
                $this->input->post('userprivilege'),
                $user_group,
                $this->input->post( 'confirm' ),
                $mode   =   'edit',
                $this->input->post( 'user_status' )
            );

            $this->notice->push_notice($this->lang->line('user-updated'));
        }

        // User Goup
        $user                   =    $this->users->auth->get_user($index);
        $user_group             =    farray($this->users->auth->get_user_groups($user->id));
        // selecting groups
        $groups                 =    $this->users->auth->list_groups();
        if (! $user) {
            return show_error( __( 'Unknow user. The use you attempted to edit has not been found.', 'aauth' ) );
        }

        $this->events->add_filter( 'gui_page_title', function( $filter ) {
            $filter     =  '<section class="content-header">
              <h1 class="no-margin">
                    ' . str_replace('&mdash; ' . get('core_signature'), '', Html::get_title()) . '<small></small>
                    <a class="btn btn-primary btn-sm pull-right ng-binding" href="' . site_url([ 'dashboard', 'users' ] ) . '">' . __( 'Return to the list', 'aauth' ) . '</a>
              </h1>

            </section>';
            return $filter;
        });

        $this->Gui->set_title(sprintf(__('Edit user &mdash; %s', 'aauth'), get('core_signature')));
        $this->load->mu_module_view( 'aauth', 'users/edit', array(
            'groups'        =>    $groups,
            'user'            =>    $user,
            'user_group'    =>    $user_group
        ));
    }

    /**
     * List users
     * @return response
     */
    public function list_users( $index = 1 )
    {
        if (
            ! User::can('edit_users') &&
            ! User::can('delete_users') &&
            ! User::can('create_users')
        ) {
            return show_error( __( 'Access denied. You\'re not allowed to see this page.', 'aauth' ) );
        }

        $this->load->library('pagination');

        $config['base_url']             =    site_url(array( 'dashboard', 'users', 'list' )) . '/';
        $config['total_rows']           =    $this->users->auth->count_users();
        $config['per_page']             =    30;
        $config['full_tag_open']        =    '<ul ="pagination">';
        $config['full_tag_close']       =    '</ul>';
        $config['next_tag_open']        =    $config['prev_tag_open']    =    $config['num_tag_open']        =    '<li>';
        $config['next_tag_close']       =    $config['prev_tag_close']    =    $config['num_tag_close']    =    '</li>';
        $config['cur_tag_open']         =    '<li ="active"><a href="#">';
        $config['cur_tag_close']        =    '</a></li>';
        $config['num_links']            =     $config['total_rows'];

        $this->pagination->initialize($config);

        $users                          =    $this->users->auth->list_users( false, $index, $config['per_page'], true);

        $this->events->add_filter( 'gui_page_title', function( $filter ) {
            $filter     =  '<section class="content-header">
              <h2 class="no-margin">
                    ' . str_replace('&mdash; ' . get('core_signature'), '', Html::get_title()) . '
                    <small><a class="btn btn-primary btn-sm pull-right ng-binding" href="' . site_url([ 'dashboard', 'users', 'create' ] ) . '">' . __( 'Add A user', 'aauth' ) . '</a></small>
              </h2>

            </section>';
            return $filter;
        });

        $this->Gui->set_title(sprintf(__('Users &mdash; %s', 'aauth'), get('core_signature')));

        $this->load->mu_module_view( 'aauth', 'users/body', array(
            'users'                    =>    $users,
            'pagination'                =>    $this->pagination->create_links()
        ));
    }

    /**
     * Delete users
     * @return redirect
     */

    public function delete( $index )
    {
        if (! User::can('delete_users')) {
            return show_error( __( 'Access denied. You\'re not allowed to see this page.', 'aauth' ) );
        }

        $user    =    $this->users->auth->get_user($index);

        if( User::id() == $user->id ) {
            redirect( array( 'dashboard', 'users?notice=cant-delete-yourself' ) );
        }

        if ($user) {
            $this->users->delete($index);
            redirect(array( 'dashboard', 'users?notice=user-deleted' ));
        }

        return show_error( __( 'Access denied. You\'re not allowed to see this page.', 'aauth' ) );
    }

    /**
     * Open use profile
     * @return void
     */
    public function profile()
    {
        if (! User::can('edit_profile')) {
            return show_error( __( 'Access denied. You\'re not allowed to see this page.', 'aauth' ) );
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('user_email', __('User Email', 'aauth'), 'valid_email');
        $this->form_validation->set_rules('old_pass', __('Old Pass', 'aauth'), 'min_length[6]');
        $this->form_validation->set_rules('password', __('Password', 'aauth'), 'min_length[6]');
        $this->form_validation->set_rules('confirm', __('Confirm', 'aauth'), 'matches[password]');

        // Launch events for user profiles edition rules
        $this->events->do_action('user_profile_rules');

        if ($this->form_validation->run()) {
            $exec    =    $this->users->edit(
                $this->users->auth->get_user_id(),
                $this->input->post('user_email'),
                $this->input->post('password'),
                $this->input->post('userprivilege'),
                null, // user Privilege can't be editer through profile dash
                $this->input->post('old_pass'),
                'profile'
            );

            // var_dump( $exec );die;

            $this->notice->push_notice_array($exec);
        }

        $this->load->library( 'oauthLibrary' );

        $data                   =   array();
        $data[ 'apps' ]         =   $this->oauthlibrary->getUserApp( User::id() );
        $this->Gui->set_title(sprintf(__('My Profile &mdash; %s', 'aauth'), get('core_signature')));

         $this->load->mu_module_view( 'aauth', 'users/profile', $data );
    }

    /**
     * Create user
     * 
     */
    public function create()
    {
        if (! User::can('create_users')) {
            return show_error( __( 'Access denied. You\'re not allowed to see this page.', 'aauth' ) );
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', __('User Name', 'aauth'), 'required|min_length[5]');
        $this->form_validation->set_rules('user_email', __('User Email', 'aauth'), 'required|valid_email');
        $this->form_validation->set_rules('password', __('Password', 'aauth'), 'required|min_length[6]');
        $this->form_validation->set_rules('confirm', __('Confirm', 'aauth'), 'required|matches[password]');
        $this->form_validation->set_rules('userprivilege', __('User Privilege', 'aauth'), 'required');

        // load custom rules
        $this->events->do_action('user_creation_rules');

        if ($this->form_validation->run()) {

            $exec    =    $this->users->create(
                $this->input->post('user_email'),
                $this->input->post('password'),
                $this->input->post('username'),
                $this->input->post('userprivilege'),
                $this->input->post( 'user_status' )
            );

            if ($exec == 'user-created') {
                redirect(array( 'dashboard', 'users?notice=' . $exec ));
                exit;
            }

            if (is_string($exec)) {
                $this->notice->push_notice($this->lang->line($exec));
            }
        }

        // selecting groups
        $groups                =    $this->users->auth->list_groups();

        $this->events->add_filter( 'gui_page_title', function( $filter ) {
            $filter     =  '<section class="content-header">
              <h1 class="no-margin">
                    ' . str_replace('&mdash; ' . get('core_signature'), '', Html::get_title()) . '<small></small>
                    <a class="btn btn-primary btn-sm pull-right ng-binding" href="' . site_url([ 'dashboard', 'users' ] ) . '">' . __( 'Return to the list', 'aauth' ) . '</a>
              </h1>

            </section>';
            return $filter;
        });

        $this->Gui->set_title(sprintf(__('Create a new user &mdash; %s', 'aauth'), get('core_signature')));

        $this->load->mu_module_view( 'aauth', 'users/create', array(
            'groups'    =>    $groups
        ));
    }

    /**
     * Get Groups
     * @return void
     */
    public function groups()
    {
        if (
            ! User::can('create_users') &&
            ! User::can('edit_users') &&
            ! User::can('delete_users')
        ) {
            return show_error( __( 'Access denied. You\'re not allowed to see this page.', 'aauth' ) );
        }

        $groups        =    $this->users->auth->list_groups();

        $this->Gui->set_title(sprintf(__('Roles &mdash; %s', 'aauth'), get('core_signature')));

        $this->load->mu_module_view( 'aauth', 'groups/body', array(
            'groups'    =>    $groups
        ));
    }
}