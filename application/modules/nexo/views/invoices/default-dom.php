<div  ng-controller="invoiceCTRL">
	<div style="background:#FFF;padding:15px;box-shadow:0 0 1px #333" class="invoice-container">
		<!-- title row -->
		<div class="row">
			<div class="col-xs-12">

			</div>
			<!-- /.col -->
		</div>
		<!-- info row -->
		<div class="row invoice-info">
			<div class="col-sm-6 col-xs-6">
				<?php echo __( 'Addrese de livraison', 'nexo' );?><br>
				<address>
					<strong><?php echo __( 'Customer :', 'nexo' );?></strong> {{ data.order[0].customer_name }}
					<br ng-show="shipping[0].address1"> {{ shipping[0].address_1 ? shipping[0].address_1 + ',' : '' }}
					<br ng-show="shipping[0].address2"> {{ shipping[0].address_2 ? shipping[0].address_2 + ',' : '' }}
					<br ng-show="shipping[0].city"> {{ shipping[0].city ? shipping[0].city + ',' : '' }} {{ shipping[0].pobox ? shipping[0].pobox + ',' : '' }}
					<br> <?php echo __( 'Téléphone', 'nexo' );?>: (555) 539-1444
					<br/> Email: mekoya@example.com
				</address>
			</div>

			<div class="col-sm-6 col-xs-6">
				<?php echo __( 'Addrese de facturation', 'nexo' );?><br>
				<b>
					<?php echo __( 'Facture N°:', 'nexo' );?> </b>{{ ( "00000" + data.order[0].ID ).slice(-6) }}
				<br/>
				<b>
					<?php echo __( 'Code :', 'nexo' );?>
				</b> {{ data.order[0].CODE }}
				<br/>
				<b>Payment Due:</b> 2/22/2014
				<br/>
				<b>Account:</b> 968-34567
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- Table row -->
		<div class="row">
			<div class="col-xs-12 table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>
								<?php echo __( 'Produit', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Prix', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Remise', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Quantité', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Total', 'nexo' );?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="item in data.products">
							<td>{{ item.DESIGN }}</td>
							<td>{{ item.PRIX | moneyFormat }}</td>
							<td>{{ item.PRIX | moneyFormat }}</td>
							<td>{{ item.QUANTITE }}</td>
							<td>{{ item.PRIX * item.QUANTITE | moneyFormat }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<div class="row">
			<!-- accepted payments column -->
			<div class="col-xs-6">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th style="width:50%"><?php echo __( 'Sous-Total', 'nexo' );?></th>
							<td>{{ subTotal( data.products ) | moneyFormat }}</td>
						</tr>
						<tr>
							<th><?php echo __( 'Livraison', 'nexo' );?>:</th>
							<td>{{ data.order[0].SHIPPING_AMOUNT | moneyFormat }}</td>
						</tr>
						<tr>
							<th><?php echo __( 'Total', 'nexo' );?></th>
							<td>{{ total()}}</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-xs-6">
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- this row will not appear when printing -->
		<div class="row no-print hidden-print">
			<div class="col-xs-12">
				<a href="javascript:void(0)" print-item=".invoice-container" class="btn btn-default">
					<i class="fa fa-print"></i> <?php echo __( 'Imprimer', 'nexo' );?></a>
				<button class="btn btn-primary pull-right" style="margin-right: 5px;">
					<i class="fa fa-download"></i> Generate PDF</button>
			</div>
		</div>
	</div>
</div>