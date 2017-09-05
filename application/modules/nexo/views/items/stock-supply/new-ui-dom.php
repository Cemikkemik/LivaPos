<div class="row" ng-controller="newSupplyUIController">
    <div class="col-md-8">
        <div class="box">
            <div class="box-header" np-autocomplete="npAutocompleteOptions">
                <input np-input-model="searchValue" ng-model-options="{ debounce : 500 }" type="text" class="search-input form-control input-lg" placeholder="<?php echo __( 'Rechercher le nom du produit, le code barre ou l\'unité de gestion du stock', 'nexo' );?>">
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
                            <td colspan="2">
                                <select ng-model="selectedDelivery" class="form-control" ng-options="delivery as delivery.TITRE for delivery in deliveries track by delivery.ID">
                                    
                                </select>
                            </td>
                            <td colspan="2">
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
    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <span ng-show="selectedDelivery.ID == 0"><?php echo __( 'Ajouter un approvisionnement', 'nexo' );?></span>
                <span ng-show="selectedDelivery.ID != 0"><?php echo __( 'Modifier un approvisionnement', 'nexo' );?></span>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="supply_name"><?php echo __( 'Titre', 'nexo' );?></label>
                    <input type="text" ng-model="deliveryTitle" class="form-control" placeholder="<?php echo __( 'Titre de l\'approvisionnement', 'nexo' );?>"/>
                </div>
                <div class="form-group">
                    <label for="supply_description"><?php echo __( 'Description', 'nexo' );?></label>
                    <textarea ng-model="deliveryDescription" name="" id="" cols="30" rows="10" class="form-control"></textarea>
                </div>
                <button ng-class="{ disabled : ( ! canSubmit ) }" ng-click="saveSupply()" class="btn btn-primary" ng-show="selectedDelivery.ID == 0">
                    <span ><?php echo __( 'Ajouter un approvisionnement', 'nexo' );?></span>
                </button>
                <button ng-click="updateSupply()" class="btn btn-default" ng-show="selectedDelivery.ID != 0">
                    <span ><?php echo __( 'Modifier l\'approvisionnement', 'nexo' );?></span>
                </button>
                <button class="btn btn-warning" ng-show="selectedDelivery.ID != 0" ng-click="cancelCreateDelivery()">
                    <span ><?php echo __( 'Annuler', 'nexo' );?></span>
                </button>
            </div>

        </div>
    </div>
</div>