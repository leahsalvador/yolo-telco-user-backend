<?php
class Users extends CI_Model
{

    //------------------------------------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    function get_user($user)
    {
        $q = $this->db->get_where('users', array('email' => $user['email'], 'password' => $user['password']), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }


    //------------------------------------------------------------------------------------------------------

    function save_login_detailes($login_details)
    {
        if ($this->db->insert('login_details', $login_details)) {
            return true;
        } else {
            return false;
        }
    }

    function check_user($user_id)
    {
        $q = $this->db->get_where('login_details', array('login_details_user_id' => $user_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
    function update_login_details($user_id, $login_details)
    {
        $this->db->where("login_details_user_id", $user_id);
        $this->db->update("login_details", $login_details);
    }

    function update_user_last_activity_time($user_id, $last_activity_time)
    {
        $this->db->where("login_details_user_id", $user_id);
        $this->db->update("login_details", $last_activity_time);
    }

    function delete_user_on_login_activity_when_logout($user_id)
    {
        $this->db->where('login_details_user_id', $user_id);
        $this->db->delete('login_details');
    }

    function save_user_location($location)
    {
        if ($this->db->insert('mqtt', $location)) {
            return true;
        } else {
            return false;
        }
    }

    function check_mqtt($user_id)
    {
        $q = $this->db->get_where('mqtt', array('user_id' => $user_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
    function update_user_location($user_id, $location)
    {
        $this->db->where("user_id", $user_id);
        $this->db->update("mqtt", $location);
    }

    function get_all_logged_in_user($user_id)
    {
        $this->db->select('login_details_user_id');
        $this->db->from('login_details');
        $this->db->where('login_details_user_id', $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function save_file($file)
    {
        if ($this->db->insert('required', $file)) {
            return true;
        } else {
            return false;
        }
    }

    function lastID()
    {
        return $this->db->insert_id();
    }

    function user_admin()
    {
        $this->db->where("user_group", 0);
        $query = $this->db->get("users");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_task_file_m($task_id)
    {
        $this->db->where("task_id", $task_id);
        $query = $this->db->get("required");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_site_tasks_video($site_task_id)
    {
        $this->db->where("task_id", $site_task_id);
        $this->db->where("file_type", "video");
        $this->db->group_by('file');
        $query = $this->db->get("required");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_site_tasks_image($site_task_id)
    {
        $this->db->where("task_id", $site_task_id);
        $this->db->where("file_type", "image");
        $this->db->group_by('file');
        $query = $this->db->get("required");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function delete_file($required_id)
    {
        $this->db->where('required_id', $required_id);
        $this->db->delete('required');
    }

    function get_team_member_id($user_id)
    {
        $q = $this->db->get_where('team_member', array('team_member_user_id' => $user_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
    
    function get_team_leader_by_user_id($id)
    {
        $q = $this->db->get_where('team', array('team_leader' => $id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
    function get_team_leader($id)
    {
        $q = $this->db->get_where('team', array('team_id' => $id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function get_user_location($user_id)
    {
        $this->db->where("user_id", $user_id);
        $query = $this->db->get("mqtt");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_video()
    {
        $q = $this->db->get("video");
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }
    function user_single()
    {
        $this->db->where("user_group", 1);
        $query = $this->db->get("users");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function received_video($user_id)
    {
        $this->db->where("user_id", $user_id);
        $query = $this->db->get("video");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function u_played_video($video_id)
    {
        $this->db->where("video_id", $video_id);
        $this->db->update("video", array('played' => 1));
    }

    function send_img($img)
    {
        if ($this->db->insert('image', $img)) {
            return true;
        } else {
            return false;
        }
    }
    function request_video($video_id)
    {
        $this->db->where("video_id", $video_id);
        $this->db->update("video", array('request' => 1));
        return true;
    }
    function get_admin_id()
    {
        $q = $this->db->get_where('users', array('user_group' => 0), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function send_team_video($login_details)
    {
        if ($this->db->insert('required', $login_details)) {
            return true;
        } else {
            return false;
        }
    }

    function get_all_from_site_tasks_by_site_id($site_task_id)
    {
        $query = $this->db->query("SELECT * FROM site_tasks WHERE site_task_id = '$site_task_id'");
        return $query->result();
    }

    function get_instruction_by_task_id($task_id)
    {
        $query = $this->db->query("SELECT * FROM video WHERE task_id = '$task_id'");
        return $query->result();
    }

    function set_to_sent($task_id, $sent)
    {
        $this->db->where("site_task_id", $task_id);
        $this->db->update("site_tasks", $sent);
    }

    function update_site_task($task_id, $data)
    {
        $this->db->where("site_task_id", $task_id);
        $this->db->update("site_tasks", $data);
        return true;
    }

    function getVideoSingle($video_id = null)
    {
        $q = $this->db->get_where('video', array('video_id' => $video_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
}
