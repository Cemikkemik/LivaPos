<?php global $Options; $this->load->config( 'rest' ); ?>
<?php include_once( APPPATH . 'modules/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script type="text/javascript">
    var commissionReportCTRL        =   function( $scope, $http ) {
        $scope.beauticans           =   <?php echo json_encode( $Alvaro_Library->get_cashiers(  $this->auth->list_users( 'shop_cashier' ) ) );?>;

        /**
         *  get Report
         *  @param void
         *  @return void
        **/

        $scope.getReport            =   function(){

            if( $scope.beautican == null ) {
                return NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select a beautican', 'alvaro' );?>' );
            }

            if( $scope.startDate == null || $scope.startDate == '' ) {
                return NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select a starting date', 'alvaro' );?>' );
            }

            if( $scope.endDate == null || $scope.endDate == '' ) {
                return NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select an ending date', 'alvaro' );?>' );
            }

            $http.post( '<?php echo site_url([ 'rest', 'alvaro_rest', 'get_commission_report', store_get_param( '?' ) ] );?>', {
                star_date       :   $scope.startDate,
                end_date        :   $scope.endDate,
                beautican       :   $scope.beautican.user_id
            },{
    			headers			:	{
    				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
    			}
    		}).then( returned => {
                $scope.commissions  =   returned.data;
            });
        }

        angular.element( '.endDate' ).bind( 'blur', function() {
            $scope.endDate  =   $( this ).val();
            $scope.startDate  =   $( '.startDate' ).val();
        });

        angular.element( '.startDate' ).bind( 'blur', function() {
            $scope.startDate  =   $( this ).val();
        });
    };

    commissionReportCTRL.$inject        =   [ '$scope', '$http' ];

    tendooApp.controller( 'commissionReportCTRL', commissionReportCTRL );

    // Date Picker
    $(function () {
    	$('#datetimepicker6').datetimepicker({
    		format	:	'YYYY-MM-DD'
    	});
    	$('#datetimepicker7').datetimepicker({
    		useCurrent: false, //Important! See issue #1075
    		format	:	'YYYY-MM-DD'
    	});
    	$("#datetimepicker6").on("dp.change", function (e) {
    		$('#datetimepicker7').data("DateTimePicker").minDate(e.date);
    	});
    	$("#datetimepicker7").on("dp.change", function (e) {
    		$('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
    	});
    });
</script>
