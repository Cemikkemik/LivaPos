<?php
$this->Gui->col_width(1, 2);

/** 
 * Adding Meta
**/

$this->events->do_action('before_nexo_email_settings', $this->Gui);

$this->events->do_action('after_nexo_email_settings', $this->Gui);

$this->Gui->output();
