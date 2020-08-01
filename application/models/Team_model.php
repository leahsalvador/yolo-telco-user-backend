<?php
class Team_model extends CI_Model
{

    //------------------------------------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //------------------------------------------------------------------------------------------------------

    function get_team($user_id)
    {
        $this->db->select('*');
        $this->db->from('team');
        $this->db->where("team_leader", $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    } //get if the  user is the leader of the team

    function get_team_name_and_leader_by_session_id($user)
    {
        $q = $this->db->get_where("team_member", array('team_member_user_id' => $user), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function get_team_name_and_leader($team_id)
    {
        $q = $this->db->get_where("team", array('team_id' => $team_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function get_team_member_by_team_id($team_id)
    {
        $this->db->where("member_team_id", $team_id);
        $query = $this->db->get("team_member");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_team_member_name($user_id)
    {
        $this->db->where("user_id", $user_id);
        $query = $this->db->get("users");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_members_name($team_id)
    {
        $this->db->where("member_team_id", $team_id);
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('team_member', 'users.user_id = team_member.team_member_user_id');
        $query = $this->db->get();
        return $query->result();
    }

    function get_team_task($team_id)
    {
        $this->db->select('*');
        $this->db->from('team_task');
        $this->db->where("team_id", $team_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_team_assigned_task($team_id)
    {
        $this->db->select('*');
        $this->db->from('assigned_task');
        $this->db->join('sites', 'assigned_task.assigned_site_id = sites.site_id');
        $this->db->where('team_id', $team_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function assign_task_to_team_member($task)
    {
        if ($this->db->insert('team_member_task', $task)) {
            return true;
        } else {
            return false;
        }
    }


    function get_assigned_task_to_team_members()
    {
        $this->db->select('*');
        $this->db->from('team_member_task');
        $this->db->join('users', 'team_member_task.team_member_user_id = users.user_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function cancel_assigned_task_to_team_member($task_id)
    {
        $this->db->where('task_id', $task_id);
        $this->db->delete('team_member_task');
    }

    function get_all_assigned_team_task_of_member($user_id)
    {
        $this->db->select('sites.site_id, sites.site_name, team_member_task.team_member_user_id');
        $this->db->from('team_member_task');
        $this->db->join('sites', 'team_member_task.site_id = sites.site_id');
        $this->db->where('team_member_user_id', $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_task_assigned_from_team_to_member($site_id, $user_id)
    {
        $this->db->select('site_tasks.site_task_id, site_tasks.site_id, site_tasks.site_task, site_tasks.task_date, site_tasks.required_video_evidence, site_tasks.required_image_evidence, site_tasks.task_status, site_tasks.send_video, site_tasks.send_image, site_tasks.video_alert, site_tasks.image_alert, team_member_task.team_id, team_member_task.task_id, team_member_task.team_member_user_id, team.team_leader, video.*');
        $this->db->from('team_member_task');
        $this->db->join('site_tasks', 'team_member_task.task_id = site_tasks.site_task_id');
        $this->db->join('team', 'team_member_task.team_member_user_id = team.team_leader','left');
        $this->db->join('video', 'site_tasks.site_task_id = video.task_id','left');
        $result = array("team_member_task.site_id" => $site_id, "team_member_task.team_member_user_id" => $user_id);
        $this->db->where($result);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function confirm_team_leader($id){
        $q = $this->db->get_where("team", array('team_leader' => $id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
}
