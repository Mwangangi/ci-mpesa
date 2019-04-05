<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Callback extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mpesa_callbacks');
        $this->load->model('test_model');
    }

    public function stk()
    {
        $data['response_body'] = $this->mpesa_callbacks->processSTKPushRequestCallback();
        $this->test_model->insert($data);
    }
}
