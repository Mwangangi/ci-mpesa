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

    public function stk()
    {
        echo json_encode($this->mpesa->STKPushSimulation(174379, "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919", "CustomerPayBillOnline", 10, 254723692500, 174379, 254723692500, "http://196.207.149.237:8080/callback/stk", "ORDER/002", "Payment for bill", "No remarks"));
    }
   
    public function stk_status()
    {
        echo json_encode($this->mpesa->STKPushQuery("ws_CO_DMZ_428722714_05042019094604363",174379, "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919"));
    }
}
