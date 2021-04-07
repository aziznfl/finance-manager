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

	function getAccountKey() {
		if ($this->input->get('accountKey') != "") {
			return $this->input->get('accountKey');
		} else if ($this->input->post('accountKey') != "") {
			return $this->input->post('accountKey');
		}
		return null;
	}

	//--------- Dashboard ---------//

	function getCardDashboard() {
		$result = $this->M_TransactionV1->getTotalInvestment($this->getAccountKey())->result();
		$total = $result[0]->total_investment;
		$investment["title"] = "Investment";
		$investment["subtitle"] = null;
		$investment["cardLevel"] = 0;
		$investment["number"] = (int)$total;
		$investment["number_text"] = number_format($total);
		echo json_encode(Array("data" => Array($investment)));
	}

	//--------- Category ---------//

	function getCategories() {
		$result = $this->listCategories();
		echo json_encode(array("data" => $result));
	}

	function getCategoriesTransaction() {
		$result = $this->M_TransactionV1->getCategories()->result_array();
		$arr = array();
		foreach ($result as $category) {
			$category["category_id"] = (int)$category["category_id"];
			$category["category_name"] = ucwords($category["category_name"]);
			$category["position"] = (int)$category["position"];
			$category["parent_id"] = (int)$category["parent_id"];
			array_push($arr, $category);
		}
		echo json_encode(array("data" => $arr));
	}

	function getCategoriesInvestment() {
		$result = $this->M_TransactionV1->getCategoriesInvestment()->result();
		echo json_encode(Array("data" => $result));
	}

	//-------- Transaction ---------//

	function insertTransaction() {
		$data['category_id'] = $this->input->post('categoryId');
		$data['transaction_identify'] = $this->input->post('transactionIdentify');
		$data['transaction_date'] = $this->input->post('date');
		$data['amount'] = $this->input->post('amount');
		$data['description'] = $this->input->post('description');
		$data['tag'] = $this->input->post('tag');
		$data['location'] = $this->input->post('location');
		$data['coordinate'] = $this->input->post('coordinate');
		$data['picture'] = $this->input->post('picture');
		$data['account_key'] = $this->input->post('accountKey');

		$addedDate = $this->input->post('addedDate');
		if ($addedDate != "" || $addedDate != null) {
			$data['added_date'] = $addedDate;
		}
		
		$affectedRows = $this->M_TransactionV1->addData("transaction", $data);
		$result['affectedRows'] = $affectedRows;
		echo json_encode(Array("data" => $result));
	}

	function editTransaction() {
		$data['category_id'] = $this->input->post('categoryId');
		$data['transaction_date'] = $this->input->post('date');
		$data['amount'] = $this->input->post('amount');
		$data['description'] = $this->input->post('description');
		$data['tag'] = $this->input->post('tag');
		$data['location'] = $this->input->post('location');
		$data['coordinate'] = $this->input->post('coordinate');
		$data['picture'] = $this->input->post('picture');
		$data['is_deleted'] = $this->input->post('isDeleted');

		$where = "transaction_identify = '".$this->input->post('transactionIdentify')."'";
		
		$affectedRows = $this->M_TransactionV1->updateData("transaction", $data, $where);
		$result['affectedRows'] = $affectedRows;
		echo json_encode(Array("data" => $result));
	}

	function getTotalPerMonthTransaction() {
		$result = $this->M_TransactionV1->getTotalPerMonthTransaction($this->getAccountKey())->result();
		echo json_encode(Array("data" => $result));
	}

	function getLastTransaction() {
		$limit = $this->input->get('limit');
		if ($limit == "") $limit = 10;
		$result = $this->M_TransactionV1->getLastTransaction($limit, $this->getAccountKey())->result_array();
		$arr = array();
		foreach ($result as $transaction) {
			$transaction["transaction_id"] = (int)$transaction["transaction_id"];
			$transaction["amount"] = (int)$transaction["amount"];
			$transaction["amount_text"] = number_format($transaction["amount"]);
			$transaction["category_name"] = ucwords($transaction["category_name"]);
			$transaction["category"] = Array("category_id" => (int)$transaction["category_id"], "category_name" => $transaction["category_name"], "icon" => $transaction["icon"], "position" => (int)$transaction["position"], "parent_id" => (int)$transaction["parent_id"]);
			unset($transaction["category_id"]);
			unset($transaction["category_name"]);
			unset($transaction["icon"]);
			unset($transaction["position"]);
			unset($transaction["parent_id"]);
			array_push($arr, $transaction);
		}
		echo json_encode(array("data" => $arr));
	}

	// function getCategoryTransaction() {
	// 	$categoryId = $this->input->get('categoryId');
	// 	$result = $this->M_TransactionV1->getCategoryTransaction($categoryId, $this->getAccountKey())->result_array();
	// 	$arr = array();
	// 	foreach ($result as $data) {
	// 		$data['category_name'] = ucwords($data['category_name']);
	// 		$data['amount'] = intval($data['amount']);
	// 		$data['amount_text'] = number_format($data['amount']);
	// 		array_push($arr, $data);
	// 	}
	// 	echo json_encode(Array("data" => $arr));
	// }

	function getTransactions() {
		$lastTransaction = $this->input->get('lastAddedTransaction');

		$result = $this->M_TransactionV1->getTransactions($lastTransaction, $this->getAccountKey())->result_array();
		$arr = array();
		foreach ($result as $transaction) {
			$transaction["transaction_id"] = (int)$transaction["transaction_id"];
			$transaction["amount"] = (int)$transaction["amount"];
			$transaction["amount_text"] = number_format($transaction["amount"]);
			$transaction["category_id"] = (int)$transaction["category_id"];
			unset($transaction["account_key"]);
			array_push($arr, $transaction);
		}
		echo json_encode(array("data" => $arr));
	}

	//-------- Investment --------//

	function getInvestmentList() {
		$accountKey = $this->getHeaderFromUrl('currentUser');
		$result = $this->M_TransactionV1->getInvestment($accountKey)->result_array();
		$portfolios = array();
		foreach ($result as $portfolio) {
			$portfolio["amount_text"] = number_format($portfolio["amount"]);

			$arr = array();
			$arr["id"] = $portfolio["transaction_investment_id"];
			$arr["date"] = $portfolio["transaction_date"];
			$arr["state_text"] = "Progress";
			$arr["description"] = $portfolio["description"];
			$arr["instrument"] = ucwords($portfolio["category_name"]);
			$arr["manager"] = $portfolio["manager"];
			$arr["amount"] = (int)$portfolio["amount"];
			$arr["amount_text"] = number_format($arr["amount"]);
			$arr["value"] = (float)$portfolio["value"];
			$arr["value_text"] = $portfolio["unit"] != "" ? $portfolio["value"] ." ". $portfolio["unit"] : null;
			$arr["outcome"] = $arr["amount"];

			// set child array
			$arr["child"] = array($portfolio);
			if (array_key_exists($portfolio["description"], $portfolios)) {
				$portfolios[$portfolio["description"]]["value"] += (float)$portfolio["value"];
				if ($portfolio["unit"] != "") {
					$portfolios[$portfolio["description"]]["value_text"] = $portfolios[$portfolio["description"]]["value"] ." ". $portfolio["unit"];
				}
				$addProfitText = '';

				$amount = $portfolios[$portfolio["description"]]["amount"];
				if ($portfolio["type"] == "income" || $portfolio["type"] == "done") {
					$amount -= $portfolio["amount"];
					if ($portfolio["type"] == "done") {
						$amount *= -1;
						$addProfitText .= " (".number_format($amount/$portfolios[$portfolio["description"]]["outcome"]*100, 2)."%)";
						$portfolios[$portfolio["description"]]["state_text"] = "Done";
					}
				} else if ($portfolio["type"] == "outcome") {
					$amount += $portfolio["amount"];
					$portfolios[$portfolio["description"]]["outcome"] += $portfolio["amount"];
				}
				$portfolios[$portfolio["description"]]["amount"] = (int)$amount;
				$portfolios[$portfolio["description"]]["amount_text"] = number_format($amount) . $addProfitText;

				array_push($portfolios[$portfolio["description"]]["child"], $portfolio);
			} else {
				$arr["amount_text"] = number_format($arr["amount"]);
				$portfolios[$portfolio["description"]] = $arr;
			}
		}
		$portfolios = array_values($portfolios);
		echo json_encode(Array("data" => $portfolios));
	}

	function getInvestmentTotal() {
		$accountKey = $this->input->get('accountKey');
		$result = $this->M_TransactionV1->getTotalInvestment($accountKey)->result_array();
		echo json_encode(Array("data" => $result[0]));
	}

	// old

	function getInvestment($investment_id) {
		$result = $this->investment($investment_id);
		$result["amount"] = (int)$result["amount"];
		$result["value"] = (int)$result["value"];
		echo json_encode($result);
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