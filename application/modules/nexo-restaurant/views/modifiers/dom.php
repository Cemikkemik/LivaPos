<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 modifiers-item" ng-click="select( modifier )" ng-class="{ 'active' : modifier.default == 1 }"
		    ng-repeat="modifier in modifiers">
			<img ng-src="
				{{ modifier.image == '' ? 
					'<?php echo module_url( 'nexo' ) . '/images/default.png';?>' : 
					'<?php echo get_store_upload_url() . '/items-images/';?>' + modifier.image 
				}}" 
				class="modifier-image">
			<p class="modifier-name">{{ modifier.name }}</p>
			<p class="modifier-price">{{ modifier.price | moneyFormat }}</p>
		</div>
	</div>
</div>