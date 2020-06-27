<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->getApiKey() == "") {
			header("location: ".base_url('account/logoutUserSettings'));
			exit;
		}

		$this->load->model('M_Transaction');
		$this->load->model('M_TransactionV1');
	}

	function getApiKey() {
		$apiKey = $this->input->get('apiKey');

		if ($apiKey != "") {
			return $apiKey;
		} else {
			return $this->session->userdata('user')->api_key;
		}
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
		$result = $this->M_TransactionV1->getTotalPerMonthTransaction($this->getApiKey())->result();
		echo json_encode(Array("data" => $result));
	}

	function getLastTransaction() {
		$limit = $this->input->get('limit');
		if ($limit == "") $limit = 10;
		$result = $this->M_TransactionV1->getLastTransaction($limit, $this->getApiKey())->result_array();
		$arr = array();
		foreach ($result as $transaction) {
			$transaction["amount"] = (int)$transaction["amount"];
			$transaction["amount_text"] = number_format($transaction["amount"]);
			$transaction["category_name"] = ucwords($transaction["category_name"]);
			$transaction["category"] = Array("category_id" => $transaction["category_id"], "category_name" => $transaction["category_name"]);
			unset($transaction["category_id"]);
			unset($transaction["category_name"]);
			array_push($arr, $transaction);
		}
		echo json_encode(array("data" => $arr));
	}

	function getCategoryTransaction() {
		$categoryId = $this->input->get('categoryId');
		$result = $this->M_TransactionV1->getCategoryTransaction($categoryId, $this->getApiKey())->result_array();
		$arr = array();
		foreach ($result as $data) {
			$data['category_name'] = ucwords($data['category_name']);
			$data['amount'] = intval($data['amount']);
			$data['amount_text'] = number_format($data['amount']);
			array_push($arr, $data);
		}
		echo json_encode(Array("data" => $arr));
	}

	//-------- Investment --------//

	function getInvestment($investment_id) {
		$result = $this->investment($investment_id);
		$result["amount"] = (int)$result["amount"];
		$result["value"] = (int)$result["value"];
		echo json_encode($result);
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