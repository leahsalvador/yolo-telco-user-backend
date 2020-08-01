<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Team  extends CI_Controller
{

    public function index()
    { }

    public function get_team_name()
    {

        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $team = $this->team_model->get_team($this->session->userdata('user')['user_id']);
        if ($team) {
            foreach ($team as $user) {
                echo $user->team_name;
            }
        }
    }

    public function get_team_id()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $team = $this->team_model->get_team($this->session->userdata('user')['user_id']);
        if ($team) {
            foreach ($team as $user) {
                echo $user->team_id;
            }
        } else {
            echo "null";
        }
    }

    public function get_team_leader_id()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $team = $this->team_model->get_team($this->session->userdata('user')['user_id']);
        if ($team) {
            foreach ($team as $user) {
                echo $user->team_leader;
            }
        } else {
            echo "null";
        }
    }



    public function get_team_members()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $member_detail = [];
        $member_id = [];
        $leader = [];
        $team_members = [];
        $found_null = false;
        $team = $this->team_model->get_team($this->session->userdata('user')['user_id']);
        //print_r($team);
        if ($team) {
            foreach ($team as $user) {
                $team = $this->team_model->get_team_member_by_team_id($user->team_id);
                if (!$team) { } else {
                    foreach ($team as $t) {
                        $member_id[] = array("member_user_id" => $t->team_member_user_id);
                    }
                }
                $leader[] = array("member_user_id" => $user->team_leader);
                $team_member = array_merge($member_id, $leader);

                if ($team_member) {
                    foreach ($team_member as $key => $team_name) {
                        $member_id = $this->team_model->get_team_member_name($team_name['member_user_id']);
                        if ($member_id) {
                            foreach ($member_id as $name) {
                                $member_detail['data'][] = array(
                                    "team_id" => $user->team_id,
                                    "leader_id" => $user->team_leader,
                                    "user_id" => $name->user_id,
                                    "first_name" => $name->first_name,
                                    "last_name" => $name->last_name,
                                    "address" => $name->address,
                                    "phone" => $name->phone,
                                    "email" => $name->email
                                );
                            }
                        }
                    }
                } else {
                    $found_null["data"] = true;
                }
            }
        } else {
            $found_null["data"] = true;
        }


        if ($found_null == true) {
            $user_team_id = '';
            $user_team_id = $this->team_model->get_team_name_and_leader_by_session_id($this->session->userdata('user')['user_id']);
            if ($user_team_id) {
                $user_team_id = $user_team_id->member_team_id;
            }

            $team_name_and_leader['data'] = $this->team_model->get_team_name_and_leader($user_team_id);
            echo json_encode($team_name_and_leader);
        } else {
            echo json_encode($member_detail);
        }
    }



    public function get_co_members($team_id = null)
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $co_members["data"] = $this->team_model->get_members_name($team_id); //
        echo json_encode($co_members);
    }

    public function get_team_task()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $task_list = $this->team_model->get_team_task($_POST['team_id']);
        echo json_encode($task_list);
    }

    public function get_team_assigned_task()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $assigned_site_for_team = $this->team_model->get_team_assigned_task($_POST['team_id']);
        echo json_encode($assigned_site_for_team);
    }

    public function assign_task_to_team_member()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $task = array(
            "team_id" => $_POST['team_id'],
            "site_id" => $_POST['site_id'],
            "team_member_user_id" => $_POST['team_member_user_id'],
            "task_id" =>  $_POST['task_id']
        );
        $this->team_model->assign_task_to_team_member($task);
    }

    public function get_already_assigned_team_task()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $taken_task_of_team_member = $this->team_model->get_assigned_task_to_team_members();
        echo json_encode($taken_task_of_team_member);
    }

    public function cancel_task_assigned_to_team_member()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $this->team_model->cancel_assigned_task_to_team_member($_POST['task_id']);
    }

    public function get_assigned_team_task_of_user()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $sites_assigned = [];
        $assigned_task = $this->team_model->get_all_assigned_team_task_of_member($this->session->userdata('user')['user_id']);
        if ($assigned_task) {
            foreach ($assigned_task as $s) {
                $sites_assigned[] = array("site_id" => $s->site_id, "site_name" => $s->site_name, "team_member_user_id" => $s->team_member_user_id);
            }
        }
        $site_detailes = array_unique($sites_assigned, SORT_REGULAR);
        echo json_encode($site_detailes);
    }

    public function get_task_assiigned_from_team_to_member()
    {
        $this->load->model('team_model');
        $this->load->library('session');

        if (empty($this->session->userdata('user'))) {
            header("location: index.html");
        }

        $site_task = $this->team_model->get_task_assigned_from_team_to_member($_POST['site_id'], $_POST['team_member_user_id']);
        echo json_encode($site_task);
    }
}
