<div class="container">
    <div class="row">
        <h3 class="text-center"><?php echo _s( 'Quels type de produit souhaitez-vous créer ?', 'nexo' );?></h3>
        <br>
        <div class="col-md-4 col-xs-6 col-lg-3" ng-repeat="item in itemTypes">
            <div class="box" ng-click="selectType( restoreSlashes( item.namespace ) )">
                <div class="box-content text-center" style="height:210px;">
                    <br>
                    <img ng-src="{{ getIcon( item.icon ) }}.png" alt="{{ item.text }}">
                    <h4 class="text-center">{{ item.text }}</h4>
                    <p class="text-center" style="padding:10px;">{{ item.desc }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
