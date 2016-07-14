<!DOCTYPE html>
<html>
<head>
<!-- Required meta tags-->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<!-- Your app title -->
<title>My App</title>
<!-- Path to Framework7 iOS CSS theme styles-->
<link rel="stylesheet" href="<?php echo module_url( 'nexo_restaurant' );?>bower_components/Framework7/dist/css/framework7.ios.min.css">
<!-- Path to Framework7 iOS related color styles -->
<link rel="stylesheet" href="<?php echo module_url( 'nexo_restaurant' );?>bower_components/Framework7/dist/css/framework7.ios.colors.min.css">
<!-- Path to your custom app styles-->
<link rel="stylesheet" href="<?php echo module_url( 'nexo_restaurant' );?>bower_components/Framework7/dist/css/my-app.css">
<script src="<?php echo module_url( 'nexo_restaurant' );?>bower_components/angular/angular.js"></script>
</head>
<body ng-app="nexo-restaurant">
<!-- Status bar overlay for full screen mode (PhoneGap) -->
<div class="statusbar-overlay"></div>
<!-- Panels overlay-->
<div class="panel-overlay"></div>
<!-- Left panel with reveal effect-->
<div class="panel panel-left panel-reveal">
    <div class="content-block">
        <p>Left panel content goes here</p>
    </div>
