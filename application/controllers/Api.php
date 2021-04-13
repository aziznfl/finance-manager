<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends MY_Controller {

	function __construct() {
		parent::__construct();

		if (!$this->getApiKey()) {
			header("location: ".base_url('account/logoutUserSettings'));
			exit;
		}

		$this->load->model('M_Transaction');
		$this->load->model('M_TransactionV1');
	}

	function getApiKey() {
		if ($this->input->get('apiKey') != "" || ($this->input->post('apiKey') != "") || $this->session->userdata('user') != "") {
			return true;
		}
		return false;
	}

	//--------- Category ---------//

	function getCategories() {
		$result = $this->listCategories();
		echo json_encode(array("data" => $result));
	}
}
?>