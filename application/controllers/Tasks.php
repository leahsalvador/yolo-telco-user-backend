<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Manila');

class Tasks extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model("Tasks_model");
    }

    function get_users_solo_tasks()
    {
        $solo_task_result = $this->Tasks_model->get_solo_user_tasks($this->session->userdata('user')['user_id']);
        echo json_encode($solo_task_result);
    }
}
