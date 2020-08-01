<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
//date_default_timezone_set('Asia/Bangkok');
date_default_timezone_set('Asia/Manila');

class User extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model("users");
	}

	public function index()
	{

		$this->load->library('session');
		$this->load->model("users");
		if (isset($_POST['email'])) {

			$email = $_POST['email'];
			$password = $_POST['password'];
			$user = array("email" => $email, "password" => $password);
			$res = $this->users->get_user($user);
			//$_SESSION['user'] = $res;
			$this->session->set_userdata('user', (array) $res);
			//log_message('error', 'indexindexindex');
			//log_message('error', @json_encode($_SESSION));

			if ($this->session->userdata('user')['user_group'] == 0) {
				echo "error";
				exit();
			}

			if ($res) {
				$user_id = $res->user_id;
				$exist = $this->users->get_team_leader_by_user_id($user_id);
				$resp = (array)$res;
				if(empty($exist)){
					$resp['team_leader'] = '0';
				}else{					
					$resp['team_leader'] = '1';
				}
				$res = (object)$resp;
				echo json_encode($res);
				$exist = $this->users->check_user($user_id);
				if (!$exist) {
					$login_details = array("login_details_user_id" => $this->session->userdata('user')['user_id'], "last_activity" => date('Y-m-d H:i:s'), "log" => 1);
					$this->users->save_login_detailes($login_details);
				} else {
					$login_details = array("login_details_user_id" => $this->session->userdata('user')['user_id'], "last_activity" => date('Y-m-d H:i:s'), "log" => 1);
					$this->users->update_login_details($user_id, $login_details);
				}
			} else {
				echo json_encode(array("response" => "dont exist"));
			}
		} else {
			echo 'error';
		}
	}

	//=================register closing =================

	public function get_assigned_site_list()
	{
		$this->load->model('users');
		$this->load->library('session');

		if (empty($this->session->userdata('user'))){
			header("location: index.html");
		}

		$assigned = $this->users->get_assigned_tasks_by_user_id($this->session->userdata('user')['user_id']);
		echo json_encode($assigned);
	}

	public function get_site_tasks_by_site_id()
	{
		$this->load->model('users');
		$this->load->library('session');

		if (empty($this->session->userdata('user'))) {
			header("location: index.html");
		}

		$site_tasks = $this->users->get_task_by_site_id($_POST['site_id']); //$_POST['site_id']
		echo json_encode($site_tasks);
	}

	public function user_activity_time_monitoring()
	{
		$this->load->library('session');
		$this->load->model("users");

		if (empty($this->session->userdata('user'))) {
			header("location: index.html");
		}

		$user_id = $this->session->userdata('user')['user_id'];
		$last_activity_time = array(
			"last_activity" => date('Y-m-d H:i:s')
		);
		$this->users->update_user_last_activity_time($user_id, $last_activity_time);
		echo date('Y-m-d H:i:s');
	}

	public function delete_user_on_login_details_who_logout()
	{
		$this->load->library('session');
		$this->load->model("users");
		$this->users->delete_user_on_login_activity_when_logout($this->session->userdata('user')['user_id']);
	}

	public function save_user_location()
	{
		$this->load->library('session');
		$this->load->model("users");
		$user_id = $this->session->userdata('user')['user_id'];
		$exist = $this->users->check_mqtt($user_id);
		if (!$exist) {
			$location = array(
				"user_id" => $user_id,
				"latitude" => $_POST['latitude'],
				"longitude" => $_POST['longitude'],
				"speed" => $_POST['speed'],
				"timestamp" => $_POST['timestamp']
			);
			$this->users->save_user_location($location);
			echo "success";
		} else {
			$location = array(
				"latitude" => $_POST['latitude'],
				"longitude" => $_POST['longitude'],
				"speed" => $_POST['speed'],
				"timestamp" => $_POST['timestamp']
			);
			$this->users->update_user_location($user_id, $location);
			echo "Updated";
		}
	}

	public function get_all_logged_in_users()
	{
		$this->load->library('session');
		$this->load->model("users");
		//	echo $_POST['user_id'];
		$logged_in_users = $this->users->get_all_logged_in_user($this->session->userdata('user')['user_id']);
		//log_message('error', 'id ' .count(false));
		//print_r($logged_in_users);
		if (!empty($logged_in_users)) {
			//log_message('error', 'good');
			echo 'good';
		} else {
			//log_message('error', 'usertesttest');
			//log_message('error', @json_encode($_SESSION));
			$this->session->sess_destroy();
			echo 'logout';
		}
	}

	public function admin_user()
	{
		$this->load->model("users");
		$admin_user = $this->users->user_admin();
		echo json_encode($admin_user);
	}
	public function save_file()
	{
		$this->load->model("users");
		$task_id = $_POST['task_id'];
		$send = $_POST['send'];
		$data = array();
		$file_type = '';
		$file_name = '';

		if ($send == "video") {
			$data = array("evidence_id" => $task_id, "send_video" => 1, "video_alert" => 0, "sent_to_admin" => 1);
			$file_name = $_POST['file_name'];
			$file_type = "video";
		} else if ($send == "image") {
			$data = array("evidence_id" => $task_id, "send_image" => 1, "image_alert" => 0, "sent_to_admin" => 1);
			$file_name = $_POST['file_name'];
			$file_type = "image";
		} else {
			$data = array("evidence_id" => $task_id, "send_document" => 1, "document_alert" => 0, "sent_to_admin" => 1);
			$file_name = $this->_upload_document($_FILES['document']);
			$file_type = "document";

			// $file_name = $this->_upload_document($_FILES['document']);
		}
		//print_r($data);
		$file = array("file" => $file_name, "file_type" => $file_type, "file_title" => $_POST['file_title'], "user_id" => $_POST['users'], "task_id" => $task_id);
		$res = $this->users->save_file($file);
		$evidence_id = $this->users->lastID();
		$update_site_task = $this->users->update_site_task($task_id, $data);
		echo 'success';
	}

	public function upload_file()
	{
		move_uploaded_file($_FILES["file"]["tmp_name"], './uploads/' . $_FILES["file"]["name"]);
	}

	public function get_task_file()
	{
		$this->load->model("users");
		$task = $this->users->get_task_file_m($_POST['task_id']);
		$list = '';
		if (!$task) { } else {
			foreach ($task as $t) {
				$file_name = $t->file;
				$text_after = substr($t->file, strpos($t->file, ".") + 1);
				if ($t->file_type == "video") {
					$list .= '<h4>Title: ' . $t->file_title . '</h4>';
					$list .= '<video width="100%" height="auto" controls><source src="http://192.168.1.5/telco_user/uploads/' . $file_name . '" type="video/' . $text_after . '">Your browser does not support the video tag.</video>';
				} else {
					$list .= '<h4>Title: ' . $t->file_title . '</h4>';
					$list .= '<img src="http://192.168.1.5/telco_user/uploads/' . $file_name . '" alt="" height="auto" width="100%">';
				}
			}
		}
		echo $list;
		// print_r($task);
	}
	public function get_video_list()
	{
		$this->load->model("users");
		$this->load->library("session");
		$video_list = $this->users->get_site_tasks_video($_POST['site_task_id']);
		echo json_encode($video_list);
	}

	public function upload_task_file(){
		$this->load->model('users');
		/*$title = $_POST['video_name'];
		$user = $_POST['users'];
		$task_id = $_POST['site_task_id'];
		$data = array("video_name" => $title, "video" => $video, "user_id" => $user, "played" => 0, "task_id" => $task_id);
		$res = $this->users->send_vid($data);//*/
		
		$this->load->model("users");
		$title = $this->_upload_task_video();
		$task_id = $_POST['task_id'];
		$send = $_POST['send'];
		$data = array();
		$file_type = '';
		$file_name = '';

		if ($send == "video") {
			$data = array("evidence_id" => $task_id, "send_video" => 1, "video_alert" => 0, "sent_to_admin" => 1);
			$file_name = $title;
			$file_type = "video";
		} else if ($send == "image") {
			$data = array("evidence_id" => $task_id, "send_image" => 1, "image_alert" => 0, "sent_to_admin" => 1);
			$file_name = $title;
			$file_type = "image";
		} else {
			$data = array("evidence_id" => $task_id, "send_document" => 1, "document_alert" => 0, "sent_to_admin" => 1);
			$file_name = $this->_upload_document($_FILES['document']);
			$file_type = "document";
			// $file_name = $this->_upload_document($_FILES['document']);
		}
		//print_r($data);
		$file = array("file" => $file_name, "file_type" => $file_type, "file_title" => $_POST['file_title'], "user_id" => $_POST['users'], "task_id" => $task_id);
		$res = $this->users->save_file($file);
		$evidence_id = $this->users->lastID();
		$update_site_task = $this->users->update_site_task($task_id, $data);
		echo 'success';
	}

	function _upload_task_video()
	{

		$this->load->library('session');
		if (isset($_FILES['file_name'])) {
			time();
			$extension = explode('.', $_FILES['file_name']['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = './uploads/' . $new_name;
			move_uploaded_file($_FILES['file_name']['tmp_name'], $destination);
			return $new_name;
		}
	}


	public function get_image_list()
	{
		$this->load->model("users");
		$this->load->library("session");
		$image_list = $this->users->get_site_tasks_image($_POST['site_task_id']);
		$list = '';
		if (!$image_list) {
			$list .= 'No Image Evidence.';
		} else {
			foreach ($image_list as $vl) {
				$list .= '<form id="send_evidence_image">';
				$list .= '<h4> Title: ' . $vl->file_title . '</h4>';
				$list .= '<img src="http://192.168.1.5/telco_user/uploads/' . $vl->file . '" alt="" height="300" width="100%">
						  <button type="button" class=" delete_task_image btn btn-danger" required_id ="' . $vl->required_id . '" id="' . $vl->task_id . '">Not Satisfied</button>&nbsp;&nbsp;<input type="button" id="team_send_image_evidence" class="btn btn-primary team_send_image_evidence" image-title="' . $vl->file_title . '" image-file-name="' . $vl->file . '" task_id="' . $vl->task_id . '" value="Send to Admin">
						  </form>';
			}
		}

		echo $list;
	}
	public function delete_task_video()
	{
		$this->load->model("users");
		$this->load->library("session");
		$site_task_id = $_POST['site_task_id'];
		$required_id = $_POST['required_id'];
		$delete_file = $this->users->delete_file($required_id);
		$update_site_task = $this->users->update_site_task($site_task_id, array("send_video" => 0, "video_alert" => 1));
	}
	public function delete_task_image()
	{
		$this->load->model("users");
		$this->load->library("session");
		$site_task_id = $_POST['site_task_id'];
		$required_id = $_POST['required_id'];
		$delete_file = $this->users->delete_file($required_id);
		$update_site_task = $this->users->update_site_task($site_task_id, array("send_image" => 0, "image_alert" => 1));
	}

	public function save_file_member()
	{
		$this->load->model("users");
		$this->load->library("session");
		$leader_id = '';
		$team_member = $this->users->get_team_member_id($this->session->userdata('user')['user_id']);
		if ($team_member) {
			$member_team_id = $team_member->member_team_id;
			$leader = $this->users->get_team_leader($member_team_id);
		}

		if ($leader) {
			$leader_id = $leader->team_leader;
		}
		$num = 0;
		$user_id = $_POST['users'];
		if ($user_id != 0) {
			$num = 1;
		}
		$task_id = $_POST['task_id'];

		$send = $_POST['send'];
		$data = array();
		$file_type = '';

		if ($send == "video") {
			$data = array("evidence_id" => $task_id, "send_video" => 1, "video_alert" => 0, "sent_to_admin" => $num);
			$file_name = $_POST['file_name'];
			$file_type = "video";
		} else if ($send == "image") {
			$data = array("evidence_id" => $task_id, "send_image" => 1, "image_alert" => 0, "sent_to_admin" => $num);
			$file_name = $_POST['file_name'];
			$file_type = "image";
		} else {
			$data = array("evidence_id" => $task_id, "send_document" => 1, "document_alert" => 0, "sent_to_admin" => $num);
			$file_name = $this->_upload_document($_FILES['document']);
			$file_type = "document";
		}

		$file = array("file" => $file_name, "file_type" => $file_type, "file_title" => $_POST['file_title'], "user_id" => $leader_id, "task_id" => $task_id);
		$res = $this->users->save_file($file);
		$evidence_id = $this->users->lastID();
		$update_site_task = $this->users->update_site_task($task_id, $data);
	}

	public function task($id = null)
	{

		$this->load->library('session');
		$this->load->model("users");

		$task["data"] = $this->users->get_task($id);
		echo json_encode($task);
	}
	public function recieved_video($id = null)
	{
		$this->load->model("users");
		$get_video['data'] = $this->users->received_video($id);
		echo json_encode($get_video);
	}
	public function played_video()
	{
		$this->load->model("users");
		$this->users->u_played_video($_POST['video_id']);
		echo "updated";
	}
	public function request_video()
	{
		$this->load->model("users");
		$this->users->request_video($_POST['video_id']);
		echo "request";
	}
	public function send_team_file()
	{
		$this->load->model("users");
		$file_type = $_POST['file_type'];
		$file = $_POST['file_name'];
		$task_id = $_POST['task_id'];
		$file_title = $_POST['file_title'];
		$admin = $this->users->get_admin_id();
		$data = array("file" => $file, "file_type" => $file_type, "file_title" => $file_title, "user_id" => $admin->user_id, "task_id" => $task_id);
		$this->users->send_team_video($data);
		$sent = array("sent_to_admin" => 1);
		$this->users->set_to_sent($task_id, $sent);
	}


	public function get_all_from_site_tasks_by_site_id()
	{
		$this->load->model('users');
		$this->load->library('session');
		$get_if_evidence_was_sent_already = $this->users->get_all_from_site_tasks_by_site_id($_POST['site_task_id']);
		echo json_encode($get_if_evidence_was_sent_already);
	}

	public function get_instruction_by_task_id()
	{
		$this->load->model('users');
		$this->load->library('session');
		$instruction = $this->users->get_instruction_by_task_id($_POST['task_id']);
		echo json_encode($instruction);
	}

	public function get_if_image_was_sent_team()
	{
		$this->load->model('users');
		$this->load->library('session');
		$site_tasks = $this->users->get_all_from_site_tasks_by_site_id($_POST['site_task_id']);
		echo json_encode($site_tasks);
	}

	function _upload_document($file = null)
	{

		$this->load->library('session');
		if (isset($file) && $file != null) {
			time();
			$extension = explode('.', $file['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = './uploads/' . $new_name;
			move_uploaded_file($file['tmp_name'], $destination);
			return $new_name;
		} else {
			return '';
		}
	}

	public function get_video($video_id = null)
	{
		$this->load->library('session');
		$this->load->model('users');
		$video_data = $this->users->getVideoSingle($video_id);
		if ($video_data) {
			echo $video_data->video;
		} else {
			echo "";
		}
	}
}//====================controller closing
