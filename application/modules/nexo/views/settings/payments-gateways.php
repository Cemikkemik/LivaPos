<?php
$this->Gui->col_width(1, 2);

/** 
 * Adding Meta
**/

$this->events->do_action('before_nexo_payments_settings', $this->Gui);



$this->events->do_action('after_nexo_payments_settings', $this->Gui);

$this->Gui->output();
