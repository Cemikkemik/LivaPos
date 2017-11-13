<div class="row" style="margin-bottom:10px">
    <div class="col-md-3">
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <button
                class="btn btn-primary"
                mwl-date-modifier
                date="viewDate"
                decrement="calendarView">
                Previous
                </button>
            </div>

            <div class="btn-group" role="group">
                <button
                class="btn btn-default"
                mwl-date-modifier
                date="viewDate"
                set-to-today>
                Today
                </button>
            </div>
            
            <div class="btn-group" role="group">
                <button
                class="btn btn-primary"
                mwl-date-modifier
                date="viewDate"
                increment="calendarView">
                Next
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-3">

    </div>
    <div class="col-md-6 text-center">
        <div class="btn-group btn-group-justified">
            <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'year'" ng-click="cellIsOpen = false">Year</label>
            <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'month'" ng-click="cellIsOpen = false">Month</label>
            <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'week'" ng-click="cellIsOpen = false">Week</label>
            <label class="btn btn-primary" ng-model="calendarView" uib-btn-radio="'day'" ng-click="cellIsOpen = false">Day</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <mwl-calendar
            view="calendarView"
            view-date="viewDate"
            events="events"
            custom-template-urls="{calendarMonthCell: 'customMonthCell.html'}"
            view-title="calendarTitle"
            day-view-event-width="100"
            on-event-click="eventClicked(calendarEvent)"
            on-event-times-changed="calendarEvent.startsAt = calendarNewEventStart; calendarEvent.endsAt = calendarNewEventEnd"
            cell-is-open="cellIsOpen">
            
        </mwl-calendar>
    </div>
</div>

<script id="customMonthCell.html" type="text/ng-template">
  <div class="cal-month-day {{day.cssClass}}">

    <span
      class="pull-right"
      data-cal-date
      ng-click="vm.calendarCtrl.dateClicked(day.date)"
      ng-bind="day.label"
      style="padding:10px 20px;border:solid 1px #AAA;border-radius:10px;cursor:pointer">
    </span>

    <small ng-show="day.events.length > 0" style="position: absolute; bottom: 10px; left: 5px">
      There are {{ day.events.length }} events on this day
    </small>

  </div>
</script>

<style type="text/css">
  .custom-template-cell {
    color: purple;
  }
  
</style>

<style>
    mwl-calendar .cal-month-day {
        height: 100px;
    }
</style>