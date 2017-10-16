<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu
{
    public static $admin_menus_core    =    array();
    public static function add_admin_menu_core($namespace, $config)
    {
        self::$admin_menus_core[ $namespace ][]    =    $config;
    }
    
    /**
     * Load Menus
     * 
     * @return void
    **/
    
    public static function load()
    {
        $core_menus    =    self::$admin_menus_core;
        foreach ($core_menus as $menu_namespace => $current_menu) {

            // check if user has permission to see that
            if( @$current_menu[ 'permission' ] != null ) {
                if( ! User::can( $current_menu[ 'permission' ] ) ) {
                    continue;
                }
            }

            $menu_status        =    '';
            $custom_ul_style    =    '';
            $custom_style        =    '';
            // Preloop, to check if this menu has an  active child
            $parent_notice_count=    0; // for displaying notice nbr count @since 1.4
            foreach ($current_menu as $_menu) {
                $parent_notice_count +=    riake('notices_nbr', $_menu);
                if (riake('href', $_menu) == current_url()) {
                    $menu_status        =    'active';
                    $custom_ul_style    =    '';//'style="display: block;"';
                }
            }
            $class            =    is_array($current_menu) && count($current_menu) > 1 ? 'treeview' : '';
            $loop_index        =    0;
            ?>
            <li class="<?php echo $class . ' ' . $menu_status . ' namespace-' . $menu_namespace ;
            ?>">
            <?php
            foreach ($current_menu as $menu) {
                if ($class != '') {
                    // If has more than one child

                    $custom_style        =    (riake('href', $menu) == current_url()) ? 'style="color:#fff"' : '';
                    if ($loop_index == 0) {
                        // First child, set a default page and first sub-menu.
                    ?>
                        <a <?php echo $custom_style;?> href="javascript:void(0)" class="<?php echo $menu_status;?>"> 
                            <i class="<?php echo riake('icon', $menu, 'fa fa-star');?>"></i> 
                            <span><?php echo riake('title', $menu);?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                            <?php if ($parent_notice_count > 0):?>
                            <small class="label pull-right bg-yellow"><?php echo $parent_notice_count;?></small>
                            <?php endif;?>
                        </a>
                        <ul <?php echo $custom_ul_style;?> class="treeview-menu">
                            <?php if ( @$menu[ 'disable' ] === false) : // is used to disable menu title showed as first submenu.?>
                            <li> 
                                <a <?php echo $custom_style;?> href="<?php echo @$menu[ 'route' ] ? site_url( 'dashboard' . implode('/', $menu[ 'route' ] ) ) : @$menu[ 'href' ];?>">
                                    <span><?php echo @$menu[ 'title'];?></span>
                                    <?php if ( @$menu[ 'notices_nbr' ] == true):?>
                                    <small class="label pull-right bg-yellow"><?php echo $menu[ 'notices_nbr' ];?></small>
                                    <?php endif;?>                 
                                </a> 
                            </li>	
                            <?php endif;?>
                    <?php

                    } else {
                        // after the first child, all are included as sub-menu
                        ?>
                        <li> 
                            <a <?php echo $custom_style;?> href="<?php echo @$menu[ 'route' ] ? site_url( 'dashboard' . implode('/', $menu[ 'route' ] ) ) : @$menu[ 'href' ];?>">
                                <span><?php echo riake('title', $menu);?></span>
                                <?php if( @$menu[ 'notices_nbr' ] ):?>
                                 <small class="label pull-right bg-yellow"><?php echo riake('notices_nbr', $menu);?></small>
                                <?php endif;?>
                            </a> 
                        </li>	
                        <?php

                    }
                    if ($loop_index == (count($current_menu) - 1)) {
                        // we're at the end of the loop, so we close the "ul"
                        ?>
                        </ul>
                        <?php
                    }
                } else { ?>
                    <a href="<?php echo riake('href', $menu, '#');?>"> 
                        <i class="<?php echo riake('icon', $menu, 'fa fa-star');?>"></i> 
                        <span><?php echo riake('title', $menu);?></span> 
                        <?php if( @$menu[ 'notices_nbr' ] ):?>
                            <small class="label pull-right bg-yellow"><?php echo riake('notices_nbr', $menu);?></small>
                        <?php endif;?>
                    </a>
                    <?php	
                }
                $loop_index++; // increment loop_index
            }
            ?>
            </li>
            <?php

        }
    }

    /**
     * Parse Menu
     */
    public static function parse( $menuHeaders )
    {
        $menusList      =   [];
        foreach( $menuHeaders as &$menus ) {
            foreach( $menus as &$menu ) {
                if( is_array( @$menu[ 'route' ] ) ) {
                    $menu[ 'route' ]    =   site_url( 'dashboard' . implode( '/', $menu[ 'route' ] ) );
                }
                $menusList[]     =   $menu;
            }
        }
        return $menusList;
    }
}
