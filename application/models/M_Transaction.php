<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Transaction extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getWhereTransaction() {
		return "(account_id = ".$this->session->userdata('user')->account_id.")";
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

	function getDashboardTransaction($type = "outcome") {
		$where = " WHERE type = '".$type."' AND transaction_date > DATE_ADD(NOW(), INTERVAL -1 YEAR) AND ".$this->getWhereTransaction() ." ";

		$query = "
			SELECT a.year, a.month, total_transaction, total_investment
			FROM (
			    SELECT extract(year FROM transaction_date) year, extract(month FROM transaction_date) month, SUM(amount) as total_transaction
			    FROM transaction
			    ".$where."
			    GROUP BY extract(year FROM transaction_date), extract(month FROM transaction_date)
			) a
			LEFT JOIN (
			    SELECT extract(year FROM transaction_date) year, extract(month FROM transaction_date) month, SUM(amount) as total_investment
			    FROM transaction_investment
			    ".$where." AND category_id != 1
			    GROUP BY extract(year FROM transaction_date), extract(month FROM transaction_date)
			) b ON a.year = b.year AND a.month = b.month
		";
		return $this->db->query($query);
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

	function getFirstTransaction() {
		$this->db->order_by("transaction_date", "ASC");
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		$this->db->limit(1);
		return $this->db->get('transaction');
	}

	function get($tag) {
		$query = "
			SELECT a.date, a.amount, a.description
			FROM (
				SELECT transaction_date as date, amount, description, added_date FROM `transaction` WHERE tag = '".$tag."'
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, description, added_date FROM transaction_oop WHERE tag = '".$tag."'
			) a
			ORDER BY a.date DESC
		";
		return $this->db->query($query);
	}

	function getAmountTag($tag) {
		$query = "
			SELECT SUM(a.amount) as total
			FROM (
				SELECT transaction_date as date, amount, tag FROM `transaction` WHERE tag = '".$tag."'
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, tag FROM transaction_oop WHERE tag = '".$tag."'
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
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
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

	function getTotalPerMonthTransaction() {
		$query = "
			SELECT EXTRACT(YEAR FROM transaction_date) AS year, EXTRACT(MONTH FROM transaction_date) AS month, SUM(amount) AS total_monthly, COUNT(transaction_id) as count_monthly
			FROM transaction
			WHERE ".$this->getWhereTransaction()." AND type = 'outcome'
			GROUP BY EXTRACT(YEAR FROM transaction_date), EXTRACT(MONTH FROM transaction_date)
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}

	function getMonthTransaction($month, $year, $category_id) {
		$this->db->join("category", "category.category_id = transaction.category_id", "left");
		$this->db->order_by("type", "DESC");
		$this->db->order_by("transaction_date", "DESC");
		$this->db->order_by("added_date", "DESC");
		$this->db->order_by("category.category_id", "ASC");
		$this->db->where("type", "outcome");
		$this->db->where("MONTH(transaction_date)", $month);
		$this->db->where("YEAR(transaction_date)", $year);
		$this->db->where($this->getWhereTransaction());
		
		if ($category_id != 0) $this->db->where("(category.category_id = ".$category_id." OR category.parent_id = ".$category_id.")");

		return $this->db->get('transaction');
	}

	function getTopTransaction($month, $year, $type = "outcome") {
		$where = "MONTH(transaction_date) = '".$month."' AND YEAR(transaction_date) = '".$year."' AND ".$this->getWhereTransaction();
		$query = "
			SELECT t.*, category.category_name
			FROM (
			SELECT 
			    (CASE WHEN category.parent_id = 1 THEN category.category_id ELSE category.parent_id END) as category_id, 
			    transaction_date, 
			    SUM(amount) as total,
			    (SUM(amount) * 100 / (SELECT SUM(amount) FROM transaction WHERE ".$where.")) as percentage
			FROM transaction
			JOIN category ON category.category_id = transaction.category_id
			WHERE ".$where."
			GROUP BY CASE WHEN category.parent_id = 1 THEN category.category_id ELSE category.parent_id END, extract(year from transaction_date), extract(month from transaction_date)
			ORDER BY total DESC) t
			LEFT JOIN category ON t.category_id = category.category_id
		";
		return $this->db->query($query);
	}

	function getRecurringTransaction() {
		$this->db->join("category", "category.category_id = transaction_recurring.category_id", "left");
		return $this->db->get("transaction_recurring");
	}

	//-------- Investment --------//

	function getOneInvestment($transaction_id) {
		$this->db->join("category_investment", "transaction_investment.category_id = category_investment.category_id", "left");
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		$this->db->where("transaction_investment_id", $transaction_id);
		return $this->db->get('transaction_investment');
	}

	function getInvestment() {
		$this->db->join("category_investment", "transaction_investment.category_id = category_investment.category_id", "left");
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		$this->db->order_by("transaction_date", "ASC");
		return $this->db->get('transaction_investment');
	}

	function getTotalInvestment() {
		$query = "
			SELECT SUM(CASE WHEN type = 'outcome' THEN amount ELSE -amount END) as total_investment
			FROM transaction_investment 
			WHERE account_id = ".$this->session->userdata('user')->account_id."
		";
		return $this->db->query($query);
	}

	//--------- Debts ---------//

	function getDebtsBalance() {
		$query = "
			SELECT to_who, SUM(
			    IF (type = 'debts' OR type = 'transfer_from', -amount, amount)
			) AS balance
			FROM `debts` WHERE ".$this->getWhereTransaction()."
			GROUP BY to_who
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