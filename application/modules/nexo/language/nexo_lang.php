<?php
// Deprecated
$lang[ 'license-expired' ]            =    tendoo_error(__('Votre licence a expiré. Veuillez contacter l\'administrateur pour renouveller votre license.', 'nexo'));
$lang[ 'invalid-activation-key' ]    =    tendoo_error(__('La licence que vous avez spécifiée est invalide. La précédente licence a été restaurée. Veuillez contacter l\'administrateur pour corriger le problème.', 'nexo'));
$lang[ 'unable-to-connect' ]        =    tendoo_error(__('Impossible d\'établir une connexion fiable vers le serveur. Verifiez votre connexion internet. <br>Vous devez vous connecter pour valider l\'authenticité de cette licence.', 'nexo'));
$lang[ 'license-activated' ]        =    tendoo_success(__('Votre licence à été activée. Merci d\'avoir renouvellé votre abonnement.', 'nexo'));
$lang[ 'license-has-expired' ]        =    tendoo_error(__('Cette licence n\'est plus valide. Veuillez fournir une licence valide.', 'nexo'));

// Quote Lang lines
$lang[ 'deleted-quotes-msg' ]        =    __('Les commandes suivantes ont été supprimées automatiquement pour expiration : %s Les produits ont été restauré dans la boutique.', 'nexo');
$lang[ 'deleted-quotes-title' ]        =    __('Suppression automatique des commandes devis', 'nexo');
$lang[ 'cant-delete-used-item' ]        =    __('Vous ne pouvez pas supprimer cet élément, car il est en cours d\'utilisation.', 'nexo');
$lang[ 'permission-denied' ]        =    __('Vous n\'avez pas l\'autorisation nécessaire pour effectuer cette action.', 'nexo');
$lang[ 'default-customer-required' ]    =    tendoo_warning(__('Vous devez définir un compte client par défaut, avant d\'effectuer une vente.', 'nexo'));
$lang[ 'order_edit_not_allowed' ]    =    tendoo_error(__('Vous ne pouvez pas modifier une commande complète ou ayant reçu une avance. Pour les commandes ayant reçu une avance, vous pouvez les compléter pour terminer le paiement.', 'nexo'));
$lang[ 'order_not_found' ]            =    tendoo_error(__('Cette commande est introuvable', 'nexo'));
$lang[ 'order_proceeded' ]            =    tendoo_success(__('La commande à été correctement été complétée', 'nexo'));
$lang[ 'nexo_order_complete' ]        =    __('Complète', 'nexo');
$lang[ 'nexo_order_advance' ]        =    __('Incomplète', 'nexo');
$lang[ 'nexo_order_quote' ]            =    __('Devis', 'nexo');
$lang[ 'disabled' ]                    =    __('Désactivée', 'nexo');
$lang[ 'nexo_flat_discount' ]        =    __('Remise à prix fixe', 'nexo');
$lang[ 'nexo_percentage_discount' ]    =    __('Remise au pourcentage', 'nexo');
$lang[ 'yes' ]                        =    __('Oui', 'nexo');
$lang[ 'no' ]                        =    __('Non', 'nexo');
$lang[ 'cash' ]                        =    __('Paiement en espèces', 'nexo');
$lang[ 'cheque' ]                    =    __('Chèque', 'nexo');
$lang[ 'bank_transfert' ]            =    __('Transfert Bancaire', 'nexo');
$lang[ 'print_disabled' ]    =    tendoo_error(__('Ce type de commande ne peut pas être imprimé.', 'nexo'));
$lang[ 'stripe']      = __('Stripe', 'nexo');
$lang[ 'nexo_order_web' ]			=	__( 'Web', 'nexo' );
$lang[ 'balance_opening' ]			=	__( 'Solde d\'ouverture', 'nexo' );
$lang[ 'balance_closing' ]			=	__( 'Solde de fermeture', 'nexo' );

// @since 2.7.5
$lang[ 'create_registers' ]			=	__( 'Créer des caisses', 'nexo' );
$lang[ 'edit_registers' ]			=	__( 'Modifier des caisses', 'nexo' );
$lang[ 'delete_registers' ]			=	__( 'Supprimer des caisses', 'nexo' );
$lang[ 'view_registers' ]			=	__( 'Peut consulter les caisses', 'nexo' );

