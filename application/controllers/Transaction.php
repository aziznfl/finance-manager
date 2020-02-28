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
    }

	function history($year = "", $month = "") {
		if ($year == "") $year = date('Y');
		if ($month == "") $month = date('n');
		$result["first_transaction"] = $this->getFirstTransaction()->result();
		$result["list_categories"] = $this->listCategories();

		$result["add_footer"] = "
			<script>
				var tableMonthTrans;
				var tableTopTrans;
				var params = '';
				var category_id = 0;

				$(function() {
					$('#date').val('".$year."-".$month."');
					changeDate();

					tableMonthTrans.on('order.dt search.dt', function() {
				        tableMonthTrans.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
				            cell.innerHTML = i+1;
				        } );
				    }).draw();

					$('#datatable-top-transaction tbody').on('click', 'tr', function() {
						var row = tableTopTrans.row(this);

						if(category_id != row.data().category_id) {
							$(this).attr('style', 'background-color: #dff0d8').siblings().attr('style', '');
							selectCategory(row.data().category_id);
						} else {
							$(this).attr('style', '');
							selectCategory(0);
						}
					});
				});

				// filter table
				$.fn.dataTable.ext.search.push(
				    function(settings, data, dataIndex) {
				        var row = tableMonthTrans.row(dataIndex);
				        var data = row.data();
				        if (category_id == 0 || category_id == data.category_id || category_id == data.parent_id) {
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
				});

				function changeDate() {
					var date = $('#date').val().split('-');
					params = date[0] + '/' + date[1];
					category_id = 0;
					window.history.pushState('object or string', 'Title', '".base_url('transaction/history/')."' + params);

					reloadMonthTransaction();
					reloadTopTransaction();
				}

				function selectCategory(category) {
					category_id = category;
					tableMonthTrans.draw();
				}

				function reloadMonthTransaction() {
					var link = '".base_url()."'+'api/getMonthTransaction/'+params + '/' + category_id;
					tableMonthTrans = $('#datatable-month-transaction').DataTable({
						'ajax': link,
						'destroy': true,
						'columns': [
							{'searchable': false, 'orderable': false, 'defaultContent': '', 'className': 'text-center'},
							{'data': 'transaction_date', 'className': 'text-center'},
							{'data': 'amount_text', 'className': 'text-right'},
							{'data': 'category_name', 'className': 'text-center'},
							{'data': 'description', 'className': 'text-center'},
							{'orderable': false, 
								'className': 'text-center',
								'render': function (param, type, data, meta) {
									return '<a href=\"".base_url('transaction/manage/tr/')."'+data.transaction_id+'\"><i class=\"fa fa-edit\"></i></a>';
								}
							}
						],
						'order': [1, 'desc']
					});
				}

				function reloadTopTransaction() {
					var link = '".base_url()."'+'api/getTopTransaction/'+params;
					tableTopTrans = $('#datatable-top-transaction').DataTable({
						'ordering': false,
						'searching': false,
						'paging': false,
						'destroy': true,
						'ajax': {
							'url': link,
							'dataSrc': function(json) {
								$('#top-floating-amount-table').html(json.total_text);
								return json.data;
							}
						},
						'columns': [
							{
								'className': 'text-center', 
								'render': function(param, type, data, meta) {
			                		return meta.row + meta.settings._iDisplayStart + 1;
			                	}
			                },
							{
								'className': 'text-center',
								'data': 'category_name'
							},
							{
								'className': 'text-right',
								'render': function(param, type, data, meta) {
									return data.total_text+' (<b>'+data.percentage+'</b>)';
								}
							}
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

	function manage($type = "tr", $id = "") {
		$script = "";

		//------- CREATE FORM -------//

		$result["date"] = array(
			'name'  => 'date',
			'class' => 'form-control datetimepicker'
		);
		$result["amount"] = array(
			'type'  => 'number',
			'name'  => 'amount',
			'class' => 'form-control text-right',
			'value' => 0
		);

		$categories = $this->listCategories();
        $list = array();
        foreach ($categories as $category) {
            $list[$category["category_id"]] = ucfirst($category["category_name"]);
            foreach ($category["child"] as $child) {
                $list[$child["category_id"]] = "- ".ucfirst($child["category_name"]);
            }
        }
        $result["category"]["list"] = $list;
        $result["category"]["tag"] = array('class' => 'form-control select2');
        $result["category"]["value"] = "";

		$categories = $this->listCategoriesInvestment();
        $result["category_investment"] = $categories;

        $result["description"] = array(
			'name'  => 'description',
			'class' => 'form-control',
			'placeholder' => 'Description'
        );

		//------- /CREATE FORM -------//

		// if edit -> get data from server
		if ($type == "tr" && $id != "") {
			$old_transaction = $this->transaction($id);
			$result["date"]["value"] = $old_transaction["transaction_date"];
        	$result["amount"]["value"] = $old_transaction["amount"];
        	$result["category"]["value"] = array($old_transaction["category_id"]);
        	$result["description"]["value"] = $old_transaction["description"];

			$result["form_hidden"] = array("transaction_id" => $id);
		} else if ($type == "iv" && $id != "") {
			$investment = $this->investment($id);
			$result["date_iv"]["value"] = $investment["transaction_date"];
			$result["amount_iv"]["value"] = $investment["amount"];
		} else {
			$result["form_hidden"] = null;
			$script = "$('input[name=date]').focus();";
		}
 
		$result["add_footer"] = "
			<script>
				$(function() {
				    ".$script."

				    $('#default-transaction').siblings().addClass('hide');
				    $('#".$type."').trigger('click');
				});

				// function for choose tabs
				$('.nav-tabs li').click(function() {
					$(this).addClass('active').siblings().removeClass('active');

					var tab = $(this).attr('data-tab');
					$('#'+tab+'-transaction').removeClass('hide').siblings().addClass('hide');
				});

				// function for choose category investment to show input value or not
				$('#input-category-investment select').change(function () {
					var unit = $('option:selected', this).attr('data-unit');

					if (unit != null) {
						$('#input-value').removeClass('hide');
						$('#label-unit-investment-category').text(unit);
					} else {
						$('#input-value').addClass('hide');
					}
				});
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/manage');
		$this->load->view('root/_footer');
	}

	function manageTransaction() {
		$arr["transaction_date"] = $this->input->post('date');
		$arr["amount"] = $this->input->post('amount');
		$arr["category_id"] = $this->input->post('category');
		$arr["description"] = $this->input->post('description');
		$transaction_id = $this->input->post('transaction_id');

		if ($transaction_id != "") {
			$where = "transaction_id = ".$this->input->post('transaction_id');
			$this->updateTransaction($arr, $where);
		} else {
			$arr["account_id"] = $this->session->userdata('user')->account_id;
			$this->addNewTransaction($arr);
		}
	}

	function manageInvestment() {
		$arr["transaction_date"] = $this->input->post('date');
		$arr["amount"] = $this->input->post('amount');
		$arr["category_id"] = $this->input->post('category');
		$arr["description"] = $this->input->post('description');
		$arr["value"] = $this->input->post('value');
		$arr["manager"] = $this->input->post('manager');
		$arr["account_id"] = $this->session->userdata('user')->account_id;

		$transaction_id = $this->input->post('transaction_investment_id');
		if ($transaction_id != "") {
			$where = "transaction_investment_id = ".$this->input->post('transaction_investment_id');
			$this->updateInvestment($arr, $where);
		} else {
			$arr["account_id"] = $this->session->userdata('user')->account_id;
			$this->addNewInvestment($arr);
		}
	}

	//------ GET -------//

	function getFirstTransaction() {
		return $this->M_Transaction->getFirstTransaction();
	}
}
?>