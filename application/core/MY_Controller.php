<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class MY_Controller extends CI_Controller {

	function __construct() {
		parent::__construct();

		// load model
		$this->load->model('CoreModel');
		
		$this->setupMenus();
	}

	function setupMenus() {
		$GLOBALS['menus'] = $this->CoreModel->getMenus();
	}

	function loginUser($email) {
		$user = $this->M_User->login($email)->result();
		return $user;
	}

	function register($data) {
		$result = $this->M_Transaction->addData("account", $data);
		return $result;
	}

	//-------- Category ---------//

	function listCategories() {
		$result = $this->M_Transaction->getCategories()->result_array();
		$all = array();
		foreach ($result as $cat) {
			if ($cat["parent_id"] == 1) {
				$cat["child"] = array();
				$all[$cat["category_id"]] = $cat;
			} else {
				array_push($all[$cat["parent_id"]]["child"], $cat);
			}
		}
		$all = array_values($all);
		return $all;
	}

	//-------- Transaction ---------//

	function updateTransaction($data, $where) {
		$result = $this->M_Transaction->updateData("transaction", $data, $where);
		header("location:".base_url());
	}

	function recurringTransaction() {
		return $this->M_Transaction->getRecurringTransaction()->result_array();
	}

	//-------- Investment --------//

	function addNewInvestment($data) {
		return $this->M_Transaction->addData("transaction_investment", $data);
	}

	function updateInvestment($data, $where) {
		return $this->M_Transaction->updateData("transaction_investment", $data, $where);
	}

	//-------- Debts --------//

	function getAllDebtsData() {
		$result['debts_list'] = $this->M_TransactionV1->getDebtsList()->result_array();
		$result['debts_balance'] = $this->M_TransactionV1->getDebtsBalance()->result();
		return $result;
	}

	function addNewDebts($data) {
		return $this->M_Transaction->addData("debts", $data);
	}

	function updateDebts($data, $where) {
		return $this->M_Transaction->updateData("debts", $data, $where);
	}

	/*--------- DELETE DATA ---------*/
	
	function deleteData($table, $where) {
		return $this->M_Transaction->deleteData($table, $where);
	}
}
