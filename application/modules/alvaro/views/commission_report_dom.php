<div ng-controller="commissionReportCTRL as vm">
    <form class="form-inline hidden-print">
        <div class='input-group date' id='datetimepicker6' ng-click="refreshValue( 'startDate' )">
        	<span class="input-group-addon"><?php _e('Date de départ', 'alvaro');?></span>
            <input ng-change="refreshValue( 'startDate' )" type='text' class="startDate form-control" ng-model="startDate" />
            <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
    	</div>
        <div class='input-group date' id='datetimepicker7' ng-click="refreshValue( 'endDate' )">
        	<span class="input-group-addon"><?php _e('Date de fin', 'alvaro');?></span>
            <input ng-change="refreshValue( 'endDate' )" type='text' class="endDate form-control" ng-model="endDate" />
            <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
    	</div>
        <div class="input-group">
          <span class="input-group-addon"><?php echo __( 'Select a beautican', 'alvaro' );?></span>
          <select ng-options="beautican as beautican.name for beautican in beauticans track by beautican.user_id" type="text" class="form-control" placeholder="" ng-model="beautican">
          </select>
        </div>
        <input type="button" class="btn btn-primary" ng-click="getReport()" value="<?php _e('Display Results', 'alvaro');?>" />
        <div class="input-group">
          <span class="input-group-btn">
            <button class="btn btn-default" print-item=".report-wrapper" type="button"><?php _e('Imprimer', 'alvaro');?></button>
          </span>
        </div>
    </form>
    <br>
    <div class="box report-wrapper">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td><?php echo __( 'Order Code', 'alvaro' );?></td>
                    <td><?php echo __( 'Title', 'alvaro' );?></td>
                    <td><?php echo __( 'Commission', 'alvaro' );?></td>
                    <td><?php echo __( 'Beautican', 'alvaro' );?></td>
                    <td><?php echo __( 'Date', 'alvaro' );?></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="commissions.length == 0">
                    <td class="text-center" colspan="5"><?php echo __( 'No commission for this beautican', 'alvaro' );?></td>
                </td>
                <tr ng-if="commissions == null">
                    <td class="text-center" colspan="5"><?php echo __( 'Please select a beautican to see the report', 'alvaro' );?></td>
                </td>

                <tr ng-repeat="commission in commissions">
                    <td>{{ commission.CODE }}</td>
                    <td>{{ commission.TITRE }}</td>
                    <td>{{ commission.commission_amount | moneyFormat }}</td>
                    <td>{{ beautican.name }}</td>
                    <td>{{ commission.date_creation | date }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
