<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account/logoutUserSettings'));
		}

		$this->load->model('M_Transaction');
	}

	function getCategories() {
		$result = $this->listCategories();
		echo json_encode(array("data" => $result));
	}

	function getCategoriesInvestment() {
		$result = $this->listCategoriesInvestment();
		echo json_encode(array("data" => $result));
	}

	//-------- Transaction ---------//

	function getTransaction($transaction_id) {
		$result = $this->transaction($transaction_id);
		echo json_encode($result);
	}

	function getTopTransaction($year = "", $month = "") {
		$result = $this->topTransaction($month, $year);
		echo json_encode(array("data" => $result));
	}

	function getMonthTransaction($year = "", $month = "") {
		$result = $this->monthTransaction($month, $year);
		echo json_encode(array("data" => $result));
	}

	//-------- Investment --------//

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
}
?>