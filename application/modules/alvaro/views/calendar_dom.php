<?php global $Options;?>
<div ng-controller="alvaroAppointment" class="box">
    <div class="box-header with-border">
        <span><?php echo __( 'Place an appointment', 'alvaro' );?></span>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center text-center" style="font-size:20px;">{{ calendarTitle }}</p>
            </div>
            <div class="col-md-4">
                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button
                        class="btn btn-primary"
                        mwl-date-modifier
                        date="viewDate"
                        decrement="calendarView"
                        ng-click="cellIsOpen = false">
                        Previous
                        </button>
                    </div>
                    <div class="btn-group">
                        <button
                        class="btn btn-default"
                        mwl-date-modifier
                        date="viewDate"
                        set-to-today
                        ng-click="cellIsOpen = false">
                        Today
                        </button>
                    </div>
                    <div class="btn-group">
                        <button
                        class="btn btn-primary"
                        mwl-date-modifier
                        date="viewDate"
                        increment="calendarView"
                        ng-click="cellIsOpen = false">
                        Next
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="btn-group btn-group-justified">
                    <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'year'" ng-click="cellIsOpen = false"><?php echo __( 'Year', 'alvaro' );?></label>
                    <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'month'" ng-click="cellIsOpen = false"><?php echo __( 'Month', 'alvaro' );?></label>
                    <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'week'" ng-click="cellIsOpen = false"><?php echo __( 'Week', 'alvaro' );?></label>
                    <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'day'" ng-click="cellIsOpen = false"><?php echo __( 'Day', 'alvaro' );?></label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button ng-if="createEvent == false"
                        class="btn btn-primary"
                        ng-model="createEvent"
                        ng-click="toggleCreateEvent( true )">
                        <?php echo __( 'Create Event', 'alvaro' );?>
                        </button>
                        <button ng-if="createEvent == true"
                        class="btn btn-danger"
                        ng-model="createEvent"
                        ng-click="toggleCreateEvent( false )">
                        <?php echo __( 'Cancel', 'alvaro' );?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-12"> <!-- ng-controller="nexo_order_list" -->
                <hr>
                <div ng-repeat="event in events" ng-style="{ left : event.left + 75, height : event.getDayViewHeight() + 30 }" ng-if="calendarView == 'day'" class="beautican-group bg-{{ event.getColor( event.beautican ) }}">
                    {{ event.beautican_name }}
                </div>
                <mwl-calendar
                view="calendarView"
                view-date="viewDate"
                events="events"
                view-title="calendarTitle"
                on-date-range-select="rangeSelected(calendarRangeStartDate, calendarRangeEndDate)"
                day-view-split="<?php echo @$Options[ store_prefix() . 'time_interval'] != null ? @$Options[ store_prefix() . 'time_interval'] : 15;?>"
                day-view-start="<?php echo @$Options[ store_prefix() . 'opening_time'] != null ? @$Options[ store_prefix() . 'opening_time'] : '00:00';?>"
                day-view-end="<?php echo @$Options[ store_prefix() . 'closing_time'] != null ? @$Options[ store_prefix() . 'closing_time'] : '23:59';?>"
                cell-is-open="cellIsOpen"
                cell-auto-open-disabled="true"
                cell-modifier="groupEvents(calendarCell)"
                on-timespan-click="timespanClicked(calendarDate, calendarCell)"
                on-event-times-changed="calendarEvent.startsAt = calendarNewEventStart; calendarEvent.endsAt = calendarNewEventEnd; eventTimesChanged(calendarEvent)"
                day-view-event-width="200"
                custom-template-urls="{ calendarDayView : 'calendarDayView.html' }"
                >
                </mwl-calendar>
            </div>
        </div>
        <style type="text/css">
        .cal-day-hour-part-time strong.ng-hide {
            display: block !important;
        }
        mwl-calendar span[data-cal-date] {
            padding:10px;
            border: solid 1px #333;
            border-radius: 10px;
            background:#EEE;
        }
        .beautican-group {
            padding: 5px 0px;
            text-align: center;
            width: 201px;
            position: absolute;
            top: 11px;
            border:dashed 1px #CCC;
            opacity: 0.8;
        }

        .cal-day-box .cal-day-hour:nth-child(odd) {
            background-color: rgba(250, 250, 250, 0.13);
        }

        .cal-day-box .cal-day-hour {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .cal-day-box .cal-day-hour-part:hover {
            background-color: rgba(160, 179, 243, 0.5);
        }
        </style>
    </div>
</div>
