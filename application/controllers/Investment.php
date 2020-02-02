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
						'ajax': '".base_url('settings/getListInvestment')."',
						'columnDefs': [
							{'targets': 0, 'data': null, 'className': 'text-center', 'orderable': false, 'searching': false},
							{'targets': 1, 'data': 'date', 'className': 'text-center'},
							{
								'targets': 2, 
								'data': 'amount_text', 
								'className': 'text-right',
								'createdCell': function(td, cellData, rowData, row, col) {
									if (rowData.state_text == 'Done') {
										if (rowData.amount <= 0) $(td).addClass('text-success');
										else $(td).addClass('text-danger');
									}
								}
							},
							{
								'targets': 3, 
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
							{'targets': 4, 'data': 'description', 'className': 'text-center'},
							{'targets': 5, 'data': 'invest', 'className': 'text-center'},
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
						html += '<td colspan=3 width=\"60%\"></td></tr>';
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
