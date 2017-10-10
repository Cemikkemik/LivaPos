<script>
moment.locale( 'fr' );
tendooApp.controller( 'ilhanCTRL', [ '$scope', '$http', '$compile', function( $scope, $http, $compile ){

     $scope.openCalendar           =    function(){

          $('[ng-controller="ilhanCTRL"]').replaceWith( `
          <div class="btn-group">
               <div class='input-group date' id='datetimepicker2' style="width:200px;float:right;">
                    <input type='text' style="height:30px" class="form-control calendar-input" />
                    <span class="input-group-addon calendar">
                         <span class="glyphicon glyphicon-calendar"></span>
                    </span>
               </div>
          </div>
          ` );

          $( '.calendar-input' ).val( v2Checkout.__inputDate );

          $(function () {
               $('#datetimepicker2').datetimepicker({
                    dayViewHeaderFormat      :    'DD/MM/YYYY HH:mm',
                    format                   :    'DD/MM/YYYY HH:mm',
                    locale                   :    'fr'
               });
          });

          $( '.calendar' ).trigger( 'click' );

          $( '.calendar-input' ).bind( 'blur', function(){
               if( $( '.calendar-input' ).val() != undefined ) {
                    v2Checkout.__inputDate         =    $( '.calendar-input' ).val();
                    $( this ).closest( '.btn-group' ).replaceWith( `
                    <button ng-controller="ilhanCTRL" ng-click="openCalendar()" class="btn btn-sm btn-default calendar-button ng-scope">
                    <i class="fa fa-calendar"></i> <?php echo __( 'Date', 'ilhan' );?></button>
                    `);

                    $( '[ng-controller="ilhanCTRL"]' ).replaceWith( $compile( 
                         $( '[ng-controller="ilhanCTRL"]' )[0].outerHTML
                    )( $scope ) );
               }
          });
     }

}]);

     NexoAPI.events.addFilter( 'test_order_type', function( status ) {
          if( v2Checkout.__inputDate != undefined && v2Checkout.__inputDate != '' ) {
               $.ajax( '<?php echo site_url([ 'dashboard', store_slug(), 'ilhan', 'change_date' ]);?>', {
                    data      :    _.extend({
                         'date'    :    v2Checkout.__inputDate,
                         'order'   :    status[1].order_id
                    }, tendoo.csrf_data ),
                    method    :    'POST',
                    success   :    function(){
                         $( 'body' ).append( '<iframe id="receipt-wrapper" style="visibility:hidden;height:0px;width:0px;position:absolute;top:0;" src="<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'print', 'order_receipt' ));?>/' + status[1].order_id + '?refresh=true&autoprint=true"></iframe>' );
                    }
               });
          }
          status[0]      =    null;
          return status;
     });

     NexoAPI.events.addFilter( 'callback_message', function( details ) {
          if( details[2] == null ) {
               details[0].title    =    '<?php echo __( 'Commande Sauvegardée', 'illhan' );?>';
               details[0].msg      =    '<?php echo __( 'La commande a été enregistré à une date différée', 'ilhan' );?>';
               details[0].type     =    'success';
          }
          return details;
     })

</script>