$lang[ 'create_registers_details' ]			=	__( 'Donne accès à la création des caisses enregistreuses.', 'nexo' );
$lang[ 'edit_registers_details' ]			=	__( 'Donne accès à la modification des caisses enregistreuses.', 'nexo' );
$lang[ 'delete_registers_details' ]			=	__( 'Donne accès à la suppressions des caisses enregistreuses.', 'nexo' );
$lang[ 'view_registers_details' ]			=	__( 'Donne accès à la liste des caisses enregistreuses.', 'nexo' );


$lang[ 'register_open' ]			=	__( 'Ouvert', 'nexo' );
$lang[ 'register_closed' ]			=	__( 'Fermé', 'nexo' );
$lang[ 'register_locked' ]			=	__( 'Verrouillé', 'nexo' );

$lang[ 'nexo_cashier' ]				=	__('Caissier', 'nexo');
$lang[ 'nexo_cashier_details' ]		=	__('Permet de gérer la vente des articles, la gestion des clients.', 'nexo');
$lang[ 'nexo_shop_manager' ]		=	__('Gérant de la boutique', 'nexo');
$lang[ 'nexo_shop_manager_details' ]=	__('Permet de gérer la vente des articles, la gestion des clients, la modification des réglages et accède aux rapports.', 'nexo');
$lang[ 'nexo_tester' ]				=	__('Privilège pour testeur', 'nexo');
$lang[ 'nexo_tester_details' ]		=	__('Effectue toutes tâches d\'ajout et de modification. Ne peux pas supprimer du contenu.', 'nexo');

$lang[ 'register_not_found' ]		=	tendoo_error( __( 'La caisse demandée est introuvable.', 'nexo' ) );
$lang[ 'register_is_closed' ]		=	tendoo_error( __( 'La caisse doit être ouverte, avant de procéder à la vente.', 'nexo' ) );
$lang[ 'unknow_register_status' ]	=	tendoo_error( __( 'Statut de la caisse inconnu.', 'nexo') );
$lang[ 'register_is_locked' ]		=	tendoo_info( __( 'Cette caisse a été expressément vérrouillée. Elle ne peut pas être ouverte. Contactez l\'administrateur pour en savoir plus.', 'nexo' ) );
$lang[ 'register_busy' ]			=	tendoo_error( __( 'La caisse est en cours d\'utilisation. Si le problème persiste, contactez l\'administrateur', 'nexo' ) );
$lang[ 'register_has_been_closed' ]		=	tendoo_success( __( 'La caisse a été correctement fermée', 'nexo' ) );

// @since 2.7.9
$lang[ 'receipt_default' ]					=	__( 'Reçu par défaut', 'nexo');
$lang[ 'custom_receipt' ]					=	__( 'Reçu personnalisé', 'nexo' );

