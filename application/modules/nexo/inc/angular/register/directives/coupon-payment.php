<script>
tendooApp.directive( 'couponPayment', function(){

    HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'h3.text-center' )
	.each( 'style', 'margin:0px;margin-bottom:10px;' )
	.textContent	=	'<?php echo _s( 'Appliquer un coupon', 'nexo' );?>';

    HTML.query( 'angular-cache' )
	.add( 'div.input-group.input-group-lg.payment-field-wrapper>span.input-group-addon.hidden-sm' )
	.textContent	=	'<?php echo _s( 'Code', 'nexo' );?>';

    HTML.query( '.payment-field-wrapper' )
	.add( 'input.form-control.coupon-field' )
	.each( 'ng-model', 'couponCode' )
	.each( 'ng-focus', 'bindKeyBoardEvent( $event )' )
	.each( 'placeholder', '<?php echo _s( 'Spécifier le code', 'nexo' );?>' );

    HTML.query( '.payment-field-wrapper' )
	.add( 'span.input-group-btn.paymentButtons>button.btn.addPaymentButton.hidden-xs.hidden-sm' )
	.each( 'ng-click', 'checkCoupon()' )
    .each( 'ng-hide', 'isCouponValid()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
	.textContent	=	'{{ defaultAddPaymentText }}';
    HTML.query( '.paymentButtons' )
	.add( 'button.btn.addPaymentButton.hidden-lg.hidden-md' )
	.each( 'ng-click', 'checkCoupon()' )
    .each( 'ng-hide', 'isCouponValid()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
    .add( 'i.fa.fa-refresh' );

    // Quick Apply Button
    HTML.query( '.paymentButtons' )
	.add( 'button.btn.applyCouponButton.hidden-xs.hidden-sm.btn-success' )
	.each( 'ng-click', 'applyCoupon()' )
    .each( 'ng-show', 'isCouponValid()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
    .textContent    =   '<?php echo _s( 'Utiliser', 'nexo' );?>';
    HTML.query( '.paymentButtons' )
	.add( 'button.btn.applyCouponButton.hidden-lg.hidden-md.btn-success' )
	.each( 'ng-click', 'applyCoupon()' )
    .each( 'ng-show', 'isCouponValid()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
    .add( 'i.fa.fa-thumbs-o-up' )
    HTML.query( 'angular-cache' ).add( 'br' );

    // Cancel coupon usage
    HTML.query( '.paymentButtons' )
	.add( 'button.btn.cancelCouponButton.btn-danger' )
	.each( 'ng-click', 'cancelCoupon()' )
    .each( 'ng-show', 'isCouponValid()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
    .add( 'i.fa.fa-remove' )
    HTML.query( 'angular-cache' ).add( 'br' );

    HTML.query( 'angular-cache' )
    .add( 'table.table.table-bordered.coupon-details>tr*6');

    HTML.query( '.coupon-details' )
    .each( 'ng-show', 'couponDetails.length > 0' );

    HTML.query( '.coupon-details tr' )
    .add( 'td*2' );

    HTML.query( '.coupon-details tr td' )
    .only(0)
    .each( 'width', '300' );

    HTML.query( '.coupon-details tr' ).only(0).query( 'td' ).only(0)
    .add( 'strong' )
    .textContent    =   '<?php echo _s( 'Code', 'nexo' );?>';
    HTML.query( '.coupon-details tr' ).only(0).query( 'td' ).only(1)
    .textContent    =   '{{ couponDetails[0].CODE }}';

    HTML.query( '.coupon-details tr' ).only(1).query( 'td' ).only(0)
    .add( 'strong' )
    .textContent    =   '<?php echo _s( 'Type', 'nexo' );?>';
    HTML.query( '.coupon-details tr' ).only(1).query( 'td' ).only(1)
    .textContent    =   '{{ couponDetails[0].DISCOUNT_TYPE }}';

    HTML.query( '.coupon-details tr' ).only(2).query( 'td' ).only(0)
    .add( 'strong' )
    .textContent    =   '<?php echo _s( 'Valeur', 'nexo' );?>';
    HTML.query( '.coupon-details tr' ).only(2).query( 'td' ).only(1)
    .textContent    =   '{{ caculateCouponAmount( couponDetails[0] ) }}';

    // For validity
    HTML.query( '.coupon-details tr' ).only(3)
    .each( 'ng-class', '{ "danger" : couponValidityError }');
    HTML.query( '.coupon-details tr' ).only(3).query( 'td' ).only(0)
    .add( 'strong' )
    .textContent    =   '<?php echo _s( 'Validité', 'nexo' );?>';
    HTML.query( '.coupon-details tr' ).only(3).query( 'td' ).only(1)
    .textContent    =   '{{ couponCheckDate( couponDetails[0].EXPIRY_DATE ) | date }}';

    // For Error
    HTML.query( '.coupon-details tr' ).only(4)
    .each( 'ng-class', '{ "danger" : couponProductError }');
    HTML.query( '.coupon-details tr' ).only(4).query( 'td' ).only(0)
    .add( 'strong' )
    .textContent    =   '<?php echo _s( 'Produits concernés', 'nexo' );?>';
    HTML.query( '.coupon-details tr' ).only(4).query( 'td' ).only(1)
    .textContent    =   '{{ couponShowProducts( couponDetails[0].PRODUCTS_IDS ) | date }}';

    // For Error
    HTML.query( '.coupon-details tr' ).only(5)
    .each( 'ng-class', '{ "danger" : couponCategoryError }');
    HTML.query( '.coupon-details tr' ).only(5).query( 'td' ).only(0)
    .add( 'strong' )
    .textContent    =   '<?php echo _s( 'Catégories concernées', 'nexo' );?>';
    HTML.query( '.coupon-details tr' ).only(5).query( 'td' ).only(1)
    .textContent    =   '{{ couponCheckCategories( couponDetails[0].PRODUCT_CATEGORIES ) | date }}';

    angular.element( '.addPaymentButton' )
	.addClass( 'btn-{{defaultAddPaymentClass}}' );

    var DOM		=	angular.element( 'angular-cache' ).html();
	angular.element( 'angular-cache' ).remove();

    return {
		template 	:	DOM
	}

    // ,
    // scope		:	{
    // 	payment							:	'=',
    // 	paidAmount						:	'=',
    // 	addPayment						:	'=',
    // 	bindKeyBoardEvent				:	'=',
    // 	cancelPaymentEdition			:	'=',
    // 	defaultAddPaymentText			:	'=',
    // 	defaultAddPaymentClass			:	'=',
    // 	defaultSelectedPaymentText		:	'=',
    // 	defaultSelectedPaymentNamespace	:	'=',
    // 	showCancelEditionButton			:	'='
    // }
});
</script>
