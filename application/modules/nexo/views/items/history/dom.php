<div ng-controller="itemHistoryCTRL">
    <div class="box">
        <div class="box-header with-border"><?php echo __( 'Historique d\'activité sur le produit', 'nexo' );?></div>
        <div class="box-body no-padding">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <td><?php echo __( 'Nom du produit', 'nexo' );?></td>
                        <td><?php echo __( 'Opération', 'nexo' );?></td>
                        <td class="text-right" width="150"><!-- <?php echo __( 'Valeur', 'nexo' );?> --></td>
                        <td class="text-right" width="50"><?php echo __( 'Quantité', 'nexo' );?></td>
                        <td class="text-right" width="150"><?php echo __( 'Total', 'nexo' );?></td>
                        <td class="text-right" width="100"><?php echo __( 'Par', 'nexo' );?></td>
                        <td class="text-right" width="150"><?php echo __( 'Effectué', 'nexo' );?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-hide="item.quantity == 0" ng-repeat="item in items track by $index" class="{{ operationClassName( item ) }}">
                        <td>{{ item.name }}</td>
                        <td>{{ operationName( item.type ) }} </td>
                        <td class="text-right"></td> <!-- {{ item.price | moneyFormat }} -->
                        <td class="text-right">{{ testOperation( item ) }} {{ item.quantity }} </td>
                        <td class="text-right">{{ item.total_price | moneyFormat }} </td>
                        <td class="text-right">{{ item.author_name }} </td>
                        <td class="text-right">{{ item.date }} </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right"> <!-- {{ total.unit_price.plus - total.unit_price.minus | moneyFormat }} --></td>
                        <td class="text-right">{{ total.quantity.plus - total.quantity.minus }}</td>
                        <td class="text-right">{{ total.total_price.plus - total.total_price.minus | moneyFormat }}</td>
                        <td class="text-right"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div ng-show="totalPage > 0">
        <ul class="pagination">
            <li ng-class="{ 'active' : currentPage == page }" ng-repeat="page in [] | range:totalPage"><a href="javascript:void(0)" ng-click="loadHistory( page )">{{ page + 1 }}</a></li>
        </ul>
    </div>
</div>
