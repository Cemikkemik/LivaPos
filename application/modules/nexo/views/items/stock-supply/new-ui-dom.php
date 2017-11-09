<div class="row" ng-controller="newSupplyUIController">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header" np-autocomplete="npAutocompleteOptions">
                <input np-input-model="searchValue" ng-model-options="{ debounce : 2000 }" type="text" class="search-input form-control input-lg" placeholder="<?php echo __( 'Rechercher le nom du produit, le code barre ou l\'unité de gestion du stock', 'nexo' );?>">
            </div>
            <div class="box-body no-padding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td width="120"><?php echo __( 'Code Barre', 'nexo' );?></td>
                            <td><?php echo __( 'Nom du produit', 'nexo' );?></td>
                            <td width="120"><?php echo __( 'Prix d\'achat', 'nexo' );?></td>
                            <td width="120"><?php echo __( 'Quantité', 'nexo' );?></td>
                            <td width="120"><?php echo __( 'Prix total', 'nexo' );?></td>
                            <td width="50"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="item in cart track by $index">
                            <td>{{ item.CODEBAR }}</td>
                            <td>{{ item.DESIGN }}</td>
                            <td class="text-right"><input number-mask min="0" max="99999" type="text" class="form-control input-sm" ng-model="item.PRIX_DACHAT"/></td>
                            <td class="text-right"><input number-mask min="1" max="99999" type="text" class="form-control input-sm" ng-model="item.SUPPLY_QUANTITY"/></td>
                            <td class="text-right">{{ item.PRIX_DACHAT * item.SUPPLY_QUANTITY | moneyFormat }}</td>
                            <td><button ng-click="removeItem( $index )" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></button></td>
                        </tr>
                        <tr ng-show="cart.length == 0">
                            <td colspan="{{ columns }}" class="text-center"><?php echo __( 'Aucun produit ajouté', 'nexo' );?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="active" ng-hide="cart.length == 0">
                            <td colspan="2"><?php echo __( 'Total', 'nexo' );?></td>
                            <td class="text-right"><strong>{{ total( cart, 'PRIX_DACHAT' ) | moneyFormat }}</strong></td>
                            <td class="text-right"><strong>{{ total( cart, 'SUPPLY_QUANTITY' ) }}</strong></td>
                            <td class="text-right"><strong>{{ total( cart, 'PRIX_DACHAT', 'SUPPLY_QUANTITY' ) | moneyFormat }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <select ng-model="selectedProvider" class="form-control" ng-options="provider as provider.NOM for provider in providers track by provider.ID">
                                    <option value=""><?php echo __( 'Choisir un fournisseur', 'nexo' );?></option>
                                </select>
                            </td>
                            <td colspan="2"><button ng-click="submitSupplying()" class="btn btn-primary"><?php echo __( 'Terminer l\'opération', 'nexo' );?></button></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>