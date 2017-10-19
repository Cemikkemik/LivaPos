<?php
/**
 *
 * Title 	:	 Dashboard model
 * Details	:	 Manage dashboard page (creating, ouput)
 *
**/

class Dashboard_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action('load_dashboard', array( $this, '__set_admin_menu' ));
        $this->events->add_action('load_dashboard', array( $this, 'before_session_starts' ));
        // $this->events->add_filter( 'dashboard_home_output', array( $this, '__home_output' ) );
    }


    /**
     * 	Edit Tendoo.php config before session starts
     *
     *	@return	: void
    **/

    public function before_session_starts()
    {
        $this->config->set_item('tendoo_logo_long', '<b>Tend</b>oo');
        $this->config->set_item('tendoo_logo_min', '<img id="tendoo-logo" style="height:30px;" src="' . img_url() . 'logo_minim.png' . '" alt=logo>');
    }

    /**
     * Load Dashboard Menu
     * [New Permission Ready]
     *
     * @return void
    **/

    public function __set_admin_menu()
    {
        $admin_menus[ 'dashboard' ][]    =    array(
            'href'            =>        site_url('dashboard'),
            'icon'            =>        'fa fa-dashboard',
            'title'            =>        __('Dashboard')
        );

        if (User::can('manage_core')) {
            $admin_menus[ 'dashboard' ][]    =    array(
                'href'            =>        site_url(array( 'dashboard', 'update' )),
                'icon'            =>        'fa fa-dashboard',
                'title'            =>        __('Update Center'),
                'notices_nbr'    =>        $this->events->apply_filters('update_center_notice_nbr', 0)
            );

            $admin_menus[ 'dashboard' ][]    =    array(
                'href'            =>        site_url(array( 'dashboard', 'about' )),
                'icon'            =>        'fa fa-dashboard',
                'title'            =>        __('About'),
            );
        }

        if (
            User::can('install_modules') ||
            User::can('update_modules') ||
            User::can('extract_modules') ||
            User::can('delete_modules') ||
            User::can('toggle_modules')
         ) {
            $admin_menus[ 'modules' ][]        =    array(
                'title'            =>        __('Modules'),
                'icon'            =>        'fa fa-puzzle-piece',
                'href'            =>        site_url('dashboard/modules')
            );
        }

        if (
            User::can('create_options') ||
            User::can('read_options') ||
            User::can('edit_options') ||
            User::can('delete_options')
         ) {
            $admin_menus[ 'settings' ][]    =    array(
                'title'            =>        __('Settings'),
                'icon'            =>        'fa fa-cogs',
                'href'            =>        site_url('dashboard/settings')
            );
        }

        foreach (force_array($this->events->apply_filters('admin_menus', $admin_menus)) as $namespace => $menus) {
            foreach ($menus as $menu) {
                Menu::add_admin_menu_core($namespace, $menu);
            }
        }
    }
}
