<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

	function listCategoriesInvestment() {
		return $this->M_Transaction->getCategoriesInvestment()->result_array();
	}

	function addNewCategory($data) {
		$result = $this->M_Transaction->addData("category", $data);
		header("location:".base_url());
	}

	function updateCategory($data, $where) {
		$result = $this->M_Transaction->updateData("category", $data, $where);
		header("location:".base_url());
	}

	//-------- Transaction ---------//

	function transaction($transaction_id) {
		$result = $this->M_Transaction->getOneTransaction($transaction_id)->result_array();
		return $result[0];
	}

	function addNewTransaction($data) {
		$result = $this->M_Transaction->addData("transaction", $data);
		header("location:".base_url());
	}

	function updateTransaction($data, $where) {
		$result = $this->M_Transaction->updateData("transaction", $data, $where);
		header("location:".base_url());
	}

	function lastTransaction($limit) {
		$result = $this->M_Transaction->getAllTransaction($limit)->result_array();
		$all = array();
		foreach ($result as $transaction) {
			$transaction["amount"] = (int)$transaction["amount"];
			$transaction["amount_text"] = number_format($transaction["amount"]);
			$transaction["category_name"] = ucwords($transaction["category_name"]);
			array_push($all, $transaction);
		}
		return $all;
	}

	function monthTransaction($month, $year, $category_id = 0) {
		$result = $this->M_Transaction->getMonthTransaction($month, $year, $category_id)->result_array();
		$all = array();
		foreach ($result as $transaction) {
			$transaction["amount"] = (int)$transaction["amount"];
			$transaction["amount_text"] = number_format($transaction["amount"]);
			$transaction["category_name"] = ucwords($transaction["category_name"]);
			array_push($all, $transaction);
		}
		return $all;
	}

	function topTransaction($month, $year) {
		$result = $this->M_Transaction->getTopTransaction($month, $year)->result_array();
		$all = array("data" => array(), "total" => 0);
		foreach ($result as $transaction) {
			$transaction["category_id"] = (int)$transaction["category_id"];
			$transaction["category_name"] = ucwords($transaction["category_name"]);
			$transaction["total"] = (int)$transaction["total"];
			$transaction["total_text"] = number_format($transaction["total"]);
			$transaction["percentage"] = number_format($transaction["percentage"], 2)."%";
			array_push($all["data"], $transaction);
			$all["total"] += $transaction["total"];
		}
		$all["total_text"] = number_format($all["total"]);
		return $all;
	}

	function recurringTransaction() {
		return $this->M_Transaction->getRecurringTransaction()->result_array();
	}

	//-------- Investment --------//

	function investment($investment_id) {
		$result = $this->M_Transaction->getOneInvestment($investment_id)->result_array();
		return $result[0];
	}

	function addNewInvestment($data) {
		return $this->M_Transaction->addData("transaction_investment", $data);
	}

	function updateInvestment($data, $where) {
		return $this->M_Transaction->updateData("transaction_investment", $data, $where);
	}

	function totalInvestment() {
		$investment = $this->M_Transaction->getTotalInvestment()->result();
		$investment = $investment[0]->total_investment;
		return $investment;
	}

	function listInvestment() {
		$result = $this->M_Transaction->getInvestment()->result_array();
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
		return $portfolios;
	}

	//-------- Debts --------//

	function getAllDebtsData() {
		$result['debts_list'] = $this->M_Transaction->getDebtsList()->result_array();
		$result['debts_balance'] = $this->M_Transaction->getDebtsBalance()->result();
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
