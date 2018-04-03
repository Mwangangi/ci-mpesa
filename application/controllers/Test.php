<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mpesa');
    }

    public function c2b()
    {
        echo json_encode($this->mpesa->c2b("600323", "CustomerPayBillOnline", "10", "254723692500", "ORDER/001"));
    }

    public function c2b()
    {
        echo json_encode($this->mpesa->STKPushSimulation("600323", "CustomerPayBillOnline", "10", "254723692500", "ORDER/001"));
    }
}
