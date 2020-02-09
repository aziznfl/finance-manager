<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('M_User');
		$this->load->model('M_Transaction');
	}

	function index($opt = "") {
		if ($this->session->userdata('user') != '') {
			header("location: ".base_url('dashboard'));
		}

		$result["opt"] = $opt;
		if ($opt == "logout") $this->session->sess_destroy();
		$this->load->view('account/login', $result);
	}

	function login() {
		if ($this->isLocalhost()) {
			$email = "aziznurfalah@gmail.com";
		} else {
			$email = $this->input->post('email');
		}
		$user = $this->loginUser($email);

		if (count($user) == 1) {
			$this->session->set_userdata('user', $user[0]);
		}

		echo count($user);
	}

	function signUp() {
		$user["name"] = $this->input->post('email');
		$user["email"] = $this->input->post('name');
		$user["image"] = $this->input->post('imageUrl');
		$result = $this->register($user);

		if ($result == 1) {
			$this->session->set_userdata('user', $user[0]);
		}

		echo $result;
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

	function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
	    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
	}
}
?>