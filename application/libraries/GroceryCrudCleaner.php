<?php
class GroceryCrudCleaner
{
    /**
     * XSS Clean for Grocery Crud
     *
     * @param Array post
     * @param String Primary key
     * @return Array
     **/
     
    public function xss_clean($post_array, $primary_key = null)
    {
        foreach ($post_array as $key => $value) {
            $post_array[$key] = xss_clean($value);
        }
    
        return $post_array;
    }
}
