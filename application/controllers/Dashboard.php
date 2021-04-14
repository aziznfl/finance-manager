<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct() {
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->model('M_Transaction');
		$this->load->model('M_TransactionV1');
    }

	public function index() {
		$result["add_footer"] = "
			<script>
				$(function() {
					fetchCardInfo();
					fetchSummaryYoYTransaction();
					
					table.on('order.dt search.dt', function() {
						table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
							cell.innerHTML = i+1;
						} );
					}).draw();
				});
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('dashboard/view');
		$this->load->view('root/_footer');
		$this->load->view('dashboard/script');
		$this->load->view('root/_end');
	}

	//----- My Function ---------//

	function changeNullChar($result) {
		foreach ($result as $key => $value) {
			$val = array_map(function($val) {
				return is_null($val) ? "-" : $val;
			}, $value);
			$result[$key] = $val;
		}
		return $result;
	}
}
