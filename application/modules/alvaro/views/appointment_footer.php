<?php
global $Options;
$this->load->config( 'rest' );
?>

<?php $this->load->module_include( 'nexo', 'angular.order-list.include' );?>

<script id="groupedMonthEvents.html" type="text/ng-template">

  <div 
    class="cal-month-day"
    ng-class="{
        'cal-day-outmonth': !day.inMonth,
        'cal-day-inmonth': day.inMonth,
        'cal-day-weekend': day.isWeekend,
        'cal-day-past': day.isPast,
        'cal-day-today': day.isToday,
        'cal-day-future': day.isFuture,
        'cal-day-selected': vm.dateRangeSelect && vm.dateRangeSelect.startDate <= day.date && day.date <= vm.dateRangeSelect.endDate,
        'cal-day-open': dayIndex === vm.openDayIndex
    }"
        >

    <span
      class="pull-right"
      data-cal-date
      ng-click="vm.calendarCtrl.dateClicked(day.date)"
      ng-bind="day.label">
    </span>

    <!--
      *** Please note ***

      The contents of the template have been stripped down for brevity.
      You will need to adapt the original template from here for all functionality to work:
      https://github.com/mattlewis92/angular-bootstrap-calendar/blob/master/src/templates/calendarMonthCell.html

      Insert the div below into the full month view template
    -->

    <div style="position: relative; top: 60px; left: 5px">
      <span ng-repeat="(beautican, events) in day.groups track by beautican">
        <span class="label bg-{{ events[0].getColor( beautican ) }}">
          {{ events.length }}
        </span>&nbsp;
      </span>
    </div>

    <div
        class="cal-day-tick"
        ng-show="dayIndex === vm.openDayIndex && vm.view[vm.openDayIndex].events.length > 0 && !vm.slideBoxDisabled">
        <i class="glyphicon glyphicon-chevron-up"></i>
        <i class="fa fa-chevron-up"></i>
    </div>

  </div>
</script>

<script type="text/ng-template" id="calendarHourList.html">
    <div class="cal-day-panel-hour">
        <div class="cal-day-hour" ng-repeat="hour in vm.hourGrid track by $index">

            <div
            class="cal-day-hour-part"
            ng-repeat="segment in hour.segments track by $index"
            ng-class="[{ 'cal-day-hour-part-selected': vm.dateRangeSelect &&
                        vm.dateRangeSelect.startDate <= segment.date &&
                        segment.date < vm.dateRangeSelect.endDate }, segment.cssClass]"
            ng-click="vm.onTimespanClick({calendarDate: segment.date})"
            mwl-droppable
            on-drop="vm.eventDropped(dropData.event, segment.date)"
            mwl-drag-select="!!vm.onDateRangeSelect"
            on-drag-select-start="vm.onDragSelectStart(segment.date)"
            on-drag-select-move="vm.onDragSelectMove(segment.nextSegmentDate)"
            on-drag-select-end="vm.onDragSelectEnd(segment.nextSegmentDate)"
            ng-if="!vm.dayWidth">
            <div class="cal-day-hour-part-time">
                <strong ng-bind="segment.date | calendarDate:'hour':true" ng-show="segment.isStart"></strong>
            </div>
            </div>

            <div
            class="cal-day-hour-part"
            ng-repeat="segment in hour.segments track by $index"
            ng-if="vm.dayWidth">
                <div class="cal-day-hour-part-time">
                    <strong ng-bind="segment.date | calendarDate:'hour':true" ng-show="segment.isStart"></strong>
                    &nbsp;
                </div>
                <div
                    class="cal-day-hour-part-spacer"
                    ng-repeat="day in segment.days track by $index"
                    ng-style="{width: (vm.dayWidth - ($last ? vm.scrollBarWidth : 0)) + 'px'}"
                    ng-class="[{ 'cal-day-hour-part-selected': vm.dateRangeSelect &&
                            vm.dateRangeSelect.startDate <= day.date &&
                            day.date < vm.dateRangeSelect.endDate }, day.cssClass]"
                    ng-click="vm.onTimespanClick({calendarDate: day.date})"
                    mwl-droppable
                    on-drop="vm.eventDropped(dropData.event, day.date)"
                    mwl-drag-select="!!vm.onDateRangeSelect"
                    on-drag-select-start="vm.onDragSelectStart(day.date)"
                    on-drag-select-move="vm.onDragSelectMove(day.nextSegmentDate)"
                    on-drag-select-end="vm.onDragSelectEnd(day.nextSegmentDate)">
                </div>
            </div>

        </div>

    </div>
</script>

<script type="text/ng-template" id="calendarDayView.html">
    <div class="cal-week-box cal-all-day-events-box" ng-if="vm.allDayEvents.length > 0">
        <div class="cal-day-panel clearfix">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cal-row-fluid">
                        <div class="cal-cell-6" ng-style="{backgroundColor: event.color.secondary}"            data-event-class            ng-repeat="event in vm.allDayEvents track by event.calendarEventId">
                            <strong>
                                <span ng-bind="event.startsAt | calendarDate:'datetime':true"></span>
                                <span ng-if="event.endsAt">
                                    - <span ng-bind="event.endsAt | calendarDate:'datetime':true"></span>
                                </span>
                            </strong>
                            <a              href="javascript:;"              class="event-item"              ng-bind-html="vm.calendarEventTitle.dayView(event) | calendarTrustAsHtml">            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cal-day-box" style="padding-top:30px">
        <div class="cal-day-panel clearfix" 
            ng-style="{height: vm.dayViewHeight + 'px', minWidth: ( ( vm.dayViewEventWidth * vm.events.length ) + 60 )  + 'px'}" 
            >
            <mwl-calendar-hour-list      
                day-view-start="vm.dayViewStart"      
                day-view-end="vm.dayViewEnd"      
                day-view-split="vm.dayViewSplit"      
                on-timespan-click="vm.onTimespanClick"      
                on-date-range-select="vm.onDateRangeSelect"      
                on-event-times-changed="vm.onEventTimesChanged"      
                view-date="vm.viewDate"      
                custom-template-urls="vm.customTemplateUrls"      
                template-scope="vm.templateScope"      
                cell-modifier="vm.cellModifier">    
            </mwl-calendar-hour-list>
            <div
                class="pull-left day-event bg-event-{{ dayEvent.event.getColor( dayEvent.event.beautican ) }}"
                ng-repeat="dayEvent in vm.nonAllDayEvents track by dayEvent.event.calendarEventId"
                ng-class="{
                    'is-complete' : dayEvent.event.ref_order != '0' && isDefined( dayEvent.event.ref_order ),
                    'is-not-complete'   :  dayEvent.event.ref_order == '0' || isUndefined( dayEvent.event.ref_order )
                }"
                ng-style="{        top: dayEvent.top - 1 + 'px',        left: dayEvent.event.left +  vm.dayViewTimePositionOffset + 'px',        height: dayEvent.height + 'px',        width: dayEvent.width + 'px',        backgroundColor: dayEvent.event.color.secondary,        borderColor: dayEvent.event.color.primary      }"
                mwl-draggable="dayEvent.event.draggable === true"
                axis="'xy'"
                snap-grid="{y: vm.dayViewEventChunkSize || 30, x: 50}"
                on-drag="vm.eventDragged(dayEvent.event, y / 30)"
                on-drag-end="vm.eventDragComplete(dayEvent.event, y / 30)"
                mwl-resizable="dayEvent.event.resizable === true && dayEvent.event.endsAt"
                resize-edges="{top: true, bottom: true}"
                on-resize="vm.eventResized(dayEvent.event, edge, y / 30)"
                on-resize-end="vm.eventResizeComplete(dayEvent.event, edge, y / 30)"
                uib-tooltip-html="vm.calendarEventTitle.dayViewTooltip(dayEvent.event) | calendarTrustAsHtml"
                tooltip-append-to-body="true">

                <!-- ng-class="dayEvent.event.cssClass" -->

                <a  href="javascript:;"   style="margin-right:5px;"      class="event-item-action text-center btn btn-primary btn-xs"        ng-repeat="action in dayEvent.event.actions track by $index"        ng-class="action.cssClass"        ng-bind-html="action.label | calendarTrustAsHtml"        ng-click="action.onClick({calendarEvent: dayEvent.event})"></a><br ng-show="dayEvent.event.actions.length > 0">

                <div>
                    <ul class="list-group" style="margin-bottom:5px;margin-top:5px;">
                        <!-- <li class="list-group-item"><?php echo __( 'Services', 'alvaro' );?></li> -->
                        <li style="padding:2px 5px;" ng-repeat="product in dayEvent.event.products" class="list-group-item">{{ product.DESIGN }} (x{{ product.QUANTITE }})</li>
                    </ul>
                    <span><strong><?php echo __( 'Customer', 'alvaro' );?></strong> : {{ dayEvent.event.order[0].customer_name }}</span><br>
                    <span><strong><?php echo __( 'Customer Phone', 'alvaro' );?></strong> : {{ dayEvent.event.order[0].customer_phone }}</span><br>
                    <span><strong><?php echo __( 'Beautican', 'alvaro' );?></strong> : {{ dayEvent.event.beautican_name }}</span>

                </div>

                <span class="cal-hours">
                    <span ng-show="dayEvent.top == 0">
                        <span ng-bind="(dayEvent.event.tempStartsAt || dayEvent.event.startsAt) | calendarDate:'day':true"></span>,
                    </span>
                    <span ng-bind="(dayEvent.event.tempStartsAt || dayEvent.event.startsAt) | calendarDate:'time':true"></span> &mdash;
                    <span ng-bind="(dayEvent.event.tempEndsAt || dayEvent.event.endsAt) | calendarDate:'time':true"></span>
                    <br>
                </span>

            </div>
        </div>
    </div>
