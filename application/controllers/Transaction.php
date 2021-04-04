<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends MY_Controller {

    function __construct() {
        parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->helper('form');
		$this->load->model('M_Transaction');
		$this->load->model('M_TransactionV1');
    }

    /*------------ MAIN ------------*/

	function history() {
		$month = $this->input->get('month');
		$year = $this->input->get('year');
		if ($year == "") $year = date('Y');
		if ($month == "") $month = date('n');
		$result["year"] = $year;
		$result["month"] = $month;

		$result["total_month_transaction"] = $this->M_TransactionV1->getTotalPerMonthTransaction();
		$result["list_categories"] = $this->listCategories();

		$result["add_footer"] = "
			<script>
				$(function() {
					$('#buttonAddTransaction').attr('href', '".base_url('transaction/manage?')."'+params);
					changeDate(".$year.",".$month.");

					tableMonthTrans.on('order.dt search.dt', function() {
						tableMonthTrans.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
							cell.innerHTML = i+1;
						} );
					}).draw();

					$('#datatable-month-transaction tbody').on('click', 'tr', function() {
						var row = tableMonthTrans.row(this).data();
						showDetailItemsTransaction(row);
					});

					$('#datatable-top-transaction tbody').on('click', 'tr', function() {
						var row = tableTopTrans.row(this);

						if(categoryId != row.data().category_id) {
							$(this).attr('style', 'background-color: #dff0d8').siblings().attr('style', '');
							selectCategory(row.data().category_id);
						} else {
							$(this).attr('style', '');
							selectCategory(0);
						}
					});

					// function for choose tabs
					$('.nav-tabs li').click(function() {
						$(this).addClass('active').siblings().removeClass('active');
			
						var tab = $(this).attr('data-tab');
						$('#'+tab+'-tab').removeClass('hide').siblings().addClass('hide');
					});

					// filter table
					$.fn.dataTable.ext.search.push(
						function(settings, data, dataIndex) {
							var row = tableMonthTrans.row(dataIndex);
							var data = row.data();
							if (categoryId == 0 || categoryId == data.category.id || categoryId == data.category.parentId) {
								return true
							}
							return false;
						}
					);

					// function for choose tabs
					$('.nav-tabs li').click(function() {
						$(this).addClass('active').siblings().removeClass('active');

						var tab = $(this).attr('data-tab');
						$('#tab-'+tab).removeClass('hide').siblings().addClass('hide');

						if (tab == 'category' && isFirstCategory) {
							renderCategory();
							isFirstCategory = false;
						}
					});
				});
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/history/view');
		$this->load->view('root/_footer');
		$this->load->view('transaction/history/script');
		$this->load->view('root/_end');
	}

	function manage() {
		$result['categories'] = $this->listCategories();

		$transactionId = $this->input->get("transactionId");
		// $url = base_url('exclusive/getTransactionFromIdentify/' . $transactionId);
		// $data = $this->getResponseFromUrl($url);

		$result["add_footer"] = "
			<script>
				$(function() {
					setTitle('".$transactionId."');
					insertNewLineItemList();
					unbindScript();
					getTransactionFromId('".$transactionId."');
				});
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/manage/view');
		$this->load->view('root/_footer');
		$this->load->view('transaction/manage/script');
		$this->load->view('root/_end');
	}

	function recurring() {
		$result["transaction"] = $this->recurringTransaction();

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/recurring');
		$this->load->view('root/_footer');
		$this->load->view('root/_end');
	}

    /*------------ /.MAIN ------------*/

	function manageTransaction() {
		if ($this->input->post('date_tr') != "") $arr["transaction_date"] = $this->input->post('date_tr');
		$arr["amount"] = $this->input->post('amount');
		$arr["category_id"] = $this->input->post('category');
		if ($this->input->post('description') != "") $arr["description"] = $this->input->post('description');
		if ($this->input->post('location') != "") $arr["location"] = $this->input->post('location');
		if ($this->input->post('tag') != "") $arr["tag"] = $this->input->post('tag');

		$transaction_id = $this->input->post('transaction_id');

		$date = strtotime($arr["transaction_date"]);
		$params = "year=".date('Y', $date).'&month='.date('n', $date);

		if ($transaction_id != "") {
			$where = "transaction_id = ".$this->input->post('transaction_id');
			$this->updateTransaction($arr, $where);
		} else {
			$timestamp = time();
			$arr["transaction_identify"] = "FMTR".$timestamp;
			$arr["account_key"] = $this->session->userdata('user')->account_key;
			$this->addNewTransaction($arr);
		}

		$from = $this->input->post["from"];
		if ($from != "" || $from != null) {
			header("location:".base_url("transaction/".$from));
		} else {
			header("location:".base_url("transaction/history?".$params));
		}
	}

	function manageInvestment() {
		if ($this->input->post('date_iv') != "") $arr["transaction_date"] = $this->input->post('date_iv');
		$arr["type"] = $this->input->post('type');
		$arr["amount"] = $this->input->post('amount_iv');
		$arr["category_id"] = $this->input->post('category_iv');
		$arr["description"] = $this->input->post('description_iv');
		$arr["value"] = $this->input->post('value');
		$arr["manager"] = $this->input->post('manager');

		$transaction_id = $this->input->post('transaction_investment_id');
		if ($transaction_id != "") {
			$where = "transaction_investment_id = ".$this->input->post('transaction_investment_id');
			$this->updateInvestment($arr, $where);
			header("location:".base_url("investment/portfolio"));
		} else {
			$arr["account_key"] = $this->session->userdata('user')->account_key;
			$this->addNewInvestment($arr);
			header("location:".base_url("investment/portfolio"));
		}
	}

	function delete() {
		$type = $this->input->get('type');
		$id = $this->input->get('id');
		if ($type == 'iv') {
			$this->deleteData("transaction_investment", array("transaction_investment_id" => $id));
			header("location:".base_url("investment/portofolio"));
		} else {
			if ($this->input->get('is_deleted')) {
				$arr["is_deleted"] = $this->input->get('is_deleted');
				$this->updateTransaction($arr, "transaction_id = ".$id);
			} else {
				$this->deleteData("transaction", "transaction_id = ".$id);
			}
			header("location:".base_url("transaction/history"));
		}
	}

	//------ GET -------//

	function getFirstTransaction() {
		return $this->M_Transaction->getFirstTransaction();
	}
}
?>
