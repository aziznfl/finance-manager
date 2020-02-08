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
		$amounts = array();
		$dashboards = $this->M_Transaction->getDashboardTransaction();
		foreach ($dashboards as $val) {
			$month = date("M-y", strtotime($val['transaction_date']));
			if (!(in_array($month, $months))) { 
				array_push($months, $month);
			}
			array_push($amounts, $val['total']);
		}
		$amounts = array_map(function($val) { return (int)$val; }, $amounts);  // change value of amunts to numeric
		
		// get several tag
		$yatim = $this->M_Transaction->getAmountTag("yatim")->result();
		$result["amountYatim"] = $yatim[0]->total;
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
			        x: -30,
			        verticalAlign: 'top',
			        y: 25,
			        floating: true,
			        backgroundColor:
			            Highcharts.defaultOptions.legend.backgroundColor || 'white',
			        borderColor: '#CCC',
			        borderWidth: 1,
			        shadow: false
			    },
			    tooltip: {
			        headerFormat: '<b>{point.x}</b><br/>',
			        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
			    },
			    plotOptions: {
			        column: {
			            stacking: 'normal',
			            dataLabels: {
			                enabled: true
			            }
			        }
			    },
			    series: [{
			        name: 'All',
			        data: ".json_encode($amounts)."
			    }]
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
