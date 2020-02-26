<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Investment extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
		}

		$this->load->model('M_Transaction');
	}

	public function list($tag = "yatim") {
		$result['list'] = $this->M_Transaction->get($tag);
		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('investment/investment_advance');
		$this->load->view('root/_footer');
	}

	public function portfolio() {
		$result["add_footer"] = "
			<script>
				$(function() {
					var table = $('#transaction_table').DataTable({
						'ajax': '".base_url('api/getListInvestment')."',
						'columns': [
							{'data': null, 'className': 'text-center', 'orderable': false, 'searching': false},
							{'data': 'date', 'className': 'text-center'},
							{
								'data': 'amount_text',
								'className': 'text-right',
								'createdCell': function(td, cellData, rowData, row, col) {
									if (rowData.state_text == 'Done') {
										if (rowData.amount > 0) $(td).addClass('text-success');
										else $(td).addClass('text-danger');
									}
								}
							},
							{
								'data': 'state_text',
								'className': 'text-center text-bold',
								'createdCell': function(td, cellData, rowData, row, col) {
								 	if (cellData == 'Done') {
										$(td).addClass('text-success');
									} else {
										$(td).addClass('text-primary');
									}
								}
							},
							{
								'className': 'text-center',
								'render': function(param, type, data, meta) {
									var valueText = '';
									if (data.value_text != null) valueText = ' ('+data.value_text+')'
									return data.description + valueText;
								}
							},
							{
								'data': 'instrument',
								'className': 'text-center'
							},
							{'data': 'manager', 'className': 'text-center'}
						],
						'order': [1, 'desc']
					});

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

				function childTranscation(data) {
					var html = '<table class=\"table table-condensed no-margin no-border\" style=\"margin: -8px\">';
					for (i = 0; i < data.length; i++) {
						var amount = data[i].amount_text;
						amount = data[i].type == \"income\" ? \"+\"+amount : \"-\"+amount;
						html += '<tr><td width=\"5%\" class=\"text-center\">-</td>';
						html += '<td class=\"text-center\">'+data[i].transaction_date+'</td>';
						html += '<td class=\"text-right\">'+amount+'</td>';
						var value = '';
						if (data[i].unit != null) value = data[i].value+' '+data[i].unit;
						html += '<td class=\"text-right\" width=\"10%\">'+value+'</td>';
						html += '<td colspan=2 width=\"50%\"></td></tr>';
					}
					html += '</table>';

					return html;
				}
			</script>
		";

		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('investment/investment_list');
		$this->load->view('root/_footer');
	}
}
