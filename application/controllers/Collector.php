<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Collector extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('google_model');
    }

    public function fetch()
    {
        $this->google_model->fetch();
    }
}