</script>
<script type="text/javascript">
tendooApp.constant('uiDatetimePickerConfig', {
    dateFormat: 'yyyy-MM-dd HH:mm',
    defaultTime: '00:00:00',
    html5Types: {
        date: 'yyyy-MM-dd',
        'datetime-local': 'yyyy-MM-dd HH:mm', // yyyy-MM-ddTHH:mm:ss.sss
        'month': 'yyyy-MM'
    },
    initialPicker: 'date',
    reOpenDefault: false,
    enableDate: true,
    enableTime: true,
    buttonBar: {
        show: true,
        now: {
            show: true,
            text: 'Now',
            cls: 'btn-sm btn-default'
        },
        today: {
            show: true,
            text: 'Today',
            cls: 'btn-sm btn-default'
        },
        clear: {
            show: true,
            text: 'Clear',
            cls: 'btn-sm btn-default'
        },
        date: {
            show: true,
            text: 'Date',
            cls: 'btn-sm btn-default'
        },
        time: {
            show: true,
            text: 'Time',
            cls: 'btn-sm btn-default'
        },
        close: {
            show: true,
            text: 'Close',
            cls: 'btn-sm btn-default'
        }
    },
    closeOnDateSelection: true,
    closeOnTimeNow: true,
    appendToBody: false,
    altInputFormats: [],
    ngModelOptions: {},
    saveAs: false,
    readAs: false
});

tendooApp.config(['calendarConfig', function(calendarConfig) {
    calendarConfig.dateFormatter = 'moment'; // use moment to format dates
    moment.locale('es');
}]);

tendooApp.directive( 'appointment', function(){
    return {
        template    :   '<h3 class="text-center"><?php echo __( 'Create an appointment', 'alvaro' );?></h3>' +

        '<p class="input-group">' +
        '<span class="input-group-addon"><?php echo _s( 'Starts At', 'nexopos' );?></span>' +
        '<input type="text" class="form-control" datetime-picker="dd-MM-yyyy HH:mm" ng-model="appointmentStartsAt" is-open="isOpen[0]"  />' +
        '<span class="input-group-btn">' +
        '<button type="button" class="btn btn-default" ng-click="openCalendar($event, 0 )"><i class="fa fa-calendar"></i></button>' +
        '</span>' +
        '</p>' +

        '<div class="input-group"><span class="input-group-addon"><?php echo _s( 'Beautican', 'nexopos' );?></span><select type="color" ng-model="appointmentBeautican" class="form-control" placeholder="" ng-options="beautican as beautican.user_name for beautican in beauticans track by beautican.user_id"></select></div><br>',
        scope       :   {
            appointmentName             :   '=',
            appointmentBeautican        :   '=',
            appointmentStartsAt         :   '=',
            appointmentEndsAt           :   '=',
            beauticans                  :   '=',
            isOpen                      :   '=',
            openCalendar                :   '='
        }
    }
});

