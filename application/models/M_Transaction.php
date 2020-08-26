<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Transaction extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getWhereTransaction() {
		return "(account_key = '".$this->session->userdata('user')->account_key."')";
	}

	function getCategories() {
		$this->db->where("category_id != 1");
		$this->db->order_by("parent_id", "ASC");
		$this->db->order_by("position", "ASC");
		return $this->db->get('category');
	}

	function getCategoriesInvestment() {
		return $this->db->get('category_investment');
	}

	function createTransactionView() {
		$query = "
			CREATE OR REPLACE VIEW transaction_view AS
			SELECT account_id, AVG(total) as average_amount_per_month, AVG(count) as average_item_per_month
			FROM (
			    SELECT account_id, SUM(amount) as total, COUNT(transaction_id) as count
			    FROM transaction
			    GROUP BY account_id, EXTRACT(YEAR FROM transaction_date), EXTRACT(MONTH FROM transaction_date)
			) transaction_extract
			GROUP BY account_id
		";

		$query_event = "
			CREATE EVENT update_transaction_view
			ON SCHEDULE EVERY 1 MINUTE
			STARTS '2020-05-09 11:25:00'
			DO
			CREATE OR REPLACE VIEW transaction_view AS
			SELECT account_id, AVG(total) as average_amount_per_month, AVG(count) as average_item_per_month
			FROM (
			    SELECT account_id, SUM(amount) as total, COUNT(transaction_id) as count
			    FROM transaction
			    GROUP BY account_id, EXTRACT(YEAR FROM transaction_date), EXTRACT(MONTH FROM transaction_date)
			) transaction_extract
			GROUP BY account_id
		";
	}

	function get($tag) {
		$query = "
			SELECT a.date, a.amount, a.description
			FROM (
				SELECT transaction_date as date, amount, description, added_date FROM `transaction` WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, description, added_date FROM transaction_oop WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
			) a
			ORDER BY a.date DESC
		";
		return $this->db->query($query);
	}

	function getAmountTag($tag) {
		$query = "
			SELECT SUM(a.amount) as total
			FROM (
				SELECT transaction_date as date, amount, tag FROM `transaction` WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, tag FROM transaction_oop WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
			) a
			GROUP BY a.tag
			ORDER BY a.date DESC
		";
		return $this->db->query($query);
	}

	//-------- Global --------//

	function addData($table, $data) {
		$this->db->insert($table, $data);
		return $this->db->affected_rows();
	}

	function updateData($table, $data, $where) {
		$this->db->where($where);
		$this->db->update($table, $data);
		return $this->db->affected_rows();
	}

	function deleteData($table, $where) {
		$this->db->delete($table, $where);
		return $this->db->affected_rows();
	}

	//-------- Transaction ---------//

	function getOneTransaction($transaction_id) {
		$this->db->where("transaction_id", $transaction_id);
		$this->db->where("account_key", $this->session->userdata('user')->account_key);
		$this->db->where($this->getWhereTransaction());
		return $this->db->get('transaction');
	}

	function getAllTransaction($limit, $type = "outcome") {
		// $this->db->limit($limit);
		$this->db->join("category", "category.category_id = transaction.category_id");
		$this->db->order_by("type", "DESC");
		$this->db->order_by("transaction_date", "DESC");
		$this->db->order_by("added_date", "DESC");
		$this->db->order_by("category.category_id", "ASC");
		$this->db->where("type", $type);
		$this->db->limit($limit);
		$this->db->where($this->getWhereTransaction());
		return $this->db->get('transaction');
	}

	function getRecurringTransaction() {
		$this->db->join("category", "category.category_id = transaction_recurring.category_id", "left");
		$this->db->where($this->getWhereTransaction());
		return $this->db->get("transaction_recurring");
	}

	//-------- Investment --------//

	function getOneInvestment($transaction_id) {
		$this->db->join("category_investment", "transaction_investment.category_id = category_investment.category_id", "left");
		$this->db->where("account_key", $this->session->userdata('user')->account_key);
		$this->db->where("transaction_investment_id", $transaction_id);
		return $this->db->get('transaction_investment');
	}

	function getInvestment() {
		$this->db->join("category_investment", "transaction_investment.category_id = category_investment.category_id", "left");
		$this->db->where("account_key", $this->session->userdata('user')->account_key);
		$this->db->order_by("transaction_date", "ASC");
		return $this->db->get('transaction_investment');
	}

	function getTotalInvestment() {
		$query = "
			SELECT SUM(CASE WHEN type = 'outcome' THEN amount ELSE -amount END) as total_investment
			FROM transaction_investment 
			WHERE account_key = '".$this->session->userdata('user')->account_key."'
		";
		return $this->db->query($query);
	}

	//--------- Debts ---------//

	function getDebtsBalance() {
		$query = "
			SELECT *
			FROM (
			    SELECT to_who, SUM(
			        IF (type = 'debts' OR type = 'transfer_from', -amount, amount)
			    ) AS balance
			    FROM debts
			    WHERE ".$this->getWhereTransaction()."
			    GROUP BY to_who
			    ORDER BY transaction_date ASC
			) AS debts_view
			WHERE debts_view.balance != 0
		";
		return $this->db->query($query);
	}

	function getDebtsList() {
		$query = "
			SELECT * FROM `debts`
			WHERE ".$this->getWhereTransaction()."
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}
}
?>