</div>
<!-- Views -->
<div class="views"> 
    <!-- Your main view, should have "view-main" class -->
    <div class="view view-main" ng-controller="main-view"> 
        <!-- Top Navbar-->
        <div class="navbar">
            <div class="navbar-inner navbar-on-left">
                <div class="left sliding" style="transform: translate3d(-67px, 0px, 0px);"><a href="index.html" class="back link"><i class="icon icon-back" style="transform: translate3d(67px, 0px, 0px);"></i><span>Back</span></a></div>
                <div class="center sliding" style="left: -6.5px; transform: translate3d(-755px, 0px, 0px);">Tabs</div>
                <div class="right"><a href="#" class="link open-panel icon-only"><i class="icon icon-bars"></i></a></div>
            </div>
            <div class="navbar-inner navbar-on-center">
                <div class="left sliding" style="transform: translate3d(0px, 0px, 0px);"><a href="index.html" class="back link"><i class="icon icon-back" style="transform: translate3d(0px, 0px, 0px);"></i><span>Back</span></a></div>
                <div class="center sliding" style="left: -6.5px; transform: translate3d(0px, 0px, 0px);">Swipeable Tabs</div>
                <div class="right"><a href="#" class="link open-panel icon-only"><i class="icon icon-bars"></i></a></div>
                <div class="subnavbar sliding" style="transform: translate3d(0px, 0px, 0px);">
                    <div class="buttons-row"> <a href="#{{ category.id }}" class="button {{ category.class }} tab-link" ng-repeat="category in categories">{{ category.name }}</a><!-- active --> 
                    </div>
                </div>
            </div>
        </div>
        <!-- Bottom Toolbar-->
        <div class="toolbar">
            <div class="toolbar-inner"> 
                <!-- Toolbar links --> 
                <a href="#" ng-click="submitOrder()" class="link">
                <?php _e( 'Submit Order', 'nexo' );?>
                </a> <a href="#" class="link">Link 2</a> </div>
        </div>
        <div class="pages navbar-through toolbar-through">
            <div data-page="tabs-swipeable" class="page with-subnavbar page-on-center">
                <div class="tabs-swipeable-wrap swiper-container swiper-container-horizontal">
                    <div class="tabs swiper-wrapper">
                        <div id="{{ category.id }}" class="page-content tab swiper-slide {{ category.swipe_status }} {{ category.class }}" ng-repeat="category in categories"> <!-- swiper-slide-active -->
                            <div class="content-block"> {{ category.swipe_status }}
                                {{ category.class }}
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse faucibus mauris leo, eu bibendum neque congue non. Ut leo mauris, eleifend eu commodo a, egestas ac urna. Maecenas in lacus faucibus, viverra ipsum pulvinar, molestie arcu. Etiam lacinia venenatis dignissim. Suspendisse non nisl semper tellus malesuada suscipit eu et eros. Nulla eu enim quis quam elementum vulputate. Mauris ornare consequat nunc viverra pellentesque. Aenean semper eu massa sit amet aliquam. Integer et neque sed libero mollis elementum at vitae ligula. Vestibulum pharetra sed libero sed porttitor. Suspendisse a faucibus lectus.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="popup pop-details">
    <div class="view navbar-fixed">
        <div class="pages">
            <div class="page">
                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="center">Popup Title</div>
                        <div class="right"><a href="#" class="link close-popup">Done</a></div>
                    </div>
                </div>
                <div class="page-content">
                    <div class="content-block">
                        <p>Here comes popup. You can put here anything, even independent view with its own navigation. Also not, that by default popup looks a bit different on iPhone/iPod and iPad, on iPhone it is fullscreen.</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse faucibus mauris leo, eu bibendum neque congue non. Ut leo mauris, eleifend eu commodo a, egestas ac urna. Maecenas in lacus faucibus, viverra ipsum pulvinar, molestie arcu. Etiam lacinia venenatis dignissim. Suspendisse non nisl semper tellus malesuada suscipit eu et eros. Nulla eu enim quis quam elementum vulputate. Mauris ornare consequat nunc viverra pellentesque. Aenean semper eu massa sit amet aliquam. Integer et neque sed libero mollis elementum at vitae ligula. Vestibulum pharetra sed libero sed porttitor. Suspendisse a faucibus lectus.</p>
                        <p>Duis ut mauris sollicitudin, venenatis nisi sed, luctus ligula. Phasellus blandit nisl ut lorem semper pharetra. Nullam tortor nibh, suscipit in consequat vel, feugiat sed quam. Nam risus libero, auctor vel tristique ac, malesuada ut ante. Sed molestie, est in eleifend sagittis, leo tortor ullamcorper erat, at vulputate eros sapien nec libero. Mauris dapibus laoreet nibh quis bibendum. Fusce dolor sem, suscipit in iaculis id, pharetra at urna. Pellentesque tempor congue massa quis faucibus. Vestibulum nunc eros, convallis blandit dui sit amet, gravida adipiscing libero.</p>
                        <p>Morbi posuere ipsum nisl, accumsan tincidunt nibh lobortis sit amet. Proin felis lorem, dictum vel nulla quis, lobortis dignissim nunc. Pellentesque dapibus urna ut imperdiet mattis. Proin purus diam, accumsan ut mollis ac, vulputate nec metus. Etiam at risus neque. Fusce tincidunt, risus in faucibus lobortis, diam mi blandit nunc, quis molestie dolor tellus ac enim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse interdum turpis a velit vestibulum pharetra. Vivamus blandit dapibus cursus. Aenean lorem augue, vehicula in eleifend ut, imperdiet quis felis.</p>
                        <p>Duis non erat vel lacus consectetur ultricies. Sed non velit dolor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin vel varius mi, a tristique ante. Vivamus eget nibh ac elit tempor bibendum sit amet vitae velit. Proin sit amet dapibus nunc, non porta tellus. Fusce interdum vulputate imperdiet. Sed faucibus metus at pharetra fringilla. Fusce mattis orci et massa congue, eget dapibus ante rhoncus. Morbi semper sed tellus vel dignissim. Cras vestibulum, sapien in suscipit tincidunt, lectus mi sodales purus, at egestas ligula dui vel erat. Etiam cursus neque eu lectus eleifend accumsan vitae non leo. Aliquam scelerisque nisl sed lacus suscipit, ac consectetur sapien volutpat. Etiam nulla diam, accumsan ut enim vel, hendrerit venenatis sem. Vestibulum convallis justo vitae pharetra consequat. Mauris sollicitudin ac quam non congue.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function getRandomArbitrary(min, max) {
	return Math.random() * (max - min) + min;
}
var NexoRest		=	angular.module( 'nexo-restaurant', [ 'angularLoad' ] );
	NexoRest.controller( 'main-view', function( $scope ) {
		
		$scope.categories	=	new Array;
		
		for( var _i = 0; _i < 3; _i++ ) {
			$scope.categories.push({
				name			:	'Category ' + _i,
				id				:	'cat'+ _i,
				items			:	new Array,
				class			:	( _i == 0 ? 'active' : '' ),
				swipe_status	:	( _i == 0 ? 'swiper-slide-active' : ( _i == 1 ? 'swiper-slide-next' : '' ) ) // ( _i == 1 ? 'swiper-slide-next' : '' )
			});
		}
		
		for( var i = 0; i < $scope.categories.length ; i++ ) {
			$scope.categories[i].items.push({
				name 	:	'Item - ' + i,
				price	:	getRandomArbitrary( 0, 800 ),
				desc	:	'Just a sample item ' + i + ', made with love',
				img		:	'http://foodman.org/data/frontFiles/restaurant/restaurant_image/1434111954_food-066.jpg'
			});
		}
		
		console.log( $scope.categories );
		
		/**
		 * Submit order
		**/
		
		$scope.submitOrder	=	function() {
			myApp.confirm( '<?php echo _s( 'Souhaitez-vous confirmer cette commande ?', 'nexo_restaurant' );?>', function( e ) {
				console.log( e );
			})
		}
		
		angularLoad.loadScript( '<?php echo module_url( 'nexo_restaurant' );?>bower_components/Framework7/dist/js/framework7.min.js' );
		angularLoad.loadScript( '<?php echo module_url( 'nexo_restaurant' );?>bower_components/Framework7/dist/js/my-app.js' );
		
		$scope.path		=	'';
		
		var myApp = new Framework7();

		// Export selectors engine
		var $$ = Dom7;
		
		// Add view
		var mainView = myApp.addView('.view-main', {
			// Because we use fixed-through navbar we can enable dynamic navbar
			dynamicNavbar: true
		});		
		
	});
</script> 
<!-- Path to Framework7 Library JS--> 

<!-- Path to your app js--> 
<!-- <script type="text/javascript" src="<?php echo module_url( 'nexo_restaurant' );?>bower_components/Framework7/dist/js/my-app.js"></script>  -->
</body>
</html>
