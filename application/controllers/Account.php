<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('User');
	}

	function loginGoogle() {
		if($this->session->userdata('login') == true){
			redirect('welcome/profile');
		}
		
		if (isset($_GET['code'])) {
			
			$this->google->getAuthenticate();
			$this->session->set_userdata('login',true);
			$this->session->set_userdata('user_profile',$this->google->getUserInfo());
			redirect('welcome/profile');
			
		} 
			
		$contents['login_url'] = $this->google->loginURL();
		$this->load->view('account/welcome_message',$contents);
	}

	function index() {
		if ($this->session->userdata('user') != '') {
			header("location: ".base_url('account/general'));
		}

		$this->load->view('account/login');
	}

	function login() {
		$username = htmlspecialchars($_POST['username']);
		$password = htmlspecialchars($_POST['password']);
		$user = $this->User->login($username, $password)->result();
		
		if (count($user) == 1) {
			$this->session->set_userdata('user', $user[0]);
			echo true;
		} else {
			echo false;
		}
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