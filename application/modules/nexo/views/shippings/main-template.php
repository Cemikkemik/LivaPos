<form name="form" action="" novalidate>
    <div class="form-group">
        <div class="input-group input-group-lg">
            <input valid-number type="text" ng-model-options="{ debounce: 1000 }" name="price" required ng-model="price" class="form-control" placeholder="<?php echo __( 'Frais de livraison', 'nexo' );?>">
            <span class="input-group-btn">
                <button ng-click="toggleOptions()" type="button" class="btn btn-default"><i class="fa fa-cogs"></i> 
                    <span ng-show="optionShowed == true"><?php echo __( 'Masquer' );?></span>
                    <span ng-show="optionShowed == false"><?php echo __( 'Options' );?></span>
                </button>
            </span>
        </div>
        <p class="help-block" ng-show="form.price.$touched || form.$submitted">
            <span  ng-show="form.price.$error.required"><?php echo __( 'Vous devez fournir une valeur sur ce champ.', 'nexo' );?></span> 
        </p>
    </div>
    <div ng-hide="optionShowed">
        <?php echo tendoo_info( __( 'Cliquer sur plus d\'options pour afficher des informations relatives au détails de livraison', 'nexo' ) );?>
    </div>
    <div ng-hide="!optionShowed">
        <div class="input-group">
            <div class="input-group-addon"><?php echo __( 'Titre de la livraison', 'nexo' );?></div>
            <input ng-model="title" type="text" class="form-control"  placeholder="<?php echo __( 'Titre de livraison', 'nexo' );?>">
        </div>

        <div class="checkbox" ng-show="isAddressValid">
            <label>
            <input type="checkbox" ng-click="toggleFillShippingInfo( useCustomerInfo )" ng-model="useCustomerInfo"> <?php echo __( 'Utiliser les informations de livraison du client sélectionné.', 'nexo' );?>
            </label>
        </div>

        <div class="form-group" ng-show="! isAddressValid">
            <br>
            <div class="bg-info text-center" style="padding:10px">
                <?php echo __( 'Vous ne pouvez pas utiliser les informations de livraison du client, car elles sont inexistantes.', 'nexo' );?>
            </div>
        </div>

        <div> <!-- ng-show="!useCustomerInfo"-->
            <h4><?php echo __( 'Informations de livraisons', 'nexo' );?></h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Nom', 'nexo' );?></div>
                            <input type="text" ng-model="name" class="form-control"  placeholder="<?php echo __( 'Nom', 'nexo' );?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Entreprise', 'nexo' );?></div>
                            <input type="text" ng-model="enterprise" class="form-control"  placeholder="<?php echo __( 'Entreprise', 'nexo' );?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Address 1', 'nexo' );?></div>
                            <input type="text" ng-model="address_1" class="form-control" placeholder="<?php echo __( 'Address 1', 'nexo' );?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Code Postale', 'nexo' );?></div>
                            <input type="text" ng-model="pobox" class="form-control" placeholder="<?php echo __( 'Code Postale', 'nexo' );?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Etat', 'nexo' );?></div>
                            <input type="text" ng-model="state" class="form-control" placeholder="<?php echo __( 'Etat', 'nexo' );?>">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Prénom', 'nexo' );?></div>
                            <input type="text" ng-model="surname" class="form-control" placeholder="<?php echo __( 'Prénom', 'nexo' );?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Address 2', 'nexo' );?></div>
                            <input type="text" ng-model="address_2" class="form-control" placeholder="<?php echo __( 'Address 2', 'nexo' );?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Pays', 'nexo' );?></div>
                            <input type="text" ng-model="country" class="form-control" placeholder="<?php echo __( 'Pays', 'nexo' );?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Ville', 'nexo' );?></div>
                            <input type="text" ng-model="city" class="form-control" placeholder="<?php echo __( 'Ville', 'nexo' );?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
    



