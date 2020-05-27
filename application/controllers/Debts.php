<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debts extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->model('M_Transaction');
	}

	function index() { }

	function list() {
		$result['list'] = "";
		$result['debts'] = $this->getAllDebtsData();

		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('debts/list');
		$this->load->view('root/_footer');
	}

	function insert() {
		$arr["transaction_date"] = $this->input->get("date");
		$arr["to_who"] = $this->input->get("who");
		$arr["type"] = $this->input->get("type");
		$arr["amount"] = $this->input->get("amount");
		$arr["description"] = $this->input->get("description");
		// $arr["deadline"] = $this->input->get("date");
		$arr["account_id"] = $this->session->userdata('user')->account_id;
		$result = $this->addNewDebts($arr);
		if ($result > 0) {
			header("location:".base_url("debts/list"));
		}
	}
}
