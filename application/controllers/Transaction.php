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
				var tableMonthTrans;
				var tableTopTrans;
				var tableCategory;
				var params = 'year=".$year."&month=".$month."';
				var category_id = 0;
				var category_tab_id = 0;
				var isFirstClick = true;
				var isFirstCategory = true;

				var list = ".json_encode($result["total_month_transaction"]->result_array()).";

				$(function() {
					$('#buttonAddTransaction').attr('href', '".base_url('transaction/manage?')."'+params);
					changeDate(".$year.",".$month.");

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

				function changeDate(year, month) {
					category_id = 0;
					params = 'year='+year+'&month='+month;
					$('#card-'+year+'-'+month).addClass('active').siblings().removeClass('active');
					$('#buttonAddTransaction').attr('href', '".base_url('transaction/manage?')."'+params);
					window.history.pushState('object or string', 'Title', '".base_url('transaction/history?')."' + params);

					renderMonthTransaction();
					renderTopTransaction();

					// set position of month balance
					var index = $.map(list, function(item, i) {
						if (item.year == year && item.month == month) { return i; }
					})[0];
					var cardViewWidth = (225 + 14)
					var cardWidth = $('.card-box').width();
					var center = ((cardWidth - cardViewWidth) / 2) - 5;
					var position = (index * cardViewWidth) - center;
					if (isFirstClick) {
						$('.card-box').scrollLeft(position);
						isFirstClick = false;
					} else {					
						$('.card-box').animate({
					      scrollLeft: position
					    }, 'slow');
					}
				}

				function selectCategory(category) {
					category_id = category;
					tableMonthTrans.draw();
				}

				function renderTopTransaction() {
					var link = '".base_url()."'+'api/getTopTransaction?'+params;
					console.log(link);
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

				function renderMonthTransaction() {
					var link = '".base_url()."' + 'api/getMonthTransaction?' + params + '&category_id=' + category_id;
					tableMonthTrans = $('#datatable-month-transaction').DataTable({
						'ajax': link,
						'destroy': true,
						'columns': [
							{'searchable': false, 'orderable': false, 'defaultContent': '', 'className': 'text-center'},
							{'data': 'transaction_date', 'className': 'text-center'},
							{
								'render': function (param, type, data, meta) {
									return textDescription(data);
								}
							},
							{'data': 'amount_text', 'className': 'text-right'},
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

				function renderCategory() {
					var link = '".base_url()."' + 'api/getCategories';
					tableCategory = $('#datatable-category').DataTable({
						'ajax': link,
						'ordering': false,
						'searching': false,
						'paging': false,
						'destroy': true,
						'columns': [
							{
								'className': 'text-center', 
								'render': function(param, type, data, meta) {
			                		return meta.row + meta.settings._iDisplayStart + 1;
			                	}
			                },
							{'data': 'category_name', 'className': 'text-capitalize'}
						]
					});

					$('#datatable-category tbody').on('click', 'tr', function() {
						var row = tableCategory.row(this);

						$(this).attr('style', 'background-color: #dff0d8').siblings().attr('style', '');
						renderSubCategory(row.data().category_id);
					});
				}

				function renderSubCategory(categoryId) {
					var link = '".base_url()."' + 'api/getCategoryTransaction?categoryId=' + categoryId;
					$.ajax({
						method: 'GET',
						url: link,
						data: {categoryId: categoryId},
						dataType: 'JSON',
        				success: function(response){
							var html = '';
							let data = response.data;
							for(var i=0; i<data.length; i++) {
								let result = data[i];

								html += '<tr>' +
									'<td class=\"text-center\">'+ (i+1) +'</td>' +
									'<td class=\"text-center\">'+ result.transaction_date +'</td>' +
									'<td>'+ textDescription(result) +'</td>' +
									'<td class=\"text-right\">'+ result.amount_text +'</td>' +
								'</tr>';
							}
							$('#datatable-sub-category tbody').html(html);
						}
					});
				}

				function textDescription(data) {
					var categoryView = '<b>'+data.category_name+'</b>';
					var descView = '';
					var tagView = '';
					
					if (data.location != null && data.location != '') { 
						categoryView += '&nbsp;&nbsp;&nbsp;<span class=\"text-primary\"><span class=\"fa fa-map-marker\"></span>&nbsp;'+data.location+'</span>';
					}
					if (data.is_deleted != 0) { categoryView += '&nbsp;&nbsp;<span class=\"label bg-red\">Deleted</span>'; }
					if (data.description != null && data.description != '') { descView = '<br/><span class=\"text-secondary\">'+data.description+'</span>'; }
					if (data.tag != null) { tagView = '<br/><span class=\"label bg-blue\">'+data.tag+'</span>'; }

					return categoryView+descView+tagView;
				}

				// function for choose tabs
				$('.nav-tabs li').click(function() {
					$(this).addClass('active').siblings().removeClass('active');

					var tab = $(this).attr('data-tab');
					$('#'+tab+'-tab').removeClass('hide').siblings().addClass('hide');
				});
			</script>
		";

		$result["add_footer"] .= " // add script for datatables
			<script>
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

					if (tab == 'category' && isFirstCategory) {
						renderCategory();
						isFirstCategory = false;
					}
				});
			</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('transaction/month_history');
		$this->load->view('root/_footer');
	}

	function manage() {
		$date = $this->input->get('date');
		$type = $this->input->get('type');
		$id = $this->input->get('id');
		$change = $this->input->get('change');
		$script = "";

		if ($type == null) $type = "tr";
		else if ($type == "iv" && $id != "") $script .= "$('#input-type').removeClass('hide');";

		$result["id"] = $id;
		//------- CREATE FORM -------//
		$result["form_hidden"] = array();
		$result["date"] = array(
			'name'  => 'date_tr',
			'class' => 'form-control datetimepicker',
			'placeholder' => "Now"
		);
		if ($date != null || $date != '') { $result["date"]["value"] = $date; }
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
        $result["location"] = array(
        	'name' => 'location',
        	'class' => 'form-control',
        	'placeholder' => 'Location'
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
				$result["location"]["value"] = $old_transaction["location"];
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
		$arr["location"] = $this->input->post('location');
		$arr["tag"] = $this->input->post('tag');
		$transaction_id = $this->input->post('transaction_id');

		$date = strtotime($arr["transaction_date"]);
		$params = "year=".date('Y', $date).'&month='.date('n', $date);

		if ($transaction_id != "") {
			$where = "transaction_id = ".$this->input->post('transaction_id');
			$this->updateTransaction($arr, $where);
		} else {
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