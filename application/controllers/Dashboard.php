<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->model('M_Transaction');
    }

	public function index() {
		// get transaction history for charts
		$months = array();
		$category = [];
		$total_transaction = array();
		$total_investment = array();
		$arrTrans = $this->M_Transaction->getDashboardTransaction()->result_array();
		foreach($arrTrans as $val) {
			$month = date("M-y", strtotime($val["month"]."/1/".$val["year"]));
			array_push($months, $month);
			array_push($total_transaction, (int)$val['total_transaction']);
			array_push($total_investment, (int)$val['total_investment']);
		}
		$value = array(array("name" => "Transaction", "data" => $total_transaction, "stack" => "Transaction"), array("name" => "Investment", "data" => $total_investment, "stack" => "Investment"));
		
		// get several tag
		$result["amountInvestment"] = $this->totalInvestment();

		$result["add_footer"] = "
		<script>
			$(function() {
				table = $('#datatable-last-transaction').DataTable({
					'orderable': false,
					'searching': false,
					'paging': false,
					'bInfo': false,
					'ajax': '".base_url()."'+'api/getLastTransaction',
					'destroy': true,
					'columns': [
						{'searchable': false, 'orderable': false, 'defaultContent': '', 'className': 'text-center'},
						{'data': 'transaction_date', 'className': 'text-center'},
						{
							'render': function (param, type, data, meta) {
								var categoryView = '<b>'+data.category_name+'</b>';
								var descView = '';
								var tagView = '';
								
								if (data.description != null && data.description != '') { descView = '<br/><span class=\"text-secondary\">'+data.description+'</span>'; }
								if (data.location != null && data.location != '') { 
									categoryView += '&nbsp;&nbsp;&nbsp;<span class=\"text-primary\"><span class=\"fa fa-map-marker\"></span>&nbsp;'+data.location+'</span>';
								}
								if (data.tag != null) { tagView = '<br/><span class=\"label bg-blue\">'+data.tag+'</span>'; }

								return categoryView+descView+tagView;
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

				table.on('order.dt search.dt', function() {
			        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
			            cell.innerHTML = i+1;
			        } );
			    }).draw();
			});

			Highcharts.chart('chart-transactions', {
			    chart: {
			        type: 'column'
			    },
			    title: {
			        text: 'Transaction (Outcome)'
			    },
			    xAxis: {
			        categories: ".json_encode($months)."
			    },
			    yAxis: {
			        min: 0,
			        title: {
			            text: 'Amount'
			        },
			        stackLabels: {
			            enabled: true,
			            style: {
			                fontWeight: 'bold',
			                color: ( // theme
			                    Highcharts.defaultOptions.title.style &&
			                    Highcharts.defaultOptions.title.style.color
			                ) || 'gray'
			            }
			        }
			    },
			    legend: {
			        align: 'right',
			        x: 0,
			        verticalAlign: 'top',
			        y: 0,
			        floating: true,
			        backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
			        borderColor: '#CCC',
			        borderWidth: 1,
			        shadow: false
			    },
			    tooltip: {
			        headerFormat: '<b>{point.x}</b><br/>',
			        pointFormat: '{series.name}: {point.y}' //<br/>Total: {point.stackTotal}
			    },
			    plotOptions: {
			        column: {
			            stacking: 'normal'
			        },
					series: {
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									console.log(this.options.key);
								}
							}
						}
					}
				},
			    series: ".json_encode($value)."
			});
		</script>
		";

		$this->load->view('root/_header', $result);
		$this->load->view('root/_menus');
		$this->load->view('dashboard');
		$this->load->view('root/_footer');
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
