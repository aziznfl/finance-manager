<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends MY_Controller {

    function __construct() {
        parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
		}

		$this->load->model('M_Transaction');
    }

	function history($year = "", $month = "") {
		if ($year == "") $year = date('Y');
		if ($month == "") $month = date('n');
		$result["first_transaction"] = $this->getFirstTransaction()->result();

		$result["add_footer"] = "
			<script>
				var tableMonthTrans;
				var tableTopTrans;

				$(function() {
					$('#date').val('".$year."-".$month."');
					changeDate();

					tableMonthTrans.on('order.dt search.dt', function() {
				        tableMonthTrans.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
				            cell.innerHTML = i+1;
				        } );
				    }).draw();
				});

				function changeDate() {
					$('#load_transactions_month').html('loading...');
					// $('#load_transactions_top').html('loading...');

					var date = $('#date').val().split('-');
					var params = date[0] + '/' + date[1];
					reloadMonthTransaction(params);
					reloadTopTransaction(params);
					window.history.pushState('object or string', 'Title', '".base_url('transaction/history/')."' + params);
				}

				function reloadMonthTransaction(params) {
					var link = '".base_url()."'+'api/getMonthTransaction/'+params;
					tableMonthTrans = $('#datatable-month-transaction').DataTable({
						'ajax': link,
						'destroy': true,
						'columns': [
							{'searchable': false, 'orderable': false, 'defaultContent': '', 'className': 'text-center'},
							{'data': 'transaction_date', 'className': 'text-center'},
							{'data': 'amount_text', 'className': 'text-right'},
							{'data': 'category_name', 'className': 'text-center'},
							{'data': 'description', 'className': 'text-center'},
							{'data': null, 'className': 'text-center', 'defaultContent': '<i class=\"fa fa-edit\"></i>'}
						],
						'order': [1, 'desc']
					});
				}

				function reloadTopTransaction(params) {
					var link = '".base_url()."'+'api/getTopTransaction/'+params;
					tableTopTrans = $('#datatable-top-transaction').DataTable({
						'ordering': false,
						'searching': false,
						'paging': false,
						'destroy': true,
						'ajax': link,
						'columns': [
							{'data': null, 'defaultContent': '', 'className': 'text-center', 'target': 0, 'render': function (data, type, row, meta) {
			                 return meta.row + meta.settings._iDisplayStart + 1;
			                }},
							{'data': 'category_name', 'className': 'text-center'},
							{'data': 'percentage', 'className': 'text-right'}
						],
						'order': [2, 'desc']
					});
				}
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/month_history');
		$this->load->view('root/_footer');
	}

	function manage() {
		$result["categories"] = $this->listCategories();
		$result["add_footer"] = "
			<script>
				$(function () {
					$('input[name=date]').focus();
				})
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/manage');
		$this->load->view('root/_footer');
	}

	//------ AJAX ------//

	function viewGetMonthTransaction($year = "", $month = "") {
		if ($year == "") $year = date('Y');
		if ($month == "") $month = date('n');
		$result["transactions"] = $this->monthTransaction($month, $year);
		echo $this->load->view('transaction/ajax/list_month_transaction', $result, TRUE);
	}

	function viewGetTopTransaction($year = "", $month = "") {
		if ($year == "") $year = date('Y');
		if ($month == "") $month = date('n');
		$result["transactions"] = $this->topTransaction($month, $year);
		echo $this->load->view('transaction/ajax/list_top_transaction', $result, TRUE);
	}

	//----- My Function -------//

	// function getMonthTransaction($year = "YEAR(CURRENT_DATE())", $month = "MONTH(CURRENT_DATE())") {
	// 	$list = $this->M_Transaction->getMonthTransaction($month, $year)->result();
	// 	print_r(json_encode($list));
	// }

	function changeNullChar($result) {
		foreach ($result->result() as $key => $value) {
			$val = array_map(function($val) {
				return is_null($val) ? "-" : $val;
			}, $value);
			$result[$key] = $val;
		}
		return $result;
	}

	//------ GET -------//

	function getFirstTransaction() {
		return $this->M_Transaction->getFirstTransaction();
	}
}
?>