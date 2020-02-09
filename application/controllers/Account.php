<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('M_User');
	}

	function index() {
		if ($this->session->userdata('user') != '') {
			header("location: ".base_url('dashboard'));
		}

		$this->load->view('account/login');
	}

	function login() {
		$user["email"] = $this->input->post('name');
		$user = $this->loginUser($User);

		if (count($user) == 1) {
			$this->session->set_userdata('user', $user[0]);
		}

		return $countUser;
	}

	function register() {
		$user["name"] = $this->input->post('email');
		$user["email"] = $this->input->post('name');
		$user["imageUrl"] = $this->input->post('imageUrl');
		$result = $this->register($user);

		if ($result) {
			$this->session->set_userdata('user', $user[0]);
		}

		return $result
	}

	function logout() {
		$this->session->sess_destroy();
		header('location:'.base_url());
	}

	function general() {
		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
		}
	}

	function logoutUserSettings() {
		$result = array("status_code" => 300, "status_text" => "You are logged out, please loggin back!");
		echo json_encode($result);
	}
}
?>