// @since 2.7.8
$lang[ 'unknow-store' ]						= 	tendoo_error( __( 'Boutique introuvable', 'nexo' ) );
$lang[ 'create_shop' ]						=	__( 'Création des boutiques', 'nexo' );
$lang[ 'delete_shop' ]						=	__( 'Suppression des boutiques', 'nexo' );
$lang[ 'edit_shop' ]						=	__( 'Modification des boutiques', 'nexo' );
$lang[ 'enter_shop' ]						=	__( 'Servir dans une boutique', 'nexo' );
$lang[ 'create_shop_details' ]				=	__( 'Cet utilisateur pourra créer des boutiques', 'nexo' );
$lang[ 'delete_shop_details' ]				=	__( 'Cet utilisateur pourra supprimer des boutiques', 'nexo' );
$lang[ 'edit_shop_details' ]				=	__( 'Cet utilisateur pourra modifier des boutiques', 'nexo' );
$lang[ 'enter_shop_details' ]				=	__( 'Cet utilisateur pourra servir dans une boutique', 'nexo' );
// @since 2.8.0
$lang[ 'opened' ]							=	__( 'Ouvert', 'nexo' );
$lang[ 'closed' ]							=	__( 'Fermé', 'nexo' );
$lang[ 'unavailable' ]						=	__( 'Indisponible', 'nexo' );
$lang[ 'nexo-feature-unavailable' ]			=	tendoo_info( __( 'Cette fonctionnalité est désactivée.', 'nexo' ) );
// @since 2.8.2
$lang[ 'physical_item' ]					=	__( 'Article Physique', 'nexo' );
$lang[ 'numerical_item' ]					=	__( 'Article Numérique', 'nexo' );
$lang[ 'item_on_sale' ]						=	__( 'En vente', 'nexo' );
$lang[ 'item_out_of_stock_disabled' ]		=	__( 'Indisponible', 'nexo' );
$lang[ 'enabled' ]							=	__( 'Activé', 'nexo' );
$lang[ 'disabled' ]							=	__( 'Désactivé', 'nexo' );
$lang[ 'feature-disabled' ]					=	tendoo_info( __( 'Cette fonctionnalité à été exprèssement désactivée.', 'nexo' ) );
$lang[ 'nexo_order_refunded' ]				=	__( 'Remboursé', 'nexo' );
$lang[ 'default' ]							=	__( 'Par défaut', 'nexo' );
$lang[ 'nexo_order_partialy_refunded' ]		=	__( 'Partiellement Remboursé', 'nexo' );
$lang[ 'creditcard' ]						=	__( 'Carte de crédit', 'nexo' );
$lang[ 'multi' ]							=	__( 'Paiement multiple', 'nexo' );

// @since 3.0.1
$lang[ 'create_coupons_details' ]           =   __( 'Donne les droits pour créer des coupons', 'nexo' );
$lang[ 'create_coupons' ]                   =   __( 'Création des coupon', 'nexo' );
$lang[ 'edit_coupons_details' ]             =   __( 'Donne les droits pour modifier des coupons', 'nexo' );
$lang[ 'edit_coupons' ]                     =   __( 'Modifier les coupons', 'nexo' );
$lang[ 'delete_coupons_details' ]           =   __( 'Donne les droits pour supprimer des coupons', 'nexo' );
$lang[ 'delete_coupons' ]                   =   __( 'Suppressions des coupons', 'nexo' );
$lang[ 'coupon' ]                           =   __( 'Coupon', 'nexo' );


// @since 3.0.20
$lang[ 'create_item_stock' ]                =   __( 'Gestion du stock', 'nexo' );
$lang[ 'create_item_stock_details' ]        =   __( 'Confère la capacité à ajouter une entrée dans le stock', 'nexo' );
$lang[ 'edit_item_stock' ]                  =   __( 'Modification du stock', 'nexo' );
$lang[ 'edit_item_stock_details' ]          =   __( 'Confère la capacité à modifier une entrée dans le stock', 'nexo' );
$lang[ 'delete_item_stock' ]                =   __( 'Gestion du stock', 'nexo' );
$lang[ 'delete_item_stock_details' ]        =   __( 'Confère la capacité à supprimer une entrée dans le stock', 'nexo' );
$lang[ 'cant_delete_stock_flow' ]           =   __( 'Vous ne pouvez pas supprimer cette entrée, car le stock disponible ne le permet pas.', 'nexo' );

$lang[ 'transfert_in' ]         =	__( 'Stock Accepté', 'nexo' );
$lang[ 'transfert_out' ] 		=	__( 'Stock Transféré', 'nexo' );
$lang[ 'transfert_rejected' ] 	=	__( 'Transfert Rejeté', 'nexo' );
$lang[ 'transfert_canceled' ]	=	__( 'Transfert Annulé', 'nexo' );
$lang[ 'defective' ]            =	__( 'Défectueux', 'nexo' );
$lang[ 'supply' ]				     =	__( 'Approvisionnement', 'nexo' );
$lang[ 'usable' ] 				     =	__( 'Retour Stock', 'nexo' );
$lang[ 'adjustment' ]			     =	__( 'Réduction', 'nexo' );
$lang[ 'import' ]			          =	__( 'Importé', 'nexo' );
$lang[ 'sale' ]                         =    __( 'Vente', 'nexo' );
$lang[ 'store-closed' ]			    =	tendoo_error( __( 'Vous ne pouvez pas accéder à cette boutique, car elle est fermée.', 'nexo' ) );