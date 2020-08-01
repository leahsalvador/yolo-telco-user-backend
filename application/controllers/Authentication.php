<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends CI_Controller
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
			$_SESSION['user'] = $res;

			if ($res) {

				if ($_SESSION['user']) {
					echo "success";
					print_r($_SESSION['user']);
				} else {
					echo "dont exist";
				}
			}
		} else {
			$this->load->view('login_view');
		}
	}

	public function register()
	{
		$this->load->model('users');

		if (isset($_POST['first_name'])) {
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$date = date('Y-m-d');

			$users = array(
				"first_name" => $first_name,
				"last_name" => $last_name,
				"address" => $address,
				"phone" => $phone,
				"email" => $email,
				"password" => $password,
				"date_created" => $date
			);

			$this->users->add_user($users);
			echo "recieve";
		} else {
			echo "do not recive";
		}
	} //=================register closing =================

	public function admin_users_view()
	{
		$this->load->library('session');
		$this->load->model("users");

		$this->load->view('telco_ui');
	} //=================admin_users_view closing =================

	public function json_users()
	{

		$this->load->library('session');
		$this->load->model("users");

		$all_users["data"] = $this->users->get_all_users();
		echo json_encode($all_users);
	}


	public function delete_user()
	{
		$this->load->library('session');

		if (isset($_POST['user_id'])) {
			$this->load->model('users');
			$user_id = $_POST['user_id'];
			$this->users->delete_User($user_id);
		}
	}


	public function update_user()
	{
		$this->load->library('session');
		$this->load->model('users');
		$user_id = $_POST['user_id'];
		if (isset($_POST['user_id'])) {

			$user_id = $_POST['user_id'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$password = $_POST['password'];

			$update_data = array(
				"first_name" => $first_name,
				"last_name" => $last_name,
				"address" => $address,
				"phone" => $phone,
				"email" => $email,
				"password" => $password
			);

			$this->users->update_user($user_id, $update_data);
			echo "Update";
		}
	}

	public function user_location()
	{
		$this->load->model('users');
		if (isset($_POST['user_id'])) {
			$user_id = $_POST['user_id'];
			$data['location'] = $this->users->get_user_location($user_id);
			echo json_encode($data);
		}
	}
	public function upload_videos()
	{
		// move_uploaded_file($_FILES["file"]["tmp_name"], '/telco');
		// echo 'marwin';
	}

	public function sended_video()
	{

		$this->load->model('users');
		$video["data"] = $this->users->get_video();
		echo json_encode($video);
	}

	public function single_user()
	{
		$this->load->model("users");
		$single_user = $this->users->user_single();
		echo json_encode($single_user);
	}

	public function upload_video()
	{
		move_uploaded_file($_FILES["file"]["tmp_name"], './uploads/' . $_FILES["file"]["name"]);
	}

	public function save_video()
	{
		echo $_POST['email'];
		echo $_POST['password'];
	}
}//====================controller closing
