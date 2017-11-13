
<div class="table-responsive">
<table class="table table-bordered table-hover">
     <thead>
          <tr>
               <th><?php echo __( 'Action', 'gastro' );?></th>
               <th><?php echo __( 'Meal & Order', 'gastro' );?></th>
               <th><?php echo __( 'Table', 'gastro' );?></th>
               <th><?php echo __( 'By', 'gastro' );?></th>
               <th><?php echo __( 'Order', 'gastro' );?></th>
          </tr>
     </thead>
     <tbody>
          <tr id="item-{{ item.COMMAND_PRODUCT_ID }}" ng-repeat="item in items track by item.COMMAND_PRODUCT_ID">
               <td>
                    <span class="hidden-lg hidden-sm">
                         <?php echo __( 'Waiter : {{ item.AUTHOR_NAME }}', 'gastro' );?><br>
                         <?php echo __( 'Code : {{ item.ORDER_CODE }}', 'gastro' );?><br>
                    </span>
                    <button ng-click="collectMeal( item.COMMAND_PRODUCT_ID, $index )" class="btn btn-primary btn-sm">
                         <?php echo __( 'Collect', 'gastro' );?>
                    </button>
               </td>
               <td>{{ item.DESIGN || item.NAME }}</td>
               <td>{{ item.TABLE_NAME }}</td>
               <td>{{ item.AUTHOR_NAME }}</td>
               <td>{{ item.ORDER_CODE }}</td>
          </tr>
          <tr ng-show="items.length == 0">
               <td class="text-center" colspan="5"><?php echo __( 'No item are ready yet...', 'gastro' );?></td>
          </tr>
     </tbody>
</table>
</div>