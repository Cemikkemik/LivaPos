<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->config->load( 'rest' );
global $Options;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo _s( 'Cutomer View', 'nexo_customer_display' );?></title>
        <link rel="stylesheet" media="screen", href="<?php echo module_url( 'nexo_customer_display' ) . '/bower_components/bootstrap/dist/css/bootstrap.min.css';?>">
        <script src="<?php echo module_url( 'nexo_customer_display' ) . '/bower_components/jquery/dist/jquery.min.js';?>" charset="utf-8"></script>
        <script src="<?php echo module_url( 'nexo_customer_display' ) . '/bower_components/angular/angular.min.js';?>" charset="utf-8"></script>
        <script src="<?php echo module_url( 'nexo_customer_display' ) . '/bower_components/angular-scroll/angular-scroll.min.js';?>" charset="utf-8"></script>
        <script src="<?php echo module_url( 'nexo' ) . '/bower_components/numeral/min/numeral.min.js';?>" charset="utf-8"></script>
        <script type="text/javascript">



            var NexoAPI                     =   new Object;

            "use strict";

            /**
            * Money format
            * @param int amount
            * @return string
            **/

            NexoAPI.Format	=	function( int, format ){
                var format	=	typeof format == 'undefined' ? '0,0.00' : format;
                return numeral( int ).format( format );
            };

			/**
			 * Currency Position
			**/

			NexoAPI.CurrencyPosition	=	function( amount ) {
				return '<?php echo addslashes($this->Nexo_Misc->display_currency('before'));?> ' + amount + ' <?php echo addslashes($this->Nexo_Misc->display_currency('after'));?>';
			}

			/**
			 * Currency Position + Money Format
			**/

			NexoAPI.DisplayMoney		=	function( amount ) {
                if( amount != '' ) {
				    return NexoAPI.CurrencyPosition( NexoAPI.Format( parseFloat( amount ) ) );
                }
			}

		</script>
    </head>
    <body ng-app="tendooApp">
        <div class="container-fluid" ng-controller="displayContent">
            <div class="row">
                <div ng-show="leftPane" class="col-lg-{{ leftPaneCols }} col-sm-{{ leftPaneCols }} col-xs-{{ leftPaneCols }}" style="background:#FEFEFE;height:{{rigthHeight}}px;background-image: url('<?php echo @$Options[ store_prefix() . 'cu_display_background_url' ];?>')">
                    <div class="row" style="margin-top:20px;">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <?php if( @$Options[ store_prefix() . 'logo_type' ] == 'text' ): ?>
                                <h2 class="text-center" style="margin-bottom:40px;"><?php echo @$Options[ store_prefix() . 'logo_text' ];?></h2>
                            <?php elseif( @$Options[ store_prefix() . 'logo_type' ] == 'logo' ):?>
                                <img style="margin:0 auto;display:block;max-height:100px;margin-bottom:30px;" src="<?php echo @$Options[ store_prefix() . 'logo_url' ];?>" alt="<?php echo @$Options[ store_prefix() . 'logo_text' ];?>" />
                            <?php endif; ?>
                            <table class="panel table table-bordered table-striped" style="margin-bottom:0px;">
                                <thead>
                                    <tr>
                                        <td><?php echo __( 'Item Name', 'nexo_customer_display' );?></td>
                                        <td width="120"><?php echo __( 'Price', 'nexo_customer_display' );?></td>
                                        <td width="120"><?php echo __( 'Quantity', 'nexo_customer_display' );?></td>
                                        <td width="120"><?php echo __( 'Discount', 'nexo_customer_display' );?></td>
                                        <td width="120"><?php echo __( 'Total', 'nexo_customer_display' );?></td>
                                    </tr>
                                </thead>
                            </table>
                            <div class="toScroll " style="height: {{ rigthHeight - ( ( rigthHeight * 70 )/ 100 ) }}px;overflow-y:hidden;margin-bottom:30px;">
                                <table class="table panel table-bordered table-striped" style="margin-bottom:0px;">
                                    <tr ng-repeat="item in items">
                                        <td><strong>{{ item.DESIGN }}</strong></td>
                                        <td width="120" class="text-right">{{ item.PRIX_DE_VENTE | moneyFormat }}</td>
                                        <td width="120" class="text-right">{{ item.QTE_ADDED }}</td>
                                        <td width="120" class="text-right">{{ item.DISCOUNT_TYPE == 'percentage' ? item.DISCOUNT_PERCENT + '%' : '' }} {{ item.DISCOUNT_TYPE == 'flat' ? item.DISCOUNT_AMOUNT : '' | moneyFormat }}</td>
                                        <td width="120" class="text-right">{{ item.PRIX_DE_VENTE * item.QTE_ADDED | moneyFormat }}</td>
                                    </tr>
                                </table>
                            </div>

                            <table class="panel table table-striped table-bordered">
                                <tr>
                                    <td><strong><?php echo __( 'SubTotal', 'nexo_customer_display' );?></strong></td>
                                    <td width="120" class="text-right">{{ subTotal | moneyFormat }}</td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo __( 'Tax', 'nexo_customer_display' );?></strong></td>
                                    <td width="120" class="text-right">{{ vat | moneyFormat }}</td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo __( 'Total Amount Due', 'nexo_customer_display' );?></strong></td>
                                    <td width="120" class="text-right">{{ totalDue | moneyFormat }}</td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo __( 'Paid', 'nexo_customer_display' );?></strong></td>
                                    <td width="120" class="text-right">{{ paidSoFar | moneyFormat }}</td>
                                </tr>
                                <tr>
                                    <td><h4><?php echo __( 'Balance', 'nexo_customer_display' );?></h4></td>
                                    <td width="120" class="text-right"><h4>{{ balance | moneyFormat }}</h3></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div ng-show="rightPane" class="rightPaneClass col-lg-{{ rightPaneCols }} col-sm-{{ rightPaneCols }} col-xs-{{ rightPaneCols }}" style="background:#fbcd4d;height:{{rigthHeight}}px">
                    <div class="text-center slider-wrapper" ng-show="sliderWrapper"></div>
                </div>
            </div>
        </div>
    </body>
    <style media="screen">
    div.rightPaneClass {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
    }

    div.slider-wrapper {
        display: inline-block;
    }
    </style>
    <script type="text/javascript">
    "use strict";
    var tendooApp     =   angular.module( 'tendooApp', [ 'duScroll' ] );
    </script>
    <?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
    <script type="text/javascript">
    "use strict";

    tendooApp.controller( 'displayContent', [ '$scope', '$http', '$compile', '$interval', function( $scope, $http, $compile, $interval ) {
        $scope.displayStatus        =   'closed';
        $scope.rigthHeight          =   window.innerHeight;
        $scope.scrollDirection      =   'down';
        $scope.scrollLaunched       = false;
        $scope.subTotal             =   0;
        $scope.totalDue             =   0;
        $scope.items                =   [];
        $scope.vat                  =   0;
        $scope.paidSoFar            =   0;
        $scope.balance              =   0;
        $scope.sliders              =   <?php echo json_encode( $sliders );?>;
        $scope.slideTimeInterval    =   <?php echo intval( @$Options[ store_prefix() . 'cud_display_slide_lifetime' ] ) == 0 ? 5000 : intval( @$Options[ store_prefix() . 'cud_display_slide_lifetime' ] ) * 1000;?>;
        $scope.sliderContent        =   '';
        $scope.sliderWrapper        =   false;
        $scope.welcomeMessage       =   '<?php echo addslashes( str_replace("\n", "", str_replace("\r", "", @$Options[ store_prefix() . 'cu_display_welcome_message' ] ) ) );?>';

        $scope.$watch( 'items', function(){
            $scope.subTotal         =   0;
            if( $( '.toScroll' ).scroll()[0].scrollHeight != $( '.toScroll' ).scroll()[0].offsetHeight ) {
                $scope.scrollTable();
            }
            angular.forEach( $scope.items, function( value, key ){
                $scope.subTotal     +=  value.QTE_ADDED * value.PRIX_DE_VENTE;
            });

            $scope.totalDue          =  parseFloat( $scope.subTotal ) + parseFloat( $scope.vat );
        });

        /**
         * Change Customer Status
        **/

        $scope.changeStatus         =   function( status ){
            if( status == 'open' ) {
                $( '.welcome_message' ).remove();
                $scope.rightPaneCols    =   4;
                $scope.leftPaneCols     =   8;
                $scope.leftPane         =   true;
                $scope.rightPane        =   true;
                $scope.sliderWrapper    =   true;

            } else if( status == 'closed') {
                $scope.rightPaneCols    =   12;
                $scope.leftPaneCols     =   0;
                $scope.leftPane         =   false;
                $scope.rightPane        =   true;
                $scope.sliderWrapper    =   false;

                if( $( '.welcome_message' ).length == 0 ) {
                    $( '.rightPaneClass' ).append( '<p class="text-center welcome_message"></p>' );
                    $( '.welcome_message' ).html( $scope.welcomeMessage );
                    angular.element( '.welcome_message' ).css( 'margin-top', $scope.rigthHeight/2 - angular.element( '.welcome_message' ).height() / 2 );
                }
            }
        }

        /**
         * Check Cart status
        **/

        $scope.checkCartStatus         =    function() {
            $interval( function(){
                $http.post( '<?php echo site_url( array( 'rest', 'customer_display', 'cart_status' ) );?>?<?php echo store_get_param( null );?>', {
                    store_id        :   <?php echo get_store_id();?>,
                    register_id     :   '<?php echo @$_GET[ 'register_id'] == null ? 'default' : $_GET[ 'register_id'];?>'
                },{
        			headers			: {
        				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
        			}
        		}).then(function( returned ){
                    if( Object.keys(returned.data).length == 0 ) {
                        $scope.changeStatus( 'closed' );
                        $scope.items        =   [];
                        $scope.subTotal     =   0;
                        $scope.vat          =   0;
                        $scope.totalDue     =   0;
                        $scope.paidSoFar    =   0;
                        $scope.balance      =   0;
                    } else if( Object.keys(returned.data).length > 0 ){
                        if( JSON.stringify($scope.items) !== JSON.stringify(returned.data.items) ) {
                            $scope.changeStatus( 'open' );
                            $scope.items        =   returned.data.items;
                            $scope.vat          =   returned.data.vat;
                            $scope.paidSoFar    =   angular.isDefined( returned.data.paidSoFar ) ? returned.data.paidSoFar : 0;
                            $scope.balance      =   angular.isDefined( returned.data.balance ) ? returned.data.balance : 0;
                            $scope.paidSoFar    =   $scope.paidSoFar == null ? 0 : $scope.paidSoFar;
                            $scope.balance      =   $scope.balance  == null ? 0 : $scope.balance;
                        }
                    }
        		});
            }, 1000)
        }

        $scope.checkCartStatus();

        $scope.changeStatus( 'closed' );

        /**
        *
        * Display Slide
        *
        * @return void
        */

        $scope.displaySlide             =   function( currentIndex ){
            if( $scope.sliders.length > 0 ) {
                if( $scope.sliders.length-1 == currentIndex ) {
                    angular.element( '.slider-wrapper' ).html( $scope.sliders[ currentIndex ].DESCRIPTION );
                    currentIndex            =   0;
                } else {
                    angular.element( '.slider-wrapper' ).html( $scope.sliders[ currentIndex ].DESCRIPTION );
                    currentIndex++;
                }
                angular.element( '.slider-wrapper' ).css( 'margin-top', $scope.rigthHeight/2 - angular.element( '.slider-wrapper' ).height() / 2 );

                console.log( currentIndex );
                return currentIndex;
            }
        }

        /**
         * Scroll table
        **/

        $scope.scrollTable              =   function( ){
            var scrollTo        =   $( '.toScroll' ).scroll()[0].scrollHeight - $( '.toScroll' ).scroll()[0].offsetHeight;
            if( scrollTo > 0 && $scope.scrollDirection == 'down' && $scope.scrollLaunched == false ) {
                $scope.scrollLaunched = true;
                angular.element( '.toScroll' ).duScrollTo( 0, Math.abs( scrollTo ), 3000 ).then( function(){
                    $scope.scrollDirection = 'up';
                    $scope.scrollLaunched = false;
                });
            } else if( scrollTo > 0 && $scope.scrollDirection == 'up' && $scope.scrollLaunched == false ) {
                $scope.scrollLaunched = true;
                angular.element( '.toScroll' ).duScrollTo( 0, 0, 3000 ).then( function(){
                    $scope.scrollLaunched   = false;
                    $scope.scrollDirection  = 'down';
                });
            }
            // scrollTop
        }

        /**
        *
        * Run Slider
        *
        * @return void
        */

        $scope.runSlider            =       function(){
            var currentIndex        =       0;
            if( $scope.sliders.length > 0 ){
                $interval( function(){
                    currentIndex    =   $scope.displaySlide( currentIndex );
                }, $scope.slideTimeInterval );
            }
            $scope.displaySlide(0);
        }

        $scope.runSlider();

    }]);

    </script>
</html>