tendooApp.controller( 'alvaroAppointment', [ '$scope', 'moment', '$compile', 'calendarConfig', '$http', '$timeout', '$interval', '__orderStatus', '__paymentName', '__windowSplash', '__stripeCheckout', function( $scope, moment, $compile, calendarConfig, $http, $timeout, $interval, __orderStatus, __paymentName, __windowSplash, __stripeCheckout ) {

    calendarConfig.templates.calendarMonthCell = 'groupedMonthEvents.html';

    $scope.$on('$destroy', function() {
        calendarConfig.templates.calendarMonthCell = 'mwl/calendarMonthCell.html';
    });

    $scope.isOpen               =   [];
    $scope.colors               =   [ 'default', 'primary', 'warning', 'danger', 'info', 'red', 'blue', 'purple', 'green', 'indigo' ];

    $scope.openCalendar         = function(e, key) {
        e.preventDefault();
        e.stopPropagation();

        $scope.isOpen[ key ]    = true;
    };

    $scope.dateOptions =    {
        formatYear          : 'yyyy',
        maxDate             : new Date(2020, 5, 22),
        minDate             : new Date(),
        startingDay         : 1
    };

    $scope.events           =   <?php echo json_encode( $Alvaro_Library->get_appointments() );?>;
    $scope.usedCol          =   new Array;
    $scope.lastPosition     =   0;

    _.each( $scope.events, function( value ) {

        if( typeof $scope.usedCol[ value.beautican ] == 'undefined' ) {
            $scope.usedCol[ value.beautican ]       =   $scope.lastPosition;
            value.left                              =   $scope.usedCol[ value.beautican ];
            $scope.lastPosition                     +=  200;
        } else {
            value.left                              =   $scope.usedCol[ value.beautican ];
        }

        value.getDayViewHeight      =   function(){
            var height  =   angular.element( '.cal-day-panel-hour' ).height();
            return height;
        }

        value.getColor         =   function( beautican ) {
            code    =    typeof $scope.colors[ parseInt( beautican ) ] != 'undefined' ? $scope.colors[ parseInt( beautican ) ] : $scope.colors[0];
            return code;
        };



        value.title         =   typeof value.order  == 'array' ? value.order[0].TITRE : '';
        value.startsAt      =   new Date( value.startsAt );
        value.endsAt        =   new Date( value.endsAt );
        value.actions       =   [
            // { // an array of actions that will be displayed next to the event title
            //     label: '<i class=\'glyphicon glyphicon-pencil\'></i>', // the label of the action
            //     cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
            //     onClick: function(args) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
            //         $scope.editEvent( args.calendarEvent.calendarEventId );
            //     }
            // },
            // { // an array of actions that will be displayed next to the event title
            //     label: '<i class=\'fa fa-eye\'></i>', // the label of the action
            //     cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
            //     onClick: function(args,foo) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
            //         $scope.openDetails( args.calendarEvent.order[0].ID, args.calendarEvent.order[0].CODE );
            //     }
            // },

        ];

        var __action    =   { // an array of actions that will be displayed next to the event title
            label: '<i class=\'fa fa-remove\'></i>', // the label of the action
            cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
            onClick: function(args,foo) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to delete this ?', 'alvaro' ). '<br><div class="form-group"><label for="">' . _s( 'Please enter the reason why you\'re deleting this.', 'alvaro' ) . '</label><textarea type="text" class="form-control reason" id="" placeholder=""></textarea></div>';?>', function( action ) {
                    if( action ) {
                        if( $( '.reason' ).val().length < 10 ) {
                            NexoAPI.Bootbox().alert( '<?php echo _s( 'You must mention a reason. That reason must be consistent.', 'alvaro' );?>' );
                            return false;
                        }
                        $http.delete( '<?php echo site_url( array( 'rest', 'alvaro_rest', 'appointments' ) );?>/' + args.calendarEvent.id + '<?php echo '?store_id=' . get_store_id();?>',{
                            headers			:	{
                                '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                            }
                        }).then(function( returned ){
                            delete $scope.events[ args.calendarEvent.calendarEventId ];
                            $http.post( '<?php echo site_url( array( 'rest', 'alvaro_rest', 'log?store_id=' . get_store_id() ) );?>',{
                                'description'           :   $( '.reason' ).val(),
                                'ref_appointment'       :   args.calendarEvent.calendarEventId,
                                'author'                :   '<?php echo User::id();?>',
                                'date_creation'         :   tendoo.now(),
                                'title'                 :   '<?php echo _s( 'Appointment deletion', 'alvaro' );?>'
                            },{
                                headers			:	{
                                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                                }
                            }).then(function( returned ) {

                            });
                        });
                    }
                })
            }
        };

        if( angular.isArray( value.order ) ) {
            if( value.order.length > 0 ) {
                if( value.order[0].TYPE != 'nexo_order_comptant' ) {
                    value.actions.push( __action );
                }
            } else {
                value.actions.push( __action );
            }
        } else {
            // console.log( value.order );
            value.actions.push( __action );
        }

        if( value.order == 0 ) {
            value.actions.push({ // an array of actions that will be displayed next to the event title
                label           : '<i class=\'fa fa-shopping-cart\'></i>', // the label of the action
                cssClass        : 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
                onClick         : function(args) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                    document.location   = "<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'registers', '__use', 'default' ]);?>/?appointment_id=" + value.id;
                }
            });
        } else if( value.order[0].TYPE == 'nexo_order_devis' ) {

            value.actions.push({
                label       :   '<i class="fa fa-money"></i>',
                cssClass    :   'pay-action',
                onClick     :   function( args ) {
                    document.location   =   "<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'registers', '__use', 'default' ]);?>/?load-order=" + value.order[0].ORDER_ID;
                }
            });

            //@since 1.8.1
            value.actions.push({
                label       :   '<i class="fa fa-envelope"></i>',
                cssClass    :   'sms-reminder',
                onClick     :   function( args ) {

                    _.templateSettings = {
                        interpolate: /\{\{(.+?)\}\}/g
                    };

                    let message             =   _.template( '<?php echo @$Options[ store_prefix() . 'calendario_sms_template' ];?>' );
                    let params               =   {
                        customer_name       :   value.order[0].NOM,
                        customer_phone      :   value.order[0].TEL,
                        order_total         :   value.order[0].TOTAL,
                        store_name          :   '<?php echo @$Options[ store_prefix() . 'site_name' ];?>',
                        store_phone         :   '<?php echo @$Options[ store_prefix() . 'nexo_shop_phone' ];?>',
                        start_date          :   moment( tendoo.now() ).to( value.startsAt )
                    }

                    let sms                 =  message( params );

                    $http.post( '<?php echo site_url([ 'rest', 'alvaro_rest', 'sms_reminder' ]);?>/' + value.order[0].ORDER_ID + '<?php echo store_get_param( null );?>', {
                        nexo_sms_service        :   '<?php echo @$Options[ store_prefix() . 'nexo_sms_service' ];?>',
                        nexo_bulksms_username   :   '<?php echo @$Options[ store_prefix() . 'nexo_bulksms_username' ];?>',
                        nexo_bulksms_password   :   '<?php echo @$Options[ store_prefix() . 'nexo_bulksms_password' ];?>',
                        nexo_bulksms_url        :   '<?php echo @$Options[ store_prefix() . 'nexo_bulksms_url' ];?>',
                        nexo_bulksms_port       :   '<?php echo @$Options[ store_prefix() . 'nexo_bulksms_port' ];?>',
                        twilio_account_sid      :   '<?php echo @$Options[ store_prefix() . 'twilio_account_sid' ];?>',
                        twilio_account_token    :   '<?php echo @$Options[ store_prefix() . 'twilio_account_token' ];?>',
                        twilio_from_number      :   '<?php echo @$Options[ store_prefix() . 'twilio_from_number' ];?>',
                        sms                     :   sms,
                        phone                   :   params.customer_phone
                    }, {
                        headers	:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function( returned ) {
                        NexoAPI.Toast()( '<?php echo __( 'The reminder message has been send', 'alvaro' );?>' );
                    }, function( returned ) {
                        if( returned.data.status == 'success' ) {
                            NexoAPI.Toast()( '<?php echo __( 'The reminder message has been send', 'alvaro' );?>' );
                        } else {
                            NexoAPI.Bootbox().alert( returned.data.error.message );
                        }
                    });
                }
            });
        }
    })
    $scope.calendarView     =   'month';
    $scope.viewDate         =   moment().startOf('month').toDate();
    $scope.timesClicked     =   0;
    $scope.createEvent      =   false;
    $scope.beauticans       =   <?php echo json_encode( ( Array ) $Alvaro_Library->get_cashiers( $this->auth->list_users( 'shop_cashier' ) ) );?>;
    var originalFormat      =   calendarConfig.dateFormats.hour;
    calendarConfig.dateFormats.hour = 'HH:mm';
    $scope.cellIsOpen       =   false ;

    calendarConfig.templates.calendarMonthCell = 'groupedMonthEvents.html';

    $scope.$on('$destroy', function() {
        calendarConfig.templates.calendarMonthCell = 'mwl/calendarMonthCell.html';
        calendarConfig.dateFormats.hour = originalFormat; // reset for other demos
    });

    /**
    *  Event Changed
    *  @param object date
    *  @return void
    **/

    $scope.eventTimesChanged    = function(event) {
        $scope.viewDate   = event.startsAt;
    };

    /**
    *  Timespan clicked
    *  @param object date
    *  @param string cell
    *  @return void
    **/

    $scope.timespanClicked = function(date, cell) {
        if ($scope.calendarView == 'month') {
            if (($scope.cellIsOpen && moment(date).startOf('day').isSame(moment($scope.viewDate).startOf('day'))) || cell.events.length === 0 || !cell.inMonth) {
                $scope.cellIsOpen = false;
            } else {
                $scope.cellIsOpen = true;
                $scope.viewDate = date;
            }
        } else if ($scope.calendarView == 'year') {
            if (($scope.cellIsOpen && moment(date).startOf('month').isSame(moment($scope.viewDate).startOf('month'))) || cell.events.length === 0) {
                $scope.cellIsOpen = false;
            } else {
                $scope.cellIsOpen = true;
                $scope.viewDate = date;
            }
        }

    };

    /**
    *  Toggle Create Event
    *  @param boolean
    *  @return void
    **/

    $scope.toggleCreateEvent    =   function( action ) {
        $scope.createEvent      =   action;
    }

    /**
    *  Edit Event
    *  @param
    *  @return
    **/

    $scope.editEvent            =   function( eventId ){
        var event               =   $scope.events[ eventId ];
        if( typeof event != 'undefiend' ) {

            $scope.appointmentName          =   event.title
            $scope.appointmentStartsAt      =   event.startsAt;
            $scope.appointmentEndsAt        =   event.endsAt;
            $scope.appointmentBeautican     =   event.beautican;
            var dom     =   '<div class="appointment"><appointment ' +
            'appointment-starts-at="appointmentStartsAt" ' +
            'appointment-ends-at="appointmentEndsAt" ' +
            'appointment-name="appointmentName" ' +
            'appointment-beautican="appointmentBeautican"' +
            'beauticans="beauticans"' +
            'open-calendar="openCalendar"' +
            'is-open="isOpen"' +
            '/></div>';

            NexoAPI.Bootbox().confirm(  dom , function( action ){
                $scope.events[ eventId ].title          =   $scope.appointmentName;
                $scope.events[ eventId ].startsAt       =   $scope.appointmentStartsAt;
                $scope.events[ eventId ].endsAt         =   $scope.appointmentEndsAt;
                $scope.events[ eventId ].beautican      =   $scope.appointmentBeautican;
            });

            $( '.appointment' ).html( $compile( $( '.appointment' ).html() )($scope) );
        }
    }

    $scope.rangeSelected    = function(startDate, endDate) {

        if( $scope.createEvent != true ) {
            return false;
        }

        $scope.appointmentName          =   '<?php echo _s( 'Unamed Appointment', 'alavaro' );?>';
        $scope.appointmentStartsAt      =   startDate; // moment( startDate ).format( 'YYYY-MM-DD HH:mm' );
        $scope.appointmentEndsAt        =   endDate; // moment( endDate ).format( 'YYYY-MM-DD HH:mm' );
        $scope.appointmentBeautican     =   null;

        NexoAPI.Bootbox().confirm( '<div class="appointment"><appointment ' +
        'appointment-starts-at="appointmentStartsAt" ' +
        'appointment-ends-at="appointmentEndsAt" ' +
        'appointment-name="appointmentName" ' +
        'appointment-color-first="appointmentColorFirst" ' +
        'appointment-color-second="appointmentColorSecond" ' +
        'appointment-beautican="appointmentBeautican"' +
        'beauticans="beauticans"' +
        'open-calendar="openCalendar"' +
        'is-open="isOpen"' +
        '/></div>', function( action ){
            if( action ) {
                if( $scope.appointmentName == '' ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'Please provide a correct name', 'alvaro' );?>' );
                    return false;
                }

                if( $scope.appointmentBeautican == null ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'Please select a beautican', 'alvaro' );?>' );
                    return false;
                }

                // + '?<?php // echo store_get_param( null );?>'

                $http.post( '<?php echo site_url( array( 'rest', 'alvaro_rest', 'appointments?store_id=' . get_store_id() ) );?>', {
                    startsAt        :   moment( startDate ).format(),
                    endsAt          :   moment( endDate ).format(),
                    beautican       :   $scope.appointmentBeautican[ 'user_id' ],
                    author          :   '<?php echo User::id();?>',
                    title           :   '<?php echo _s( 'Unamed Appointment', 'alvaro' );?>',
                    ref_order       :   0,
                    date_creation   :   tendoo.now(),
                    is_appointment  :   'yes'
                },{
                    headers			:	{
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then(function( returned ){

                    var value   =   {};

                    if( typeof $scope.usedCol[ $scope.appointmentBeautican.user_id ] == 'undefined' ) {
                        $scope.usedCol[ $scope.appointmentBeautican.user_id ]       =   $scope.lastPosition;
                        value.left                              =   $scope.usedCol[ $scope.appointmentBeautican.user_id ];
                        $scope.lastPosition                     +=  200;
                    } else {
                        value.left                              =   $scope.usedCol[ $scope.appointmentBeautican.user_id ];
                    }

                    var event       =   {
                        title           :   $scope.appointmentName, // The title of the event
                        beautican       :   $scope.appointmentBeautican,
                        startsAt        :   $scope.appointmentStartsAt, // A javascript date object for when the event starts
                        left            :   value.left,
                        getColor        :   function( beautican ) {
                            code    =    typeof $scope.colors[ parseInt( beautican ) ] != 'undefined' ? $scope.colors[ parseInt( beautican ) ] : $scope.colors[0];
                            return code;
                        },
                        endsAt          :   $scope.appointmentEndsAt, // Optional - a javascript date object for when the event ends
                        color           :   '#666',
                        id              :    returned.data.id,
                        actions         : [
                            // { // an array of actions that will be displayed next to the event title
                            //     label: '<i class=\'glyphicon glyphicon-pencil\'></i>', // the label of the action
                            //     cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
                            //     onClick: function(args) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                            //         $scope.editEvent( args.calendarEvent.calendarEventId );
                            //     }
                            // },
                            { // an array of actions that will be displayed next to the event title
                                label           : '<i class=\'fa fa-shopping-cart\'></i>', // the label of the action
                                cssClass        : 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
                                onClick         : function(args) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                                    document.location   = "<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'registers', '__use', 'default' ]);?>/?appointment_id=" + returned.data.id;
                                }
                            },{ // an array of actions that will be displayed next to the event title
                                label: '<i class=\'fa fa-remove\'></i>', // the label of the action
                                cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
                                onClick: function(args,foo) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                                    NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to delete this ?', 'alvaro' ). '<br><div class="form-group"><label for="">' . _s( 'Please enter the reason why you\'re deleting this.', 'alvaro' ) . '</label><textarea type="text" class="form-control reason" id="" placeholder=""></textarea></div>';?>', function( action ) {
                                        if( action ) {
                                            if( $( '.reason' ).val().length < 10 ) {
                                                NexoAPI.Bootbox().alert( '<?php echo _s( 'You must mention a reason. That reason must be consistent.', 'alvaro' );?>' );
                                                return false;
                                            }
                                            $http.delete( '<?php echo site_url( array( 'rest', 'alvaro_rest', 'appointments' ) );?>/' + args.calendarEvent.id + '<?php echo '?store_id=' . get_store_id();?>',{
                                                headers			:	{
                                                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                                                }
                                            }).then(function( returned ){
                                                delete $scope.events[ args.calendarEvent.calendarEventId ];
                                                $http.post( '<?php echo site_url( array( 'rest', 'alvaro_rest', 'log?store_id=' . get_store_id() ) );?>',{
                                                    'description'           :   $( '.reason' ).val(),
                                                    'ref_appointment'       :   args.calendarEvent.calendarEventId,
                                                    'author'                :   '<?php echo User::id();?>',
                                                    'date_creation'         :   tendoo.now(),
                                                    'title'                 :   '<?php echo _s( 'Appointment deletion', 'alvaro' );?>'
                                                },{
                                                    headers			:	{
                                                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                                                    }
                                                }).then(function( returned ) {

                                                });
                                            });
                                        }
                                    })
                                }
                            }
                        ],
                        draggable: true, //Allow an event to be dragged and dropped
                        resizable: true, //Allow an event to be resizable
                        // incrementsBadgeTotal: true, //If set to false then will not count towards the badge total amount on the month and year view
                        // recursOn: 'year', // If set the event will recur on the given period. Valid values are year or month
                        cssClass: 'a-css-class-name', //A CSS class (or more, just separate with spaces) that will be added to the event when it is displayed on each view. Useful for marking an event as selected / active etc
                        allDay: false // set to true to display the event as an all day event on the day view
                    }
                    $scope.events.push( event );
                    $scope.toggleCreateEvent( false );
                },function(){
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'You can\'t create an appointment, since you already have one at the same moment.', 'alvaro' );?>');
                });
            }
        });

        $( '.appointment' ).html( $compile( $( '.appointment' ).html() )($scope) );
    };

    // Feature from NexoPOS Order List
    $scope.order_status		=	{
        comptant			:	'nexo_order_comptant',
        avance				:	'nexo_order_advance',
        complete			:	'nexo_order_complete',
        devis				:	'nexo_order_devis'
    }

    $scope.ajaxHeader		=	{
        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
        // 'Content-Type'											: 	'application/x-www-form-urlencoded'
    }

    $scope.window			=	{
        height				:	window.innerHeight < 600 ? 600 : window.innerHeight
    }

    /**
     * addTO
    **/

    $scope.addTo				=	function( place, barcode ) {
        _.each( $scope.orderItems, function( value, key ) {
            if( value.CODEBAR == barcode ) {
                if( place == 'defective' ) {
                    if( $scope.orderItems[ key ].QUANTITE > 0 ) {

                        $scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) - 1;
                        $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE	=	parseInt( $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE ) + 1;

                        if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
                            $scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) - 1;
                            $scope.orderItems[ key ].DEFECTUEUX				=	parseInt( $scope.orderItems[ key ].DEFECTUEUX ) + 1;
                        }

                        var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

                        if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
                            var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
                                salePrice		-=	percentPrice;
                        } else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
                                salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
                        }

                        $scope.toRefund			+=	salePrice;
                        $scope.order.TOTAL		-=	salePrice;

                    } else {
                        NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'alvaro' );?>', '<?php echo _s( 'You cannot remove quantity', 'alvaro' );?>' );
                    }
                } else if( place == 'def_to_stock' ) {
                    if( $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE > 0 ) {

                        var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

                        if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
                            var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
                                salePrice		-=	percentPrice;
                        } else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
                                salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
                        }

                        $scope.toRefund			-=	salePrice;
                        $scope.order.TOTAL		+=	salePrice;

                        $scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) + 1;
                        $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE	=	parseInt( $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE ) - 1;

                        if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
                            $scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) + 1;
                            $scope.orderItems[ key ].DEFECTUEUX				=	parseInt( $scope.orderItems[ key ].DEFECTUEUX ) - 1;
                        }



                    } else {
                        NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'alvaro' );?>', '<?php echo _s( 'You cannot restore quantity', 'alvaro' );?>' );
                    }
                } else if( place == 'active' ) {
                    if( $scope.orderItems[ key ].QUANTITE > 0 ) {

                        $scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) - 1;
                        $scope.orderItems[ key ].CURRENT_USABLE_QTE		=	parseInt( $scope.orderItems[ key ].CURRENT_USABLE_QTE ) + 1;

                        if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
                            $scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) - 1;
                            $scope.orderItems[ key ].QUANTITE_RESTANTE		=	parseInt( $scope.orderItems[ key ].QUANTITE_RESTANTE ) + 1;
                        }

                        var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

                        if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
                            var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
                                salePrice		-=	percentPrice;
                        } else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
                                salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
                        }

                        $scope.toRefund			+=	salePrice;
                        $scope.order.TOTAL		-=	salePrice;

                    } else {
                        NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'alvaro' );?>', '<?php echo _s( 'You cannot restore quantity', 'alvaro' );?>' );
                    }
                } else if( place == 'act_to_stock' ) {
                    if( $scope.orderItems[ key ].CURRENT_USABLE_QTE > 0 ) {

                        var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

                        if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
                            var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
                                salePrice		-=	percentPrice;
                        } else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
                                salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
                        }

                        $scope.toRefund			-=	salePrice;
                        $scope.order.TOTAL		+=	salePrice;

                        $scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) + 1;
                        $scope.orderItems[ key ].CURRENT_USABLE_QTE		=	parseInt( $scope.orderItems[ key ].CURRENT_USABLE_QTE ) - 1;

                        if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
                            $scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) + 1;
                            $scope.orderItems[ key ].QUANTITE_RESTANTE		=	parseInt( $scope.orderItems[ key ].QUANTITE_RESTANTE ) - 1;
                        }

                    } else {
                        NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'alvaro' );?>', '<?php echo _s( 'You cannot restore quantity', 'alvaro' );?>' );
                    }
                }
            }
        });
    }

    /**
     * Control Cash Payment
    **/

    $scope.controlCashAmount	=	function(){
        if( parseFloat( $scope.cashPaymentAmount ) > 0 && parseFloat( $scope.cashPaymentAmount ) <= parseFloat( $scope.orderBalance ) ) {
            $scope.paymentDisabled		=	false;
        } else if( parseFloat( $scope.cashPaymentAmount ) > parseFloat( $scope.orderBalance ) ) {
            $scope.cashPaymentAmount	=	parseFloat( $scope.orderBalance );
            NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'alvaro' );?>', '<?php echo _s( 'The payment cannot exceed the amount to pay.', 'alvaro' );?>' );
        } else {
            $scope.cashPaymentAmount	=	0;
            $scope.paymentDisabled		=	true;
        }
    };

    /**
     * Create Options
    **/

    $scope.createOptions		=	function(){
        return	{
                details				:	{
                title				:	'<?php echo _s( 'Details', 'alvaro' );?>',
                visible				:	false,
                class				:	'default',
                content				:	'',
                namespace			:	'details',
                icon				:	'fa fa-eye'
            },	payment				:	{
                title				:	'<?php echo _s( 'Payment', 'alvaro' );?>',
                visible				:	false,
                class				:	'default',
                content				:	'',
                namespace			:	'payment',
                icon				:	'fa fa-money'
            }, refund			:	{
                title				:	'<?php echo _s( 'Remboursement', 'alvaro' );?>',
                visible				:	false,
                class				:	'default',
                content				:	'',
                namespace			:	'refund',
                icon				:	'fa fa-frown-o'
            }/*, cancel			:	{
                title				:	'<?php echo _s( 'Cancel', 'alvaro' );?>',
                visible				:	false,
                class				:	'default',
                content				:	'',
                namespace			:	'cancel',
                icon				:	'fa fa-eye'
            }, print			:	{
                title				:	'<?php echo _s( 'Print', 'alvaro' );?>',
                visible				:	false,
                class				:	'default',
                content				:	'',
                namespace			:	'print',
                icon				:	'fa fa-eye'
            }*/
        };
    };

    /**
     * Disable Payment
    **/

    $scope.disablePayment		=	function( payment ){
        if( payment == 'cash' ) {
            $scope.paymentDisabled	=	true;
        }
    }

    /**
     * Load Content
    **/

    $scope.loadContent			=	function( option ){
        if( option.namespace 		==	'details' ) {

            HTML.query( '[data-namespace="' + option.namespace + '"]' ).only(0).add( 'my-spinner' ).each( 'namespace', 'spinner' );

            $( '[data-namespace="' + option.namespace + '"]' ).html( $compile( $( '[data-namespace="' + option.namespace + '"]' ).html() )($scope) );

            $scope.openSpinner();

            $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_item' ) );?>' + '/' + $scope.order_id + '?<?php echo store_get_param( null );?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ){

                $scope.items			=	returned.data.items;
                $scope.order			=	returned.data.order;
                $scope.order.GRANDTOTAL	=	0;
                $scope.orderCode		=	$scope.order.CODE;
                $scope.order.CHARGE		=	NexoAPI.ParseFloat( $scope.order.REMISE ) + NexoAPI.ParseFloat( $scope.order.RABAIS ) + NexoAPI.ParseFloat( $scope.order.RISTOURNE );

                // hide unused options
                if( $scope.order.TYPE == 'nexo_order_comptant' ) {
                    // delete $scope.options.payment;
                }

                // hide unused options
                if( $scope.order.TYPE == 'nexo_order_devis' ) {
                    delete $scope.options.refund;
                }

                // Sum total
                _.each( $scope.items, function( value ) {
                    $scope.order.GRANDTOTAL	+=	( value.QUANTITE * value.PRIX_DE_VENTE );
                });

                // Remaining
                $scope.order.BALANCE		=	Math.abs( NexoAPI.ParseFloat( $scope.order.TOTAL - $scope.order.SOMME_PERCU ) );

                var content		=
                '<div class="row">' +
                    '<div class="col-lg-8" style="height:{{ wrapperHeight }}px;overflow-y: scroll;">' +
                        '<h5><?php echo _s( 'Items List', 'alvaro' );?></h5>' +
                        '<table class="table table-bordered table-striped">' +
                            '<thead>' +
                                '<tr>' +
                                    '<td><?php echo _s( 'Item Name', 'alvaro' );?></td>' +
                                    '<td><?php echo _s( 'SKU', 'alvaro' );?></td>' +
                                    '<td><?php echo _s( 'Unit Price', 'alvaro' );?></td>' +
                                    '<td><?php echo _s( 'Quantity', 'alvaro' );?></td>' +
                                    '<td><?php echo _s( 'Sub-Total', 'alvaro' );?></td>' +
                                '</tr>' +
                            '</thead>' +
                            '<tbody>' +
                                '<tr ng-repeat="item in items">' +
                                    '<td>{{ item.DESIGN }}</td>' +
                                    '<td>{{ item.SKU }}</td>' +
                                    '<td>{{ item.PRIX_DE_VENTE | moneyFormat }}</td>' +
                                    '<td>{{ item.QUANTITE }}</td>' +
                                    '<td>{{ item.PRIX_DE_VENTE * item.QUANTITE | moneyFormat }}</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td colspan="4"><strong><?php echo _s( 'Sub-total', 'alvaro' );?></strong> </td>' +
                                    '<td>{{ order.GRANDTOTAL | moneyFormat }}</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td colspan="4"><strong><?php echo _s( 'Discount (-)', 'alvaro' );?></strong></td>' +
                                    '<td>{{ order.CHARGE | moneyFormat }}</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td colspan="4"><strong><?php echo _s( 'VAT (+)', 'alvaro' );?></strong> </td>' +
                                    '<td>{{ order.TVA | moneyFormat }}</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td colspan="4"><strong><?php echo _s( 'Total', 'alvaro' );?></strong></td>' +
                                    '<td>{{ order.TOTAL | moneyFormat }}</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td colspan="4"><strong><?php echo _s( 'Paid (+)', 'alvaro' );?></strong></td>' +
                                    '<td>{{ order.SOMME_PERCU | moneyFormat }}</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td colspan="4"><strong><?php echo _s( 'Balance (=)', 'alvaro' );?></strong></td>' +
                                    '<td>{{ order.BALANCE | moneyFormat }}</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</div>' +
                    '<div class="col-lg-4">' +
                        '<h5><?php echo _s( 'Order Details', 'alvaro' );?></h5>' +
                        '<ul class="list-group">' +
                          '<li class="list-group-item"><strong><?php echo _s( 'Author :', 'alvaro' );?></strong> {{ order.AUTHOR_NAME }}</li>' +
                          '<li class="list-group-item"><strong><?php echo _s( 'Done At :', 'alvaro' );?></strong> {{ order.DATE_CREATION | date:short }}</li>' +
                          '<li class="list-group-item"><strong><?php echo _s( 'Customer :', 'alvaro' );?></strong> {{ order.CLIENT_NAME }}</li>' +
                          '<li class="list-group-item"><strong><?php echo _s( 'Status :', 'alvaro' );?></strong> {{ order.TYPE | orderStatus }}</li>' +
                        '</ul>' +
                    '</div>' +
                '</div>';

                $scope.closeSpinner();

                $( '[data-namespace="details"]' ).html( $compile(content)($scope) );
            });
        }
        else if( option.namespace == 'payment' ) {

            $scope.cashPaymentAmount	=	0;

            $( '[data-namespace="payment"]' ).html( '' );

            HTML.query( '[data-namespace="payment"]' ).only(0).add( 'div.row>div.col-lg-6*2' );

            var cols	=	HTML.query( '[data-namespace="payment"] div .col-lg-6' );

                cols.only(0)
                    .add( 'h4.text-center{<?php echo _s( 'Make a Payment', 'alvaro' );?>}');

                cols.only(0)
                    .add( 'div>.input-group.payment-selection>span.input-group-addon{<?php echo _s( 'Choose a payment', 'alvaro' );?>}' );
                cols.only(0).query( 'div>.input-group' )
                    .add( 'select.form-control' )
                    .each( 'ng-model', 'paymentSelected' )
                    .each( 'ng-options', 'key as value for ( key, value ) in paymentOptions' )
                    .each( 'ng-change', 'loadPaymentOption()' )
                    .each( 'ng-disabled', 'disablePaymentsOptions' );

                cols.only(0)
                    .add( 'h4>strong.text-center{<?php echo _s( 'Left to pay', 'alvaro' );?>}' )
                    .each( 'ng-hide', 'disablePaymentsOptions' )
                    .add( 'span.amount-to-pay' )
                    .textContent	=	' :  {{ order.BALANCE | moneyFormat }}';

                cols.only(0)
                    .add( 'div.payment-option-box' );

                cols.only(0)
                    .add( 'div.notice-wrapper.alert.alert-info' ).textContent	=	'{{noticeText}}';

                cols.only(1)
                    .add( 'h4.text-center{<?php echo _s( 'Payment History', 'alvaro' );?>}' );

                cols.only(1)
                    .add( 'table.table.table-bordered>thead>tr.payment-history-thead>td*4' );

                cols.only(1)
                    .query( 'table' )
                    .add( 'tbody.payment-history' );

                cols.only(1)
                    .each( 'class', 'col-lg-6 payment-history-col' );

                cols.query( '.notice-wrapper' ).each( 'ng-show', 'showNotice' );

                $( '.payment-history-col' ).attr( 'style', 'height:{{ wrapperHeight }}px;overflow-y: scroll;' );

            var	tableHeadTD						=	HTML.query( '.payment-history-thead td' );
                tableHeadTD.only(0).textContent	=	'<?php echo _s( 'Amount', 'alvaro' );?>';
                tableHeadTD.only(1).textContent	=	'<?php echo _s( 'Cashier', 'alvaro' );?>';
                tableHeadTD.only(2).textContent	=	'<?php echo _s( 'Payment Mode', 'alvaro' );?>';
                tableHeadTD.only(3).textContent	=	'<?php echo _s( 'Date', 'alvaro' );?>';

            HTML.query( '[data-namespace="payment"]' ).only(0).add( 'my-spinner' ).each( 'namespace', 'spinner' );

            $( '[data-namespace="payment"]' ).html( $compile( $( '[data-namespace="payment"]' ).html() )($scope) );

            $scope.openSpinner();

            $http.get(
                '<?php echo site_url( array( 'rest', 'nexo', 'order' ) );?>' + '/' +
                $scope.order_id + '?<?php echo store_get_param( null );?>',
            {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( response ){

                $scope.showNotice				=	false;
                $scope.disablePaymentsOptions	=	false;
                $scope.noticeText				=	'';
                $scope.paymentOptions			=	<?php echo json_encode( $this->config->item( 'nexo_payments_types' ) );?>;
                $scope.paymentSelected			=	null;
                $scope.orderBalance				=	NexoAPI.ParseFloat( response.data[0].TOTAL ) - NexoAPI.ParseFloat( response.data[0].SOMME_PERCU );

                // check if Stripe Payment is disabled
                <?php
                if( @$Options[ store_prefix() . 'nexo_enable_stripe' ] == 'no' ) {
                    ?>
                    delete $scope.paymentOptions.stripe;
                    <?php
                }
                ?>

                if( response.data[0].TYPE == $scope.order_status.comptant ) {

                    $scope.showNotice				=	true;
                    $scope.disablePaymentsOptions	=	true;
                    $scope.noticeText				=	'<?php echo _s( 'This order doesn\'t require further payement.', 'alvaro' );?>';

                } else if( response.data[0].TYPE == $scope.order_status.devis ) {
                    $scope.showNotice	=	true;
                    $scope.noticeText	=	'<?php echo _s( 'This order can accept payment. Please choose payment type you would like to apply to this order.', 'alvaro' );?>';
                } else if( response.data[0].TYPE == $scope.order_status.avance ) {
                    $scope.showNotice	=	true;
                    $scope.noticeText	=	'<?php echo _s( 'Choose the payment type you would like to apply to this order', 'alvaro' );?>';
                }


                $http({
                    headers	:	$scope.ajaxHeader,
                    url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.CODE + '?<?php echo store_get_param( null );?>',
                    method	:	'GET'
                }).then(function( response ) {

                    $scope.order.HISTORY	=	response.data

                    HTML.query( '.payment-history' )
                        .add( 'tr' )
                        .each( 'ng-repeat', 'payment in order.HISTORY | orderBy : "DATE_CREATION" : true' )
                        .add( 'td' )
                        .textContent	=	'{{ payment.MONTANT | moneyFormat }}';

                    HTML.query( '.payment-history tr' )
                        .add( 'td' )
                        .textContent	=	'{{ payment.AUTHOR_NAME }}';

                    HTML.query( '.payment-history tr' )
                        .add( 'td' )
                        .textContent	=	'{{ payment.PAYMENT_TYPE | paymentName }}';

                    HTML.query( '.payment-history tr' )
                        .add( 'td' )
                        .textContent	=	'{{ payment.DATE_CREATION }}';

                    $( '[data-namespace="payment"]' ).html( $compile( $( '[data-namespace="payment"]' ).html() )($scope) );

                    $scope.closeSpinner();
                });
            });

        }
        else if( option.namespace == 'refund' ) {

            $( '[data-namespace="' + option.namespace + '"]' ).html('');

            $scope.toRefund			=	0;
            $scope.currentValue		=	0;

            HTML.query( '[data-namespace="' + option.namespace + '"]' ).only(0).add( 'div.row>div.col-lg-8.col-md-8.refund-row' ); // .each( 'style', 'height:{{ wrapperHeight | number }}px;overflow-y: scroll;"' );
            HTML.query( '[data-namespace="' + option.namespace + '"] div.row' ).only(0).add( 'div.col-lg-4.col-md-4.cart' );
            // Title
            HTML.query( '.refund-row' ).only(0).add( 'h4.text-center{<?php echo _s( 'Refund', 'alvaro' );?>}' );
            // Defective Stock Table
            HTML.query( '.refund-row' ).only(0).add( 'table.table.table-bordered.refund-table' ).add( 'thead>tr>td.text-center*8' );
            HTML.query( '.refund-table td' ).only(0).textContent	=	'<?php echo _s( 'Name', 'alvaro' );?>';
            HTML.query( '.refund-table td' ).only(1).textContent	=	'<?php echo _s( 'Quantity défectueuse', 'alvaro' );?>';
            HTML.query( '.refund-table td' ).only(2).add( 'i.fa.fa-arrow-right.toCurrentStock' );
            HTML.query( '.refund-table td' ).only(3).add( 'i.fa.fa-arrow-left.toDefective' );
            HTML.query( '.refund-table td' ).only(4).textContent	=	'<?php echo _s( 'Quantity Sold', 'alvaro' );?>';
            HTML.query( '.refund-table td' ).only(5).add('i.fa.fa-arrow-right' );
            HTML.query( '.refund-table td' ).only(6).add('i.fa.fa-arrow-left' );
            HTML.query( '.refund-table td' ).only(7).textContent	=	'<?php echo _s( 'Quantity in good condition', 'alvaro' );?>';

            // Cart Table
            HTML.query( '.cart' ).only(0).add( 'h4.text-center{<?php echo _s( 'Refund State', 'alvaro' );?>}' );
            HTML.query( '.cart' ).only(0).add( 'h4{<?php echo _s( 'Value :', 'alvaro' );?>}>span.current-order-value.pull-right' ).textContent	=	'{{ order.TOTAL | moneyFormat }}';
            HTML.query( '.cart' ).only(0).add( 'h4{<?php echo _s( 'Refund :', 'alvaro' );?>}>span.current-order-value.pull-right' ).textContent	=	'{{ toRefund | moneyFormat }}';
            HTML.query( '.cart' ).only(0).add( 'button.btn.btn-primary{<?php echo _s( 'Confirm the refund', 'alvaro' );?>}' ).each( 'ng-click', 'proceedRefund()' );

            HTML.query( '[data-namespace="' + option.namespace + '"]' ).only(0).add( 'my-spinner' ).each( 'namespace', 'spinner' );

            $( '.refund-row' ).attr( 'style', 'height:{{ wrapperHeight }}px;overflow-y: scroll;' );

            $( '[data-namespace="' + option.namespace + '"]' ).html( $compile( $( '[data-namespace="' + option.namespace + '"]' ).html() )($scope) );

            $scope.openSpinner();

            $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_items_defectives' ) );?>/' + $scope.orderCode + '?<?php echo store_get_param( null );?>', {
                headers		:	$scope.ajaxHeader
            }).then(function( response ) {
                $scope.orderItems		=	response.data;

                _.each( $scope.orderItems, function( value, key ) {
                    if( $scope.orderItems[key].CURRENT_DEFECTIVE_QTE == null ) {
                        $scope.orderItems[key].CURRENT_DEFECTIVE_QTE = 0;
                    }

                    $scope.orderItems[key].CURRENT_USABLE_QTE 	=  0;

                    if( $scope.orderItems[key].STOCK_ENABLED != '1' ) {
                        $scope.orderItems[key].QUANTITY				=	'...';
                        $scope.orderItems[key].QUANTITE_RESTANTE	=	'...';
                        $scope.orderItems[key].DEFECTUEUX			=	'...';
                    }

                    // Manage unlimited items
                });

                HTML.query( '.refund-table' ).only(0).add( 'tbody>tr' ).each( 'ng-repeat', 'item in orderItems' );
                HTML.query( '.refund-table tbody tr' ).only(0).add( 'td.text-center*8' );

                HTML.query( '.refund-table tbody tr td' ).only(0).textContent = '{{ item.DESIGN }}';
                HTML.query( '.refund-table tbody tr td' ).only(1).textContent = '{{ item.CURRENT_DEFECTIVE_QTE }}/{{ item.DEFECTUEUX - item.CURRENT_DEFECTIVE_QTE }}';
                HTML.query( '.refund-table tbody tr td' ).only(2).each( 'ng-click', 'addTo( "def_to_stock", item.CODEBAR )' ).add( 'i.fa.fa-arrow-right' );
                HTML.query( '.refund-table tbody tr td' ).only(3).each( 'ng-click', 'addTo( "defective", item.CODEBAR )' ).add( 'i.fa.fa-arrow-left' );
                HTML.query( '.refund-table tbody tr td' ).only(4).textContent = '{{item.QUANTITE}}/{{item.QUANTITE_VENDU}}';
                HTML.query( '.refund-table tbody tr td' ).only(5).each( 'ng-click', 'addTo( "active", item.CODEBAR )' ).add( 'i.fa.fa-arrow-right' );
                HTML.query( '.refund-table tbody tr td' ).only(6).each( 'ng-click', 'addTo( "act_to_stock", item.CODEBAR )' ).add( 'i.fa.fa-arrow-left' );
                HTML.query( '.refund-table tbody tr td' ).only(7).textContent = '{{ item.CURRENT_USABLE_QTE }}/{{ item.QUANTITE_RESTANTE }}';

                $( '[data-namespace="refund"] .refund-table' ).html( $compile( $( '[data-namespace="refund"] .refund-table' ).html() )($scope) );

                $scope.closeSpinner();
            });

        }
    }

    /**
     * Load Grand Spinner
    **/

    $scope.loadGrandSpinner		=	function(){

        $scope.showGrandSpinner		=	false;

        if( angular.element( '.modal-content .grandSpinnerWrapper' ).length == 0 ) {
            angular.element( '.modal-content' ).append( '<div class="grandSpinnerWrapper"><grand-spinner/></div>' );
            $( '.modal-content .grandSpinnerWrapper' ).html( $compile( $( '.modal-content .grandSpinnerWrapper' ).html() )($scope) );
        }
    }

    /**
     * Load Payment Option
    **/

    $scope.loadPaymentOption	=	function(){

        if( $scope.paymentSelected == 'cash' ) {

            $scope.paymentDisabled	=	true;

            $( '.payment-option-box' ).html( $compile( '<cash-payment/>' )($scope) );

        } else if( $scope.paymentSelected == 'bank' ) {

            $scope.paymentDisabled	=	true;

            $( '.payment-option-box' ).html( $compile( '<bank-payment/>' )($scope) );

        } else if( $scope.paymentSelected == 'stripe' ) {

            $scope.paymentDisabled	=	true;

            $( '.payment-option-box' ).html( $compile( '<stripe-payment/>' )($scope) );

        } else {
            $( '.payment-option-box' ).html('');
        }
    }

    /**
     * Load Stripe Payment
    **/

    $scope.loadStripeCheckout	=	function(){
        // __stripeCheckout
        <?php if( in_array(strtolower(@$Options[ store_prefix() . 'nexo_currency_iso' ]), $this->config->item('nexo_supported_currency')) ) {
            ?>
            var	CartToPayLong		=	numeral( $scope.cashPaymentAmount ).multiply(100).value();
            <?php
        } else {
            ?>
            var	CartToPayLong		=	NexoAPI.Format( $scope.cashPaymentAmount, '0.00' );
            <?php
        };?>

        __stripeCheckout.run( CartToPayLong, $scope.order.CODE, $scope );

        __stripeCheckout.handler.open({
            name			: 	'<?php echo @$Options[ store_prefix() . 'site_name' ];?>',
            description		: 	'<?php echo _s( 'Complete the order payment : ', 'alvaro' );?>' + $scope.order.CODE,
            amount			: 	CartToPayLong,
            currency		: 	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?>'
        });
    };

    /**
     * Toggle Tab
    **/

    $scope.toggleTab			=	function( option ){

        _.each( $scope.options, function( value, key ) {
            $scope.options[key].visible		=	false;
            $scope.options[key].class		=	'default';
        });

        option.visible			=	true;
        option.class			=	'active'

        $scope.loadContent( option );
    };

    /**
     * Open Details
    **/

    $scope.openDetails			=	function( order_id, order_code ) {

        $scope.order_id		=	order_id;
        $scope.orderCode	=	order_code;
        $scope.options		=	$scope.createOptions();

        var content			=
        '<h4 class="text-center"><?php echo _s( 'Order Options', 'alvaro' );?> : {{ orderCode }}</h4>' +
        '<div class="row" style="border-top:solid 1px #EEE;">' +
            '<div class="col-lg-2 col-sm-2" style="padding:0px;margin:0px;">' +
                '<div class="list-group">' +
                  '<a style="border-radius:0;border-left:0px; border-right:0px;" data-menu-namespace="{{ option.namespace }}" href="#" ng-repeat="option in options" ng-click="toggleTab( option )" class="list-group-item {{ option.class }}"><i class="{{ option.icon }}"></i> {{ option.title }}</a>' +
                '</div>' +
            '</div>' +
            '<div class="col-lg-10 col-sm-10 details-wrapper" style="border-left:solid 1px #EEE;height:{{ window.height / 1.5 }}px;">' +
                '<div ng-repeat="option in options" ng-show="option.visible" data-namespace="{{ option.namespace }}" >' +
                '</div>' +
            '</div>' +
        '</div>';

        bootbox.alert( {
            message		:	'<dom></dom>',
            onEscape	:	false,
            closeButton	:	false
        });

        $( 'dom' ).append( $compile(content)($scope) );


        $( '.modal-dialog' ).css( 'width', '80%' );
        $( '.modal-body' ).css( 'padding-bottom', 0 );

        $scope.wrapperHeight		=	$scope.window.height / 1.5;

        // Default Tab is loaded
        $scope.toggleTab( $scope.options.details );

        // Load Grand Spinner
        $scope.loadGrandSpinner();

    }

    /**
     * Proceed Payment
    **/

    $scope.proceedPayment		=	function( paymentType, askConfirm, callback ) {

        askConfirm		=	typeof askConfirm == 'undefined' ? true : askConfirm;

        if( askConfirm ) {

            bootbox.confirm( '<?php echo _s( 'Would you confirm the payment ?', 'alvaro' );?>', function( action ) {
                if( action ) {
                    $http({
                        url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.ID + '<?php echo store_get_param( '?' );?>',
                        method	:	'POST',
                        data	:	{
                            amount		:	$scope.cashPaymentAmount,
                            author		:	'<?php echo User::id();?>',
                            date		:	'<?php echo date_now();?>',
                            order_code	:	$scope.order.CODE,
                            payment_type:	paymentType
                        },
                        headers			:	$scope.ajaxHeader
                    }).then(function( response ){
                        $scope.loadContent( $scope.createOptions().payment );
                        if( typeof callback == 'function' ) {
                            callback( response );
                        }
                    });
                }
            });

        } else {

            $http({
                url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.ID + '<?php echo store_get_param( '?' );?>',
                method	:	'POST',
                data	:	{
                    amount		:	$scope.cashPaymentAmount,
                    author		:	'<?php echo User::id();?>',
                    date		:	'<?php echo date_now();?>',
                    order_code	:	$scope.order.CODE,
                    payment_type:	paymentType
                },
                headers			:	$scope.ajaxHeader
            }).then(function( response ){
                $scope.loadContent( $scope.createOptions().payment );
                callback( response );
            });

        }
    }

    /**
     * PRoceed Refund
    **/

    $scope.proceedRefund		=	function(){
        if( $scope.toRefund > 0 ) {
            NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to confirm the refund ?', 'alvaro' );?>', function( action ){
                if( action ) {
                    $scope.openSpinner( 'grand' );
                    $http({
                        headers		:	$scope.ajaxHeader,
                        method		:	'POST',
                        url			:	'<?php echo site_url( array( 'rest', 'nexo', 'order_refund' ) );?>' + '/' + $scope.order.CODE + '?<?php echo store_get_param( null );?>',
                        data		:	{
                            items	:	$scope.orderItems,
                            author	:	'<?php echo User::id();?>',
                            date		:	tendoo.now()
                        }

                    }).then(function( data ) {
                        $scope.closeSpinner( 'grand' );
                    }, function( data ) {
                        $scope.closeSpinner( 'grand' );
                        NexoAPI.Bootbox().alert( '<?php echo _s( 'An error occured', 'alvaro' );?>' );
                    });
                }
            });
        } else {
            NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'alvaro' );?>', '<?php echo _s( 'You need to adjust stock before proceeding to the refund.', 'alvaro' );?>' );
        }
    }

    /**
     * Show Spinner
    **/

    $scope.showSpinner			=	false;

    $scope.openSpinner			=	function( namespace ){
        if( namespace == 'grand' ) {
            $( '.modal-content .grandSpinnerWrapper' ).html( $compile( $( '.modal-content .grandSpinnerWrapper' ).html() )($scope) );
            $scope.showGrandSpinner		=	true;
        } else {
            $scope.showSpinner			=	true;
        }
    }

    $scope.closeSpinner			=	function( namespace ){
        if( namespace == 'grand' ) {
            $( '.modal-content .grandSpinnerWrapper' ).html( $compile( $( '.modal-content .grandSpinnerWrapper' ).html() )($scope) );
            $scope.showGrandSpinner		=	false;
        } else {
            $scope.showSpinner			=	false;
        }
    }

    $(document).ready(function(e) {
       // $( '.modal-content' ).html( $compile( $( '.modal-content' ).html() )( $scope ) );
    });

    $( document ).ajaxComplete(function(){
        $( '.tools' ).html( $compile( $( '.tools' ).html() )( $scope ) );
    });

    $scope.groupEvents = function(cell) {
      cell.groups = {};
      if( angular.isDefined( cell.events ) ) {
          cell.events.forEach(function(event) {
            cell.groups[event.beautican] = cell.groups[event.beautican] || [];
            cell.groups[event.beautican].push(event);
          });
      }
    };

    $interval( () => {
        if( angular.element( '.cal-day-panel-hour' ).length == 1 ) {
            if( angular.element( '.beautican-group' ).length == 0 ) {
                let dom     =   
                '<div class="beautican-container"><div ng-repeat="event in events" ng-style="{ left : event.left + 60, top : -30, height : event.getDayViewHeight() + 30 }" ng-if="calendarView == \'day\'" class="beautican-group bg-event-{{ event.getColor( event.beautican ) }}">' +
                    '<strong>{{ event.beautican_name }}</strong>' +
                '</div></div>';

                angular.element( '.cal-day-panel-hour' ).before( dom );
                $( '.beautican-container' ).html( $compile( $( '.beautican-container' ).html() )($scope) );
            }            
        }
        
    }, 1000 );


}]);
</script>
<style media="screen">
    .is-complete {
        background: #a9d1fb;
        border: 1px solid #60a9cc;
        opacity: 0.5;

    }
    .is-not-complete {
        background: #EEE !important;
        border: 1px solid #AAA !important;
    }
    .day-event {
        border: solid 1px #333;
    }
    .bg-event- {
        background:#DDD;
    }

    .bg-event-red {
        background: rgba(245, 126, 126, 0.70);
    }
    .bg-event-blue {
        background: rgba(126, 167, 245, 0.70);
    }
    .bg-event-green {
        background: rgba(126, 245, 163, 0.70);
    }
    .bg-event-indigo {
        background: rgba(126, 228, 245, 0.70);
    }
    .bg-event-purple {
        background: rgba(217, 126, 245, 0.70);
    }
    .bg-event-pink {
        background: rgba(245, 126, 213, 0.70);;
    }
    .bg-event-primary {
        background: rgba(149, 150, 209, 0.70);
    }
    .bg-event-warning {
        background: rgba(236, 248, 139, 0.70);
    }
    .bg-event-info {
        background: rgba(143, 200, 203, 0.70);
    }
    .bg-event-danger {
        background: rgba(184, 92, 142, 0.70);
        color : #000;
    }
    .label-red {
        background: rgba(245, 126, 126, 0.40);
    }
    .label {
        color : #333;
        border: solid 1px #333;
    }
</style>
