<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debts extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
		}

		$this->load->model('M_Transaction');
	}

	public function list() {
		$result['list'] = "";
		$result['debts_balance'] = $this->M_Transaction->getDebtsBalance()->result();
		$result['debts_list'] = $this->M_Transaction->getDebtsList()->result();

		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('debts/list');
		$this->load->view('root/_footer');
	}
}
