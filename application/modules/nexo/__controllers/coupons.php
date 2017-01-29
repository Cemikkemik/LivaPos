<?php
class NexoCouponController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  index
     *  @param
     *  @return
    **/

    public function index( $page = 'list' )
    {
        $AnguCrud       =   new AngularCrudLibrary( store_prefix() . 'nexo_coupons' );

        $AnguCrud->setColumns([
            'ID'                =>  __( 'Id', 'nexo' ),
            'CODE'              =>  __( 'Code', 'nexo' ),
            'DISCOUNT_TYPE'     =>  __( 'Type', 'nexo' ),
            'AMOUNT'            =>  __( 'Valeur / Pourcentage', 'nexo' ),
            'EXPIRY_DATE'       =>  __( 'Date D\'expiration', 'nexo' ),
            'ITEM_IDS'          =>  __( 'Produits ciblés', 'nexo' ),
            'ITEMS_CATEGORIES'  =>  __( 'Catégorie ciblées', 'nexo' ),
            // 'USAGE_LIMIT'       =>  __( 'Limite d\'utilisation', 'nexo' ),
            // 'MINIMUM_AMOUNT'    =>  __( 'Montant minimal', 'nexo' ),
            'CASHIERS_IDS'      =>  __( 'Caissier Récompensé', 'nexo' )
        ]);

        $AnguCrud->setRelation([
            'PRODUCTS_IDS'      =>  [
                'table'         =>  store_prefix() . 'nexo_articles',
                'col'           =>  'DESIGN',
                'comparison'    =>  'ID',
                'alias'         =>  'ITEM_IDS'
            ],
            'PRODUCT_CATEGORIES'  =>  [
                'table'         =>  store_prefix() . 'nexo_categories',
                'col'           =>  'NOM',
                'comparison'    =>  'ID',
                'alias'         =>  'ITEMS_CATEGORIES'
            ],
            'REWARDED_CASHIER'  =>  [
                'table'         =>  'aauth_users',
                'col'           =>  'name',
                'comparison'    =>  'id',
                'alias'         =>  'CASHIERS_IDS'
            ]
        ]);

        $AnguCrud->config([
            'baseUrl'           =>  site_url( array( 'dashboard', store_slug(), 'nexo_coupons', 'index' ) ),
            'page'              =>  $page,
            'crudTitle'         =>  __( 'Coupons', 'nexo' ),
            'primaryCol'        =>  'ID',
            'fieldsType'        =>  [
                'AMOUNT'                    =>  'number',
                'MINIMUM_AMOUNT'            =>  'number',
                'USAGE_LIMIT'               =>  'number',
                'DISCOUNT_TYPE'             =>  'select_options',
                'ITEM_IDS'                  =>  'select_relation_multiple',
                'ITEMS_CATEGORIES'          =>  'select_relation_multiple',
                'CASHIERS_IDS'              =>  'select_relation',
                'EXPIRY_DATE'               =>  'datetime'
            ],
            'validations'       =>  [
                'AMOUNT'        =>  [ 'required' ],
                'CODE'          =>  [ 'required' ]
            ],
            'selectOptions'     =>  [
                'DISCOUNT_TYPE' =>  [
                    [ 'key' =>  'percentage', 'value' =>  __( 'Pourcentage', 'nexo' )],
                    [ 'key' =>  'fixed', 'value' =>  __( 'Montant Fixe', 'nexo' )],
                ]
            ],
            'fieldDescription'  =>  [
                'AMOUNT'        =>  __( 'Définissez la valeur du coupon, en pourcentage ou montant fixe.', 'nexo' ),
                'CODE'          =>  __( 'Ce champ représente l\'identifiant du coupon. Il ne peut être utilisé qu\'une seul fois.', 'nexo' ),
                'PRODUCTS_IDS'  =>  __( 'Ce coupon ne s\'appliquera au panier que si un des produits sélectionné n\'est ajouté au panier.', 'nexo' ),
                'DISCOUNT_TYPE' =>  __( 'Type du coupon : Pourcentage ou Montant Fixe.', 'nexo' ),
                'USAGE_LIMIT'   =>  __( 'Après un nombre définit d\'utilisation, ce coupon ne sera plus valable', 'nexo' ),
                'MINIMUM_AMOUNT'    =>  __( 'Montant minimal du panier afin que le coupon ne puisse s\'appliquer', 'nexo' ),
                'PRODUCT_CATEGORIES'    =>  __( 'Ce coupon ne s\'appliquera au panier que si une produit du panier appartient à une des catégories sélectionnée.', 'nexo' ),
                'REWARDED_CASHIER'  =>  __( 'Lorsque les récompenses des caissiers est activée, à chaque utilisation du coupon, le caissier sélectionné recevra des points.', 'nexo' ),
                'EXPIRY_DATE'       =>  __( 'Ce coupon ne sera plus utilisable après cette date.', 'nexo' )
            ]
        ]);

        $AnguCrud->addDefaultButton([
            'text'  =>  __( 'Imprimer', 'nexo' ),
            'url'   =>  site_url( array( 'dashboard', 'nexo_coupons', 'print_all' ) )
        ]);

        $AnguCrud->addSelectingButton([
            'icon'  =>  'print',
            'allow_multiple'    =>  true, // only_multiple, only_unique
            'label' =>  __( 'Imprimer', 'nexo' ),
            'url'   =>  site_url( array( 'dashboard', 'nexo_coupons', 'print_selected' ) )
        ]);

        return $AnguCrud->LoadView();
    }
}
