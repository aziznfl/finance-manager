<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			if ($this->input->get('apiKey') == '') {
				header("location: ".base_url('account/logoutUserSettings'));
				exit;
			}
		}

		$this->load->model('M_Transaction');
		$this->load->model('M_TransactionV1');
	}

	function getCategories() {
		$result = $this->listCategories();
		echo json_encode(array("data" => $result));
	}

	function getCategoriesInvestment() {
		$result = $this->listCategoriesInvestment();
		echo json_encode(array("data" => $result));
	}

	function addCategory() {
		$arr["category_name"] = $this->input->post('name');
		$arr["parent_id"] = $this->input->post('parent');
		$arr["position"] = 0;

		if ($category_id != "") {
			$where = "category_id = ".$this->input->post('category_id');
			$this->updateCategory($arr, $where);
		} else {
			$this->addNewCategory($arr);
		}
	}

	//-------- Transaction ---------//

	function getTransaction($transaction_id) {
		$result = $this->transaction($transaction_id);
		$result["amount"] = (int)$result["amount"];
		echo json_encode($result);
	}

	function getTopTransaction() {
		$month = $this->input->get('month');
   		$year = $this->input->get('year');
		$result = $this->topTransaction($month, $year);
		echo json_encode($result);
	}

	function getMonthTransaction() {
		$month = $this->input->get('month');
   		$year = $this->input->get('year');
   		$category_id = $this->input->get('category_id');

   		if (!isset($category_id)) $category_id = 0;

		$result = $this->monthTransaction($month, $year, $category_id);
		echo json_encode(array("data" => $result));
	}

	function getTotalPerMonthTransaction() {
		$apiKey = $this->input->get('apiKey');
		echo json_encode($this->M_TransactionV1->getTotalPerMonthTransaction($apiKey)->result());
	}

	//-------- Investment --------//

	function getInvestment($investment_id) {
		$result = $this->investment($investment_id);
		$result["amount"] = (int)$result["amount"];
		$result["value"] = (int)$result["value"];
		echo json_encode($result);
	}

	function getLastTransaction($limit = 10) {
		$result = $this->lastTransaction($limit);
		echo json_encode(array("data" => $result));
	}

	function getTotalInvestment() {
		$result = $this->totalInvestment();
		echo json_encode(array("number" => $result, "text" => number_format($result)));
	}

	function getListInvestment() {
		$result = $this->listInvestment();
		echo json_encode(array("data" => $result));
	}

	//-------- Debts --------//

	function getAllDebts() {
		$result['debts_list'] = $this->M_Transaction->getDebtsList()->result();
		$result['debts_balance'] = $this->M_Transaction->getDebtsBalance()->result();
		echo json_encode($result);
	}
}
?>