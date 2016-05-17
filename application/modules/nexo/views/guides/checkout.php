<script type="text/javascript">
$( document ).ready( function(){
	// Instance the tour
	var tour = new Tour({
		  steps: [
		  {
			element: "#meta-produits",
			title: "<?php echo addslashes(__('Détails de la commande', 'nexo'));?>",
			content: "<?php echo addslashes(__('Vous pouvez dans cette zone choisir un client, créer un client, définir un moyen de paiement, la somme perçu, et la remise expresse.', 'nexo'));?>"
		  },
		  {
			element: "#codebar-wrapper",
			title: "<?php echo addslashes(__('Gestion des produits', 'nexo'));?>",
			content: "<?php echo addslashes(__('Cette zone vous permet principalement d\'ajouter un produit en utilisant un lecteur de code barre. Vous pouvez également utiliser la liste des produits disponible en cliquant sur "Afficher la liste".', 'nexo'));?>",
			placement	:	'bottom',
			onShow	:	function(){
				 $( '#fetch_codebar' ).trigger( 'click' );
			}
		  },
		  {
			element: "#filter-wrapper",
			title: "<?php echo addslashes(__('Liste des produits', 'nexo'));?>",
			content: "<?php echo addslashes(__('Vous pouvez rapidement trouver un produit disponible dans votre boutique. Les produits épuisés ne s\'affichent pas. Cette section vous permettra également de voir les différents produits qui sont en promotion.', 'nexo'));?>",
			placement	:	'right',
			onEnd	:	function(){
				 $( '#fetch_codebar' ).trigger( 'click' );
			}
		  },
		  {
			element: "#nexo-cart",
			title: "<?php echo addslashes(__('Contenu du panier', 'nexo'));?>",
			content: "<?php echo addslashes(__('Tous les produits ajoutés dans le panier s\'affichent ici. Vous pouvez depuis cette section augmenter ou réduire la quantité. Les produits en promotion sont mis en surbrillance.', 'nexo'));?>",
			placement	:	'top'
		  },
		  {
			element: "#nexo-checkout-details-guide",
			title: "<?php echo addslashes(__('Détails de la commande', 'nexo'));?>",
			content: "<?php echo addslashes(__('Cette section affiche les détails de la commande. Le montant de la commandee, les taxes, la rémise, la somme perçu, et la somme à rembourser.', 'nexo'));?>",
			placement	:	'left'
		  }
		  
		],
		storage		:	false,
		backdrop	:	true
	});
	
	// Initialize the tour
	tour.init();
	
	// Start the tour
	tour.start();
});
</script>