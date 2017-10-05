
<table class="table table-bordered table-hover">
     <thead>
          <tr>
               <th><?php echo __( 'Meal & Order', 'nexo-restaurant' );?></th>
               <th><?php echo __( 'Table', 'nexo-restaurant' );?></th>
               <th><?php echo __( 'By', 'nexo-restaurant' );?></th>
               <th><?php echo __( 'Order', 'nexo-restaurant' );?></th>
               <th><?php echo __( 'Action', 'nexo-restaurant' );?></th>
          </tr>
     </thead>
     <tbody>
          <tr id="item-{{ item.COMMAND_PRODUCT_ID }}" ng-repeat="item in items track by item.COMMAND_PRODUCT_ID">
               <td>{{ item.DESIGN || item.NAME }}</td>
               <td>{{ item.TABLE_NAME }}</td>
               <td>{{ item.AUTHOR_NAME }}</td>
               <td>{{ item.ORDER_CODE }}</td>
               <td>
                    <button ng-click="collectMeal( item.COMMAND_PRODUCT_ID, $index )" class="btn btn-primary btn-sm">
                         <?php echo __( 'Collect', 'nexo-restaurant' );?>
                    </button>
               </td>
          </tr>
          <tr ng-show="items.length == 0">
               <td class="text-center" colspan="5"><?php echo __( 'No item are ready yet...', 'nexo-restaurant' );?></td>
          </tr>
     </tbody>
</table>