<?php
use Carbon\Carbon;
class ApiNexoReports extends Tendoo_Api
{
    public function monthly_sales()
    {
        $start_date         =    $this->post( 'start_date' );
        $end_date           =    $this->post( 'end_date' );
    }
}