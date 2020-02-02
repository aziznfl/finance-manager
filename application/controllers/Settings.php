<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account/logoutUserSettings'));
		}

		$this->load->model('M_Transaction');
	}

	function getCategories() {
		$result = $this->getCategories();
		echo json_encode(array("data" => $result));
	}

	//-------- Investment --------//

	function getTotalInvestment() {
		$investment = $this->totalInvestment();
		echo json_encode(array("number" => $investment, "text" => number_format($investment)));
	}

	function getListInvestment() {
		$result = $this->listInvestment();
		echo json_encode(array("data" => $result));
	}

	//-------- Transaction ---------//

	function getTopTransaction($year = "", $month = "") {
		$result = $this->topTransaction($month, $year);
		echo json_encode(array("data" => $result));
	}

	function getMonthTransaction($year = "", $month = "") {
		$result = $this->monthTransaction($month, $year)->result();
		echo json_encode(array("data" => $result));
	}
}
?>