<div  ng-controller="invoiceCTRL">
	<div style="background:#FFF;padding:15px;box-shadow:0 0 1px #333">
		<!-- title row -->
		<div class="row">
			<div class="col-xs-12">

			</div>
			<!-- /.col -->
		</div>
		<!-- info row -->
		<div class="row invoice-info">
			<div class="col-sm-6 col-xs-6">
				From
				<address>
					<strong><?php echo __( 'Customer :', 'nexo' );?></strong> {{ data.order[0].customer_name }}
					<br ng-show="shipping[0].address1"> {{ shipping[0].address_1 }},
					<br ng-show="shipping[0].address2"> {{ shipping[0].address_2 }},
					<br ng-show="shipping[0].city"> {{ shipping[0].city }}, {{ shipping[0].pobox }}
					<br> <?php echo __( 'Téléphone', 'nexo' );?>: (555) 539-1444
					<br/> Email: mekoya@example.com
				</address>
			</div>

			<div class="col-sm-6 col-xs-6">
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
				<p class="lead">Payment Methods:</p>
				<img src="../../dist/img/credit/visa.png" alt="Visa" />
				<img src="../../dist/img/credit/mastercard.png" alt="Mastercard" />
				<img src="../../dist/img/credit/american-express.png" alt="American Express" />
				<img src="../../dist/img/credit/paypal2.png" alt="Paypal" />
				<p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
					Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr jibjab, movity
					jajah plickers sifteo edmodo ifttt zimbra.
				</p>
			</div>
			<!-- /.col -->
			<div class="col-xs-6">
				<p class="lead">Amount Due 2/22/2014</p>
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th style="width:50%">Subtotal:</th>
							<td>$250.30</td>
						</tr>
						<tr>
							<th>Tax (9.3%)</th>
							<td>$10.34</td>
						</tr>
						<tr>
							<th>Shipping:</th>
							<td>$5.80</td>
						</tr>
						<tr>
							<th>Total:</th>
							<td>$265.24</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- this row will not appear when printing -->
		<div class="row no-print">
			<div class="col-xs-12">
				<a href="invoice-print.html" target="_blank" class="btn btn-default">
					<i class="fa fa-print"></i> Print</a>
				<button class="btn btn-primary pull-right" style="margin-right: 5px;">
					<i class="fa fa-download"></i> Generate PDF</button>
			</div>
		</div>
	</div>
</div>