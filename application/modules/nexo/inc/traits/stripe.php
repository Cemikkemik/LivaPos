<?php

include_once(APPPATH . '/modules/nexo/vendor/autoload.php');

trait Nexo_stripe
{
    public function stripe_post()
    {
        \Stripe\Stripe::setApiKey($this->post('apiKey'));
        
        // Get the credit card details submitted by the form
        $token = $this->post('id');
        
        // Create the charge on Stripe's servers - this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
            "amount" => $this->post('amount'), // amount in cents, again
            "currency" => $this->post('currency'),
            "source" => $token,
            "description" => $this->post('description')
            ));
            
            $this->response(array(
                'status'    =>    'payment_success'
            ), 200);
        } catch (\Stripe\Error\Card $e) {
            $this->response($e, 403);
        }
    }
}
