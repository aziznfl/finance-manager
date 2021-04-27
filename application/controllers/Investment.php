<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Investment extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->model('M_Transaction');
	}

	public function list($tag = "yatim") {
		$yatim = $this->M_Transaction->getAmountTag("yatim")->result();
		$result["amountYatim"] = $yatim[0]->total;
		$result['list'] = $this->M_Transaction->get($tag);
		
		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('investment/investment_advance');
		$this->load->view('root/_footer');
		$this->load->view('root/_end');
	}

	public function manage() {
		$investmentIdentify = $this->input->get("id");
		if (isset($_GET["updateValue"])) {
			$updateValue = true;
		} else {
			$updateValue = false;
		}

		$result["add_footer"] = "
			<script>
				var investmentIdentify = '" . $investmentIdentify . "';
				var updateValue = ". json_encode($updateValue) .";
				$(function() {
					unbindScript();
				});
			</script>
		";

		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('investment/manage/view');
		$this->load->view('root/_footer');
		$this->load->view('investment/manage/script');
		$this->load->view('root/_end');
	}

	public function portfolio() {
		$result["add_footer"] = "
			<script>
            	var link = apiUrl() + 'investment/portfolio';
				$(function() {
					table.on('order.dt search.dt', function() {
				        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
				            cell.innerHTML = i+1;
				        } );
				    }).draw();

				    $('#transaction_table tbody').on('click', 'td', function () {
				    	var tr = $(this).closest('tr');
        				var row = table.row(tr);
        				if (row.child.isShown()) {
        					row.child.hide();
        				} else {
        					var data = row.data();
        					var html = childTranscation(data.child);
        					row.child(html).show();
        				}
				    });
				});
			</script>
		";

		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('investment/portfolio/view');
		$this->load->view('root/_footer');
		$this->load->view('investment/portfolio/script');
		$this->load->view('root/_end');
	}
}
