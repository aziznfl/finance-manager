<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
		}

		$this->load->model('M_Transaction');
    }

	public function index() {
		// get last transaction
		$result["transactions"] = $this->M_Transaction->getAllTransaction(10);

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
