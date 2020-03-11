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

    /*------------ MAIN ------------*/

	function history($year = "", $month = "") {
		if ($year == "") $year = date('Y');
		if ($month == "") $month = date('n');
		$result["year"] = $year;
		$result["month"] = $month;

		$result["first_transaction"] = $this->getFirstTransaction()->result();
		$result["list_categories"] = $this->listCategories();

		$result["add_footer"] = "
			<script>
				var tableMonthTrans;
				var tableTopTrans;
				var params = '';
				var category_id = 0;

				$(function() {
					$('#buttonAddTransaction').attr('href', '".base_url('transaction/manage?year='.$year.'&month='.$month)."');
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
					$('#buttonAddTransaction').attr('href', '".base_url('transaction/manage?year=')."'+date[0]+'&month='+date[1]);
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
									return '<a href=\"".base_url('transaction/manage?type=tr&id=')."'+data.transaction_id+'\"><i class=\"fa fa-edit\"></i></a>';
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

	function manage() {
		$month = $this->input->get('month');
		$year = $this->input->get('year');
		$type = $this->input->get('type');
		$id = $this->input->get('id');
		$change = $this->input->get('change');
		$script = "";

		if ($type == null) $type = "tr";
		else if ($type == "iv" && $id != "") $script .= "$('#input-type').removeClass('hide');";

		//------- CREATE FORM -------//
		$result["form_hidden"] = array();
		$result["date"] = array(
			'name'  => 'date_tr',
			'class' => 'form-control datetimepicker',
			'placeholder' => "Now"
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

        $result["description"] = array(
			'name'  => 'description',
			'class' => 'form-control',
			'placeholder' => 'Description'
        );
        $result["tag"] = array(
        	'name' => 'tag',
        	'class' => 'form-control',
        	'placeholder' => 'Tag'
        );
        $result["date_iv"] = $result["date"];
        $result["date_iv"]["name"] = 'date_iv';
        $result["amount_iv"] = $result["amount"];
        $result["amount_iv"]["name"] = 'amount_iv';
        $result["manager"] = array(
        	'name' => 'manager',
        	'class' => 'form-control',
        	'placeholder' => 'Manager'
        );
        $result["description_iv"] = $result["description"];
        $result["description_iv"]["name"] = 'description_iv';

		$categories = $this->listCategoriesInvestment();
        $result["category_investment"] = $categories;
		//------- /.CREATE FORM -------//

		// if edit -> get data from server
		if ($type == "tr") {
			if ($id != "") {
				$old_transaction = $this->transaction($id);
				$result["date"]["value"] = $old_transaction["transaction_date"];
				$result["amount"]["value"] = $old_transaction["amount"];
				$result["category"]["value"] = array($old_transaction["category_id"]);
				$result["description"]["value"] = $old_transaction["description"];
				$result["tag"]["value"] = $old_transaction["tag"];

				$result["form_hidden"] = array("transaction_id" => $id);
			} else {
				if ($amount = $this->input->get('amount')) $result["amount"]["value"] = $amount;
				if ($category = $this->input->get('category')) $result["category"]["value"] = array($category);
				if ($description = $this->input->get('desc')) $result["description"]["value"] = $description;
			}
		} else if ($type == "iv" && $id != "") {
			$investment = $this->investment($id);
			$result["category_iv"] = $investment["category_id"];
			$result["manager"]["value"] = $investment["manager"];
			$result["description_iv"]["value"] = $investment["description"];
			if ($change == "edit") {
				$result["date_iv"]["value"] = $investment["transaction_date"];
				$result["form_hidden"] = array("transaction_investment_id" => $id);
				$result["amount_iv"]["value"] = $investment["amount"];
			} else {
				$result["type"]["value"] = $investment["type"];
				$result["manager"]["readonly"] = "";
				$result["description_iv"]["readonly"] = "";
			}
		}
 
		$result["add_footer"] = "
			<script>
				$(function() {
				    ".$script."

				    $('#tr-transaction').siblings().addClass('hide');
				    $('#".$type."').trigger('click');
				});

				// function for choose tabs
				$('.nav-tabs li').click(function() {
					$(this).addClass('active').siblings().removeClass('active');

					var tab = $(this).attr('data-tab');
					$('#'+tab+'-transaction').removeClass('hide').siblings().addClass('hide');
					$('input[name=date_'+tab+']').focus();
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

	function recurring() {
		$result["transaction"] = $this->recurringTransaction();

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/recurring');
		$this->load->view('root/_footer');
	}

    /*------------ /.MAIN ------------*/
    
	function manageTransaction() {
		if ($this->input->post('date_tr') != "") $arr["transaction_date"] = $this->input->post('date_tr');
		$arr["amount"] = $this->input->post('amount');
		$arr["category_id"] = $this->input->post('category');
		$arr["description"] = $this->input->post('description');
		$arr["tag"] = $this->input->post('tag');
		$transaction_id = $this->input->post('transaction_id');

		$date = strtotime($arr["transaction_date"]);
		$params = date('Y', $date) . '/' . date('n', $date);

		if ($transaction_id != "") {
			$where = "transaction_id = ".$this->input->post('transaction_id');
			$this->updateTransaction($arr, $where);
			header("location:".base_url("transaction/history/".$params));
		} else {
			$arr["account_id"] = $this->session->userdata('user')->account_id;
			$this->addNewTransaction($arr);
			header("location:".base_url("transaction/history/".$params));
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
		$arr["account_id"] = $this->session->userdata('user')->account_id;

		$transaction_id = $this->input->post('transaction_investment_id');
		if ($transaction_id != "") {
			$where = "transaction_investment_id = ".$this->input->post('transaction_investment_id');
			$this->updateInvestment($arr, $where);
			header("location:".base_url("investment/portfolio"));
		} else {
			$arr["account_id"] = $this->session->userdata('user')->account_id;
			$this->addNewInvestment($arr);
			header("location:".base_url("investment/portfolio"));
		}
	}

	function delete() {
		$type = $this->input->get('type');
		$id = $this->input->get('id');
		if ($type == 'tr') {
			$this->deleteData("transaction", array("transaction_id" => $id));
			header("location:".base_url());
		} else if ($type == 'iv') {
			$this->deleteData("transaction_investment", array("transaction_investment_id" => $id));
			header("location:".base_url("investment/portofolio"));
		} else {
			header("location:".base_url());
		}
	}

	//------ GET -------//

	function getFirstTransaction() {
		return $this->M_Transaction->getFirstTransaction();
	}
}
?>