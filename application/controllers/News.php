<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class News extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct()
    {
        parent::__construct();
        $this->__resTraitConstruct();
        $this->load->model('report_model');
    }

    public function report_get()
    {
        $data = $this->report_model->get($this->get('hours'));
        $this->response($data);
    }

}