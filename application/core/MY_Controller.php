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

	function listCategories() {
		$result = $this->M_Transaction->getCategories()->result_array();
		return $result;
	}

	//-------- Transaction ---------//

	function monthTransaction($month, $year) {
		$result = $this->M_Transaction->getMonthTransaction($month, $year)->result_array();
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
		$all = array();
		foreach ($result as $transaction) {
			$transaction["category_id"] = (int)$transaction["category_id"];
			$transaction["category_name"] = ucwords($transaction["category_name"]);
			$transaction["total"] = (int)$transaction["total"];
			$transaction["total_text"] = number_format($transaction["total"]);
			$transaction["percentage"] = number_format($transaction["percentage"], 2)."%";
			array_push($all, $transaction);
		}
		return $all;
	}

	//-------- Investment --------//

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
			$arr["date"] = $portfolio["transaction_date"];
			$arr["amount"] = (int)$portfolio["amount"];
			$arr["amount_text"] = number_format($arr["amount"]);
			$arr["state_text"] = !$portfolio["is_done"] ? "Progress" : "Done";
			$arr["description"] = $portfolio["description"];
			$arr["invest"] = $portfolio["invest"];
			$arr["child"] = array($portfolio);
			if (array_key_exists($portfolio["description"], $portfolios)) {
				$amount = $portfolios[$portfolio["description"]]["amount"];
				$amount += $portfolio["is_done"] == 0 ? $portfolio["amount"] : -$portfolio["amount"];
				$portfolios[$portfolio["description"]]["amount"] = (int)$amount;

				if ($portfolio["is_done"]) {
					$amount *= -1;
				}
				$portfolios[$portfolio["description"]]["amount_text"] = number_format($amount);
				array_push($portfolios[$portfolio["description"]]["child"], $portfolio);
			} else {
				$arr["amount_text"] = number_format($arr["amount"]);
				$portfolios[$portfolio["description"]] = $arr;
			}
		}
		$portfolios = array_values($portfolios);
		return $portfolios;
	}
}
