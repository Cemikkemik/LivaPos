<li class="dropdown notifications-menu nexo-notifications" ng-controller="nexoNotificationCTRL">
    <a href="#" ng-click="toggleMenu()">
        <i class="fa fa-flag-o"></i>
        <!--<img style="height:30px; margin-bottom:-15px;margin-top:-15px;" src="<?php echo module_url( 'nexo' ) . '/images/nexopos-logo.png';?>" alt="logo"/>-->
        <span ng-show="notices.length > 0" class="label label-warning">{{ notices.length }}</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">{{ '<?php echo _s( 'Vous avez %s notification(s)', 'nexo' );?>'.replace( '%s', notices.length ) }}</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <li ng-repeat="notice in notices">
                    <a ng-href="{{ notice.LINK }}" style="white-space:inherit">
                        <i class="{{ notice.ICON }} text-aqua"></i> <span ng-bind-html="notice.MESSAGE"></span>
                        <span ng-click="delete( notice.ID )" onClick="return false" class="btn btn-primary btn-xs pull-right"><i class="fa fa-times"></i></span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    </li>