<?php
class Tasks_model extends CI_Model
{

    //------------------------------------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_solo_user_tasks($users_user_id)
    {
        $query = $this->db->query("SELECT 
        assigned_task.assigned_task_id,
        assigned_task.user_id,
        assigned_task.team_id,
        assigned_task.assigned_task_id,
        sites.site_name,
        users.first_name,
        users.last_name,
        team.team_name,
        team.team_leader,
        team.team_leader_name,
        video.played,video.task_id as video_task_id,
        video.request, 
        video.video_id,
        video.video,
        site_tasks.* 

        FROM site_tasks 

         INNER JOIN assigned_task
         ON site_tasks.site_id = assigned_task.assigned_site_id 

         LEFT JOIN users 
         ON assigned_task.user_id = users.user_id 

         LEFT JOIN team 
         ON assigned_task.team_id = team.team_id 
         
         LEFT JOIN sites 
         ON assigned_task.assigned_site_id = sites.site_id

         LEFT JOIN video
         ON site_tasks.site_task_id = video.task_id
         
         WHERE assigned_task.user_id = $users_user_id         
         ORDER BY task_date ASC


         ");


        return $query->result();
    }
}//model closing
