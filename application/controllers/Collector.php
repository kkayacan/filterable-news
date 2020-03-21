<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Collector extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('google_model');
        $this->load->model('newsapi_model');
    }

    public function fetch()
    {
        if($_SERVER['SERVER_ADDR'] == $this->_get_client_ip_server()) {
            $this->newsapi_model->fetch();
            $data = $this->google_model->fetch();
            $this->response($data);
        }
    }

    function _get_client_ip_server() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
