<style type="text/css">
@import url(http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css);
.col-item {
	border: 1px solid #E1E1E1;
	border-radius: 5px;
	background: #FAFAFA	;
}
.col-item .photo img {
	margin: 0 auto;
	width: 100%;
}
.col-item .info {
	padding: 10px;
	border-radius: 0 0 5px 5px;
	margin-top: 1px;
}
.col-item:hover .info {
	background-color: #F5F5DC;
}
.col-item .price {
	/*width: 50%;*/
	float: left;
	margin-top: 5px;
}
.col-item .price h5 {
	line-height: 20px;
	margin: 0;
}
.price-text-color {
	color: #219FD1;
}
.col-item .info .rating {
	color: #777;
}
.col-item .rating {
	/*width: 50%;*/
	float: left;
	font-size: 17px;
	text-align: right;
	line-height: 52px;
	margin-bottom: 10px;
	height: 52px;
}
.col-item .separator {
	border-top: 1px solid #E1E1E1;
}
.clear-left {
	clear: left;
}
.col-item .separator p {
	line-height: 20px;
	margin-bottom: 0;
	margin-top: 10px;
	text-align: center;
}
.col-item .separator p i {
	margin-right: 5px;
}
.col-item .btn-add {
	width: 50%;
	float: left;
}
.col-item .btn-add {
	border-right: 1px solid #E1E1E1;
}
.col-item .btn-details {
	width: 50%;
	float: left;
	padding-left: 10px;
}
.controls {
	margin-top: 20px;
}
[data-slide="prev"] {
 margin-right: 10px;
}
</style>
<div ng-app="resto">
    <div class="nav-tabs-custom" ng-controller="restoCtrl" >
        <ul class="nav nav-tabs">
            <li class="{{ category.status }}" ng-repeat="category in categories"><a href="#{{ category.namespace }}" data-toggle="tab" aria-expanded="false">{{ category.title }}</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="{{ category.namespace }}" ng-repeat="category in categories">
                <div class="row">
                    <div class="col-sm-3" ng-repeat="item in category.items" style="margin-bottom:15px;">
                        <div class="col-item">
                            <div class="photo"> <img src="{{ item.thumb }}" class="img-responsive" style="height:150px" alt="a" /> </div>
                            <div class="info">
                                <div class="row">
                                    <div class="price col-md-6">
                                        <h5>{{ item.name }}</h5>
                                        <h5 class="price-text-color"> {{ item.price }}</h5>
                                    </div>
                                    <div class="rating hidden-sm col-md-6"> <i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star"> </i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star"> </i><i class="fa fa-star"></i> </div>
                                </div>
                                <div class="separator clear-left">
                                    <p class="btn-add"> <i class="fa fa-shopping-cart"></i><a class="hidden-sm">Add to cart</a></p>
                                    <p class="btn-details"> <i class="fa fa-list"></i><a ng-click="pos.openDetails( this )" class="hidden-sm">More details</a></p>
                                </div>
                                <div class="clearfix"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.tab-pane --> 
        </div>
        <!-- /.tab-content --> 
    </div>
</div>
<script type="text/javascript">
var Resto	=	angular.module( 'resto', [] );
	Resto.controller( 'restoCtrl', function( $scope ) {
		
		$scope.pos			=	new Object;
		$scope.categories	=	new Object;
		
		$scope.categories[ 'food' ]	=	{
			title		:	'Food',
			namespace	:	'food',
			items		:	[],
			status		:	'active'
		};
		
		$scope.categories[ 'bread' ]=	{
			title		:	'Bread',
			namespace	:	'bread',
			items		:	[],
			status		:	''
		}
		
		$scope.categories[ 'juices' ]	=	{
			title		:	'Juices',
			namespace	:	'juices',
			items		:	[],
			status		:	''
		}
		
		/*$scope.categories[ 'cakes' ]	=	{
			title		:	'Cakes',
			namespace	:	'cakes',
			items		:	[],
			status		:	''
		}*/
		
		$scope.pos.openDetails			=	function( e ) {
			bootbox.alert( 'bonjour' );
		}
		
		// Preparing Food
		
		$scope.images_categories	=	new Object;
		
		$scope.images_categories[ 'food' ]	=	[ 'http://localhost/images/1.jpg', 'http://localhost/images/2.jpg', 'http://localhost/images/2sisters-food-group-roast-banner5.jpg', 'http://localhost/images/7838a2f8-fb2d-48e8-abc9-f7db942d3ede.jpg', 'http://localhost/images/3041647-poster-p-1-most-innovative-companies-2015-next-sectors-food.jpg', 'http://localhost/images/Beautiful-Food-Photos-15.jpg', 'http://localhost/images/Cheese_crust_pizza.jpg', 'http://localhost/images/In-N-Out_Burger_cheeseburgers.jpg', 'http://localhost/images/YUMMY-FAST-FOOD-fast-food-33414496-1280-720.jpg', 'http://localhost/images/corn-dog-fair-food.jpg', 'http://localhost/images/healthfitnessrevolution-com.jpg', 'http://localhost/images/hp_slide_11.jpg', 'http://localhost/images/miami-italian-food-delivery.jpg' ];
		
		$scope.images_categories[ 'juices' ]	=	[ 'http://localhost/images/juices/Juice-Trio.jpg', 'http://localhost/images/juices/400-04337926c.jpg', 'http://localhost/images/juices/182926-425x339-beet-juice.jpg', 'http://localhost/images/juices/Green-Juice-2.jpg', 'http://localhost/images/juices/How-to-make-orange-juice-1.jpg', 'http://localhost/images/juices/glass-of-pomegranate-juice.jpg', 'http://localhost/images/juices/juicespair-large_transeo_i_u9APj8RuoebjoAHt0k9u7HhRJvuo-ZLenGRumA.jpg', 'http://localhost/images/juices/sunmagic.jpg' ];
		
		$scope.images_categories[ 'bread' ]		=	[ 'http://localhost/images/bread/20110617-no-knead-bread-10.jpg', 'http://localhost/images/bread/Bread%20(1).jpg', 'http://localhost/images/bread/bread-can-cats-eat.jpg', 'http://localhost/images/bread/bread.jpg', 'http://localhost/images/bread/iStock_000013234402XSmall.jpg', 'http://localhost/images/bread/images.jpg', 'http://localhost/images/bread/img_7272.jpg' ];		
		
		_.each( $scope.images_categories, function( images, cat_name ) {
			
			var foods_images		=	images;	
			
			_.each( images, function( image, _key ) {
				var index			=	Math.floor(Math.random() * foods_images.length );
				var thumb			=	foods_images[ index ];
					foods_images.splice(index,1);
	
					$scope.categories[ cat_name ].items.push({
						name	:	'Item ' + _key,
						price	:	chance.dollar({ max : 100 }),
						desc	:	chance.sentence({ words : 15 }),
						thumb	:	thumb
					})
			});			
		});
	});
	
	/** **/
</script>
