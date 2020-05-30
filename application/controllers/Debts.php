<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debts extends MY_Controller {

	function __construct() {
		parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->model('M_Transaction');
	}

	function index() { }

	function list() {
		$result['list'] = "";
		$result['debts'] = $this->getAllDebtsData();

		$result['add_footer'] = "
			<script>
				$('#modal-default').on('show.bs.modal', function (event) {
					var button = $(event.relatedTarget)
					var id = button.data('id');
					console.log(id);

					var modal = $(this);
					var title = 'Add transaction debts';
					var buttonTitle = 'Add';
					if (id !== '') {
						title = 'Edit transaction debts';
						buttonTitle = 'Edit';
					}
					
					modal.find('.modal-title').text(title);
					modal.find('.modal-body').html(formInputDebts(id));
					modal.find('form').attr({method: 'GET', action: '".base_url('debts/insert')."'});
					modal.find(':submit').html(buttonTitle);
				});

				function formInputDebts(id) {
					var html = '<input type=\"hidden\" name=\"debts_id\" value=\"'+id['debts_id']+'\" />' +
					'<div class=\"form-group\">' +
			            '<label>Date <span class=\"text-red\">*)</span></label>' +
			            '<div class=\"input-group\">' +
			                '<div class=\"input-group-addon\">' +
			                    '<span class=\"fa fa-calendar\"></span>' +
			                '</div>' +
			                '<input class=\"form-control datetimepicker\" type=\"text\" name=\"date\" placeholder=\"Date\" value=\"'+id['transaction_date']+'\" />' +
			            '</div>' +
			        '</div>' +
			        '<div class=\"form-group\">' +
			            '<label>Amount <span class=\"text-red\">*)</span></label>' +
			            '<div class=\"input-group\">' +
			                '<div class=\"input-group-addon\">Rp.</div>' +
			                '<input class=\"form-control\" type=\"number\" name=\"amount\" placeholder=\"Amount\" value=\"'+id['amount']+'\" />' +
			            '</div>' +
			        '</div>' +
			        '<div class=\"form-group\">' +
			            '<label>Type <span class=\"text-red\">*)</span></label>' +
			            '<select class=\"form-control\" name=\"type\">' +
			                '<option value=\"debts\">Debts</option>' +
			                '<option value=\"receivables\">Receivables</option>' +
			                '<option value=\"transfer_to\">Transfer to</option>' +
			                '<option value=\"transfer_from\">Transfer from</option>' +
			            '</select>' +
			        '</div>' +
			        '<div class=\"form-group\">' +
			            '<label>Who <span class=\"text-red\">*)</span></label>' +
			            '<div class=\"input-group\">' +
			                '<div class=\"input-group-addon\">' +
			                    '<span class=\"fa fa-user\"></span>' +
			                '</div>' +
			                '<input class=\"form-control\" type=\"text\" name=\"who\" placeholder=\"Who\" value=\"'+id['to_who']+'\" />' +
			            '</div>' +
			        '</div>' +
			        '<div class=\"form-group\">' +
			            '<label>Description</label>' +
			            '<input class=\"form-control\" type=\"text\" name=\"description\" placeholder=\"Description\" value=\"'+id['description']+'\" />' +
			        '</div>' +
			        '<div class=\"form-group\">' +
			            '<label>Deadline</label>' +
			            '<div class=\"input-group\">' +
			                '<div class=\"input-group-addon\">' +
			                    '<span class=\"fa fa-calendar\"></span>' +
			                '</div>' +
			                '<input class=\"form-control datetimepicker\" name=\"deadline\" placeholder=\"Deadline\" />' +
			            '</div>' +
			        '</div>';
			        return html;
				}
			</script>
		";

		$this->load->view('root/_header', $result, $GLOBALS);
		$this->load->view('root/_menus');
		$this->load->view('debts/list');
		$this->load->view('root/_footer');
	}

	function insert() {
		$arr["transaction_date"] = $this->input->get("date");
		$arr["to_who"] = $this->input->get("who");
		$arr["type"] = $this->input->get("type");
		$arr["amount"] = $this->input->get("amount");
		$arr["description"] = $this->input->get("description");
		// $arr["deadline"] = $this->input->get("date");
		$arr["account_id"] = $this->session->userdata('user')->account_id;

		if ($this->input->get("debts_id") !== 'undefined') print_r($arr);

		// $result = $this->addNewDebts($arr);
		// if ($result > 0) {
		// 	header("location:".base_url("debts/list"));
		// }
	}
}
