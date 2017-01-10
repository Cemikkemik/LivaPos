<?php
Trait Nexo_coupons {

    /**
     *  Coupon Delete
     *  @param int coupon id
     *  @return json
    **/

    public function coupon_delete( $id )
    {
        $query  =   $this->db->where( 'id', $id )->delete( store_prefix() . 'nexo_coupons' );
        if( $query ) {
            return $this->__success();
        }
        return $this->__failed();
    }

    /**
     *  Coupon Get Code
     *  @param
     *  @return
    **/

    public function coupon_code_get( $code )
    {
        $this->response(
            $this->db->where( 'CODE', $code )->get( store_prefix() . 'nexo_coupons' )->result(),
            200
        );
    }

    /**
     *  Coupon GET
     *  @param int coupon id
     *  @return json
    **/

    public function coupon_get( $id )
    {
        $this->response(
            $this->db->where( 'ID', $id )->get( store_prefix() . 'nexo_coupons' )->result(),
            200
        );
    }

    /**
     *  Coupons QUERY
     *  @param
     *  @return
    **/

    public function coupons_get()
    {
        $this->response(
            $this->db->get( store_prefix() . 'nexo_coupons' )->result(),
            200
        );
    }

    /**
     *  Coupon POST
     *  @param
     *  @return
    **/

    public function coupon_post()
    {
        $data           =   [];
        foreach( array( 'CODE', 'DESCRIPTION', 'DATE_CREATION', 'DATE_MOD', 'AUTHOR', 'AMOUNT', 'EXPIRY_DATE', 'USAGE_COUNT', 'INDIVIDUAL_USE', 'PRODUCTS_IDS', 'EXCLUDE_PRODUCTS_IDS', 'USAGE_LIMIT', 'USAGE_LIMIT_PER_USER', 'LIMIT_USAGE_TO_X_ITEMS', 'EXCLUDE_PRODUCT_CATEGORIES', 'EXCLUDE_SALE_ITEMS', 'MINIMUM_AMOUNT', 'MAXIMUM_AMOUNT', 'USED_BY', 'EMAIL_RESTRICTIONS', 'REWARDED_CASHIER' ) as $field ) {
            $data[ $field ] =   $this->post( $field );
        }

        $query  =   $this->db->insert( store_prefix() . 'nexo_coupons', $data );
        if( $query ) {
            return $this->__success();
        }
        return $this->__failed();
    }

    /**
     *  Coupon Update
     *  @param int coupon id
     *  @return json
    **/

    public function coupon_update( $id )
    {
        $data           =   [];
        foreach( array( 'CODE', 'DESCRIPTION', 'DATE_CREATION', 'DATE_MOD', 'AUTHOR', 'AMOUNT', 'EXPIRY_DATE', 'USAGE_COUNT', 'INDIVIDUAL_USE', 'PRODUCTS_IDS', 'EXCLUDE_PRODUCTS_IDS', 'USAGE_LIMIT', 'USAGE_LIMIT_PER_USER', 'LIMIT_USAGE_TO_X_ITEMS', 'EXCLUDE_PRODUCT_CATEGORIES', 'EXCLUDE_SALE_ITEMS', 'MINIMUM_AMOUNT', 'MAXIMUM_AMOUNT', 'USED_BY', 'EMAIL_RESTRICTIONS' ) as $field ) {
            $data[ $field ] =   $this->post( $field );
        }

        $query  =   $this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_coupons', $data );
        if( $query ) {
            return $this->__success();
        }
        return $this->__failed();
    }

} ?>
